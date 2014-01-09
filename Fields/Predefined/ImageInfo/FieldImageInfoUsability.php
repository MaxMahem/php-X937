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
    
    public function __construct($fieldNumber, $fieldNamePrefix, $position) {
	$fieldName = $fieldNamePrefix . ' ' . 'Usability';
	parent::__construct($fieldNumber, $fieldName, $position);
    }
    
    public static function defineValues()
    {	
	$definedValues = array(
	    self::TEST_NOT_DONE => 'Test Not Done',
	    self::UNUSEABLE     => "Data is unusable and unreadable",
	    self::USABLE        => "Data is usable and readable",
	);
	
	return $definedValues;
    }
}