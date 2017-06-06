<?php

namespace X937\Fields\VariableLength\Binary;

/**
 * A field containing binary data.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class BinaryData extends \X937\Fields\VariableLength\VariableLength
{
    const FORMAT_BASE64 = 'base64';
    const FORMAT_BINARY = 'binary';
    
    /**
     * A field containing binary data. Note we want a parent record for this one
     * @param \X937\Record\Record $record parent record
     * @param int $fieldNumber
     * @param string $filedName
     * @param string $usage
     * @param int $position
     * @param int $size
     */
    public function __construct($fieldNumber, $filedName, $usage, $position, $size)
    {    
    parent::__construct($fieldNumber, $filedName, $usage, $position, $size, self::TYPE_BINARY);
    }
    
    /**
     * Return the value.
     * @param string $format Format to return the value.
     * @return string
     */
    public function getValue($format = self::FORMAT_RAW)
    {
    switch($format) {
        case self::FORMAT_BINARY:
        return $this->getValueBinary();
        case self::FORMAT_BASE64:
        return $this->getValueBase64();
        default:
        return parent::getValue($format);
    }
    }
    
    public function getValueBinary() {
    return $this->value;
    }
    
    public function getValueBase64() {
    return base64_encode($this->value);
    }
    
    /**
     * Returns the number of bytes in the field. If 0 bytes, returns blank.
     * @return string
     */
    public function getValueFormated() {
    // strelen should work here even though this data is binary. We actually
    // the value in 8 bit byets.
    $size = strlen($this->value);
    
    // if size is 0, return nothing. This should always be the case for 0
    // length fields, which is possible for variable length fields.
    if ($size === 0) {
        return '';
    }
    return \X937\Fields\SizeBytes::formatBytes($size) . ' ' . 'Binary Data';
    }
}