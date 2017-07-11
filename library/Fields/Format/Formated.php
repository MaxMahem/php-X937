<?php

namespace X937\Fields\Format;

use X937\Fields;

/**
 * Writes the fields value Formated (human readable) fashion.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Formated implements TextFormatInterface
{
    /**
     * Returns a formated field.
     * 
     * @todo add more formating based on type!
     * @param \X937\Fields\Field $field the field to write.
     * @return string formated field
     */
    public function format(Fields\Field $field): string
    {
        $value = $field->getValue();
        
        // logic set here is complex, so we set some variables and check each state.
        $valueIsBlank           = (trim($value) === '');
        $fieldMandatory         = ($field->usage === Fields\Field::USAGE_MANDATORY);
        
        // translate our value
        $translatedValue = FormatTranslate::formatTranslate($field);
        // if the translated value is different then the normal value, return it.
        if ($translatedValue !== $value) {
            return $translatedValue;
        }
        
        if ($valueIsBlank && !$fieldMandatory) {
            return '';
        }
        
        switch ($field->subtype) {
            case Fields\FieldSubType::DATE:
                return Util::formatDate($value);
            case Fields\FieldSubType::TIME:
                return Util::formatTIme($value);
            case Fields\FieldSubType::AMOUNT:
                return Util::formatAmount($value);
            case Fields\FieldSubType::BYTES:
                return Util::formatBytes($value);
            case Fields\FieldSubType::COUNT:
                return Util::formatCount($value);
        }
        
        $value = trim($value);    // space should not be signifigant on either side.
        return ltrim($value, '0');      // 0's are not signifigant.
    }
}