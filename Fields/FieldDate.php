<?php

namespace X937\Fields;

/**
 * Field containing a date, YYYYMMDD format.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldDate extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Date', $usage, $position, 8, Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated()
    {
	$date     = $this->value;
	$dateTime = \DateTime::createFromFormat('Ymd', $date);
	
	return $dateTime->format('Y-m-d');
    }
    
    protected function addClassValidators()
    {
	$this->validator->addValidator(new \ValidatorDate('Ymd'));
    }
}