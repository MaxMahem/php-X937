<?php

/**
 * Description of Validator
 *
 * @author astanley
 */
interface ValidatorInterface {
    public function validate($value);
    public function getMessages();
}

abstract class AbstractValidator implements ValidatorInterface {
    const ERROR = 'ABSTRACT ERROR.';
    
    abstract public function validate($value);
    public static function getMessages() {
	// a little cleverness here. self::ERROR would always return 'ABSTRACT ERROR'
	// this classes constant. static::ERROR tatic will return the child classes
	// overrident constant.
	return static::ERROR;
    }
}

class Validator implements ValidatorInterface {
    /* array of validator objects */
    private $validators;
    private $error;
    
    public function addValidator(ValidatorInterface $validator) {
	$this->validators[] = $validator;
	return $this;
    }

    public function validate($value) {
	foreach($this->validators as $validator) {
	    // print_r($validator);
	    
	    if ($validator->validate($value) === FALSE) {
		$this->error = $validator->getMessages();
		return FALSE;
	    }
	}
	
	return TRUE;
    }
    
    public function getMessages() {
	return $this->getMessages();
    }
    
    public function permitedValues() {
	foreach($this->validators as $validator) {
	    $permitedValues[] = $validator->permitedValues();
	}
	
	$permitedValue = implode(PHP_EOL, $permitedValues);
	return $permitedValue;
    }
}

class ValidatorUsageManditory extends AbstractValidator implements ValidatorInterface {
    const ERROR = 'Field is mandatory.';
    
    public function validate($value) {
	if (is_null($value) === TRUE) {
	    return FALSE;
	}
	
	return TRUE;
    }
}

class ValidatorTypeNumeric extends AbstractValidator implements ValidatorInterface {
    const ERROR = 'Field must be numeric';
    
    public function validate($value) { 
	return is_numeric($value);
    }
}

class ValidatorTypeAlphabetic extends AbstractValidator implements ValidatorInterface {
    const ERROR = 'Field must be Alphabetic';
    
    public function validate($value) {
	return ctype_alpha($value);
    }
}

class ValidatorTypeAlphameric extends AbstractValidator implements ValidatorInterface {
    const ERROR = 'Field must be alphanumeric';
    
    public function validate($value) {
	return ctype_alnum($value);
    }
}

class ValidatorTypeBlank extends AbstractValidator implements ValidatorInterface {
    const ERROR = 'Must be blank';
    
    public function validate($value) {
	$value = trim($value);
	return empty($value);
    }
}

/**
 * @todo add rest of Validator Type Checks
 */

class ValidatorSize implements ValidatorInterface {
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
    
    public function getMessages() {
	return "Field must be $this->fieldLength long.";
    }
}

class ValidatorValueEnumerated implements ValidatorInterface {
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
    
    public function getMessages() {
	return "Field is not a permited value.";
    }
    
    public function permitedValues() {	
	return 'Permited values:' . ' ' . implode(', ', $this->legalValues);
    }
}

class ValidatorRoutingNumber extends AbstractValidator implements ValidatorInterface {
    const ERROR = 'Must be a valid ABA routing number';

    public function validate($routingNumber) {
	// sum the 9 digit routing number via the routing number validation scheme.
	// Check details here: http://en.wikipedia.org/wiki/Routing_transit_number
	
	$validationSum  = 3 * ($routingNumber[0] + $routingNumber[3] + $routingNumber[6]);
	$validationSum += 7 * ($routingNumber[1] + $routingNumber[4] + $routingNumber[7]);
	$validationSum += 1 * ($routingNumber[2] + $routingNumber[5] + $routingNumber[8]);
	
	if (($validationSum % 10) === 0) {
	    return TRUE;
	} else {
	    return FALSE;
	}
    }
}