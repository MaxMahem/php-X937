<?php
/**
 * Description of X937FieldVariableLength
 *
 * @author astanley
 */

/**
 * just a holder for now.
 */
class X937FieldVariableLength extends X937Field {
    // do nothing
}

abstract class X937FieldBinary extends X937FieldVariableLength {
    const TYPE_BINARY = 'Binary';
    
    public function getValueFormated() {
	return 'Binary Data';
    }
    
    public function __construct($record, $fieldNumber, $filedName, $usage, $position, $size) {
	$this->record = $record;
	
	parent::__construct($fieldNumber, $filedName, $usage, $position, $size, self::TYPE_BINARY);
    }
    
    public function parseValue() {
	$rawRecordData = $this->record->getRawRecordData();
	
	parent::parseValue($rawRecordData);
    }
}

class X937FieldDigitalSignature extends X937FieldBinary {
    public function __construct($record, $offset, $size) {
	parent::__construct($record, 17, 'Digital Signature', X937Field::CONDITIONAL, 111 + $offset, $size);
    }
}

class X937FieldImageData extends X937FieldBinary {
    public function __construct($record, $offset, $size) {
	parent::__construct($record, 19, 'Image Data', X937Field::MANDATORY, 118 + $offset, $size);
    }
}