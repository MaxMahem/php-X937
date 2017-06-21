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
class Record extends \X937\Container implements \ArrayAccess, \Countable, \IteratorAggregate {
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
     * List of variable keys, for fields with variable lengths.
     * @var array
     */
    protected $keyArray;
    
    // record properties names (leaf, parsed)
    const PROP_NAME           = 'name';
    const PROP_TYPE           = 'type';
    const PROP_USAGE          = 'usage';
    const PROP_VALIDATION     = 'validation';
    const PROP_LENGTH         = 'length';
    const PROP_VARIABLELENGTH = 'variableLength';
    const PROP_FIELDCOUNT     = 'fieldCount';
    
    // record properties names (branch)
    const PROP_FIELDS         = 'fields';
    
    // record properties name (infered)
    const PROP_VARIABLE       = 'variable';

    // record properties
    const PARSED_PROPERTIES = [
        self::PROP_NAME,
        self::PROP_TYPE,
        self::PROP_USAGE,
        self::PROP_VALIDATION,
        self::PROP_LENGTH,
        self::PROP_VARIABLELENGTH,
        self::PROP_FIELDCOUNT,
    ];
    
    const PROPERTIES = self::PARSED_PROPERTIES + [self::PROP_FIELDS, self::PROP_VARIABLE];

    /**
     * Creates a X937Record. 
     * 
     * @param array $recordTemplate a template with the 'bones' of the record.
     */
    public function __construct(array $recordTemplate) {
        $this->template = $recordTemplate;
        
        // build the SplFixedArray that will hold the fields
        $fieldCount = $this->template[self::PROP_FIELDCOUNT];
        $this->fields = new \SplFixedArray($fieldCount);
        
        // create the records fields
        $fields = $recordTemplate[self::PROP_FIELDS];
        foreach ($fields as $id => $fieldTemplate) {
            // since our fields are indexed by 1, and our array by 0, we need to subtract one            
            $fieldIndex = $id - 1;
            $fieldName  = $fieldTemplate[Field::PROP_NAME];
            $this->fields[$fieldIndex] = new \X937\Fields\Field($fieldTemplate, $this);
            
            // since objects are passed by reference, both indexes point to the same object
            $this->fieldsRef[$fieldName] = $this->fields[$fieldIndex];
            
            // populate our keyArray if necessary.
            if (isset($fieldTemplate[Field::PROP_VALUEKEY])) {
                $key = $fieldTemplate[Field::PROP_VALUEKEY];
                $this->keyArray[$key] = $this->fields[$fieldIndex];
            }
        }
    }
    
    /**
     * Gets the field position, including calculations for variable position if
     * necessary.
     * 
     * @param Field $field 
     * @return int
     */
    protected function getFieldPosition(Field $field):int {
        // some fields may have a variableposition, if so, we need to parse
        // the variable position field which will contain a record in the format
        // ###+X(+Y) where ### is a static offset, and X (and possibly Y are
        // variables that the record should be offset.
        // these variables should already be populated into our $keyArray above
        if (isset($field->variablePosition)) {
            $positionArray = explode('+', $field->variablePosition);

            $position = 0;
            foreach($positionArray as $positionItem) {
                if (is_numeric($positionItem)) {
                    $position += $positionItem;
                } else {
                    $position += (int) $this->keyArray[$positionItem]->getValue();
                }
            }

            $position -= 1;
        } else {
            // standard field
            $position = $field->position - 1;
        }
        
        return (int) $position;
    }
    
    /**
     * Gets the length of a given field, derived from other fields if necessary.
     * 
     * @param Field $field
     * @return int
     */
    protected function getFieldLength(Field $field): int {
        // for fields with a variable length, the field Variblelength will 
        // contain a variable which identifies what part of the records
        // variable length it composes. We populate the length with the value
        // of this variable
        if(isset($field->variableLength)) {
            $lengthVariable = $field->variableLength;
            $fieldLength = $this->keyArray[$lengthVariable]->getValue();
        } else {
            // normal field, just return length.
            $fieldLength = $field->length;
        }
        
        return (int) $fieldLength;
    }
    
    /**
     * recalculate the length of the record. Should only be called in the context
     * of variable length records.
     */
    protected function calculateLength() {
        // recalculation is only possible on variable length records.
        if ($this->variable == 'true') {
            $length = 0;
            foreach ($this->fields as $field) {
                $length += $field->length;
            }
        } else {
            $type = $this->type;
            trigger_error("recalculateLength called on record type $type with static length, nothing done.");
        }
        
        $this->template[Record::PROP_LENGTH] = $length;
    }
    
    /**
     * Updates a field with a new value given the keys association.
     * 
     * @param string $key The key of the field associated with this value.
     * @param string $value The new value for this field.
     * @throws \InvalidArgumentException If the matching key is not found in the array.
     */
    public function updateAssociatedField(string $key, string $value) {
        if (!array_key_exists($key, $this->keyArray)) {
            throw new \InvalidArgumentException("Key $key not found in association array");
        }
        
        $field = $this->keyArray[$key];
        $field->set($value);
        
        $this->calculateLength();
    }
    
    /**
     * Parses in a string of data into the record, populating all its records.
     * 
     * @param string $data The data to be parsed
     * @param string $dataType The type of data, EBCDIC or ASCII
     * @return bool If the parse was sucesfull.
     * @throws \InvalidArgumentException If gets an invalid argument.
     */
    public function parse(string $data, string $dataType = X937\Util::DATA_EBCDIC): bool {
        if (!array_key_exists($dataType, \X937\Util::DATA_TYPES)) {
            throw new \InvalidArgumentException("Invalid data type: $dataType");
        }
        
        // parse through all our fields
        foreach ($this->fields as $field) {
            $position = $this->getFieldPosition($field);
            $length   = $this->getFieldLength($field); 
            $rawValue = substr($data, $position, $length);
            
            // if our data is binary we also do not want to translate it.
            if (($dataType === \X937\Util::DATA_EBCDIC) && 
                    ($field->type != Field::TYPE_BINARY)) {
                $asciiValue = \X937\Util::e2a($rawValue);
            } else {
                $asciiValue = $rawValue;
            }
            
            $field->set($asciiValue, false);
        }
        
        // if our record was a variable length one, we need to calculate its length.
        if ($this->variable == 'true') {
            $this->calculateLength();
        }
        return true;
    }
    
    /**
     * Performs internal sanity checking on the internally generated RecordTempalte
     * to see if it violates constraints of field count, length, and overlap.
     * 
     * @return bool always returns True, because it will throw an exception otherwise.
     * @throws \InvalidArgumentException If the recordTemplate doesn't validate.
     */
    public static function validateTemplate(array $recordArray): bool {
        $recordType     = $recordArray[self::PROP_TYPE];
        $recordVariable = $recordArray[self::PROP_VARIABLE];

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
            
            // check if our field is variable and the record is not.
            if (($recordVariable == false) && ($fieldArray[Field::PROP_VARIABLE] == true)) {
                throw new \InvalidArgumentException("Record type $recordType has a variable field $fieldOrder but the record is not infered variable.");
            }
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
       
    /**
     * Returns all the data for a given record.
     * 
     * @param string $dataType Either Util::DATA_ASCII or Util::DATA_EBCDIC
     * @return string The record data.
     */
    public function getData(string $dataType = \X937\Util::DATA_ASCII): string {
        $data = '';
        foreach ($this->fields as $field) {
            $data .= $field->getValue($dataType);
        }
        
        return $data;
    }
    
    /**
     * Returns an Array Iterator object of the fields (natively a SplFixedArray),
     * this lets X937Record implement tranversible.
     * 
     * @return ArrayIterator
     */
    public function getIterator() { return $this->fields; }
    
    /**
     * Returns a count of the number of fields. For Countable.
     * 
     * @return int
     */
    public function count(): int { return count($this->fields); }
    
    /**
     * Tells if a given field is set or not.
     * 
     * @param type $offset Either the fields name or its order (1 indexed).
     * @return type 
     */
    public function offsetExists($offset): Field {
        if (is_numeric($offset)) {
            return isset($this->fields[$offset]);
        } else {
            return isset($this->fieldsRef[$offset]);
        }
    }
    
    /**
     * Does nothing, set access is not allowed.
     * 
     * @param type $offset
     * @throws \InvalidArgumentException
     */
    public function offsetUnset($offset): void { throw new \InvalidArgumentException("Set access by constructor only"); }
    
    /**
     * Does nothing, set access is not allowed.
     * 
     * @param type $offset
     * @param type $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value): void { throw new \InvalidArgumentException("Set access by constructor only"); }
    
    /**
     * Returns the Field specified by offset.
     * 
     * @param type $offset Either the fields name or its order (1 indexed).
     * @return Field
     */
    public function offsetGet($offset): Field {
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
    
    /**
     * Calls all the validation routines for the records fields and the record itself.
     * 
     * @return string a complied string of all the errors, with newlines.
     */
    public function validate(): string {
        $error = '';
        $fieldErrors = array();
        
        foreach ($this->fields as $order => $field) {
            $fieldValidation = $field->validate();
            if(!empty($fieldValidation)) {
                $fieldErrors[] = $fieldValidation;
            }
        }
        
        /**
         * @todo Do record level validation.
         */
        
        if (!empty($fieldErrors)) {
            $name = $this->name;
            $type = $this->type;
            $errorBase  = "Error validating Record Type $type: $name:";
            $errorField = implode(PHP_EOL . '  ', $fieldErrors);
            $error      = $errorBase . PHP_EOL . '  ' . $errorField . PHP_EOL;
        }
        
        return $error;
    }
}