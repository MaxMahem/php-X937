<?php

require_once 'X937Field.php';
require_once 'Validator.php';

abstract class X937FieldPredefined extends X937Field {
    public abstract static function defineValues();
    
    protected function addClassValidators() {
	$legalValues          = array_keys(self::defineValues());
	$legalValuesValidator = new FieldValidatorValueEnumerated($legalValues);
	$this->validator->addValidator($legalValuesValidator);
    }
    
    public function translatedValue() {
	return static::translate($this->value);
    }
    
    public static function translate($value) {
	$legalValues = static::defineValues();
	
	if (array_key_exists($value, $legalValues)) {
	    $translatedValue = $legalValues[$legalValues];
	    if (gettype($translatedValue) !== 'string') {
		throw new LogicException('Bad data type in X937Field Value table. All values should be strings.');
	    }
	} else {
	    $translatedValue = 'Undefined';
	}
	
	return $translatedValue;
    }
}

class X937FieldRecordType extends X937Field {
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
	parent::__construct(1, 'Record Type', X937Field::MANDATORY, 1, 2, X937Field::NUMERIC);
	
	$this->value = $value;
    }
    
    public function defineValues() {
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
    const X9371994 = 01;
    const X9372001 = 02;
    const X9372003 = 03;
    
    public function __construct() {
	parent::__construct(1, 'Specification Level', X937Field::MANDATORY,    3,  2, X937Field::NUMERIC);
    }
    
    public static function defineValues() {
	$X937FieldSpecificationLevels = array(
	    self::X9371994 => 'X9.37-1994',
	    self::X9372001 => 'X9.37-2001',
	    self::X9372003 => 'X9.37-2003'
	);
	
	return $X937FieldSpecificationLevels;
    }
}

class X937FieldTestFile extends X937FieldPredefined {
    const PRODUCTION_FILE = 'P';
    const TEST_FILE       = 'T';
    
    public function __construct() {
	parent::__construct(3, 'Test File Indicator', X937Field::MANDATORY, 5, 1, X937Field::ALPHABETIC);
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
	parent::__construct(8, 'Resend Indicator', X937Field::MANDATORY, 36, 1, X937Field::ALPHABETIC);
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
    
    public function __construct() {
	parent::__construct(2, 'Collection Type Indicator', X937Field::MANDATORY, 3, 2, X937Field::NUMERIC);
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
}

class X937FieldCashLetterRecordType extends X937FieldPredefined {
    const NO_ELECTRONIC_OR_IMAGE_RECORDS                       = 'N';
    const ELECTRONIC_RECORDS_NO_IMAGES                         = 'E';
    const ELECTRONIC_AND_IMAGE_RECORDS                         = 'I';
    const ELECTRONIC_AND_IMAGE_RECORDS_PREVIOUS_CORRESPONDANCE = 'F';
    
    public function __construct() {
	parent::__construct(8, 'Cash Letter Record Type Indicator', X937Field::MANDATORY, 43, 1, X937Field::ALPHABETIC);
    }

    public static function defineValues() {
	$X937FieldCashLetterRecordTypeIndicators = array(
	    self::NO_ELECTRONIC_OR_IMAGE_RECORDS                       => 'No electronic check records or image records',
	    self::ELECTRONIC_RECORDS_NO_IMAGES                         => 'Electronic check records with no images',
	    self::ELECTRONIC_AND_IMAGE_RECORDS                         => 'Electronic check records and image records',
	    self::ELECTRONIC_AND_IMAGE_RECORDS_PREVIOUS_CORRESPONDANCE => 'Electronic check records and iamge records that corespond with previous cash letter',
	);
	
	return $X937FieldCashLetterRecordTypeIndicators;
    }
}

class X937FieldCashLetterDocumentationType extends X937FieldPredefined {
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
    
    public function __construct() {
	parent::__construct(8, 'Cash Letter Record Type Indicator', X937Field::MANDATORY, 43, 1, X937Field::ALPHABETIC);
    }

    public static function defineValues() {
	$X937FieldCashLetterDocumentationTypeIndicators = array(
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
	
	return $X937FieldCashLetterDocumentationTypeIndicators;
    }
}