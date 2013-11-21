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

class X937FieldSpecificationLevel extends X937FieldPredefined {
    const X9371994 = 01;
    const X9372001 = 02;
    const X9372003 = 03;
    
    public function __construct() {
	parent::__construct(1, 'Specification Level', X937Field::MANDATORY,    3,  2, X937Field::NUMERIC);
    }
    
    public function getSpecificatonLevelName() {
	$X937FieldSpecificationLevels = self::defineValues();
	return $X937FieldSpecificationLevels[$this->value];
    }
    
    public static function defineValues() {
	$X937FieldSpecificationLevels = array(
	    X937FieldSpecificationLevel::X9371994 => 'X9.37-1994',
	    X937FieldSpecificationLevel::X9372001 => 'X9.37-2001',
	    X937FieldSpecificationLevel::X9372003 => 'X9.37-2003'
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
	    X937FieldTestFile::PRODUCTION_FILE => 'Production File',
	    X937FieldTestFile::TEST_FILE       => 'Test File',
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
	    X937FieldResend::RESEND_FILE   => 'Production File',
	    X937FieldResend::ORIGINAL_FILE => 'Test File',
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
	    X937FieldCollectionType::PRELIMINARY_FORWARD_INFORMATION         => 'Preliminary Forward Information',
	    X937FieldCollectionType::FORWARD_PRESENTMENT                     => 'Forward Presentment',
	    X937FieldCollectionType::FORWARD_PRESENTMENT_SAME_DAY_SETTLEMENT => 'Forward Presentment - Same-Day Settlement',
	    X937FieldCollectionType::RETURN_CHECKS                           => 'Return',
	    X937FieldCollectionType::RETURN_NOTIFICATION                     => 'Return Notification',
	    X937FieldCollectionType::PRELIMINARY_RETURN_NOTIFICATION         => 'Preliminary Return Notification',
	    X937FieldCollectionType::FINAL_RETURN_NOTIFICATION               => 'Final Return Notification',
	    X937FieldCollectionType::ACCOUNT_TOTALS                          => 'Account Totals',
	    X937FieldCollectionType::NO_DETAIL                               => 'No Detail',
	    X937FieldCollectionType::BUNDLES_NOT_SAME                        => 'Bundles not the same collection type',
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
	$X937FieldCashLetterRecordTypeIndicator = array(
	    X937FieldCashLetterRecordType::NO_ELECTRONIC_OR_IMAGE_RECORDS                       => 'No electronic check records or image records',
	    X937FieldCashLetterRecordType::ELECTRONIC_RECORDS_NO_IMAGES                         => 'Electronic check records with no images',
	    X937FieldCashLetterRecordType::ELECTRONIC_AND_IMAGE_RECORDS                         => 'Electronic check records and image records',
	    X937FieldCashLetterRecordType::ELECTRONIC_AND_IMAGE_RECORDS_PREVIOUS_CORRESPONDANCE => 'Electronic check records and iamge records that corespond with previous cash letter',
	);
	
	return $X937FieldCashLetterRecordTypeIndicator;
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
	    X937FieldCashLetterDocumentationType::NO_IMAGE_PAPER_SEPERATE                        => 'No image provided, paper provided separately',
	    X937FieldCashLetterDocumentationType::NO_IMAGE_PAPER_SEPERATE_IMAGE_ON_REQUEST       => 'No image provided, paper provided separetly, image upon request',
	    X937FieldCashLetterDocumentationType::IMAGE_SEPERATE_NO_PAPER                        => 'Image provided separetly, no paper provided',
	    X937FieldCashLetterDocumentationType::IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       => 'Image provided separetly, no paper provided, image upon request',
	    X937FieldCashLetterDocumentationType::IMAGE_AND_PAPER_SEPERATE                       => 'Image and paper provided separetly',
	    X937FieldCashLetterDocumentationType::IMAGE_AND_PAPER_SEPERATE_IMAGE_ON_REQUEST      => 'Image and paper provided separetly, image upon request',
	    X937FieldCashLetterDocumentationType::IMAGE_INCLUDED_NO_PAPER                        => 'Image included, no paper provided',
	    X937FieldCashLetterDocumentationType::IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       => 'Image included, no paper provided, image upon request',
	    X937FieldCashLetterDocumentationType::IMAGE_INCLUDED_PAPER_SEPERATE                  => 'Image included, paper provided separetly',
	    X937FieldCashLetterDocumentationType::IMAGE_INCLUDED_PAPER_SEPERATE_IMAGE_ON_REQUEST => 'Image included, paper provided separetly',
	    X937FieldCashLetterDocumentationType::NO_IMAGE_NO_PAPER                              => 'No image provided, no paper provided',
	    X937FieldCashLetterDocumentationType::NO_IMAGE_NO_PAPER_IMAGE_ON_REQUEST             => 'No image provided, no paper provided, image upon request',
	    X937FieldCashLetterDocumentationType::NO_IMAGE_NO_PAPER_ELECTRONIC_CHECK_SEPERATE    => 'No image provided, no paper provided, Electronic Check provided seperately',
	    X937FieldCashLetterDocumentationType::NOT_SAME_TYPE                                  => 'Not Same Type',
	);
	
	return $X937FieldCashLetterDocumentationTypeIndicators;
    }
}