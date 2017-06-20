<?php

namespace X937\Record;

use X937\Fields\Predefined\RecordType;
use X937\Fields\Field;

/**
 * A factor class to generate new X937Record from different sorts of input.
 *
 * @author astanley
 */
class Factory
{
    
    // record types
    const RECORD_TYPE_FILE_HEADER             = '01';
    const RECORD_TYPE_CASH_LETTER_HEADER      = '10';
    const RECORD_TYPE_BUNDLE_HEADER           = '20';
    const RECORD_TYPE_CHECK_DETAIL            = '25';
    const RECORD_TYPE_CHECK_DETAIL_ADDENDUM_A = '26';
    const RECORD_TYPE_CHECK_DETAIL_ADDENDUM_B = '27';
    const RECORD_TYPE_CHECK_DETAIL_ADDENDUM_C = '28';
    const RECORD_TYPE_RETURN_RECORD           = '31';
    const RECORD_TYPE_RETURN_ADDENDUM_A       = '32';
    const RECORD_TYPE_RETURN_ADDENDUM_B       = '33';
    const RECORD_TYPE_RETURN_ADDENDUM_C       = '34';
    const RECORD_TYPE_RETURN_ADDENDUM_D       = '35';
    const RECORD_TYPE_ACCOUNT_TOTALS_DETAIL   = '40';
    const RECORD_TYPE_NON_HIT_TOTALS_DETAIL   = '41';
    const RECORD_TYPE_IMAGE_VIEW_DETAIL       = '50';
    const RECORD_TYPE_IMAGE_VIEW_DATA         = '52';
    const RECORD_TYPE_IMAGE_VIEW_ANALYSIS     = '54';
    const RECORD_TYPE_BUNDLE_CONTROL          = '70';
    const RECORD_TYPE_BOX_SUMMARY             = '75';
    const RECORD_TYPE_ROUTING_NUMBER_SUMMARY  = '85';
    const RECORD_TYPE_CASH_LETTER_CONTROL     = '90';
    const RECORD_TYPE_FILE_CONTROL            = '99';

    // record definitions
    const RECORD_TYPE_DEFINITIONS = [
        self::RECORD_TYPE_FILE_HEADER             => 'File Header Record',
        self::RECORD_TYPE_CASH_LETTER_HEADER      => 'Cash Letter Header Record',
        self::RECORD_TYPE_BUNDLE_HEADER           => 'Bundle Header Record',
        self::RECORD_TYPE_CHECK_DETAIL            => 'Check Detail Record',
        self::RECORD_TYPE_CHECK_DETAIL_ADDENDUM_A => 'Check Detail Addendum A Record',
        self::RECORD_TYPE_CHECK_DETAIL_ADDENDUM_B => 'Check Detail Addendum B Record',
        self::RECORD_TYPE_CHECK_DETAIL_ADDENDUM_C => 'Check Detail Addendum C Record',
        self::RECORD_TYPE_RETURN_RECORD           => 'Return Record',
        self::RECORD_TYPE_RETURN_ADDENDUM_A       => 'Retrun Addendum A Record',
        self::RECORD_TYPE_RETURN_ADDENDUM_B       => 'Return Addendum B Record',
        self::RECORD_TYPE_RETURN_ADDENDUM_C       => 'Return Addendum C Record',
        self::RECORD_TYPE_RETURN_ADDENDUM_D       => 'Return Addendum D Record',
        self::RECORD_TYPE_ACCOUNT_TOTALS_DETAIL   => 'Account Totals Detail Record',
        self::RECORD_TYPE_NON_HIT_TOTALS_DETAIL   => 'Non-Hit Total Detail Record',
        self::RECORD_TYPE_IMAGE_VIEW_DETAIL       => 'Image View Detail Record',
        self::RECORD_TYPE_IMAGE_VIEW_DATA         => 'Image View Data Record',
        self::RECORD_TYPE_IMAGE_VIEW_ANALYSIS     => 'Image View Analysis',
        self::RECORD_TYPE_BUNDLE_CONTROL          => 'Bundle Control Record',
        self::RECORD_TYPE_BOX_SUMMARY             => 'Box Summary Record',
        self::RECORD_TYPE_ROUTING_NUMBER_SUMMARY  => 'Routing Number Summary Record',
        self::RECORD_TYPE_CASH_LETTER_CONTROL     => 'Cash Letter Control Record',
        self::RECORD_TYPE_FILE_CONTROL            => 'File Control Record',
    ];
    
    /**
     * Contains a template of reference record structures, parsed from the
     * specification file. Used to create record objects.
     * @todo consider using an array of template objects for this instead?
     * 
     * @var array
     */
    protected $recordsTemplateArray;
    
    protected $globalPredefines;
    
    /**
     * Parses a Dictonary dom element into an array of key-value pairs. Plus
     * one special element that defines if the dictonary is comprehensive or not
     * 
     * @param \DOMElement $dictonaryDOM the Dictonary to be parsed
     * @return array an array of key value pairs for the dictonary.
     */
    protected function parseDictonary(\DOMElement $dictonaryDOM): array {  
        $dictonaryArray['comprehensive'] = $dictonaryDOM->getAttribute('comprehensive');

        $valuesDOM = $dictonaryDOM->getElementsByTagName('value');
        foreach ($valuesDOM as $valueDOM) {
            $key = $valueDOM->getAttribute('key');
            $dictonaryArray[$key] = $valueDOM->nodeValue;
        }

        return $dictonaryArray;
    }
    
    /**
     * Parses a given element for elements contained in the property list array.
     * Returns these items as key-value pairs in an array 
     * 
     * @param \DOMElement $elementDOM the Dom element to be parsed
     * @param array $properties a key-value list of array elements.
     * @return array
     */
    protected function parseProperties(\DOMElement $elementDOM, array $properties): array {
        foreach ($properties as $property) {
            $propertyDOM = $elementDOM->getElementsByTagName($property)->item(0);
            
            if ($propertyDOM !== NULL) {
                $propertyArray[$property] = $propertyDOM->nodeValue;
            } else {
                continue;
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
            
            $propertyArray[Field::PROP_VARIABLE] = true;
        }
        
        // variablePosition must be in the form, ###+X+Y, and if so we need
        // to get the static part of the length, which must be index 0.
        if (isset($propertyArray[Field::PROP_VARIABLEPOSITION])) {
            $positionArray = explode('+', $propertyArray[Field::PROP_VARIABLEPOSITION]);
            $propertyArray[Field::PROP_POSITION] = $positionArray[0];
            
            $propertyArray[Field::PROP_VARIABLE] = true;
        }
        
        // if we didn't set the record to variable above, we want to set it to false now
        if (!isset($propertyArray[Field::PROP_VARIABLE])) {
            $propertyArray[Field::PROP_VARIABLE] = false;
        }        
        
        return $propertyArray;    
    }
    
    protected function parseField(\DOMElement $fieldDOM): array {
        $fieldArray = self::parseProperties($fieldDOM, \X937\Fields\Field::PARSED_PROPERTIES);
                
        // parse the dictonary values
        $dictonaryArray = array();
        $dictonaryDOM   = $fieldDOM->getElementsByTagName(Field::PROP_DICTONARY)->item(0);

        // if a field does not have a dictonary (allowed), then the above will return null, so we catch that case.
        if ($dictonaryDOM !== NULL) {
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

    public function __construct(string $specXMLFile) {
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
            $fieldsDOM  = $specXPath->query('./fields/field|./fields/predefined', $recordDOM);           
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
                    $recordArray['fields']     = $fieldsArray;
                    Record::validateTemplate($recordArray);
                    break;
            }
            
            $recordType = $recordArray['type'];
            $this->recordsTemplateArray[$recordType] = $recordArray;
        }
    }

    public function generateRecord(string $recordData, string $dataType) {
        $recordTypeRaw = substr($recordData, 0, 2);
        
        switch ($dataType) {
            case \X937\File::DATA_ASCII:
                $recordType = $recordTypeRaw;
                break;
            case \X937\File::DATA_EBCDIC:
                if (PHP_OS == 'Linux') {
                    $recordType = iconv(\X937\Util::DATA_EBCDIC, \X937\Util::DATA_ASCII, $recordTypeRaw);
                } else {
                    $recordType = \X937\Util::e2a($recordTypeRaw);
                }
                break;
        default:
            throw new \InvalidArgumentException("Bad dataType passed: $dataType");
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