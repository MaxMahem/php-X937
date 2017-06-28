<?php

namespace X937\Writer\Format;

use X937\Fields\Field;

/**
 * Writes Binary Fields in Base64.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Stub implements TextFormatInterface, BinaryFormatInterface
{

    /**
     * Returns the Field data as a stub.
     * @param \X937\Fields\Field $field
     * @return string The binary data encoded Base64
     */
    public function format(Field $field): string
    {
        // if the field has no length, and is not mandatory, then we return ''
        if (($field->length == 0) && ($field->usage !== Field::USAGE_MANDATORY)) {
            return '';
        }
        
        $type = ($field->type == \X937\Fields\Type::BINARY) ? 'Binary Data' : 'Character Data';
        $bytes = self::formatBytes($field->length);
        return "$type, $bytes";
    }
    
    public static function formatBytes($bytes) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

        $bytes = max($bytes, 0);
        $precision = strlen($bytes) - 2;
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 
}