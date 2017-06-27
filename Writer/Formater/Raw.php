<?php

namespace X937\Writer\Formater;

/**
 * Writes the field value in it's raw (untranslated) fashion, ASCII for
 * non-binary data.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Raw implements FormaterInterface
{
    /**
     * Returns a raw field.
     * @param \X937\Fields\Field $field the field to write.
     * @return string formated field
     */
    public function writeField(\X937\Fields\Field $field): string
    {
        return $field->getValue();
    }
}