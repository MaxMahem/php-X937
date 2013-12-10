<?php

class X937FieldName extends X937Field 
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position, $size) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Name', X937Field::USAGE_CONDITIONAL, $position, $size, X937Field::TYPE_ALPHAMERICSPECIAL);
    }	
}

class X937FieldNameInstitution extends X937FieldName
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position) {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Institution', $position, 18);
    }
}

class X937FieldNameSecurity extends X937FieldName
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position) {
	parent::__construct($fieldNumber, 'Security' . ' ' . $fieldNamePrefix, $position, 16);
    }
}