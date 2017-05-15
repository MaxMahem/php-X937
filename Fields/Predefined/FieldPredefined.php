<?php

namespace X937\Fields\Predefined;

use X937\Fields\Field;

/**
 * FieldsPredefined is an abstract base class which defines methods for other
 * fields with predefined values.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley
 */
abstract class FieldPredefined extends Field
{
    public abstract static function defineValues();
    
    protected function addClassValidators()
    {
	$legalValues          = array_keys(static::defineValues());
	$legalValuesValidator = new \ValidatorValueEnumerated($legalValues);
	$this->validator->addValidator($legalValuesValidator);
    }
    
    public static function translate($value)
    {
	$legalValues = static::defineValues();
        	
	if (array_key_exists((string)$value, $legalValues)) {
	    $translatedValue = $legalValues[(string)$value];
	    if (is_string($translatedValue) === FALSE) {
		throw new \LogicException("Bad data type $translatedValue in X937Field Value table. All values should be strings.");
	    }
	} else {
	    $translatedValue = 'Undefined';
	}
	
	return $translatedValue;
    }
    
    /**
     * Returns a value plus it's translation if we know it.
     * @param string $value
     * @return string Formated value + translation.
     */
    protected static function formatValue($value) {
	return $value . ' ' . static::translate($value);
    }
    
    /**
     * Get the signifigant value. This includes preceding 0's for this type.
     * @return string
     */
    public function getValueSignifigant() {
	return $this->value;
    }
}

require_once 'FieldCashLetterType.php';
require_once 'FieldCollectionType.php';
require_once 'FieldDocType.php';
require_once 'FieldFedWorkType.php';
require_once 'RecordType.php';
require_once 'FieldResend.php';
require_once 'FieldReturnReason.php';
require_once 'FieldSpecificationLevel.php';
require_once 'FieldTestFile.php';
require_once 'FieldVariableSize.php';
require_once 'ViewSide.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ImageInfo' . DIRECTORY_SEPARATOR . 'FieldImageInfo.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ImageView' . DIRECTORY_SEPARATOR . 'ImageView.php';