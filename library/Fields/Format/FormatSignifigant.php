<?php

namespace X937\Fields\Format;

use X937\Fields;

/**
 * Writes the signigant values of fields.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FormatSignifigant implements TextFormatInterface
{
    /**
     * Returns a the signifigant values of the field.
     * @param \X937\Fields\Field $field the field to write.
     * @return string the signifigant values of the field.
     */
    public function format(Fields\Field $field): string
    {
        // if our field is a comprehensive dictonary, we want to return the
        // exact value and not trim
        if ($field->comprehensive === 'true') {
            return $field->getValue();
        }
        
        $value = trim($field->getValue());    // space should not be signifigant on either side.
        return ltrim($value, '0');      // 0's are not signifigant.
    }
}