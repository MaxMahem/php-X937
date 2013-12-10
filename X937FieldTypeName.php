<?php

abstract class X937FieldName extends X937Field 
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position, $size) {
	// append 'Name' to the end of the name. Results in: $fieldNamePrefix Name
	$fieldNamePrefix .= ' ' . 'Name';
	parent::__construct($fieldNumber, $fieldNamePrefix, X937Field::USAGE_CONDITIONAL, $position, $size, X937Field::TYPE_ALPHAMERICSPECIAL);
    }	
}

class X937FieldNameInstitution extends X937FieldName
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position) {
	// append 'Institiuion' to the end of the name. Results in: $fieldNamePrefix Instition Name
	$fieldNamePrefix .= ' ' . 'Institution';
	parent::__construct($fieldNumber, $fieldNamePrefix, $position, 18);
    }
}

class X937FieldNameContact extends X937FieldName
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position)
    {
	// append 'Contact' to the end of the name. Results in: $fieldNamePrefix Contact Name
	$fieldNamePrefix .= ' ' . 'Contact';
	parent::__construct($fieldNumber, $fieldNamePrefix, $position, 14);
    }
}

class X937FieldNamePayee extends X937FieldName
{
    public function __construct()
    {
	parent::__construct(8, 'Payee', 59, 15);
    }
}

class X937FieldNameSecurity extends X937FieldName
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position) {
	// prepend 'Security' to the begining of the name.
	$fieldNamePrefix = 'Security' . ' ' . $fieldNamePrefix;
	parent::__construct($fieldNumber, $fieldNamePrefix, $position, 16);
    }
}

class X937FieldNamePayerAccount extends X937FieldName
{
    public function __construct()
    {	
	parent::__construct(6, 'Payor Account', 59, 22);
    }
}