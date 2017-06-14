<?php

namespace X937\Record;

use X937\X937File;
use X937\Fields\Predefined\RecordType;
use X937\Fields\Field2;

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
        if (isset($propertyArray[Field2::PROP_VARIABLELENGTH])) {
            $lengthArray = explode('+', $propertyArray[Field2::PROP_VARIABLELENGTH]);
            if (is_numeric($lengthArray[0])) {
                $propertyArray[Field2::PROP_LENGTH] = $lengthArray[0];
            } else {
                $propertyArray[Field2::PROP_LENGTH] = 0;
            }
        }
        
        // variablePosition must be in the form, ###+X+Y, and if so we need
        // to get the static part of the length, which must be index 0.
        if (isset($propertyArray[Field2::PROP_VARIABLEPOSITION])) {
            $positionArray = explode('+', $propertyArray[Field2::PROP_VARIABLEPOSITION]);
            $propertyArray[Field2::PROP_POSITION] = $positionArray[0];
        }
        
        return $propertyArray;    
    }
    
    protected function parseField(\DOMElement $fieldDOM): array {
        $fieldArray = self::parseProperties($fieldDOM, \X937\Fields\Field2::LEAF_PROPERTIES);
                
        // parse the dictonary values
        $dictonaryArray = array();
        $dictonaryDOM   = $fieldDOM->getElementsByTagName(Field2::PROP_DICTONARY)->item(0);

        // if a field does not have a dictonary (allowed), then the above will return null, so we catch that case.
        if ($dictonaryDOM !== NULL) {
            // some dictonaries mearly point back to the global dictonaries, so handle that.
            $dictonaryRef = $dictonaryDOM->getAttribute('ref');
            if (array_key_exists($dictonaryRef, $this->globalPredefines)) {
                $dictonaryArray = $this->globalPredefines[$dictonaryRef];
            } else {
                $dictonaryArray = self::parseDictonary($dictonaryDOM);
            }

            $fieldArray[Field2::PROP_DICTONARY] = $dictonaryArray;
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
            $recordArray = self::parseProperties($recordDOM, Record2::LEAF_PROPERTIES);
            
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
            }
            
            // forbidden records lack field data, so we handle that.
            switch ($recordDOM->tagName) {
                case 'forbidden':
                    // do nothing
                    break;
                case 'record':
                    $recordArray['fields']     = $fieldsArray;
                    Record2::validateTemplate($recordArray);
                    break;
            }
            
            $recordType = $recordArray['type'];
            $this->recordsTemplateArray[$recordType] = $recordArray;
        }
    }
    
    
    /**
     * Performs internal sanity checking on the internally generated RecordTempalte
     * to see if it violates constraints of field count, length, and overlap.
     * 
     * @return bool always returns True, because it will throw an exception otherwise.
     * @throws \InvalidArgumentException If the recordTemplate doesn't validate.
     */
    protected function validateRecordTemplate(array $recordArray): bool {
        $recordType = $recordArray[Record2::PROP_TYPE];

        // validate each field
        $currentPos = $idStart = 1;
        foreach ($recordArray['fields'] as $fieldOrder => $fieldArray) {            
            $position = $fieldArray[Field2::PROP_POSITION];
            $length   = $fieldArray[Field2::PROP_LENGTH];

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
        }

        // validate record length and count
        $recordLength = $recordArray[Record2::PROP_LENGTH];
        $recordCount  = $recordArray[Record2::PROP_FIELDCOUNT];
        $end -= 1; // move the end back one to correctly calculate.

        // we validate against 
        if ($recordLength != $end) {
            throw new \InvalidArgumentException("Record type $recordType's length of $recordLength does not match calculated length of $end");
        }
        if ($recordCount != $idEnd) {
            throw new \InvalidArgumentException("Record type $recordType's field count of $recordCount does not match calculated count of $idEnd");
        }
        
        //if we get here, everything is great.
        return true;
    }

    public function generateRecord(string $recordData, string $dataType) {
        $recordTypeRaw = substr($recordData, 0, 2);
        
        switch ($dataType) {
            case X937File::DATA_ASCII:
                $recordType = $recordTypeRaw;
                break;
            case X937File::DATA_EBCDIC:
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
            $record = new Record2($this->recordsTemplateArray[$recordType]);
            $record->parse($recordData, $dataType);
            
            return $record;
        } else {
            throw new \InvalidArgumentException("No matching record template for record type $recordType");
        }
    }
    
    /**
     * Generates an appropriate X937 Record from the raw record data.
     * 
     * @param string $recordData raw Record data as from the X937 file, generally EBCDIC format.
     * @param string $dataType   the type of the data a X937File const, either DATA_EBCDIC or DATA_ASCII
     * @return Record returns an X937Record of the appropriate type.
     */
    public static function GenerateFromRawData($recordData, $dataType) {
        $recordTypeRaw = substr($recordData, 0, 2);
        
        switch ($dataType) {
            case X937File::DATA_ASCII:
                $recordType = $recordTypeRaw;
                break;
            case X937File::DATA_EBCDIC:
                if (PHP_OS == 'Linux') {
                    $recordType = iconv(X937File::DATA_EBCDIC, X937File::DATA_ASCII, $recordTypeRaw);
                } else {
                    $recordType = self::e2aConverter($recordTypeRaw);
                }
                break;
        default:
            throw new \InvalidArgumentException("Bad dataType passed: $dataType");
        }
            
        return self::newRecord($recordType, $recordData, $dataType);
    }
    
    /**
     * Converts the dreaded EBCDIC to ASCII
     * @param string $eBinaryString raw EBCDIC hex string, in the format \xF0\xF1...
     * @return string Decoded ASCII data
     */
     public static function e2aConverter($eBinaryString ) {
        $e2aTable = [];
        $e2aTable['40'] = ' ';
        $e2aTable['4A'] = "¢";
        $e2aTable['4B'] = ".";
        $e2aTable['4C'] = "<";
        $e2aTable['4D'] = "(";
        $e2aTable['4E'] = "+";
        $e2aTable['4F'] = "|";
        $e2aTable['5A'] = "!";
        $e2aTable['5B'] = "$";
        $e2aTable['5C'] = "*";
        $e2aTable['5D'] = ")";
        $e2aTable['5E'] = ";";
        $e2aTable['5F'] = "¬";
        $e2aTable['60'] = "-";
        $e2aTable['61'] = "/";
        $e2aTable['6A'] = "¦";
        $e2aTable['6B'] = ",";
        $e2aTable['6C'] = "%";
        $e2aTable['6D'] = "_";
        $e2aTable['6E'] = ">";
        $e2aTable['6F'] = "?";
        $e2aTable['79'] = "`";
        $e2aTable['7A'] = ":";
        $e2aTable['7B'] = "#";
        $e2aTable['7C'] = "@";
        $e2aTable['7D'] = "'";
        $e2aTable['7E'] = "=";
        $e2aTable['7F'] = '"';
        $e2aTable['81'] = "a";
        $e2aTable['82'] = "b";
        $e2aTable['83'] = "c";
        $e2aTable['84'] = "d";
        $e2aTable['85'] = "e";
        $e2aTable['86'] = "f";
        $e2aTable['87'] = "g";
        $e2aTable['88'] = "h";
        $e2aTable['89'] = "i";
        $e2aTable['91'] = "j";
        $e2aTable['92'] = "k";
        $e2aTable['93'] = "l";
        $e2aTable['94'] = "m";
        $e2aTable['95'] = "n";
        $e2aTable['96'] = "o";
        $e2aTable['97'] = "p";
        $e2aTable['98'] = "q";
        $e2aTable['99'] = "r";
        $e2aTable['A1'] = "~";
        $e2aTable['A2'] = "s";
        $e2aTable['A3'] = "t";
        $e2aTable['A4'] = "u";
        $e2aTable['A5'] = "v";
        $e2aTable['A6'] = "w";
        $e2aTable['A7'] = "x";
        $e2aTable['A8'] = "y";
        $e2aTable['A9'] = "z";
        $e2aTable['C0'] = "{";
        $e2aTable['C1'] = "A";
        $e2aTable['C2'] = "B";
        $e2aTable['C3'] = "C";
        $e2aTable['C4'] = "D";
        $e2aTable['C5'] = "E";
        $e2aTable['C6'] = "F";
        $e2aTable['C7'] = "G";
        $e2aTable['C8'] = "H";
        $e2aTable['C9'] = "I";
        $e2aTable['D0'] = "}";
        $e2aTable['D1'] = "J";
        $e2aTable['D2'] = "K";
        $e2aTable['D3'] = "L";
        $e2aTable['D4'] = "M";
        $e2aTable['D5'] = "N";
        $e2aTable['D6'] = "O";
        $e2aTable['D7'] = "P";
        $e2aTable['D8'] = "Q";
        $e2aTable['D9'] = "R";
        $e2aTable['E0'] = '\\';
        $e2aTable['E2'] = "S";
        $e2aTable['E3'] = "T";
        $e2aTable['E4'] = "U";
        $e2aTable['E5'] = "V";
        $e2aTable['E6'] = "W";
        $e2aTable['E7'] = "X";
        $e2aTable['E8'] = "Y";
        $e2aTable['E9'] = "Z";
        $e2aTable['F0'] = "0";
        $e2aTable['F1'] = "1";
        $e2aTable['F2'] = "2";
        $e2aTable['F3'] = "3";
        $e2aTable['F4'] = "4";
        $e2aTable['F5'] = "5";
        $e2aTable['F6'] = "6";
        $e2aTable['F7'] = "7";
        $e2aTable['F8'] = "8";
        $e2aTable['F9'] = "9";
        $e2aTable['FF'] = "E0";

        // loop until there is no more conversion.
        $asciiOut = "";    
        while(strlen($eBinaryString)>=1)
        {
            $thisEbcdic = strtoupper(bin2hex(substr($eBinaryString, 0, 1)));
            if (array_key_exists($thisEbcdic, $e2aTable)) {
                $asciiOut = $asciiOut . $e2aTable[$thisEbcdic];
            } else {
                $asciiOut .= ' ';
            }
            $eBinaryString = substr($eBinaryString, 1);

        }    

        return $asciiOut;
    }

    /**
     * Creates an new X937Record from appropriate input.
     * @param string $recordType the type of the record, in ASCII
     * @param string $recordData the raw record data
     * @param string $dataType the type of the record, X937File const either DATA_EBCDIC or DATA_ASCII
     * @return Record an approriate record type
     * @throws InvalidArgumentException if given bad data
     */
    private static function newRecord($recordType, $recordData, $dataType = X937File::DATA_EBCDIC) {
        if (array_key_exists($recordType, self::RECORD_TYPE_DEFINITIONS) === FALSE) {
            throw new \InvalidArgumentException("Bad record type passed.");
        }

        // convert the record data if necessary.
        /**
         * @todo consider how to handle binary data here? Or push this elsware.
         * Currently it will warn on binary data, which we suppress.
         */
        if ($dataType === X937File::DATA_EBCDIC) {
            if (PHP_OS == 'Linux') { 
                $recordDataASCII = @iconv(X937File::DATA_EBCDIC, X937File::DATA_ASCII, $recordData);
            } else {
                $recordDataASCII = self::e2aConverter($recordData);
            }
        } else {
            $recordDataASCII = $recordData;
        }

        switch ($recordType) {
            // header Record
            case RecordType::VALUE_FILE_HEADER:
                return new FileHeader($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_CASH_LETTER_HEADER:
                return new CashLetterHeader($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_BUNDLE_HEADER:
                return new BundleHeader($recordType, $recordDataASCII, $recordData);

            // check detail Record
            case RecordType::VALUE_CHECK_DETAIL:
                return new CheckDetail($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_CHECK_DETAIL_ADDENDUM_A:
                return new CheckDetailAddendumA($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_CHECK_DETAIL_ADDENDUM_B:
                return new VariableLength\CheckDetailAddendumB($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_CHECK_DETAIL_ADDENDUM_C:
                return new CheckDetailAddendumC($recordType, $recordDataASCII, $recordData);    

            // return detail Record
            case RecordType::VALUE_RETURN_RECORD:
                return new ReturnRecord($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_RETURN_ADDENDUM_A:
                return new ReturnAddendumA($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_RETURN_ADDENDUM_B:
                return new ReturnAddendumB($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_RETURN_ADDENDUM_C:
                return new VariableLength\ReturnAddendumC($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_RETURN_ADDENDUM_D:
                return new ReturnAddendumD($recordType, $recordDataASCII, $recordData);

            // image view Record
            case RecordType::VALUE_IMAGE_VIEW_DETAIL:
                return new ImageViewDetail($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_IMAGE_VIEW_DATA:
            /**
             * @todo special data handling here for the binary data.
             */
                return new VariableLength\ImageViewData($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_IMAGE_VIEW_ANALYSIS:
                return new ImageViewAnalysis($recordType, $recordDataASCII, $recordData);

            // control/summary Record
            case RecordType::VALUE_BUNDLE_CONTROL:
                return new BundleControl($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_BOX_SUMMARY:
                return new BoxSummary($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_ROUTING_NUMBER_SUMMARY:
                return new RoutingNumberSummary($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_CASH_LETTER_CONTROL:
                return new CashLetterControl($recordType, $recordDataASCII, $recordData);
            case RecordType::VALUE_FILE_CONTROL:
                return new FileControl($recordType, $recordDataASCII, $recordData);
            default:
               // emmit notice, we shouldn't get any of these.
                trigger_error('Invalid record passed, data is unhandled.');
                return new Generic($recordType, $recordDataASCII, $recordData);
        }
    }
}