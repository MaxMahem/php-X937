<?php namespace X937\Fields;

use X937\Validator as Validator;

/**
 * Contains a specific X937Field
 *
 * @author astanley
 */
abstract class Field {
    // Usage Types
    const USAGE_CONDITIONAL = 'C';
    const USAGE_MANDATORY   = 'M';
	
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
    
    // value formats
    const FORMAT_RAW         = 'raw';
    const FORMAT_SIGNIFIGANT = 'signifigant';
    const FORMAT_FORMATED    = 'formated';
    
    // length & position magic numbers
    const LENGTH_VARIABLE   = -1;
    const POSITION_VARIABLE = -1;
    
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
	
    public function __construct($fieldNumber, $fieldName, $usage, $position, $size, $type) {
	// validate inputs:
	if (is_int($fieldNumber) === false) {
	    throw new \InvalidArgumentException('Fieldnumber must be an integer.');
	}
	if (is_string($fieldName) === false) {
	    throw new \InvalidArgumentException('Field Name must be a string.');
	}
	if (($usage !== self::USAGE_CONDITIONAL) && ($usage !== self::USAGE_MANDATORY)) {
	    throw new \InvalidArgumentException('Usage must be a usage constant');
	}
	if (is_int($position) === false) {
	    throw new \InvalidArgumentException('Position must be an integer');
	}
	if (is_int($size) === false) {
	    throw new \InvalidArgumentException('Size must be an integer');
	}
	if (array_key_exists($type, self::defineTypes()) === false) {
	    throw new \InvalidArgumentException('Type must be a type constant');
	}
	
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldName;
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = $size;
	$this->type        = $type;
	
	$this->addBaseValidators();
	$this->addClassValidators();
    }
    
    public static function defineTypes() {
	$legalTypes = array(
	    self::TYPE_ALPHABETIC                  => 'Alphabetic characters (A-Z, a-z) and space.',
	    self::TYPE_NUMERIC                     => 'Numeric characters (0-9)',
	    self::TYPE_BLANK                       => 'Blank character, space (ASCII 0x20, EBCDIC 0x40)',
	    self::TYPE_SPECIAL                     => 'Any printable character (ASCII > 0x1F, EBCIDC > 0x3F',
	    self::TYPE_ALPHAMERIC                  => 'Any Alphabetic or Numeric character',
	    self::TYPE_ALPHAMERICSPECIAL           => 'Any Alphabetic, Numeric, or Special character.',
	    self::TYPE_NUMERICBLANK                => 'Any Numeric or Blank character',
	    self::TYPE_NUMERICSPECIAL              => 'Any Numeric of Special character',
	    self::TYPE_NUMERICBLANKSPECIALMICR     => 'Any Numeric Character, Dash (-), or Asterisk (*)',
	    self::TYPE_NUMERICBLANKSPECIALMICRONUS => 'Any Numeric Character, Dash (-), Asterisk (*), or Forward Slash (/)',
	    self::TYPE_BINARY                      => 'Binary Data',
	);
	
	return $legalTypes;
    }
    /**
     * adds the base Validators to the field, based on attributes we can pre-determine.
     */
    protected function addBaseValidators() {
	// initialize validator
	$this->validator = new Validator\Validator();
	
	// add validator based on usage.
	if ($this->usage === Field::USAGE_MANDATORY) {
	    $this->validator->addValidator(new Validator\ValidatorUsageManditory());
	}
	
	// add validator based on size.
	$this->validator->addValidator(new Validator\ValidatorSize($this->size));
	
	// add validator based on type.
	switch ($this->type) {
	    case Field::TYPE_ALPHABETIC:
		$this->validator->addValidator(new Validator\ValidatorTypeAlphabetic());
		break;
	    case Field::TYPE_NUMERIC:
		$this->validator->addValidator(new Validator\ValidatorTypeNumeric());
		break;
	    case Field::TYPE_BLANK:
		$this->validator->addValidator(new Validator\ValidatorTypeBlank());
		break;
	    case Field::TYPE_SPECIAL:
		// insert validators
		break;
	    case Field::TYPE_ALPHAMERIC:
		$this->validator->addValidator(new Validator\ValidatorTypeAlphameric());
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
    public function getName()   { return $this->fieldName; }
    public function getNumber() { return $this->fieldNumber; }
    public function getUsage()       { return $this->usage; }
    public function getPosition()    { return $this->position; }
    public function getSize()        { return $this->size; }
    public function getType()        { return $this->type; }
    
    /**
     * Return the value.
     * @param string $format Format to return the value.
     * @return string
     */
    public function getValue($format = self::FORMAT_RAW) {
        switch ($format) {
	    case self::FORMAT_FORMATED:
		return $this->getValueFormated();
	    case self::FORMAT_SIGNIFIGANT:
		return $this->getValueSignifigant();
	    case self::FORMAT_RAW:
		return $this->getValueRaw();
	    default:
		trigger_error('Invalid format type passed', E_USER_ERROR);
		return $this->value;
	}
    }
    
    /**
     * Return the value, after calling the classes formating function. Nothing
     * if blank.
     * @return string
     */
    public function getValueFormated() {
	if ($this->type === self::TYPE_BINARY) {
	    return 'Binary Data';
	}
	
	$value = $this->getValueSignifigant();
	
	// if value is blank we don't want to return that an not call the other
	// format functions.
	if ($value === '') {
	    return '';
	}
	
	return static::formatValue($value);
    }
    
    /**
     * Return the signifigant parts of the value, but in raw. Generally leading
     * 0's are not signifigant and are stripped. Binary data is stubbed.
     * @return string
     */
    public function getValueSignifigant() {
	if ($this->type === self::TYPE_BINARY) {
	    return 'Binary Data';
	}
	
	$value = trim($this->value);
	return ltrim($value, '0');
    }
    
    /**
     * Returns the raw value.
     * @return string
     */
    public function getValueRaw() {
	return $this->value;
    }

    public function parseValue($recordData) {
	if (is_string($recordData) === FALSE) {
	    throw new \InvalidArgumentException("Bad recordData passed. String expected.");
	}
	
	$this->value = substr($recordData, $this->position - 1, $this->size);
    }
    
    /**
     * Returns the value formated. By default just a trim, but classes override.
     * @param string $value
     * @return string
     */
    protected static function formatValue($value)
    {
	return trim($value);
    }
}