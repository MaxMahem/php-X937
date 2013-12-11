<?php

namespace X937\Fields\Predefined\ImageInfo;

/**
 * Field containing information on an image quality test.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldImageInfoQuality extends FieldImageInfo
{
    const CONDITION_PRESENT     = 1;
    const CONDITION_NOT_PRESENT = 2;
    
    public static function defineValues()
    {
	$definedValues = array(
	    self::TEST_NOT_DONE         => 'Test Not Done',
	    self::CONDITION_PRESENT     => 'Condition Present',
	    self::CONDITION_NOT_PRESENT => 'Condition Not Present',
	);
	
	return $definedValues;
    }
}