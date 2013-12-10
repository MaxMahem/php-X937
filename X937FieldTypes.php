<?php

/**
 * Just a stub, relies on parent class for all methods ATM.
 */
class X937FieldGeneric extends X937Field {
    // a stub
}

class X937FieldReserved extends X937Field {
    public function __construct($fieldNumber, $position, $size) {
	parent::__construct($fieldNumber, 'Reserved', X937Field::USAGE_MANDATORY, $position, $size, X937Field::TYPE_BLANK);
    }
}

class X937FieldUser extends X937Field {
    public function __construct($fieldNumber, $position, $size) {
	parent::__construct($fieldNumber, 'User Field', X937Field::USAGE_CONDITIONAL, $position, $size, X937Field::TYPE_ALPHAMERICSPECIAL);
    }
}

class X937FieldDate extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Date', $usage, $position, 8, X937Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated() {
	$date     = $this->value;
	$dateTime = DateTime::createFromFormat('Ymd', $date);
	
	return $dateTime->format('Y-m-d');
    }
    
    protected function addClassValidators() {
	$this->validator->addValidator(new ValidatorDate('Ymd'));
    }
}

class X937FieldTime extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Time', $usage, $position, 4, X937Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated() {
	$time     = $this->value;
	$dateTime = DateTime::createFromFormat('Hi', $time);
	
	return $dateTime->format('H:i');
    }

    public static function translate($value) {
	return '24 Hour Clock';
    }
}

class X937FieldInstitutionName extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Name', $usage, $position, 18, X937Field::TYPE_ALPHABETIC);
    }
}

class X937FieldContactName extends X937Field {
    public function __construct($fieldNumber, $fieldName, $usage, $position) {
	parent::__construct($fieldNumber, $fieldName, $usage, $position, 14, X937Field::TYPE_ALPHAMERICSPECIAL);
    }	
}

class X937FieldPhoneNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Phone Number', $usage, $position, 10, X937Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated() {
	$value = $this->value;
	return substr($value, 0, 3) . '-' . substr($value, 3, 3) . '-' . substr($value, 6, 4);
    }

}

class X937FieldRoutingNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Routing Number', $usage, $position, 9, X937Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated() {
	return substr_replace($this->value, '-', 5, 0);
    }

    protected function addClassValidators() {
	$this->validator->addValidator(new ValidatorRoutingNumber());
    }
}

class X937FieldDepositAccountNumber extends X937Field {
    public function __construct($fieldNumber, $position) {
	parent::__construct($fieldNumber, 'Deposit Account Number at BOFD', X937Field::USAGE_CONDITIONAL, $position, 18, X937Field::TYPE_ALPHAMERICSPECIAL);
    }	
}

class X937FieldItemAmount extends X937Field {
    public function __construct($fieldNumber, $position) {
	parent::__construct($fieldNumber, 'Item Amount', X937Field::USAGE_MANDATORY, $position, 10, X937Field::TYPE_NUMERIC);
    }	
}

class X937FieldItemSequenceNumber extends X937Field {
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Item Sequence Number', $usage, $position, 15, X937Field::TYPE_NUMERICBLANK);
    }	
}

class X937FieldReturnReason extends X937Field {
    public function __construct($fieldNumber, $usage, $position) {
	parent::__construct($fieldNumber, 'Return Reason', $usage, $position, 1, X937Field::TYPE_ALPHAMERIC);
    }
}