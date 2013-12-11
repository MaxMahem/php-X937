<?php

namespace X937\Fields;

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
    
    /**
     * Returns a routing number formated ####-#### without the check digit.
     * @return string
     */
    public function getValueFormated()
    {
	$value = substr($this->value, 0, 8);
	
	// insert - in the middle of the number
	$value = substr_replace($value, '-', 5, 0);
	return ;
    }

    protected function addClassValidators()
    {
	$this->validator->addValidator(new \ValidatorRoutingNumber());
    }
}