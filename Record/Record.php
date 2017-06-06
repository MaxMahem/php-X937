<?php

namespace X937\Record;

use X937\Fields\Predefined\RecordType;
use X937\Fields\Field;

/**
 * X937Record represent a single variable length line of a X937 file.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley
 */
abstract class Record implements \IteratorAggregate, \Countable {
    const DATA_ASCII  = 'ASCII';
    const DATA_EBCDIC = 'EBCDIC-US';
    
    const EBCDIC_2_ASCII_TABLE = array(
        '40' => ' ',
        '4A' => '¢',
        '4B' => '.',
        '4C' => '<',
        '4D' => '(',
        '4E' => '+',
        '4F' => '|',
        '5A' => '!',
        '5B' => '$',
        '5C' => '*',
        '5D' => ')',
        '5E' => ';',
        '5F' => '¬',
        '60' => '-',
        '61' => '/',
        '6A' => '¦',
        '6B' => ',',
        '6C' => '%',
        '6D' => '_',
        '6E' => '>',
        '6F' => '?',
        '79' => '`',
        '7A' => ':',
        '7B' => '#',
        '7C' => '@',
        '7D' => '\'',
        '7E' => '=',
        '7F' => '\"',
        '81' => 'a',
        '82' => 'b',
        '83' => 'c',
        '84' => 'd',
        '85' => 'e',
        '86' => 'f',
        '87' => 'g',
        '88' => 'h',
        '89' => 'i',
        '91' => 'j',
        '92' => 'k',
        '93' => 'l',
        '94' => 'm',
        '95' => 'n',
        '96' => 'o',
        '97' => 'p',
        '98' => 'q',
        '99' => 'r',
        'A1' => '~',
        'A2' => 's',
        'A3' => 't',
        'A4' => 'u',
        'A5' => 'v',
        'A6' => 'w',
        'A7' => 'x',
        'A8' => 'y',
        'A9' => 'z',
        'C0' => '{',
        'C1' => 'A',
        'C2' => 'B',
        'C3' => 'C',
        'C4' => 'D',
        'C5' => 'E',
        'C6' => 'F',
        'C7' => 'G',
        'C8' => 'H',
        'C9' => 'I',
        'D0' => '}',
        'D1' => 'J',
        'D2' => 'K',
        'D3' => 'L',
        'D4' => 'M',
        'D5' => 'N',
        'D6' => 'O',
        'D7' => 'P',
        'D8' => 'Q',
        'D9' => 'R',
        'E0' => '\\',
        'E2' => 'S',
        'E3' => 'T',
        'E4' => 'U',
        'E5' => 'V',
        'E6' => 'W',
        'E7' => 'X',
        'E8' => 'Y',
        'E9' => 'Z',
        'F0' => '0',
        'F1' => '1',
        'F2' => '2',
        'F3' => '3',
        'F4' => '4',
        'F5' => '5',
        'F6' => '6',
        'F7' => '7',
        'F8' => '8',
        'F9' => '9',  
    );
    
    const ASCII_2_EBCDIC_TABLE = array(
        ' ' => '40',
        '¢' => '4A',
        '.' => '4B',
        '<' => '4C',
        '(' => '4D',
        '+' => '4E',
        '|' => '4F',
        '!' => '5A',
        '$' => '5B',
        '*' => '5C',
        ')' => '5D',
        ';' => '5E',
        '¬' => '5F',
        '-' => '60',
        '/' => '61',
        '¦' => '6A',
        ',' => '6B',
        '%' => '6C',
        '_' => '6D',
        '>' => '6E',
        '?' => '6F',
        '`' => '79',
        ':' => '7A',
        '#' => '7B',
        '@' => '7C',
        '\'' => '7D',
        '=' => '7E',
        '"' => '7F',
        'a' => '81',
        'b' => '82',
        'c' => '83',
        'd' => '84',
        'e' => '85',
        'f' => '86',
        'g' => '87',
        'h' => '88',
        'i' => '89',
        'j' => '91',
        'k' => '92',
        'l' => '93',
        'm' => '94',
        'n' => '95',
        'o' => '96',
        'p' => '97',
        'q' => '98',
        'r' => '99',
        '~' => 'A1',
        's' => 'A2',
        't' => 'A3',
        'u' => 'A4',
        'v' => 'A5',
        'w' => 'A6',
        'x' => 'A7',
        'y' => 'A8',
        'z' => 'A9',
        '{' => 'C0',
        'A' => 'C1',
        'B' => 'C2',
        'C' => 'C3',
        'D' => 'C4',
        'E' => 'C5',
        'F' => 'C6',
        'G' => 'C7',
        'H' => 'C8',
        'I' => 'C9',
        '}' => 'D0',
        'J' => 'D1',
        'K' => 'D2',
        'L' => 'D3',
        'M' => 'D4',
        'N' => 'D5',
        'O' => 'D6',
        'P' => 'D7',
        'Q' => 'D8',
        'R' => 'D9',
        '\\' => 'E0',
        'S' => 'E2',
        'T' => 'E3',
        'U' => 'E4',
        'V' => 'E5',
        'W' => 'E6',
        'X' => 'E7',
        'Y' => 'E8',
        'Z' => 'E9',
        '0' => 'F0',
        '1' => 'F1',
        '2' => 'F2',
        '3' => 'F3',
        '4' => 'F4',
        '5' => 'F5',
        '6' => 'F6',
        '7' => 'F7',
        '8' => 'F8',
        '9' => 'F9',
    );
        
    /**
     * The type of the record. Should be one of the class constants.
     * @var int
     */
    protected $recordType;

    /**
     * The raw record string. Generally EBCDIC data, possibly binary or ASCII.
     * @var string
     */
    protected $recordData;
    
    /**
     * The raw record data, preserved so we can have it for binary fields.
     * @var string
     */
    protected $recordDataRaw;
    
    /**
     * The length of the record. In bytes
     * @var int 
     */
    protected $recordLength;

    /**
     * Contains all the field in the record.
     * @var SplFixedArray
     */
    protected $fields;

    /**
     * Reference array that links field name => field number.
     * @var array
     */
    protected $fieldsRef;

    /**
     * Creates a X937Record. Basic input validation, currently ignores TIFF data.
     * Calls addFields which should be overriden in a subclass to add all the
     * fields to the record. And then calls all those fields parseValue function
     * to parse in the data.
     * @param string $recordType the type of the record, in ASCII.
     * @param string $recordData the translated data for the record.
     * @param string $recordDataRaw the raw (untranslated) data for the record.
     * @throws InvalidArgumentException If given bad input.
     */
    public function __construct($recordType, $recordData, $recordDataRaw) {
    // input validation
        if (array_key_exists($recordType, RecordType::defineValues()) === FALSE) { 
        throw new \InvalidArgumentException("Bad record: $recordData passed.");
    }
    if (is_string($recordData) === FALSE) {
        throw new \InvalidArgumentException("Bad data type " . \gettype($recordData) . " passed.");
    }
        
        $this->recordType    = $recordType;
    $this->recordData    = $recordData;
        $this->recordDataRaw = $recordDataRaw;
        $this->recordLength  = strlen($recordDataRaw);

    $this->addFields();
    
    // added error check because I seem to be missing some.
    foreach($this->fields as $field) {
        assert(($field instanceof Field), "Field" . ' ' . $this->fields->key() . ' ' . "undefined.");
    }
    
    foreach($this->fields as $field) {
        if ($field->getType() === Field::TYPE_BINARY) {
        $field->parseValue($this->recordDataRaw);
        } else {
        $field->parseValue($this->recordData);
        }
    }
    }
    
    /**
     * Returns an Array Iterator object of the fields (natively a SplFixedArray),
     * this lets X937Record implement tranversiable.
     * @return ArrayIterator
     */
    public function getIterator() { return $this->fields; }
    
    /**
     * Returns a count of the number of fields. For Countable.
     * @return int
     */
    public function count() { return count($this->fields); }
    
    public function validate() {
    foreach ($this->fields as $field) {
        echo $field->validate();
    }
    }

    /**
     * Get the Record Type, should be one of the class constents.
     * @return int The record type of the record.
     */
    public function getType() { return $this->recordType; }
    
    /**
     * Get the record length. Should be the same for raw and translated data, I hope.
     * @return int The current length of the record.
     */
    public function getLength() { return strlen($this->recordDataRaw); }
    
    /**
     * Get the raw Record data
     * @return string Translated Record Data
     */
    public function getData() { return $this->recordData; }
        
    /**
     * Get the raw Record data
     * @return string Raw (untranslated) Record Data
     */
    public function getDataRaw() { return $this->recordDataRaw; }

    /**
     * Gets the field according to its field number (1 indexed)
     * @param int $fieldNumber the number of the field (1 indexed)
     * @return field the field requested
     */
    public function getFieldByNumber($fieldNumber) { return $this->fields[$fieldNumber-1]; }
    
    /**
     * Returns the field named.
     * @todo more elegant handling of out of range fields.
     * @param string $fieldName
     * @return \X937\Fields\Field the field named.
     */
    public function getFieldByName($fieldName) 
    {
    $fieldNumber = $this->fieldsRef[$fieldName];
    return $this->fields[$fieldNumber];
    }
    
    public function setData($data, $dataType = self::DATA_ASCII) {
        $this->recordData = $data;
        $this->recordDataRaw = self::a2eConverter($data);
    }

    abstract public static function defineFields();
    
    protected function addFields()
    {
    $fields     = static::defineFields();
    $fieldCount = count($fields);
    
    $this->fields = new \SplFixedArray($fieldCount);
        
    foreach ($fields as $field) {
        $this->addField($field);
    }
    }

    /**
     * Adds a X937Field (or one of it's subclasses) to the Record.
     * @param X937Field $field
     */
    protected function addField(\X937\Fields\Field $field) {
    // since field numbers are 1 indexed and the array 0 indexed, we need to
    // subtract one to correlate.
    $fieldNumber = $field->getNumber() - 1;
    
    // assign our field to the array.
        $this->fields[$fieldNumber] = $field;
    
    // update fieldRef with a key to correct position.
    $this->fieldsRef[$field->getName()] = $fieldNumber;
    }
    
    /**
     * Converts a string of ASCII into EBCDIC
     * @param string $aString ASCII string
     * @return string EBCDIC string
     */
    public static function a2eConverter($aString) {
        // loop though string converting to EBCDIC
        $eOut = "";    
        while(strlen($aString)>=1)
        {
            $thisASCII = substr($aString, 0, 1);

            if (array_key_exists($thisASCII, self::ASCII_2_EBCDIC_TABLE)) {
                $eOut = $eOut . hex2bin(self::ASCII_2_EBCDIC_TABLE[$thisASCII]);
            } else {
                $eOut .= hex2bin(self::ASCII_2_EBCDIC_TABLE[' ']);
                trigger_error("Unhandled ASCII Character " . bin2hex($thisASCII));
                echo $aString;
            }
            $aString = substr($aString, 1);

        }    

        return $eOut;
    }
}