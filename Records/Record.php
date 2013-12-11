<?php

namespace X937\Records;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fields' . DIRECTORY_SEPARATOR . 'Field.php';

use X937\Fields\Predefined\FieldRecordType;

/**
 * X937Records represent a single variable length line of a X937 file.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley
 */
abstract class Record implements \IteratorAggregate, \Countable {
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
     * Reference array that links field name => filed number.
     * @var array
     */
    protected $fieldsRef;

    /**
     * Creates a X937Record. Basic input validation, currently ignores TIFF data.
     * Calls addFields which should be overriden in a subclass to add all the
     * fields to the record. And then calls all those fields parseValue function
     * to parse in the data.
     * @param string $recordType the type of the record, in ASCII.
     * @param string $recordData the raw data for the record. EBCDIC/Binary/ASCII
     * @throws InvalidArgumentException If given bad input.
     */
    public function __construct($recordType, $recordData) {
	// input validation
        if (array_key_exists($recordType, FieldRecordType::defineValues()) === FALSE) { 
	    throw new InvalidArgumentException("Bad record: $recordData passed.");
	}
	if (is_string($recordData) === FALSE) {
	    throw new InvalidArgumentException("Bad data type $recordData passed.");
	}

        $this->recordType = $recordType;
	$this->recordData = $recordData;

	$this->addFields();
	
	// added error check because I seem to be missing some.
	foreach($this->fields as $field) {
	    if (($field instanceof \X937\Fields\Field) === FALSE) {
		throw new LogicException("Field" . ' ' . $this->fields->key() . ' ' . "undefined.");
	    }
	}
	
	foreach($this->fields as $field) {
	    $field->parseValue($this->recordData);
	}
    }
    
    /**
     * Returns an Array Iterator object of the fields (natively a SplFixedArray),
     * this lets X937Record implement tranversiable.
     * @return ArrayIterator
     */
    public function getIterator() {
	return $this->fields;
    }
    
    /**
     * Returns a count of the number of fields. For Countable.
     * @return int
     */
    public function count() {
	return count($this->fields);
    }
    
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
    public function getData() { return $this->recordData; }

    public function getFieldByNumber($fieldNumber) { return $this->fields[$fieldNumber-1]; }
    public function getFieldByName($fieldName)     { return $this->fields[$this->fieldsRef[$fieldName]]; }

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
        $this->fields[$field->getFieldNumber()-1]  = $field;
	
	// update fieldRef with pointer to correct position.
	$this->fieldsRef[$field->getFieldName()-1] = $field->getFieldNumber();
    }
}