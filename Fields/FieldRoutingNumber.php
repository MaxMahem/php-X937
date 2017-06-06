<?php namespace X937\Fields;

/**
 * Field containing a routing number. 9 digits, including a check digit.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldRoutingNumber extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
    parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Routing Number', $usage, $position, 9, Field::TYPE_NUMERIC);
    }

    protected function addClassValidators()
    {
    $this->validator->addValidator(new \X937\Validator\ValidatorRoutingNumber());
    }
    
    /**
     * Returns a routing number formated ####-#### without the check digit.
     * @return string
     */
    protected static function formatValue($value)
    {
    $shortenedValue = substr($value, 0, 8);
    
    // insert - in the middle of the number
    $replacedValue = substr_replace($shortenedValue, '-', 4, 0);
    return $replacedValue;
    }
}