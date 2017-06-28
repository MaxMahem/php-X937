<?php

namespace X937\Writer\Format;

/**
 * Writes the fields value in EBCDIC.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class EBCDIC implements TextFormatInterface
{
    /**
     * Returns a formated field.
     * 
     * @todo add more formating based on type!
     * @param \X937\Fields\Field $field the field to write.
     * @return string formated field
     */
    public function writeField(\X937\Fields\Field $field): string
    {
        return \X937\Util::a2e($field->getValue());
    }
}