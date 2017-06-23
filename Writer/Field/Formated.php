<?php

namespace X937\Writer\Field;

/**
 * Writes the fields value Formated (human readable) fashion.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Formated implements \X937\Writer\FieldInterface
{
    /**
     * Returns a formated field.
     * @param \X937\Fields\Field $field the field to write.
     * @return string formated field
     */
    public function writeField(\X937\Fields\Field $field)
    {
        if ($field->type == \X937\Fields\Type::BINARY) {
            $bytes = strlen($field->getValue());
            return "Binary data, $bytes bytes";
        } else {
            return trim($field->getValue());
        }
    }
}