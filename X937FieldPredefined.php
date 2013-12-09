<?php

require_once 'X937Field.php';
require_once 'Validator.php';

abstract class X937FieldPredefined extends X937Field {
    public abstract static function defineValues();
    
    protected function addClassValidators() {
	$legalValues          = array_keys(static::defineValues());
	$legalValuesValidator = new ValidatorValueEnumerated($legalValues);
	$this->validator->addValidator($legalValuesValidator);
    }
    
    public static function translate($value) {
	$legalValues = static::defineValues();
	
	if (array_key_exists($value, $legalValues)) {
	    $translatedValue = $legalValues[$value];
	    if (is_string($translatedValue) === FALSE) {
		throw new LogicException("Bad data type $translatedValue in X937Field Value table. All values should be strings.");
	    }
	} else {
	    $translatedValue = 'Undefined';
	}
	
	return $translatedValue;
    }
}

class X937FieldRecordType extends X937FieldPredefined {
    const FILE_HEADER             = '01';
    const CASH_LETTER_HEADER      = '10';
    const BUNDLE_HEADER           = '20';
    const CHECK_DETAIL            = '25';
    const CHECK_DETAIL_ADDENDUM_A = '26';
    const CHECK_DETAIL_ADDENDUM_B = '27';
    const CHECK_DETAIL_ADDENDUM_C = '28';
    const RETURN_RECORD           = '31';
    const RETURN_ADDENDUM_A       = '32';
    const RETURN_ADDENDUM_B       = '33';
    const RETURN_ADDENDUM_C       = '34';
    const RETURN_ADDENDUM_D       = '35';
    const ACCOUNT_TOTALS_DETAIL   = '40';
    const NON_HIT_TOTALS_DETAIL   = '41';
    const IMAGE_VIEW_DETAIL       = '50';
    const IMAGE_VIEW_DATA         = '52';
    const IMAGE_VIEW_ANALYSIS     = '54';
    const BUNDLE_CONTROL          = '70';
    const BOX_SUMMARY             = '75';
    const ROUTING_NUMBER_SUMMARY  = '85';
    const CASH_LETTER_CONTROL     = '90';
    const FILE_CONTROL            = '99';
    
    public function __construct($value) {
	parent::__construct(1, 'Record Type', X937Field::USAGE_MANDATORY, 1, 2, X937Field::TYPE_NUMERIC);
	
	$this->value = $value;
    }
    
    public static function defineValues() {
	$X937FieldRecordTypes = array(
	    self::FILE_HEADER             => 'File Header Record',
	    self::CASH_LETTER_HEADER      => 'Cash Letter Header Record',
	    self::BUNDLE_HEADER           => 'Bundle Header Record',
	    self::CHECK_DETAIL            => 'Check Detail Record',
            self::CHECK_DETAIL_ADDENDUM_A => 'Check Detail Addendum A Record',
	    self::CHECK_DETAIL_ADDENDUM_B => 'Check Detail Addendum B Record',
	    self::CHECK_DETAIL_ADDENDUM_C => 'Check Detail Addendum C Record',
	    self::RETURN_RECORD           => 'Return Record',
	    self::RETURN_ADDENDUM_A       => 'Retrun Addendum A Record',
	    self::RETURN_ADDENDUM_B       => 'Return Addendum B Record',
	    self::RETURN_ADDENDUM_C       => 'Return Addendum C Record',
	    self::RETURN_ADDENDUM_D       => 'Return Addendum D Record',
	    self::ACCOUNT_TOTALS_DETAIL   => 'Account Totals Detail Record',
	    self::NON_HIT_TOTALS_DETAIL   => 'Non-Hit Total Detail Record',
	    self::IMAGE_VIEW_DETAIL       => 'Image View Detail Record',
	    self::IMAGE_VIEW_DATA         => 'Image View Data Record',
	    self::IMAGE_VIEW_ANALYSIS     => 'Image View Analysis',
	    self::BUNDLE_CONTROL          => 'Bundle Control Record',
	    self::BOX_SUMMARY             => 'Box Summary Record',
	    self::ROUTING_NUMBER_SUMMARY  => 'Routing Number Summary Record',
	    self::CASH_LETTER_CONTROL     => 'Cash Letter Control Record',
	    self::FILE_CONTROL            => 'File Control Record',
	);
	
	return $X937FieldRecordTypes;
    }

}

class X937FieldSpecificationLevel extends X937FieldPredefined {
    const X9371994 = '01';
    const X9372001 = '02';
    const X9372003 = '03';
    const X9100180 = '20';
    
    public function __construct() {
	parent::__construct(2, 'Specification Level', X937Field::USAGE_MANDATORY, 3, 2, X937Field::TYPE_NUMERIC);
    }
    
    public static function defineValues() {
	$X937FieldSpecificationLevels = array(
	    self::X9371994 => 'X9.37-1994',
	    self::X9372001 => 'X9.37-2001',
	    self::X9372003 => 'X9.37-2003',
	    self::X9100180 => 'X9.100-180',
	);
	
	return $X937FieldSpecificationLevels;
    }
}

class X937FieldTestFile extends X937FieldPredefined {
    const PRODUCTION_FILE = 'P';
    const TEST_FILE       = 'T';
    
    public function __construct() {
	parent::__construct(3, 'Test File Indicator', X937Field::USAGE_MANDATORY, 5, 1, X937Field::TYPE_ALPHABETIC);
    }
    
    public static function defineValues() {
	$X937FieldTestFileIndicators = array(
	    self::PRODUCTION_FILE => 'Production File',
	    self::TEST_FILE       => 'Test File',
	);
	
	return $X937FieldTestFileIndicators;
    }
}

class X937FieldResend extends X937FieldPredefined {
    const RESEND_FILE   = 'Y';
    const ORIGINAL_FILE = 'N';
    
    public function __construct() {
	parent::__construct(8, 'Resend Indicator', X937Field::USAGE_MANDATORY, 36, 1, X937Field::TYPE_ALPHABETIC);
    }
    
    public static function defineValues() {
	$X937FieldResendIndicators = array(
	    self::RESEND_FILE   => 'Production File',
	    self::ORIGINAL_FILE => 'Test File',
	);
	
	return $X937FieldResendIndicators;
    }
}

class X937FieldCollectionType extends X937FieldPredefined {
    const PRELIMINARY_FORWARD_INFORMATION         = 00;
    const FORWARD_PRESENTMENT                     = 01;
    const FORWARD_PRESENTMENT_SAME_DAY_SETTLEMENT = 02;
    const RETURN_CHECKS                           = 03;
    const RETURN_NOTIFICATION                     = 04;
    const PRELIMINARY_RETURN_NOTIFICATION         = 05;
    const FINAL_RETURN_NOTIFICATION               = 06;
    const ACCOUNT_TOTALS                          = 10;
    const NO_DETAIL                               = 20;
    const BUNDLES_NOT_SAME                        = 99;
    
    /**
     * The record type of this field, one of X937Field::CASH_LETTER_HEADER, or
     * X937Field::BUNDLE_HEADER
     * @var string 
     */
    private $recordType;
    
    public function __construct($recordType) {
	$this->recordType = $recordType;
	
	if (in_array($recordType, array(X937FieldRecordType::CASH_LETTER_HEADER, X937FieldRecordType::BUNDLE_HEADER)) === FALSE) {
	    throw new InvalidArgumentException('Bad record type');
	}
	
	parent::__construct(2, 'Collection Type Indicator', X937Field::USAGE_MANDATORY, 3, 2, X937Field::TYPE_NUMERIC);
    }

    public static function defineValues() {
	$X937FieldCollectionTypeIndicators = array(
	    self::PRELIMINARY_FORWARD_INFORMATION         => 'Preliminary Forward Information',
	    self::FORWARD_PRESENTMENT                     => 'Forward Presentment',
	    self::FORWARD_PRESENTMENT_SAME_DAY_SETTLEMENT => 'Forward Presentment - Same-Day Settlement',
	    self::RETURN_CHECKS                           => 'Return',
	    self::RETURN_NOTIFICATION                     => 'Return Notification',
	    self::PRELIMINARY_RETURN_NOTIFICATION         => 'Preliminary Return Notification',
	    self::FINAL_RETURN_NOTIFICATION               => 'Final Return Notification',
	    self::ACCOUNT_TOTALS                          => 'Account Totals',
	    self::NO_DETAIL                               => 'No Detail',
	    self::BUNDLES_NOT_SAME                        => 'Bundles not the same collection type',
	);
	
	return $X937FieldCollectionTypeIndicators;
    }
    
    /**
     * This is overridden because the valid values differs depending upon the record type.
     */
    protected function addClassValidators() {
	$legalValues = array_keys(self::defineValues());
	
	switch ($this->recordType) {
	    // Check Letter Header can use the default.
	    case X937FieldRecordType::CASH_LETTER_HEADER:
		break;
	    
	    // Bundle Header Records do not permit option 10, 20, and 99.
	    case X937FieldRecordType::BUNDLE_HEADER:
		unset($legalValues[self::ACCOUNT_TOTALS]);
		unset($legalValues[self::NO_DETAIL]);
		unset($legalValues[self::BUNDLES_NOT_SAME]);
		break;
		
	    // we would normaly error check here, but that should be handled in the constructor.
	}
	
	$legalValuesValidator = new ValidatorValueEnumerated($legalValues);
	$this->validator->addValidator($legalValuesValidator);
    }
}

class X937FieldCashLetterType extends X937FieldPredefined {
    const NO_ELECTRONIC_OR_IMAGE_RECORDS                       = 'N';
    const ELECTRONIC_RECORDS_NO_IMAGES                         = 'E';
    const ELECTRONIC_AND_IMAGE_RECORDS                         = 'I';
    const ELECTRONIC_AND_IMAGE_RECORDS_PREVIOUS_CORRESPONDANCE = 'F';
    
    public function __construct() {
	parent::__construct(8, 'Cash Letter Record Type Indicator', X937Field::USAGE_MANDATORY, 43, 1, X937Field::TYPE_ALPHABETIC);
    }

    public static function defineValues() {
	$definedValues = array(
	    self::NO_ELECTRONIC_OR_IMAGE_RECORDS                       => 'No electronic check records or image records',
	    self::ELECTRONIC_RECORDS_NO_IMAGES                         => 'Electronic check records with no images',
	    self::ELECTRONIC_AND_IMAGE_RECORDS                         => 'Electronic check records and image records',
	    self::ELECTRONIC_AND_IMAGE_RECORDS_PREVIOUS_CORRESPONDANCE => 'Electronic check records and image records that corespond with previous cash letter',
	);
	
	return $definedValues;
    }
}

class X937FieldDocType extends X937FieldPredefined {
    const NO_IMAGE_PAPER_SEPERATE                        = 'A';
    const NO_IMAGE_PAPER_SEPERATE_IMAGE_ON_REQUEST       = 'B';
    const IMAGE_SEPERATE_NO_PAPER                        = 'C';
    const IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       = 'D';
    const IMAGE_AND_PAPER_SEPERATE                       = 'E';
    const IMAGE_AND_PAPER_SEPERATE_IMAGE_ON_REQUEST      = 'F';
    const IMAGE_INCLUDED_NO_PAPER                        = 'G';
    const IMAGE_INCLUDED_NO_PAPER_IMAGE_ON_REQUEST       = 'H';
    const IMAGE_INCLUDED_PAPER_SEPERATE                  = 'I';
    const IMAGE_INCLUDED_PAPER_SEPERATE_IMAGE_ON_REQUEST = 'J';
    const NO_IMAGE_NO_PAPER                              = 'K';
    const NO_IMAGE_NO_PAPER_IMAGE_ON_REQUEST             = 'L';
    const NO_IMAGE_NO_PAPER_ELECTRONIC_CHECK_SEPERATE    = 'M';
    const NOT_SAME_TYPE                                  = 'Z';
    
    /**
     * The record type of this field, one of X937Field::CASH_LETTER_HEADER,
     * X937Field::CHECK_DETAIL, or X937Field::RETURN_RECORD
     * @var string 
     */
    private $recordType;
    
    /**
     * Creates new X937FieldDocType
     * @param  string $recordType The record type of this field: X937Field::CASH_LETTER_HEADER,
     * X937Field::CHECK_DETAIL, or X937Field::RETURN_RECORD
     * @throws InvalidArgumentException if Given a bad record type
     */
    public function __construct($recordType) {
	$this->recordType = $recordType;
	
	switch ($recordType) {
	    case X937FieldRecordType::CASH_LETTER_HEADER:
		$fieldNumber = 9;
		$fieldName   = 'Cash Letter Documentation Type Indicator';
		$position    = 44;
		$usage       = X937Field::USAGE_MANDATORY;
		break;
	    case X937FieldRecordType::CHECK_DETAIL:
		$fieldNumber = 9;
		$fieldName   = 'Documentation Type Indicator';
		$position    = 73;
		$usage       = X937Field::USAGE_CONDITIONAL;
		break;
	    case X937FieldRecordType::RETURN_RECORD:
		$fieldNumber = 8;
		$fieldName   = 'Return Documentation Type Indicator';
		$position    = 45;
		$usage       = X937Field::USAGE_CONDITIONAL;
		break;
	    default:
		throw new InvalidArgumentException('Bad record type.');
		break;
	}
	
	parent::__construct($fieldNumber, $fieldName, $usage, $position, 1, X937Field::TYPE_ALPHAMERIC);
    }

    public static function defineValues() {
	$legalValues = array(
	    self::NO_IMAGE_PAPER_SEPERATE                        => 'No image provided, paper provided separately',
	    self::NO_IMAGE_PAPER_SEPERATE_IMAGE_ON_REQUEST       => 'No image provided, paper provided separetly, image upon request',
	    self::IMAGE_SEPERATE_NO_PAPER                        => 'Image provided separetly, no paper provided',
	    self::IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       => 'Image provided separetly, no paper provided, image upon request',
	    self::IMAGE_AND_PAPER_SEPERATE                       => 'Image and paper provided separetly',
	    self::IMAGE_AND_PAPER_SEPERATE_IMAGE_ON_REQUEST      => 'Image and paper provided separetly, image upon request',
	    self::IMAGE_INCLUDED_NO_PAPER                        => 'Image included, no paper provided',
	    self::IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       => 'Image included, no paper provided, image upon request',
	    self::IMAGE_INCLUDED_PAPER_SEPERATE                  => 'Image included, paper provided separetly',
	    self::IMAGE_INCLUDED_PAPER_SEPERATE_IMAGE_ON_REQUEST => 'Image included, paper provided separetly',
	    self::NO_IMAGE_NO_PAPER                              => 'No image provided, no paper provided',
	    self::NO_IMAGE_NO_PAPER_IMAGE_ON_REQUEST             => 'No image provided, no paper provided, image upon request',
	    self::NO_IMAGE_NO_PAPER_ELECTRONIC_CHECK_SEPERATE    => 'No image provided, no paper provided, Electronic Check provided seperately',
	    self::NOT_SAME_TYPE                                  => 'Not Same Type',
	);
	
	return $legalValues;
    }
    
    /**
     * This is overridden because the valid values differs depending upon the record type.
     */
    protected function addClassValidators() {
	$legalValues = array_keys(self::defineValues());
	
	switch ($this->recordType) {
	    // Check Detail Records can use the default.
	    case X937FieldRecordType::CASH_LETTER_HEADER:
		break;
	    
	    // Check Detail Records and Return Records do not permit option Z.
	    case X937FieldRecordType::CHECK_DETAIL:
		unset($legalValues[self::NOT_SAME_TYPE]);
		break;
	    case X937FieldRecordType::RETURN_RECORD:
		unset($legalValues[self::NOT_SAME_TYPE]);
		break;
		
	    // we would normaly error check here, but that should be handled in the constructor.
	}
	
	$legalValuesValidator = new ValidatorValueEnumerated($legalValues);
	$this->validator->addValidator($legalValuesValidator);
    }
}

class X937FieldFedWorkType extends X937FieldPredefined {
    const CITY                      = '1';
    const CITY_GROUP                = '2';
    const CITY_FINE_SORT            = '3';
    const RCPC                      = '4';
    const RCPC_GROUP                = '5';
    const RCPC_FINE_SORT            = '6';
    const HIGH_DOLLAR_GROUP_SORT    = '7';
    const COUNTRY                   = '8';
    const COUNTRY_GROUP_SORT        = '9';
    const COUNTRY_FINE_SORT         = '0';
    const OTHER_DISTRICT            = 'A';
    const OTHER_DISTRICT_GROUP_SORT = 'B';
    const MIXED                     = 'C';
    const CITY_RCPC_MIXED           = 'D';
    const PAYOR_GROUP_SORT          = 'E';
    
    public function __construct() {
	parent::__construct(13, 'Fed Work Type', X937Field::USAGE_CONDITIONAL, 77, 1, X937Field::TYPE_ALPHAMERIC);
    }

    public static function defineValues() {
	$definedValues = array(
	    self::CITY                      => 'City',
	    self::CITY_GROUP                => 'City Group',
	    self::CITY_FINE_SORT            => 'City Fine Sort',
	    self::RCPC                      => 'RCPC',
	    self::RCPC_GROUP                => 'RCPC Group',
	    self::RCPC_FINE_SORT            => 'RCPC Fine Sort',
	    self::HIGH_DOLLAR_GROUP_SORT    => 'High Dollar Group Sort',
	    self::COUNTRY                   => 'Country',
	    self::COUNTRY_GROUP_SORT        => 'Country Group Sort',
	    self::COUNTRY_FINE_SORT         => 'Country Group Sort',
	    self::OTHER_DISTRICT            => 'Other District',
	    self::OTHER_DISTRICT_GROUP_SORT => 'Other District Group Sort',
	    self::MIXED                     => 'Mixed',
	    self::CITY_RCPC_MIXED           => 'City/RCPC Mixed',
	    self::PAYOR_GROUP_SORT          => 'Payor Group Sort',
	);
	
	return $definedValues;
    }
}

class X937FieldVariableSizeIndicator extends X937FieldPredefined {
    const FIXED    = '0';
    const VARIABLE = '1';
    
    public function __construct() {
	parent::__construct(2, 'Variable Size Record Indicator', X937Field::USAGE_MANDATORY, 3, 1, X937Field::TYPE_NUMERIC);
    }

    public static function defineValues() {
	$definedValues = array(
	    self::FIXED    => 'Fixed Size',
	    self::VARIABLE => 'Variable Size',
	);
	
	return $definedValues;
    }
}

abstract class X937FieldImageInfo extends X937FieldPredefined {
    const TEST_NOT_DONE = 0;
    
    public function __construct($fieldNumber, $fieldName, $position) {
	parent::__construct($fieldNumber, $fieldName, X937Field::USAGE_CONDITIONAL, $position, 1);
    }
    
    public static function defineValues() {
	$definedValues = array(
	    self::TEST_NOT_DONE         => 'Test Not Done',
	);
	
	return $definedValues;
    }
}

class X937FieldImageInfoQuality extends X937FieldImageInfo {
    const CONDITION_PRESENT     = 1;
    const CONDITION_NOT_PRESENT = 2;
    
    public static function defineValues() {
	$definedValues = array(
	    self::TEST_NOT_DONE         => 'Test Not Done',
	    self::CONDITION_PRESENT     => 'Condition Present',
	    self::CONDITION_NOT_PRESENT => 'Condition Not Present',
	);
	
	return $definedValues;
    }
}

class X937FieldImageInfoUsability extends X937FieldPredefined {
    const TEST_NOT_DONE  = 0;
    const UNUSEABLE      = 1;
    const USABLE         = 2;
    
    public static function defineValues() {
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