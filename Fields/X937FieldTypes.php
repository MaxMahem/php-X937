<?php

namespace X937\Fields;

/**
 * Just a stub, relies on parent class for all methods ATM.
 */
class Generic extends Field
{
    // a stub
}

class Reserved extends Field
{
    public function __construct($fieldNumber, $position, $size)
    {
	parent::__construct($fieldNumber, 'Reserved', Field::USAGE_MANDATORY, $position, $size, Field::TYPE_BLANK);
    }
}

class User extends Field
{
    public function __construct($fieldNumber, $position, $size)
    {
	parent::__construct($fieldNumber, 'User Field', Field::USAGE_CONDITIONAL, $position, $size, Field::TYPE_ALPHAMERICSPECIAL);
    }
}

class Date extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Date', $usage, $position, 8, Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated()
    {
	$date     = $this->value;
	$dateTime = \DateTime::createFromFormat('Ymd', $date);
	
	return $dateTime->format('Y-m-d');
    }
    
    protected function addClassValidators()
    {
	$this->validator->addValidator(new \ValidatorDate('Ymd'));
    }
}

class Time extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Time', $usage, $position, 4, Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated()
    {
	$time     = $this->value;
	$dateTime = \DateTime::createFromFormat('Hi', $time);
	
	return $dateTime->format('H:i');
    }

    public static function translate($value)
    {
	return '24 Hour Clock';
    }
}

class PhoneNumber extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Phone Number', $usage, $position, 10, Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated()
    {
	$value = $this->value;
	return substr($value, 0, 3) . '-' . substr($value, 3, 3) . '-' . substr($value, 6, 4);
    }
}

class RoutingNumber extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Routing Number', $usage, $position, 9, Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated()
    {
	return substr_replace($this->value, '-', 5, 0);
    }

    protected function addClassValidators()
    {
	$this->validator->addValidator(new \ValidatorRoutingNumber());
    }
}

class DepositAccountNumber extends Field
{
    public function __construct($fieldNumber, $position)
    {
	parent::__construct($fieldNumber, 'Deposit Account Number at BOFD', Field::USAGE_CONDITIONAL, $position, 18, Field::TYPE_ALPHAMERICSPECIAL);
    }	
}

class Amount extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position, $size, $usage = Field::USAGE_MANDATORY)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Amount', $usage, $position, $size, Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated()
    {
	$value = $this->value / 100;

	return '$' . number_format($value, 2);
    }
}

class ItemSequenceNumber extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Item Sequence Number', $usage, $position, 15, Field::TYPE_NUMERICBLANK);
    }	
}

class ReturnReason extends Field
{
    public function __construct($fieldNumber, $usage, $position)
    {
	parent::__construct($fieldNumber, 'Return Reason', $usage, $position, 1, Field::TYPE_ALPHAMERIC);
    }
}