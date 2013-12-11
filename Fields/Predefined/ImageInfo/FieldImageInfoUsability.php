<?php

namespace X937\Fields\Predefined\ImageInfo;

/**
 * Field containing information on an Image Usability Test
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldImageInfoUsability extends FieldImageInfo
{
    const UNUSEABLE      = 1;
    const USABLE         = 2;
    
    public static function defineValues()
    {
	// cut the usability part of the name out here so we can use it in our
	// definition below.
	$imagePartName = preg_replace(' Usability', '', $this->fieldName);
	
	$definedValues = array(
	    self::TEST_NOT_DONE => 'Test Not Done',
	    self::UNUSEABLE     => "$imagePartName data is unusable and unreadable",
	    self::USABLE        => "$imagePartName data is usable and readable",
	);
	
	return $definedValues;
    }
}