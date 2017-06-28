<?php

namespace X937\Writer\Format;

/**
 * Writes the field value in it's raw state without any adjustments.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Raw implements TextFormatInterface, BinaryFormatInterface
{
    /**
     * Returns a raw field.
     * @param \X937\Fields\Field $field the field to write.
     * @return string formated field
     */
    public function format(\X937\Fields\Field $field): string
    {
        return $field->getValue();
    }
}