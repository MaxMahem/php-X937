<?php

namespace X937\Fields\Predefined;

/**
 * Field Indicating if a record is variable size or not.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldVariableSize extends FieldPredefined
{
    const FIXED    = '0';
    const VARIABLE = '1';
    
    public function __construct()
    {
	parent::__construct(2, 'Variable Size Record Indicator', self::USAGE_MANDATORY, 3, 1, self::TYPE_NUMERIC);
    }

    public static function defineValues()
    {
	$definedValues = array(
	    self::FIXED    => 'Fixed Size',
	    self::VARIABLE => 'Variable Size',
	);
	
	return $definedValues;
    }
}