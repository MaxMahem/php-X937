<?php

namespace X937\Fields\Predefined;

/**
 * Field indicating if the file is resend or not.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldResend extends FieldPredefined
{
    const VALUE_RESEND_FILE   = 'Y';
    const VALUE_ORIGINAL_FILE = 'N';
    
    public function __construct()
    {
	parent::__construct(8, 'Resend Indicator', self::USAGE_MANDATORY, 36, 1, self::TYPE_ALPHABETIC);
    }
    
    public static function defineValues()
    {
	$definedValues = array(
	    self::VALUE_RESEND_FILE   => 'Resend File',
	    self::VALUE_ORIGINAL_FILE => 'Original File',
	);
	
	return $definedValues;
    }
}