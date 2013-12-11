<?php

namespace X937\Fields\Predefined;

/**
 * Field indicating if Test or Production file
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldTestFile extends FieldPredefined
{
    const PRODUCTION_FILE = 'P';
    const TEST_FILE       = 'T';
    
    public function __construct()
    {
	parent::__construct(3, 'Test File Indicator', self::USAGE_MANDATORY, 5, 1, self::TYPE_ALPHABETIC);
    }
    
    public static function defineValues()
    {
	$X937FieldTestFileIndicators = array(
	    self::PRODUCTION_FILE => 'Production File',
	    self::TEST_FILE       => 'Test File',
	);
	
	return $X937FieldTestFileIndicators;
    }
}