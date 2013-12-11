<?php

namespace X937\Fields;

abstract class Name extends Field 
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position, $size)
    {
	// append 'Name' to the end of the name. Results in: $fieldNamePrefix Name
	$fieldNamePrefix .= ' ' . 'Name';
	parent::__construct($fieldNumber, $fieldNamePrefix, Field::USAGE_CONDITIONAL, $position, $size, Field::TYPE_ALPHAMERICSPECIAL);
    }	
}

class NameInstitution extends Name
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position)
    {
	// append 'Institiuion' to the end of the name. Results in: $fieldNamePrefix Instition Name
	$fieldNamePrefix .= ' ' . 'Institution';
	parent::__construct($fieldNumber, $fieldNamePrefix, $position, 18);
    }
}

class NameContact extends Name
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position)
    {
	// append 'Contact' to the end of the name. Results in: $fieldNamePrefix Contact Name
	$fieldNamePrefix .= ' ' . 'Contact';
	parent::__construct($fieldNumber, $fieldNamePrefix, $position, 14);
    }
}

class NamePayee extends Name
{
    public function __construct()
    {
	parent::__construct(8, 'Payee', 59, 15);
    }
}

class NameSecurity extends Name
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position) {
	// prepend 'Security' to the begining of the name.
	$fieldNamePrefix = 'Security' . ' ' . $fieldNamePrefix;
	parent::__construct($fieldNumber, $fieldNamePrefix, $position, 16);
    }
}

class NamePayerAccount extends Name
{
    public function __construct()
    {	
	parent::__construct(6, 'Payor Account', 59, 22);
    }
}