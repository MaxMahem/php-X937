<?php

namespace X937\Fields\Predefined;

/**
 * Field containing the cash letter type.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldCashLetterType extends FieldPredefined
{
    const NO_ELECTRONIC_OR_IMAGE_Record                       = 'N';
    const ELECTRONIC_Record_NO_IMAGES                         = 'E';
    const ELECTRONIC_AND_IMAGE_Record                         = 'I';
    const ELECTRONIC_AND_IMAGE_Record_PREVIOUS_CORRESPONDANCE = 'F';
    
    public function __construct()
    {
	parent::__construct(8, 'Cash Letter Record Type Indicator', self::USAGE_MANDATORY, 43, 1, self::TYPE_ALPHABETIC);
    }

    public static function defineValues()
    {
	$definedValues = array(
	    self::NO_ELECTRONIC_OR_IMAGE_Record                       => 'No electronic check Record or image Record',
	    self::ELECTRONIC_Record_NO_IMAGES                         => 'Electronic check Record with no images',
	    self::ELECTRONIC_AND_IMAGE_Record                         => 'Electronic check Record and image Record',
	    self::ELECTRONIC_AND_IMAGE_Record_PREVIOUS_CORRESPONDANCE => 'Electronic check Record and image Record that corespond with previous cash letter',
	);
	
	return $definedValues;
    }
}