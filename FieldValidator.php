<?php

/**
 * Description of Validator
 *
 * @author astanley
 */
interface FieldValidatorInterface {
    public function validate($value);
    public function error();
}

class FieldValidator implements FieldValidatorInterface {
    /* array of validator objects */
    private $validators;
    private $error;
    
    public function addValidator(FieldValidatorInterface $validator) {
	$this->validators[] = $validator;
	return $this;
    }

    public function validate($value) {
	foreach($this->validators as $validator) {
	    if ($validator->validate($value) === FALSE) {
		$this->error = $validator->error();
		return FALSE;
	    }
	}
	
	return TRUE;
    }
    
    public function error() {
	return $this->error();
    }
}

class FieldValidatorUsageManditory implements FieldValidatorInterface {
    public function validate($value) {
	if (is_null($value) === TRUE) {
	    return FALSE;
	}
	
	return TRUE;
    }
    
    public function error() {
	return "Field is required.";
    }
}

class FieldValidatorTypeNumeric implements FieldValidatorInterface {
    public function validate($value) { 
	return is_numeric($value);
    }
    
    public function error() {
	return "Field must be numeric.";
    }        
}

class FieldValidatorTypeAlphabetic implements FieldValidatorInterface {
    public function validate($value) {
	return ctype_alpha($value);
    }
    
    public function error() {
	return "Field must be Alphabetic";
    }
}

class FieldValidatorTypeAlphameric implements FieldValidatorInterface {
    public function validate($value) {
	return ctype_alnum($value);
    }
    
    public function error() {
	return "Field must be alphanumeric";
    }
}

class FieldValidatorTypeBlank implements FieldValidatorInterface {
    public function validate($value) {
	$value = trim($value);
	return empty($value);
    }
    
    public function error() {
	return "Field must be blank";
    }
}

/**
 * @todo add rest of Validator Type Checks
 */

class FieldValidatorSize implements FieldValidatorInterface {
    private $fieldLength;
    
    public function __construct($fieldLength) {
	if (!is_integer($fieldLength)) {
	    throw new InvalidArgumentException("Invalid argument passed to new FieldValidatorSize: $fieldLength. Integer expected");
	}
	
	$this->fieldLength = $fieldLength;
    }
    
    public function validate($value) {
	return strlen($value) === $this->fieldLength;
    }
    
    public function error() {
	return "Field must be $this->fieldLength long.";
    }
}

class FieldValidatorValueEnumerated implements FieldValidatorInterface {
    private $legalValues;
    
    public function __construct(array $legalValues) {
	$this->legalValues = $legalValues;
    }

    public function validate($value) {
	if (is_null($value) === TRUE) {
	    return FALSE;
	}
	
	return TRUE;
    }
    
    public function getLegalValues() { return $this->legalValues; }
    
    public function error() {
	return "Field is not a permited value.";
    }
}

?>
