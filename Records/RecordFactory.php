<?php

namespace X937\Records;

use X937\Fields\Field;

/**
 * A factor class to generate new X937Record from different sorts of input.
 *
 * @author astanley
 */
class RecordFactory
{
    /**
     * Contains a template of reference record structures, parsed from the
     * specification file. Used to create record objects.
     * @todo consider using an array of template objects for this instead?
     *
     * @var array
     */
    protected $recordsTemplateArray;

    protected $globalPredefines;

    public function __construct(string $specXMLFile)
    {
        // guard input
        $specDOM = new \DOMDocument();
        if (!$specDOM->load($specXMLFile)) {
            throw new \InvalidArgumentException("Loading of XML file $specXMLFile failed.");
        }
        if (!$specDOM->schemaValidate(__DIR__ . DIRECTORY_SEPARATOR . 'X937Specification.xsd')) {
            throw new \InvalidArgumentException("$specXMLFile failed schema validation.");
        }

        // create our XPath
        $specXPath = new \DOMXPath($specDOM);

        // parse global dictonaries, at least one is mandatory.
        $dictonaryDOMList = $specXPath->query('/records/dictonaries/dictonary');
        foreach ($dictonaryDOMList as $dictonaryDOM) {
            $id = $dictonaryDOM->getAttribute('id');
            $this->globalPredefines[$id] = self::parseDictonary($dictonaryDOM);
        }

        // parse global pre-defined fields, at least one is mandatory
        $fieldsDOMList = $specXPath->query('/records/predefines/field');
        foreach ($fieldsDOMList as $predefinedFieldDOM) {
            $id = $predefinedFieldDOM->getAttribute('id');
            $this->globalPredefines[$id] = $this->parseField($predefinedFieldDOM);
        }

        // parse each record
        $recordDOMList = $specXPath->query('/records/record|forbidden');
        foreach ($recordDOMList as $recordDOM) {
            // parse the root level record properties
            $recordArray = self::parseProperties($recordDOM, Record::PARSED_PROPERTIES);

            // parse the field properties
            $fieldsArray = array();
            $fieldsDOM = $specXPath->query('./fields/field|./fields/predefined', $recordDOM);
            foreach ($fieldsDOM as $fieldDOM) {
                $fieldOrder = $fieldDOM->getAttribute('order');
                switch ($fieldDOM->tagName) {
                    case 'field':
                        $fieldsArray[$fieldOrder] = $this->parseField($fieldDOM);
                        break;
                    case 'predefined':
                        $globalFieldId = $fieldDOM->getAttribute('ref');
                        $fieldsArray[$fieldOrder] = $this->globalPredefines[$globalFieldId];
                        break;
                }
                // set the field order
                $fieldsArray[$fieldOrder][Field::PROP_ORDER] = $fieldOrder;
            }

            // forbidden records lack field data, so we handle that.
            switch ($recordDOM->tagName) {
                case 'forbidden':
                    // do nothing
                    break;
                case 'record':
                    $recordArray['fields'] = $fieldsArray;
                    self::validateTemplate($recordArray);
                    break;
            }

            $recordType = $recordArray['type'];
            $this->recordsTemplateArray[$recordType] = $recordArray;
        }
    }

    /**
     * Parses a Dictonary dom element into an array of key-value pairs. Plus
     * one special element that defines if the dictonary is comprehensive or not
     *
     * @param \DOMElement $dictonaryDOM the Dictonary to be parsed
     * @return array an array of key value pairs for the dictonary.
     */
    protected function parseDictonary(\DOMElement $dictonaryDOM): array
    {
        $dictonaryArray = array();

        $valuesDOM = $dictonaryDOM->getElementsByTagName('value');
        foreach ($valuesDOM as $valueDOM) {
            $key = $valueDOM->getAttribute('key');
            $dictonaryArray[$key] = $valueDOM->nodeValue;
        }

        return $dictonaryArray;
    }

    protected function parseField(\DOMElement $fieldDOM): array
    {
        $fieldArray = self::parseProperties($fieldDOM, Field::PARSED_PROPERTIES);

        // parse the dictonary values
        $dictonaryDOM = $fieldDOM->getElementsByTagName(Field::PROP_DICTONARY)->item(0);

        // if a field does not have a dictonary (allowed), then the above will return null, so we catch that case.
        if ($dictonaryDOM !== NULL) {
            $fieldArray[Field::PROP_DICT_COVERAGE] = $dictonaryDOM->getAttribute(Field::PROP_DICT_COVERAGE);

            // some dictonaries mearly point back to the global dictonaries, so handle that.
            $dictonaryRef = $dictonaryDOM->getAttribute('ref');
            if (array_key_exists($dictonaryRef, $this->globalPredefines)) {
                $dictonaryArray = $this->globalPredefines[$dictonaryRef];
            } else {
                $dictonaryArray = self::parseDictonary($dictonaryDOM);
            }

            $fieldArray[Field::PROP_DICTONARY] = $dictonaryArray;
        }

        return $fieldArray;
    }

    /**
     * Parses a given element for elements contained in the property list array.
     * Returns these items as key-value pairs in an array
     *
     * @param \DOMElement $elementDOM the Dom element to be parsed
     * @param array $properties a key-value list of array elements.
     * @return array
     */
    protected function parseProperties(\DOMElement $elementDOM, array $properties): array
    {
        foreach ($properties as $property) {
            $propertyDOM = $elementDOM->getElementsByTagName($property)->item(0);

            if ($propertyDOM !== NULL) {
                $propertyArray[$property] = $propertyDOM->nodeValue;
            } else {
                $propertyArray[$property] = null;
            }
        }

        // variableLength can be in the form, ###+X+Y, and if so we need
        // to get the static part of the length, which must be index 0.
        if (isset($propertyArray[Field::PROP_VARIABLELENGTH])) {
            $lengthArray = explode('+', $propertyArray[Field::PROP_VARIABLELENGTH]);
            if (is_numeric($lengthArray[0])) {
                $propertyArray[Field::PROP_LENGTH] = $lengthArray[0];
            } else {
                $propertyArray[Field::PROP_LENGTH] = 0;
            }
        }

        // variablePosition must be in the form, ###+X+Y, and if so we need
        // to get the static part of the length, which must be index 0.
        if (isset($propertyArray[Field::PROP_VARIABLEPOSITION])) {
            $positionArray = explode('+', $propertyArray[Field::PROP_VARIABLEPOSITION]);
            $propertyArray[Field::PROP_POSITION] = $positionArray[0];
        }

        return $propertyArray;
    }

    /**
     * Performs internal sanity checking on the internally generated RecordTempalte
     * to see if it violates constraints of field count, length, and overlap.
     *
     * @return bool always returns True, because it will throw an exception otherwise.
     * @throws \InvalidArgumentException If the recordTemplate doesn't validate.
     */
    public static function validateTemplate(array $recordArray): bool
    {
        $recordType = $recordArray[Record::PROP_TYPE];
        $recordVariable = isset($recordArray[Record::PROP_VARIABLELENGTH]);

        // validate each field
        $currentPos = $idStart = 1;
        foreach ($recordArray['fields'] as $fieldOrder => $fieldArray) {
            $position = $fieldArray[Field::PROP_POSITION];
            $length = $fieldArray[Field::PROP_LENGTH];

            // if the record start position doesn't equals our calculated currentPos, then we have a gap.
            if ($position != $currentPos) {
                throw new \InvalidArgumentException("Gap in record type $recordType at field $fieldOrder position $position expected position $currentPos");
            }

            // validate that our fieldId's are in sequence.
            if ($idStart != $fieldOrder) {
                throw new \InvalidArgumentException("Field Id $fieldOrder out of sequence. Expceted $idStart");
            }

            // calculate where the range should end. The end becomes the new currentPos.
            $end = $position + $length;
            $currentPos = $end;
            $idEnd = $idStart;
            $idStart++;

            // check if our field is variable and the record is not.
            if ((!$recordVariable) && (isset($fieldArray[Field::PROP_VARIABLEPOSITION]))) {
                throw new \InvalidArgumentException("Records type $recordType has a variable field $fieldOrder but the record is not infered variable.");
            }
        }

        // validate record length and count
        $recordLength = $recordArray[Record::PROP_LENGTH];
        $recordCount = $recordArray[Record::PROP_FIELDCOUNT];
        $end -= 1; // move the end back one to correctly calculate.

        // we validate against
        if ($recordLength != $end) {
            throw new \InvalidArgumentException("Records type $recordType's length of $recordLength does not match calculated length of $end");
        }
        if ($recordCount != $idEnd) {
            throw new \InvalidArgumentException("Records type $recordType's field count of $recordCount does not match calculated count of $idEnd");
        }

        //if we get here, everything is great.
        return true;
    }

    public function generateRecord(string $recordData, string $dataType)
    {
        $recordTypeRaw = substr($recordData, 0, 2);

        switch ($dataType) {
            case \X937\File::DATA_ASCII:
                $recordType = $recordTypeRaw;
                break;
            case \X937\File::DATA_EBCDIC:
                $recordType = \X937\Fields\Format\Util::e2a($recordTypeRaw);
                break;
            default:
                throw new \InvalidArgumentException("Bad dataType passed: $dataType");
        }
        
        // type 68, User Records need Form and Version data to decode them.
        if ($recordType === RecordType::USER_RECORD) {
            $formatVersion = substr($recordData, 32, 3) . substr($recordData, 35, 3);
            switch ($dataType) {
                case \X937\File::DATA_ASCII:
                    $recordType .= $formatVersion;
                    break;
                case \X937\File::DATA_EBCDIC:
                    $recordType .= \X937\Fields\Format\Util::e2a($formatVersion);
                    break;
            }
            $recordType .= substr($recordData, 32, 3) . substr($recordData, 35, 3);
        }

        // check if we have a record type in our array for this record type, we should.
        // the index of that record template should corespond with that record type
        if (isset($this->recordsTemplateArray[$recordType])) {
            $record = new Record($this->recordsTemplateArray[$recordType]);
            $record->parse($recordData, $dataType);

            return $record;
        } else {
            throw new \InvalidArgumentException("No matching record template for record type $recordType");
        }
    }
}