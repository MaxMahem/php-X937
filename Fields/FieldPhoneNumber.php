<?php

namespace X937\Fields;

/**
 * A field containing a phone number. 10 digits, numbers only (#########)
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldPhoneNumber extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
    parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Phone Number', $usage, $position, 10, Field::TYPE_NUMERIC);
    }
    
    /**
     * Returns the phone number formated ###-###-####
     * @return string
     */
    protected static function formatValue($value)
    {
    return substr($value, 0, 3) . '-' . substr($value, 3, 3) . '-' . substr($value, 6, 4);
    }
}