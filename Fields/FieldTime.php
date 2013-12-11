<?php

namespace X937\Fields;

/**
 * Field containing a time. HHMM format, 24 hour clock.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldTime extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Time', $usage, $position, 4, Field::TYPE_NUMERIC);
    }
    
    public function getValueFormated()
    {
	$time     = $this->value;
	$dateTime = \DateTime::createFromFormat('Hi', $time);
	
	return $dateTime->format('H:i');
    }

    public static function translate($value)
    {
	return '24 Hour Clock';
    }
}