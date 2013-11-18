<?php

require_once 'FieldValidator.php';
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
     * @var FieldValidator
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
	$this->validator = new FieldValidator();
	
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

    // validate?
    public static function validate() {}

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

class X937FieldRecordType extends X937Field {
    public function __construct($value) {
	parent::__construct(1, 'Record Type', X937Field::MANDATORY, 1, 2, X937Field::NUMERIC);
	
	$this->value = $value;
    }
    
    protected function addClassValidators() {	
	$legalRecordTypes = X937Record::getRecordTypes();
	$this->validator->addValidator(new FieldValidatorValueEnumerated($legalRecordTypes));
    }
}

class X937FieldReserved extends X937Field {
    public function __construct($fieldNumber, $position, $size) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = 'Reserved';
	$this->usage       = X937Field::MANDATORY;
	$this->position    = $position;
	$this->size        = $size;
	$this->type        = X937Field::BLANK;
    }
}

class X937FieldUser extends X937Field {
    public function __construct($fieldNumber, $position, $size) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = 'User Field';
	$this->usage       = X937Field::CONDITIONAL;
	$this->position    = $position;
	$this->size        = $size;
	$this->type        = X937Field::ALPHAMERICSPECIAL;
    }
}

class X937FieldDate extends X937Field {
    public function __construct($fieldNumber, $fieldName, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldName;
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 8;
	$this->type        = X937Field::NUMERIC;
    }	
}

class X937FieldTime extends X937Field {
    public function __construct($fieldNumber, $fieldName, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldName;
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 4;
	$this->type        = X937Field::NUMERIC;
    }	
}

class X937FieldInstitutionName extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldNamePrefix . ' ' . 'Name';
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 18;
	$this->type        = X937Field::ALPHABETIC;
    }	
}

class X937FieldContactName extends X937Field {
    public function __construct($fieldNumber, $fieldName, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldName;
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 14;
	$this->type        = X937Field::ALPHAMERICSPECIAL;
    }	
}

class X937FieldPhoneNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldNamePrefix . ' ' . 'Phone Number';
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 10;
	$this->type        = X937Field::NUMERIC;
    }	
}

class X937FieldRoutingNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldNamePrefix . ' ' . 'Routing Number';
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 9;
	$this->type        = X937Field::NUMERIC;
    }	
}

class X937FieldDepositAccountNumber extends X937Field {
    public function __construct($fieldNumber, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = 'Deposit Account Number at BOFD';
	$this->usage       = X937Field::CONDITIONAL;
	$this->position    = $position;
	$this->size        = 18;
	$this->type        = X937Field::ALPHAMERICSPECIAL;
    }	
}

class X937FieldItemAmount extends X937Field {
    public function __construct($fieldNumber, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = 'Item Amount';
	$this->usage       = X937Field::MANDATORY;
	$this->position    = $position;
	$this->size        = 10;
	$this->type        = X937Field::NUMERIC;
    }	
}

class X937FieldItemSequenceNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = $fieldNamePrefix . ' ' . 'Item Sequence Number';
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 15;
	$this->type        = X937Field::NUMERICBLANK;
    }	
}

class X937FieldReturnReason extends X937Field {
    public function __construct($fieldNumber, $usage, $position) {
	$this->fieldNumber = $fieldNumber;
	$this->fieldName   = 'Return Reason';
	$this->usage       = $usage;
	$this->position    = $position;
	$this->size        = 1;
	$this->type        = X937Field::ALPHAMERIC;
    }	
}
?>
