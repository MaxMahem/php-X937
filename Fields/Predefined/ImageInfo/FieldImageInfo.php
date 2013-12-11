<?php

namespace X937\Fields\Predefined\ImageInfo;

use X937\Fields\Predefined\FieldPredefined;

/**
 * Abstract base clase for Image Info fields. They share some const's in common.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
abstract class FieldImageInfo extends FieldPredefined
{
    const TEST_NOT_DONE = 0;
    
    public function __construct($fieldNumber, $fieldName, $position)
    {
	parent::__construct($fieldNumber, $fieldName, self::USAGE_CONDITIONAL, $position, 1);
    }
    
    public static function defineValues()
    {
	$definedValues = array(
	    self::TEST_NOT_DONE         => 'Test Not Done',
	);
	
	return $definedValues;
    }
}

require_once 'FieldImageInfoQuality.php';
require_once 'FieldImageInfoUsability.php';