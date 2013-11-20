<?php

require_once 'X937FieldValidator.php';
/**
 * Description of X937Field
 *
 * @author astanley
 */
class X937Field {
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
	    $this->validator->addValidator(new FieldValidatorUsageManditory());
	}
	
	// add validator based on size.
	$this->validator->addValidator(new FieldValidatorSize($this->size));
	
	// add validator based on type.
	switch ($this->type) {
	    case X937Field::ALPHABETIC:
		$this->validator->addValidator(new FieldValidatorTypeAlphabetic());
		break;
	    case X937Field::NUMERIC:
		$this->validator->addValidator(new FieldValidatorTypeNumeric());
		break;
	    case X937Field::BLANK:
		$this->validator->addValidator(new FieldValidatorTypeBlank());
		break;
	    case X937Field::SPECIAL:
		// insert validators
		break;
	    case X937Field::ALPHAMERIC:
		$this->validator->addValidator(new FieldValidatorTypeAlphameric());
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
    public function getValue()       { return $this->value; }

    public function parseValue($recordASCII) {
	    $this->value = substr($recordASCII, $this->position - 1, $this->size);
    }
}

abstract class X937FieldWithDefinedTypes extends X937Field {
    public abstract static function defineValues();
    
    protected function addClassValidators() {
	$legalValues          = array_keys(self::defineValues());
	$legalValuesValidator = new FieldValidatorValueEnumerated($legalValues);
	$this->validator->addValidator($legalValuesValidator);
    }
    
    public function translateThisValue() {
	return self::translate($this->value);
    }
    
    public static function translate($value) {
	$legalValues = self::defineValues();
	
	if (array_key_exists($value, $legalValues)) {
	    $translatedValue = $legalValues[$legalValues];
	    if (gettype($translatedValue) !== 'string') {
		throw new LogicException('Bad data type in X937Field Value table. All values should be strings.');
	    }
	} else {
	    $translatedValue = 'Undefined';
	}
	
	return $translatedValue;
    }
}

class X937FieldRecordType extends X937Field {
    public function __construct($value) {
	parent::__construct(1, 'Record Type', X937Field::MANDATORY, 1, 2, X937Field::NUMERIC);
	
	$this->value = $value;
    }
    
    protected function addClassValidators() {	
	$legalRecordTypes = X937Record::defineRecordTypes();
	$this->validator->addValidator(new FieldValidatorValueEnumerated($legalRecordTypes));
    }
}

class X937FieldSpecificationLevel extends X937FieldWithDefinedTypes {
    const X9371994 = 01;
    const X9372001 = 02;
    const X9372003 = 03;
    
    public function __construct() {
	parent::__construct(1, 'Specification Level', X937Field::MANDATORY,    3,  2, X937Field::NUMERIC);
    }
    
    public function getSpecificatonLevelName() {
	$X937FieldSpecificationLevels = self::defineValues();
	return $X937FieldSpecificationLevels[$this->value];
    }
    
    public static function defineValues() {
	$X937FieldSpecificationLevels = array(
	    X937FieldSpecificationLevel::X9371994 => 'X9.37-1994',
	    X937FieldSpecificationLevel::X9372001 => 'X9.37-2001',
	    X937FieldSpecificationLevel::X9372003 => 'X9.37-2003'
	);
	
	return $X937FieldSpecificationLevels;
    }
}

class X937FieldTestFileIndicator extends X937FieldWithDefinedTypes {
    const PRODUCTION_FILE = 'P';
    const TEST_FILE       = 'T';
    
    public function __construct() {
	parent::__construct(3, 'Test File Indicator', X937Field::MANDATORY, 5, 1, X937Field::ALPHABETIC);
    }
    
    public static function defineValues() {
	$X937FieldTestFileIndicators = array(
	    X937FieldTestFileIndicator::PRODUCTION_FILE => 'Production File',
	    X937FieldTestFileIndicator::TEST_FILE       => 'Test File',
	);
	
	return $X937FieldTestFileIndicators;
    }
}

class X937FieldReserved extends X937Field {
    public function __construct($fieldNumber, $position, $size) {
	parent::__construct($fieldNumber, 'Reserved', X937Field::MANDATORY, $position, $size, X937Field::BLANK);
    }
}

class X937FieldUser extends X937Field {
    public function __construct($fieldNumber, $position, $size) {
	parent::__construct($fieldNumber, 'User Field', X937Field::CONDITIONAL, $position, $size, X937Field::ALPHAMERICSPECIAL);
    }
}

class X937FieldDate extends X937Field {
    public function __construct($fieldNumber, $fieldName, $usage, $position) {
	parent::__construct($fieldNumber, $fieldName, $usage, $position, 8, X937Field::NUMERIC);
    }	
}

class X937FieldTime extends X937Field {
    public function __construct($fieldNumber, $fieldName, $usage, $position) {
	parent::__construct($fieldNumber, $fieldName, $usage, $position, 4, X937Field::NUMERIC);
    }	
}

class X937FieldInstitutionName extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Name', $usage, $position, 18, X937Field::ALPHABETIC);
    }
}

class X937FieldContactName extends X937Field {
    public function __construct($fieldNumber, $fieldName, $usage, $position) {
	parent::__construct($fieldNumber, $fieldName, $usage, $position, 14, X937Field::ALPHAMERICSPECIAL);
    }	
}

class X937FieldPhoneNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Phone Number', $usage, $position, 10, X937Field::NUMERIC);
    }	
}

class X937FieldRoutingNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Routing Number', $usage, $position, 9, X937Field::NUMERIC);
    }	
}

class X937FieldDepositAccountNumber extends X937Field {
    public function __construct($fieldNumber, $position) {
	parent::__construct($fieldNumber, 'Deposit Account Number at BOFD', X937Field::CONDITIONAL, $position, 18, X937Field::ALPHAMERICSPECIAL);
    }	
}

class X937FieldItemAmount extends X937Field {
    public function __construct($fieldNumber, $position) {
	parent::__construct($fieldNumber, 'Item Amount', X937Field::MANDATORY, $position, 10, X937Field::NUMERIC);
    }	
}

class X937FieldItemSequenceNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Item Sequence Number', $usage, $position, 15, X937Field::NUMERICBLANK);
    }	
}

class X937FieldReturnReason extends X937Field {
    public function __construct($fieldNumber, $usage, $position) {
	parent::__construct($fieldNumber, 'Return Reason', $usage, $position, 1, X937Field::ALPHAMERIC);
    }
}