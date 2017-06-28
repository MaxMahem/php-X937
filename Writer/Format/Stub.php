<?php

namespace X937\Writer\Format;

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
    public function writeField(\X937\Fields\Field $field): string
    {
        $type = ($field->type == \X937\Fields\Type::BINARY) ? 'Binary Data' : 'Character Data';
        $bytes = $field->length;
        return "$type, $bytes bytes";
    }
}