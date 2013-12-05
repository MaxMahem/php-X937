<?php

require_once 'Validator.php';
/**
 * Description of X937Field
 *
 * @author astanley
 */
class X937Field {
    /**
     * pointer back to the X937Record that contains the field.
     * @var X937Record
     */
    protected $record;
    
    protected $fieldNumber; // the sequential number of the field in the record
    protected $fieldName;   // the filed name;
    protected $usage;       // usage, Mandatory or Conditional.
    protected $position;    // starting ending location of the field
    protected $size;        // number of characters within the field
    protected $type;        // type of characters within the field
    
    /**
     * Field Validator used to validate the field.
     * @var Validator
     */
    protected $validator;

    /**
     * The value of the field, always ASCII data.
     * @var string 
     */
    protected $value;	    // the value of the field;
	
    // Usage Types
    const MANDATORY   = 'M';
    const CONDITIONAL = 'C';
	
    // field types
    const ALPHABETIC                  = 'A';
    const NUMERIC                     = 'N';
    const BLANK                       = 'B';
    const SPECIAL                     = 'S';
    const ALPHAMERIC                  = 'AN';
    const ALPHAMERICSPECIAL           = 'ANS';
    const NUMERICBLANK                = 'NB';
    const NUMERICSPECIAL              = 'NS';
    const NUMERICBLANKSPECIALMICR     = 'NBSM';
    const NUMERICBLANKSPECIALMICRONUS = 'NBSMOS';
    const BINARY                      = 'Binary';
	
    public function __construct($fieldNumber, $filedName, $usage, $position, $size, $type) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $filedName;
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = $size;
	$this->type        = $type;
	
	$this->addValidators();
	$this->addClassValidators();
    }
    
    protected function addValidators() {
	// initialize validator
	$this->validator = new Validator();
	
	// add validator based on usage.
	if ($this->usage === X937Field::MANDATORY) {
	    $this->validator->addValidator(new ValidatorUsageManditory());
	}
	
	// add validator based on size.
	$this->validator->addValidator(new ValidatorSize($this->size));
	
	// add validator based on type.
	switch ($this->type) {
	    case X937Field::ALPHABETIC:
		$this->validator->addValidator(new ValidatorTypeAlphabetic());
		break;
	    case X937Field::NUMERIC:
		$this->validator->addValidator(new ValidatorTypeNumeric());
		break;
	    case X937Field::BLANK:
		$this->validator->addValidator(new ValidatorTypeBlank());
		break;
	    case X937Field::SPECIAL:
		// insert validators
		break;
	    case X937Field::ALPHAMERIC:
		$this->validator->addValidator(new ValidatorTypeAlphameric());
		break;
	    /**
	     * @todo add rest of validators.
	     */
	    default:
		// possibly throw error here?
		break;
	}
    }
    
    protected function addClassValidators() {}

    // validate
    public function validate() {
	return $this->validator->validate($this->value);
    }

    // getters
    public function getFieldName()   { return $this->fieldName; }
    public function getFieldNumber() { return $this->fieldNumber; }
    public function getUsage()       { return $this->usage; }
    public function getPosition()    { return $this->position; }
    public function getSize()        { return $this->size; }
    public function getType()        { return $this->type; }
    
    /**
     * Return the value.
     * @return string
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * Return the value, formated nicely.
     * @return string
     */
    public function getValueFormated() {
	return trim($this->value);
    }

    public function parseValue($recordData) {
	if (is_string($recordData) === FALSE) {
	    throw new InvalidArgumentException("Bad recordData passed. String expected.");
	}
	
	$this->value = substr($recordData, $this->position - 1, $this->size);
    }
    
    public static function translate($value) {
	// stub for later classes.
	return '';
    }
    
    public function translatedValue() {
	return static::translate($this->value);
    }
}