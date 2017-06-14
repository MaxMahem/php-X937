<?php

namespace X937\Record;

use X937\Fields\Field;

/**
 * X937Record represent a single variable length line of a X937 file.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley
 */
class Record implements \X937\Record\RecordInterface {
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
    
    // record properties names (leaf)
    const PROP_NAME           = 'name';
    const PROP_TYPE           = 'type';
    const PROP_USAGE          = 'usage';
    const PROP_VALIDATION     = 'validation';
    const PROP_LENGTH         = 'length';
    const PROP_VARIABLELENGTH = 'variableLength';
    const PROP_FIELDCOUNT     = 'fieldCount';
    
    // record properties names (branch)
    const PROP_FIELDS         = 'fields';

    // record properties
    const LEAF_PROPERTIES = [
        self::PROP_NAME,
        self::PROP_TYPE,
        self::PROP_USAGE,
        self::PROP_VALIDATION,
        self::PROP_LENGTH,
        self::PROP_VARIABLELENGTH,
        self::PROP_FIELDCOUNT,
    ];
    
    const PROPERTIES = self::LEAF_PROPERTIES + [self::PROP_FIELDS];
    
    protected $recordTemplate;

    /**
     * Creates a X937Record. 
     * 
     * @param array $recordTemplate a template with the 'bones' of the record.
     */
    public function __construct(array $recordTemplate) {
        $this->recordTemplate = $recordTemplate;
        
        // build the SplFixedArray that will hold the fields
        $fieldCount = $this->recordTemplate['fieldCount'];
        $this->fields = new \SplFixedArray($fieldCount);
        
        // create the records fields
        $fields = $recordTemplate[self::PROP_FIELDS];
        
        foreach ($fields as $id => $fieldTemplate) {
            // since our fields are indexed by 1, and our array by 0, we need to subtract one            
            $fieldIndex = $id - 1;
            $fieldName  = $fieldTemplate[Field::PROP_NAME];
            $this->fields[$fieldIndex] = new \X937\Fields\Field($fieldTemplate);
            
            // since objects are passed by reference, both indexes point to the same object
            $this->fieldsRef[$fieldName] = $this->fields[$fieldIndex];
        }        
    }
    
    public function parse(string $data, string $dataType = X937\Util::DATA_EBCDIC): bool {
        if (!array_key_exists($dataType, \X937\Util::DATA_TYPES)) {
            throw new \InvalidArgumentException("Invalid data type: $dataType");
        }
        
        // $keyArray will hold sets of key Value pairs that tells us where the
        // variable position are for our variable length fields. the $key is in
        // the record spec, while the value is in the parsed data.
        $keyArray = array();
        
        foreach ($this->fields as $field) {
            $fieldTemplate = $field->getTemplate();
            $fieldPosition = $fieldTemplate[Field::PROP_POSITION] - 1;
            $fieldSize     = $fieldTemplate[Field::PROP_LENGTH];
            
            if (isset($fieldTemplate[Field::PROP_VARIABLEPOSITION])) {
                $positionArray = explode('+', $fieldTemplate[Field::PROP_VARIABLEPOSITION]);
                
                $fieldPosition = 0;
                foreach($positionArray as $positionItem) {
                    if (is_numeric($positionItem)) {
                        $fieldPosition += $positionItem;
                    } else {
                        $fieldPosition += $keyArray[$positionItem];
                    }
                }
                
                $fieldPosition -= 1;
            }
            
            if(isset($fieldTemplate[Field::PROP_VARIABLELENGTH])) {
                $lengthVariable = $fieldTemplate[Field::PROP_VARIABLELENGTH];
                $fieldSize = $keyArray[$lengthVariable];
            }
            
            $rawValue = substr($data, $fieldPosition, $fieldSize);
            
            // if our data is binary we also do not want to translate it.
            if (($dataType === \X937\Util::DATA_EBCDIC) && 
                    ($fieldTemplate[Field::PROP_TYPE] != Field::TYPE_BINARY)) {
                $asciiValue = \X937\Util::e2a($rawValue);
            } else {
                $asciiValue = $rawValue;
            }
            
            // if we have a keyValue property, then the field indicates the 
            // length of another field. So we set its value and key into our
            // array for this purpose.
            if (isset($fieldTemplate[Field::PROP_VALUEKEY])) {
                $keyArray[$fieldTemplate[Field::PROP_VALUEKEY]] = $asciiValue;
            }
            
            $field->set($asciiValue);
        }
        
        return true;
    }
    
    public function __get($name) {
        if (isset($this->recordTemplate[$name])) {
            return $this->recordTemplate[$name];
        } else {
            trigger_error("Attempted to get property $name which is undefined.");
            return null;
        }
    }
    
    /**
     * Performs internal sanity checking on the internally generated RecordTempalte
     * to see if it violates constraints of field count, length, and overlap.
     * 
     * @return bool always returns True, because it will throw an exception otherwise.
     * @throws \InvalidArgumentException If the recordTemplate doesn't validate.
     */
    public static function validateTemplate(array $recordArray): bool {
        $recordType = $recordArray[self::PROP_TYPE];

        // validate each field
        $currentPos = $idStart = 1;
        foreach ($recordArray['fields'] as $fieldOrder => $fieldArray) {            
            $position = $fieldArray[Field::PROP_POSITION];
            $length   = $fieldArray[Field::PROP_LENGTH];

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
        $recordLength = $recordArray[self::PROP_LENGTH];
        $recordCount  = $recordArray[self::PROP_FIELDCOUNT];
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
    
    public function __isset($name) {
        return isset($this->recordTemplate[$name]);
    }
    
    public function getData(string $dataType = \X937\Util::DATA_ASCII): string {
        $data = '';
        foreach ($this->fields as $field) {
            $data .= $field->getValue();
        }
        
        return $data;
    }
    
    /**
     * Returns an Array Iterator object of the fields (natively a SplFixedArray),
     * this lets X937Record implement tranversible.
     * @return ArrayIterator
     */
    public function getIterator() { return $this->fields; }
    
    /**
     * Returns a count of the number of fields. For Countable.
     * 
     * @return int
     */
    public function count(): int { return count($this->fields); }
    
    public function offsetExists($offset) {
        if (is_numeric($offset)) {
            return isset($this->fields[$offset]);
        } else {
            return isset($this->fieldsRef[$offset]);
        }
    }
    
    public function offsetUnset($offset) { throw new \InvalidArgumentException("Set access by constructor only"); }
    
    public function offsetSet($offset, $value) { throw new \InvalidArgumentException("Set access by constructor only"); }
    
    public function offsetGet($offset) {
        // check if our access is valid for one of the two arrays, and if so return it.
        if (is_numeric($offset)) {
            if (isset($this->fields[$offset])) {
                return $this->fields[$offset - 1];
            }
        } else {
            if (isset($this->fieldsRef[$offset])) { 
                return $this->fieldsRef[$offset];
            }
        }
        
        // otherwise, return null.
        trigger_error("Attempted to get field $offset, which does not exist");
        return null;
    }
    
    public function validate() {
        foreach ($this->fields as $field) {
            $field->validate();
        }
        
        /**
         * @todo Do record level validation.
         */
    }

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
}