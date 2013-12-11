<?php

namespace X937\Fields\Predefined\ImageView;

/**
 * Abstract base class for later types.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
abstract class FieldImageView extends \X937\Fields\Predefined\FieldPredefined
{
    public function __construct($fieldNumber, $fieldNameSufix, $position, $size) {
	$fieldName = 'Image View' . ' ' . $fieldNameSufix;
	parent::__construct($fieldNumber, $fieldName, self::USAGE_MANDATORY, $position, 2, self::TYPE_NUMERIC);
    }
    
    /**
     * Inner function for translating values with a reserved range.
     * @param string $value Value to be translated
     * @param int $startNoAgreement beginning of no agreement required reserved range
     * @param int $endNoAgreement end of no agreement required reserved range
     * @param int $startAgreement start of agreement required reserved range
     * @return string
     */
    protected static function reservedTranslation($value, $startNoAgreement, $endNoAgreement, $startAgreement)
    {
	if       (($value >= $startNoAgreement)  && ($value <= $endNoAgreement)) {
	    return 'Reserved (Agreement is not required)';
	} elseif (($value >= $startAgreement)    && ($value <= 99)) {
	    return 'Reserved';
	} else {
	    return parent::translate($value);
	}
    }
}

require_once 'FieldImageViewCompression.php';
require_once 'FieldImageViewFormat.php';
