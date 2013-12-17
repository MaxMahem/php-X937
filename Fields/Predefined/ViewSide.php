<?php

namespace X937\Fields\Predefined;

/**
 * Indicates which side the coresponding image data is of.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class ViewSide extends FieldPredefined
{
    const VALUE_FRONT = 0;
    const VALUE_REAR = 1;
        
    public function __construct()
    {
	parent::__construct(8, 'View Side Indicator', self::USAGE_MANDATORY, 32, 1, self::TYPE_NUMERIC);
    }

    public static function defineValues()
    {
	$definedValues = array(
	    self::VALUE_FRONT => 'Front Image View',
	    self::VALUE_REAR  => 'Rear Image View',
	);
	
	return $definedValues;
    }
}