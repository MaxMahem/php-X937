<?php

namespace X937\Fields;

/**
 * A field containing a monitary value. No decimal places, divide by 100 to get
 * dollar amount.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Amount extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $position, $size, $usage = Field::USAGE_MANDATORY)
    {
	parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Amount', $usage, $position, $size, Field::TYPE_NUMERIC);
    }
    
    /**
     * gets the value formated as normal for US currency, with proper decimal
     * places and thousands seperator, $###,###.##.
     * @return string
     */
    public function getValueFormated()
    {
	$value = $this->value / 100;

	return '$' . number_format($value, 2);
    }
    
    public function getValueSignifigant() {
	return ltrim($this->value, '0 ');
    }
}