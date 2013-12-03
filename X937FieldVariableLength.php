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
    const BINARY = 'Binary';
    
    public function __construct($fieldNumber, $filedName, $usage, $position, $size) {
	parent::__construct($fieldNumber, $filedName, $usage, $position, $size, X937Field::BINARY);
    }
    
    public function parseValue() {
	$this->value = 'Binary Data';
}
}

class X937FieldDigitalSignature extends X937FieldBinary {
    public function __construct($offset, $size) {
	parent::__construct(17, 'Digital Signature', X937Field::CONDITIONAL, 111 + $offset, $size);
    }
}

class X937FieldImageData extends X937FieldBinary {
    public function __construct($offset, $size) {
	parent::__construct(19, 'Image Data', X937Field::MANDATORY, 118 + $offset, $size);
    }
}