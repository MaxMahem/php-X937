<?php
/**
 * X937Records represent a single variable length line of a X937 file.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3 (or later)
 * @copyright Copyright (c) 2013, Austin Stanley
 */

require_once 'X937Field.php';
require_once 'X937FieldPredefined.php';

class X937Record implements IteratorAggregate {
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
     * The record string in ASCII format.
     * @var string
     */
    protected $recordASCII;

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
     * @param type $recordTypeASCII the type of the record, in ASCII. Should be
     * one of the class constants.
     * @param type $recordData the raw data for the record. EBCDIC/Binary/ASCII
     * @throws InvalidArgumentException If given bad input.
     */
    public function __construct($recordTypeASCII, $recordData) {
	// input validation
        if (!is_string($recordData)) { throw new InvalidArgumentException("Bad record: $recordData passed to new X937Record"); }

        $this->recordType = $recordTypeASCII;

        // check for the IMAGE_VIEW_DETAIL Record type. This is a TIFF record, and in this case we only want the first 117 bytes of EBCDIC data,
        // the rest is TIFF.
        if ($this->recordType == X937FieldRecordType::IMAGE_VIEW_DATA) {
            $this->recordData  = substr($recordData, 0, 117);
            $this->recordASCII = iconv('EBCDIC-US', 'ASCII', substr($recordData, 0, 117));
	} else {
            $this->recordData  = $recordData;
            $this->recordASCII = iconv('EBCDIC-US', 'ASCII', $recordData);
	}

	$this->addFields();
	
	// added error check because I seem to be missing some.
	foreach($this->fields as $field) {
	    if (($field instanceof X937Field) === FALSE) {
		print_r($this->fields);
		throw new LogicException("Field" . ' ' . $this->fields->key() . ' ' . "undefined.");
	    }
	}
	
	foreach($this->fields as $field) {
	    $field->parseValue($this->recordASCII);
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
    
    public function validate() {
	foreach ($this->fields as $field) {
	    echo $field->validate();
	}
    }

    /**
     * Get the Record Type, should be one of the class constents.
     * @return int The record type of the record.
     */
    public function getRecordType()  { return $this->recordType; }
    public function getRecordData()  { return $this->recordData; }
    public function getRecordASCII() { return $this->recordASCII; }
    public function getFields()      { return $this->fields; }

    public function getFieldByNumber($fieldNumber) { return $this->fields[$fieldNumber-1]; }
    public function getFieldByName($fieldName)     { return $this->fields[$this->fieldsRef[$fieldName]]; }

    protected function addFields() { 
	$this->fields = new SplFixedArray(0);
    }

    /**
     * Adds a X937Field (or one of it's subclasses) to the Record.
     * @param X937Field $field
     */
    protected function addField(X937Field $field) {
        $this->fields[$field->getFieldNumber()-1]  = $field;
	
	// update fieldRef with pointer to correct position.
	$this->fieldsRef[$field->getFieldName()-1] = $field->getFieldNumber();
    }
}