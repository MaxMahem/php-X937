<?php

namespace X937\Writer\Format;

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
        
        // check first for a dictonary translation.
        if (isset($field->dictonary)) {
            // logic set here is complex, so we set some variables and check each state.
            $comprehensiveDictonary = ($field->comprehensive === 'true');
            $haveTranslation        = (isset($field->dictonary[$value]));
            $valueIsBlank           = (trim($value) === '');
            $fieldMandatory         = ($field->usage === Fields\Field::USAGE_MANDATORY);
            
            // get our translation, if possible.
            $translation = ($haveTranslation) ? $field->dictonary[$value] : 'No Translation';
            
            // first check if we have a translation, if we have one, we always
            // want to return it.
            if ($haveTranslation) {
                // if we have a translation, we want to wrap the value if it is blank
                if ($valueIsBlank) {
                    $value = "'$value'";
                }
            
                return "$value - $translation";
            }
            
            // here we do not have a translation of our value.
            // if the value is blank, and the field is not mandatory, then we return an empty string.
            if ($valueIsBlank && !$fieldMandatory) {
                return '';
            }
            
            // here we do not have a translation of our value, and the value is mandatory.
            // if the dictonary is comprehensive, we want to indicate that there
            // should be a translation and there is not. Otherwise, we just return the value.
            if ($comprehensiveDictonary) {
                // if we have should have a translation, we want to wrap the value if it is blank
                if ($valueIsBlank) {
                    $value = "'$value'";
                }
                return "$value - $translation";
            } else {
                return $value;
            }
        }
        
        $value = trim($value);    // space should not be signifigant on either side.
        return ltrim($value, '0');      // 0's are not signifigant.
    }
}