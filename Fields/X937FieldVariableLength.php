<?php

namespace X937\Fields;

/**
 * Description of X937FieldVariableLength
 *
 * @author astanley
 */

/**
 * just a holder for now.
 */
abstract class VariableLength extends Field
{
    // do nothing
}

class ImageKey extends VariableLength
{
    public function __construct($fieldNumber, $filedName, $position, $size) {
	parent::__construct($fieldNumber, $filedName, Field::USAGE_CONDITIONAL, $position, $size, Field::TYPE_ALPHAMERICSPECIAL);
    }
}

abstract class Binary extends VariableLength
{
    const TYPE_BINARY = 'Binary';
    
    public function getValueFormated()
    {
	return 'Binary Data';
    }
    
    public function __construct($record, $fieldNumber, $filedName, $usage, $position, $size)
    {
	$this->record = $record;
	
	parent::__construct($fieldNumber, $filedName, $usage, $position, $size, self::TYPE_BINARY);
    }
    
    public function parseValue()
    {
	$rawRecordData = $this->record->getRawRecordData();
	
	parent::parseValue($rawRecordData);
    }
}

class DigitalSignature extends Binary
{
    public function __construct($record, $offset, $size)
    {
	parent::__construct($record, 17, 'Digital Signature', Field::USAGE_CONDITIONAL, 111 + $offset, $size);
    }
}

class ImageData extends Binary
{
    public function __construct($record, $offset, $size)
    {
	parent::__construct($record, 19, 'Image Data', Field::USAGE_MANDATORY, 118 + $offset, $size);
    }
}