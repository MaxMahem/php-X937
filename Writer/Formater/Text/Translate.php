<?php

namespace X937\Writer\Formater\Text;

use X937\Fields;

/**
 * Writes the fields value Formated (human readable) fashion.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Translate implements \X937\Writer\Formater\FormaterInterface
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
        if (isset($field->dictonary)) {
            $value = $field->getValue();
            $translation = isset($field->dictonary[$value]) ? $field->dictonary[$value] : 'No Translation';
            return $value . ' ' . $translation;
        } else {
            return trim($field->getValue());
        }
    }
}