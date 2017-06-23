<?php

namespace X937\Writer\Field;

/**
 * Writes the signigant values of fields.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Signifigant implements \X937\Writer\FieldInterface
{
    /**
     * Returns a the signifigant values of the field.
     * @param \X937\Fields\Field $field the field to write.
     * @return string the signifigant values of the field.
     */
    public function writeField(\X937\Fields\Field $field)
    {
        return $field->getValue(\X937\Fields\Field::FORMAT_SIGNIFIGANT);
    }
}