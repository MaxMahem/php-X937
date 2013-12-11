<?php

namespace X937\Fields;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Predefined' . DIRECTORY_SEPARATOR . 'FieldPredefined.php';

/**
 * @todo: make autoloader work to remove this stuff.
 */
require_once 'FieldAmount.php';
require_once 'FieldDate.php';
require_once 'FieldGeneric.php';
require_once 'FieldPhoneNumber.php';
require_once 'FieldReserved.php';
require_once 'FieldRoutingNumber.php';
require_once 'FieldTime.php';
require_once 'FieldUser.php';

require_once 'FieldTypeName.php';
require_once 'FieldTypes.php';
require_once 'FieldVariableLength.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Validator.php';

/**
 * Contains a specific X937Field
 *
 * @author astanley
 */
abstract class Field {
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
    const USAGE_MANDATORY   = 'M';
    const USAGE_CONDITIONAL = 'C';
	
    // field types
    const TYPE_ALPHABETIC                  = 'A';
    const TYPE_NUMERIC                     = 'N';
    const TYPE_BLANK                       = 'B';
    const TYPE_SPECIAL                     = 'S';
    const TYPE_ALPHAMERIC                  = 'AN';
    const TYPE_ALPHAMERICSPECIAL           = 'ANS';
    const TYPE_NUMERICBLANK                = 'NB';
    const TYPE_NUMERICSPECIAL              = 'NS';
    const TYPE_NUMERICBLANKSPECIALMICR     = 'NBSM';
    const TYPE_NUMERICBLANKSPECIALMICRONUS = 'NBSMOS';
    const TYPE_BINARY                      = 'Binary';
	
    public function __construct($fieldNumber, $filedName, $usage, $position, $size, $type) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $filedName;
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = $size;
	$this->type        = $type;
	
	$this->addBaseValidators();
	$this->addClassValidators();
    }
    
    /**
     * adds the base Validators to the field, based on attributes we can pre-determine.
     */
    protected function addBaseValidators() {
	// initialize validator
	$this->validator = new \Validator();
	
	// add validator based on usage.
	if ($this->usage === Field::USAGE_MANDATORY) {
	    $this->validator->addValidator(new \ValidatorUsageManditory());
	}
	
	// add validator based on size.
	$this->validator->addValidator(new \ValidatorSize($this->size));
	
	// add validator based on type.
	switch ($this->type) {
	    case Field::TYPE_ALPHABETIC:
		$this->validator->addValidator(new \ValidatorTypeAlphabetic());
		break;
	    case Field::TYPE_NUMERIC:
		$this->validator->addValidator(new \ValidatorTypeNumeric());
		break;
	    case Field::TYPE_BLANK:
		$this->validator->addValidator(new \ValidatorTypeBlank());
		break;
	    case Field::TYPE_SPECIAL:
		// insert validators
		break;
	    case Field::TYPE_ALPHAMERIC:
		$this->validator->addValidator(new \ValidatorTypeAlphameric());
		break;
	    /**
	     * @todo add rest of validators.
	     */
	    default:
		// possibly throw error here?
		break;
	}
    }
    
    /**
     * stub for later overloading.
     */
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