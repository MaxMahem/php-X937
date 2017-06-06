<?php

namespace X937\Fields;

/**
 * A field containing a size bytes, typically another field.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class SizeBytes extends Field
{
    public function __construct($fieldNumber, $fieldName, $usage, $position, $size)
    {;
    parent::__construct($fieldNumber, $fieldName, $usage, $position, $size, self::TYPE_NUMERIC);
    }
    
    public function getValueSignifigant() {
    return ltrim($this->value, '0 ');
    }
    
    public static function formatBytes($bytes, $signifigantDigits = 2) {
    if ((is_numeric($bytes) === false) || ($bytes < 0)) {
            throw new \InvalidArgumentException("Non-numeric or negative byte count given to formatByte. $bytes");
//        return trim($bytes);
    }
    
    // handle 0 byte case.
    if ($bytes == 0) {
        return '0b';
    }
    
    $units = array('b', 'kB', 'MB'); 

    // get the power we are going to convert using.
    $powRaw = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    
    // min the pow so we don't overflow our units.
    $powMin = min($powRaw, count($units) - 1); 

    // convert our bytes.
    $convertedBytes = $bytes / pow(1024, $powMin);
    
    // get our unit
    $unit = $units[$powMin];
    
        return round($convertedBytes, $signifigantDigits) . $unit;
    }
    
    /**
     * Return the value formated.
     * @param type $string
     * @return string
     */
    protected static function formatValue($value) {
    return self::formatBytes($value);
    }
}
