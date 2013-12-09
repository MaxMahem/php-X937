<?php

require_once 'X937Field.php';
require_once 'X937FieldPredefined.php';

/**
 * This class is just a stub for record types we haven't implemented yet.
 */
class X937RecordGeneric extends X937Record { }

// File Header Record - Type 01
class X937RecordFileHeader  extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(14);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::FILE_HEADER));
	$this->addField(new X937FieldSpecificationLevel());
	$this->addField(new X937FieldTestFile());
	$this->addField(new X937FieldRoutingNumber(4, 'Immediate Destination',    X937Field::USAGE_MANDATORY,    6));
	$this->addField(new X937FieldRoutingNumber(5, 'Immediate Origin',         X937Field::USAGE_MANDATORY,   15));
	$this->addField(new X937FieldDate(6, 'File Creation Date',                X937Field::USAGE_MANDATORY,   24));
	$this->addField(new X937FieldTime(7, 'File Creation Time',                X937Field::USAGE_MANDATORY,   32));
	$this->addField(new X937FieldResend());
	$this->addField(new X937FieldInstitutionName( 9, 'Immediate Destination', X937Field::USAGE_CONDITIONAL, 37));
	$this->addField(new X937FieldInstitutionName(10, 'Immediate Origin',      X937Field::USAGE_CONDITIONAL, 55));
	$this->addField(new X937FieldGeneric(11, 'File ID Modifer',               X937Field::USAGE_CONDITIONAL, 73,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(12, 'Country Code',                  X937Field::USAGE_CONDITIONAL, 74,  2, X937Field::TYPE_ALPHABETIC));
	$this->addField(new X937FieldUser(13, 76,  4));
	$this->addField(new X937FieldReserved(14, 80,  1));
    }
}

// Cash Letter Header Record - Type 10
class X937RecordCashLetterHeader  extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(15);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CASH_LETTER_HEADER));
	$this->addField(new X937FieldCollectionType(X937FieldRecordType::CASH_LETTER_HEADER));
	$this->addField(new X937FieldRoutingNumber(3, 'Destination',            X937Field::USAGE_MANDATORY,    5));
	$this->addField(new X937FieldRoutingNumber(4, 'ECE Instituion',         X937Field::USAGE_MANDATORY,   14));
	$this->addField(new X937FieldDate(5, 'Cash Letter Business Date',       X937Field::USAGE_MANDATORY,   23));
	$this->addField(new X937FieldDate(6, 'Cash Letter Creation Date',       X937Field::USAGE_MANDATORY,   31));
	$this->addField(new X937FieldTime(7, 'Cash Letter Creation Time',       X937Field::USAGE_MANDATORY,   39));
	$this->addField(new X937FieldCashLetterType());
	$this->addField(new X937FieldDocType(X937FieldRecordType::CASH_LETTER_HEADER));
	$this->addField(new X937FieldGeneric(10, 'Cash Letter ID',              X937Field::USAGE_CONDITIONAL, 45,  8, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldContactName(11, 'Originator Contact Name', X937Field::USAGE_CONDITIONAL, 53));
	$this->addField(new X937FieldPhoneNumber(12, 'Originator Contact',      X937Field::USAGE_CONDITIONAL, 67));
	$this->addField(new X937FieldFedWorkType());
	$this->addField(new X937FieldUser(14, 78,  2));
	$this->addField(new X937FieldReserved(15, 80,  1));
    }
}

// Bundle Header Record - Type 20
class X937RecordBundleHeader extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(12);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::BUNDLE_HEADER));
	$this->addField(new X937FieldCollectionType(X937FieldRecordType::BUNDLE_HEADER));
	$this->addField(new X937FieldRoutingNumber( 3, 'Destination',      X937Field::USAGE_MANDATORY,    5));
	$this->addField(new X937FieldRoutingNumber( 4, 'ECE Institution',  X937Field::USAGE_MANDATORY,   14));
	$this->addField(new X937FieldDate(5, 'Bundle Business Date',       X937Field::USAGE_MANDATORY,   23));
	$this->addField(new X937FieldDate(6, 'Bundle Creation Date',       X937Field::USAGE_MANDATORY,   31));
	$this->addField(new X937FieldGeneric( 7, 'Bundle ID',              X937Field::USAGE_CONDITIONAL, 39, 10, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric( 8, 'Bundle Sequence Number', X937Field::USAGE_CONDITIONAL, 49,  4, X937Field::TYPE_NUMERICBLANK));
	$this->addField(new X937FieldGeneric( 9, 'Cycle Number',           X937Field::USAGE_CONDITIONAL, 53,  2, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldRoutingNumber(10, 'Return Location',  X937Field::USAGE_CONDITIONAL, 55));
	$this->addField(new X937FieldUser(11, 64,  5));
	$this->addField(new X937FieldReserved(12, 69, 12));
    }
}

// Check Detail Record - Type 25
class X937RecordCheckDetail extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(14);    
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL));
	$this->addField(new X937FieldGeneric( 2, 'Auxiliary On-Us',                       X937Field::USAGE_CONDITIONAL,  3, 15, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric( 3, 'External Processing Code',              X937Field::USAGE_CONDITIONAL, 18,  1, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric( 4, 'Payor Bank Routing Number',             X937Field::USAGE_MANDATORY,   19,  8, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric( 5, 'Payor Bank Routing Number Check Digit', X937Field::USAGE_CONDITIONAL, 27,  1, X937Field::TYPE_NUMERICBLANKSPECIALMICR));
	$this->addField(new X937FieldGeneric( 6, 'On-Us',                                 X937Field::USAGE_MANDATORY,   28, 20, X937Field::TYPE_NUMERICBLANKSPECIALMICRONUS));
	$this->addField(new X937FieldItemAmount(7, 48));
	$this->addField(new X937FieldItemSequenceNumber(8, 'ECE Institution',             X937Field::USAGE_MANDATORY, 58));
	$this->addField(new X937FieldDocType(X937FieldRecordType::CHECK_DETAIL));
	$this->addField(new X937FieldGeneric(10, 'Return Acceptance Indicator',           X937Field::USAGE_CONDITIONAL, 74,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(11, 'MICR Valid Indicator',                  X937Field::USAGE_CONDITIONAL, 75,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(12, 'BOFD Indicator',                        X937Field::USAGE_MANDATORY,   76,  1, X937Field::TYPE_ALPHABETIC));
	$this->addField(new X937FieldGeneric(13, 'Check Detail Record Addendum Count',    X937Field::USAGE_MANDATORY,   77,  2, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(14, 'Correction Indicator',                  X937Field::USAGE_CONDITIONAL, 79,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(14, 'Archive Type Indicator',                X937Field::USAGE_CONDITIONAL, 80,  1, X937Field::TYPE_ALPHAMERIC));
    }
}

// Check Detail Record - Type 26
class X937RecordCheckDetailAddendumA extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(13);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_A));
	$this->addField(new X937FieldGeneric( 2, 'Check Detail Addendum A Record Number', X937Field::USAGE_MANDATORY,    3,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldRoutingNumber(3, 'BOFD',                             X937Field::USAGE_CONDITIONAL,  4));
	$this->addField(new X937FieldDate(4, 'BOFD Endorsement Date',                     X937Field::USAGE_MANDATORY, 13));
	$this->addField(new X937FieldItemSequenceNumber( 5, 'BOFD',                       X937Field::USAGE_CONDITIONAL, 21));
	$this->addField(new X937FieldDepositAccountNumber(6,                              X937Field::USAGE_CONDITIONAL, 36));
	$this->addField(new X937FieldGeneric( 7, 'BOFD Deposit Branch',                   X937Field::USAGE_CONDITIONAL, 54,  5, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric( 8, 'Payee Name',                            X937Field::USAGE_CONDITIONAL, 59, 15, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric( 9, 'Truncation Indicator',                  X937Field::USAGE_CONDITIONAL, 74,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(10, 'BOFD Conversion Indicator',             X937Field::USAGE_CONDITIONAL, 75,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(11, 'BOFD Correction Indicator',             X937Field::USAGE_CONDITIONAL, 76,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldUser(12, 77,  1));
	$this->addField(new X937FieldReserved(13, 78, 3));
    }
}

// Check Detail Record - Type 28
class X937RecordCheckDetailAddendumC extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(11);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_C));
	$this->addField(new X937FieldGeneric(2, 'Check Detail Addendum C Record Number', X937Field::USAGE_MANDATORY,    3,  2, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldRoutingNumber(3, 'Endorsing Bank',                  X937Field::USAGE_CONDITIONAL,  5));
	$this->addField(new X937FieldDate(4, 'Endorsing Bank Endorsement Date',          X937Field::USAGE_CONDITIONAL, 14));
	$this->addField(new X937FieldItemSequenceNumber(5, 'Endorsing Bank',             X937Field::USAGE_CONDITIONAL, 22));
	$this->addField(new X937FieldGeneric(6, 'Truncation Indicator',                  X937Field::USAGE_CONDITIONAL, 37,  1, X937Field::TYPE_ALPHABETIC));
	$this->addField(new X937FieldGeneric(7, 'Endorsing Bank Conversion Indicator',   X937Field::USAGE_CONDITIONAL, 38,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(8, 'Endorsing Bank Correction Indicator',   X937Field::USAGE_CONDITIONAL, 39,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldReturnReason(9, X937Field::USAGE_CONDITIONAL, 40));
	$this->addField(new X937FieldUser(10, 41,  15));
	$this->addField(new X937FieldReserved(11, 56, 15));
    }
}

// Return Record - Type 31
class X937RecordReturnRecord extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(14);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::RETURN_RECORD));
	$this->addField(new X937FieldGeneric( 2, 'Payor Bank Routing Number',             X937Field::USAGE_MANDATORY,    3,  8, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric( 3, 'Payor Bank Routing Number Check Digit', X937Field::USAGE_CONDITIONAL, 11,  1, X937Field::TYPE_NUMERICBLANKSPECIALMICR));
	$this->addField(new X937FieldGeneric( 4, 'On-Us Return Record',                   X937Field::USAGE_CONDITIONAL, 12, 20, X937Field::TYPE_NUMERICBLANKSPECIALMICRONUS));
	$this->addField(new X937FieldItemAmount(5, 32));
	$this->addField(new X937FieldReturnReason(6, X937Field::USAGE_MANDATORY, 42));
	$this->addField(new X937FieldGeneric( 7, 'Return Reason Addendum Count',          X937Field::USAGE_MANDATORY,   43,  2, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldDocType(X937FieldRecordType::RETURN_RECORD));
	$this->addField(new X937FieldDate(9, 'Forward Bundle Date',                       X937Field::USAGE_CONDITIONAL, 46));
	$this->addField(new X937FieldItemSequenceNumber(10, 'ECE Institution',            X937Field::USAGE_CONDITIONAL, 54));
	$this->addField(new X937FieldGeneric(11, 'External Processing Code',              X937Field::USAGE_CONDITIONAL, 69,  1, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric(12, 'Return Notification Indicator',         X937Field::USAGE_CONDITIONAL, 70,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(13, 'Return Archive Type Indicator',         X937Field::USAGE_CONDITIONAL, 71,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldReserved(14, 72, 9));
    }
}

// Return Addendum A - Type 32
class X937RecordReturnAddendumA extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(13);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_A));
	$this->addField(new X937FieldGeneric( 2, 'Return Addendum A Record Number', X937Field::USAGE_MANDATORY,    3,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldRoutingNumber(3, 'BOFD',                       X937Field::USAGE_CONDITIONAL,  4));
	$this->addField(new X937FieldDate(4, 'BOFD Endorsement Date',               X937Field::USAGE_CONDITIONAL, 13)); // This field has some additional data maybe?
	$this->addField(new X937FieldItemSequenceNumber(5, 'BOFD',                  X937Field::USAGE_CONDITIONAL, 21));
	$this->addField(new X937FieldDepositAccountNumber(6,                        X937Field::USAGE_CONDITIONAL, 36));
	$this->addField(new X937FieldGeneric( 7, 'BOFD Deposit Branch',             X937Field::USAGE_CONDITIONAL, 54,  5, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric( 8, 'Payee Name',                      X937Field::USAGE_CONDITIONAL, 59, 15, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric( 9, 'Truncation Indicator',            X937Field::USAGE_CONDITIONAL, 74,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(10, 'BOFD Conversion Indicator',       X937Field::USAGE_CONDITIONAL, 75,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(11, 'BOFD Correction Indicator',       X937Field::USAGE_CONDITIONAL, 76,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldUser(12, 77,  1));
	$this->addField(new X937FieldReserved(13, 78, 3));
    }
}

// Return Addendum B - Type 33
class X937RecordReturnAddendumB extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(6);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_B));
	$this->addField(new X937FieldInstitutionName(2, 'Payor Bank',    X937Field::USAGE_CONDITIONAL,  3));
	$this->addField(new X937FieldGeneric(3, 'Auxiliary On-Us',       X937Field::USAGE_CONDITIONAL, 21, 15, X937Field::TYPE_NUMERICBLANKSPECIALMICR));
	$this->addField(new X937FieldItemSequenceNumber(4, 'Payor Bank', X937Field::USAGE_CONDITIONAL, 36));
	$this->addField(new X937FieldDate(5, 'Payor Bank Business Date', X937Field::USAGE_CONDITIONAL, 51,  8, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(6, 'Payor Account Name',    X937Field::USAGE_CONDITIONAL, 59, 22, X937Field::TYPE_ALPHAMERICSPECIAL));
    }
}

// Return Addendum D - Type 35
class X937RecordReturnAddendumD extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(11);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_D));
	$this->addField(new X937FieldGeneric(2, 'Return Addendum D Record Number',     X937Field::USAGE_MANDATORY,    3,  2, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldRoutingNumber(3, 'Endorsing Bank',                X937Field::USAGE_CONDITIONAL,  5));
	$this->addField(new X937FieldDate(4, 'Endorsing Bank Endorsement Date',        X937Field::USAGE_CONDITIONAL, 14));
	$this->addField(new X937FieldItemSequenceNumber(5, 'Endorsing Bank',           X937Field::USAGE_CONDITIONAL, 22));
	$this->addField(new X937FieldGeneric(6, 'Truncation Indicator',                X937Field::USAGE_CONDITIONAL, 37,  1, X937Field::TYPE_ALPHABETIC));
	$this->addField(new X937FieldGeneric(7, 'Endorsing Bank Conversion Indicator', X937Field::USAGE_CONDITIONAL, 38,  1, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(8, 'Endorsing Bank Correction Indicator', X937Field::USAGE_CONDITIONAL, 39,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldReturnReason(9, X937Field::USAGE_CONDITIONAL, 40));
	$this->addField(new X937FieldUser(10, 41,  15));
	$this->addField(new X937FieldReserved(11, 56, 15));
    }
}

// Account Totals Detail - Type 40
class X937RecordAccountTotalsDetail extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::ACCOUNT_TOTALS_DETAIL));
	$this->addField(new X937FieldRoutingNumber(2, 'Destination',                   X937Field::USAGE_MANDATORY,  5));
	$this->addField(new X937FieldGeneric(3, 'Key Account / Low Account in Range',  X937Field::USAGE_MANDATORY, 12, 18, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(4, 'Key Account / High Account in Range', X937Field::USAGE_MANDATORY, 30, 18, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(5, 'Total Item Count',                    X937Field::USAGE_MANDATORY, 48, 12, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(6, 'Total Item Amount',                   X937Field::USAGE_MANDATORY, 60, 14, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldUser(7, 74, 4));
	$this->addField(new X937FieldReserved(8, 78, 3));
    }
}

// Non-Hit Totals Detail - Type 41
class X937RecordNonHitTotalsDetail extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(7);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::NON_HIT_TOTALS_DETAIL));
	$this->addField(new X937FieldRoutingNumber(2, 'Destination', X937Field::USAGE_MANDATORY,  5));
	$this->addField(new X937FieldGeneric(3, 'Non-Hit Indicator', X937Field::USAGE_MANDATORY, 12, 01, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(4, 'Total Item Count',  X937Field::USAGE_MANDATORY, 13, 12, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(5, 'Total Item Amount', X937Field::USAGE_MANDATORY, 25, 14, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldUser(6, 39, 12));
	$this->addField(new X937FieldReserved(7, 51, 30));
    }
}

// Image View Detail Record - Type 50
class X937RecordImageViewDetail extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(17);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::IMAGE_VIEW_DETAIL));
	$this->addField(new X937FieldGeneric(2, 'Image Indicator',                             X937Field::USAGE_MANDATORY,    3, 1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldRoutingNumber(3, 'Image Creator',                         X937Field::USAGE_MANDATORY,    4));
	$this->addField(new X937FieldDate(4, 'Image Creator Date',                             X937Field::USAGE_MANDATORY,   13));
	$this->addField(new X937FieldGeneric( 5, 'Image View Format Indicator',                X937Field::USAGE_MANDATORY,   21, 2, X937Field::TYPE_NUMERICBLANK));
	$this->addField(new X937FieldGeneric( 6, 'Image View Compression Algorithm Identifer', X937Field::USAGE_MANDATORY,   23, 2, X937Field::TYPE_NUMERICBLANK));
	$this->addField(new X937FieldGeneric( 7, 'Image View Data Size',                       X937Field::USAGE_CONDITIONAL, 25, 7, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric( 8, 'View Side Indicator',                        X937Field::USAGE_MANDATORY,   32, 1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric( 9, 'View Descriptor',                            X937Field::USAGE_MANDATORY,   33, 2, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(10, 'Digital Signature Indicator',                X937Field::USAGE_MANDATORY,   35, 1, X937Field::TYPE_NUMERICBLANK));
	$this->addField(new X937FieldGeneric(11, 'Digital Signature Method',                   X937Field::USAGE_MANDATORY,   36, 2, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(12, 'Security Key Size',                          X937Field::USAGE_CONDITIONAL, 38, 5, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(13, 'Start of Protected Data',                    X937Field::USAGE_CONDITIONAL, 43, 7, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(14, 'Length of Protected Data',                   X937Field::USAGE_CONDITIONAL, 50, 7, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(15, 'Image Recreate Indicator',                   X937Field::USAGE_CONDITIONAL, 57, 1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldUser(16, 58, 8));
	$this->addField(new X937FieldReserved(17, 66, 15));
    }
}

/**
 * Image View Analysis Record - Type 54
 */
class X937RecordImageViewAnalysis extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(46);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::IMAGE_VIEW_ANALYSIS));
	$this->addField(new X937FieldGeneric(2, 'Global Image Qualilty',      X937Field::USAGE_MANDATORY, 3, 1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(3, 'Global Image Usability',     X937Field::USAGE_MANDATORY, 4, 1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(4, 'Imaging Bank Specific Test', X937Field::USAGE_MANDATORY, 5, 1, X937Field::TYPE_NUMERIC));

	// Image Quality Information (Fields 5-24)
	$this->addField(new X937FieldImageQualityInfo( 5, 'Partial Image',               6));
	$this->addField(new X937FieldImageInfoQuality( 6, 'Excessive Image Skew',        7));
	$this->addField(new X937FieldImageInfoQuality( 7, 'Piggyback Image',             8));
	$this->addField(new X937FieldImageInfoQuality( 8, 'Too Light or Too Dark',       9));
	$this->addField(new X937FieldImageInfoQuality( 9, 'Streaks and/or Bands',       10));
	$this->addField(new X937FieldImageInfoQuality(10, 'Below Minimum Image Size',   11));
	$this->addField(new X937FieldImageInfoQuality(11, 'Exceeds Maximum Image Size', 12));
	$this->addField(new X937FieldReserved(12, 13, 1));
	$this->addField(new X937FieldReserved(13, 14, 1));
	$this->addField(new X937FieldReserved(14, 15, 1));
	$this->addField(new X937FieldReserved(15, 16, 1));
	$this->addField(new X937FieldReserved(16, 17, 1));
	$this->addField(new X937FieldReserved(17, 18, 1));
	$this->addField(new X937FieldReserved(18, 19, 1));
	$this->addField(new X937FieldReserved(19, 20, 1));
	$this->addField(new X937FieldReserved(20, 21, 1));
	$this->addField(new X937FieldReserved(21, 22, 1));
	$this->addField(new X937FieldReserved(22, 23, 1));
	$this->addField(new X937FieldReserved(23, 24, 1));
	$this->addField(new X937FieldReserved(24, 25, 1));

	// Image Usability Information (Fields 25-44)
	$this->addField(new X937FieldGeneric(25, 'Image-Enabled POD',   X937Field::USAGE_CONDITIONAL, 26, 1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(26, 'Source Document Bad', X937Field::USAGE_CONDITIONAL, 27, 1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldImageInfoUsability(27, 'Date Usability',                        28));
	$this->addField(new X937FieldImageInfoUsability(28, 'Payee Usability',                       29));
	$this->addField(new X937FieldImageInfoUsability(29, 'Convenience Amount Usability',          30));
	$this->addField(new X937FieldImageInfoUsability(30, 'Legal Amount Usability',                31));
	$this->addField(new X937FieldImageInfoUsability(31, 'Signature Usability',                   32));
	$this->addField(new X937FieldImageInfoUsability(32, 'Payor Name and Address Usability',      33));
	$this->addField(new X937FieldImageInfoUsability(33, 'MICR Line Usability',                   34));
	$this->addField(new X937FieldImageInfoUsability(34, 'Memo Line Usability',                   35));
	$this->addField(new X937FieldImageInfoUsability(35, 'Payor Bank Name and Address Usability', 36));
	$this->addField(new X937FieldImageInfoUsability(36, 'Payee Endorsement Usability',           37));
	$this->addField(new X937FieldImageInfoUsability(37, 'BOFD Endorsement Usability',            38));
	$this->addField(new X937FieldReserved(38, 39, 1));
	$this->addField(new X937FieldReserved(39, 40, 1));
	$this->addField(new X937FieldReserved(40, 41, 1));
	$this->addField(new X937FieldReserved(41, 42, 1));
	$this->addField(new X937FieldReserved(42, 43, 1));
	$this->addField(new X937FieldReserved(43, 44, 1));
	$this->addField(new X937FieldReserved(44, 45, 1));
	
	// Image Analysis User Information (Field 45)
	$this->addField(new X937FieldUser(45, 46, 20));
	$this->addField(new X937FieldReserved(46, 66, 80));
    }
}

// Bundle Control Record - Type 70
class X937RecordBundleControl extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(7);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::BUNDLE_CONTROL));
	$this->addField(new X937FieldGeneric(2, 'Items Within Bundle Count',  X937Field::USAGE_MANDATORY,    3,  4, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(3, 'Bundle Total Amount',        X937Field::USAGE_MANDATORY,    7, 12, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(4, 'MICR Valid Total Amount',    X937Field::USAGE_CONDITIONAL, 19, 12, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(5, 'Images within Bundle Count', X937Field::USAGE_CONDITIONAL, 31,  5, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldUser(6, 36, 20));
	$this->addField(new X937FieldReserved(7, 56, 25));
    }
}

// Box Summary - Type 75
class X937RecordBoxSummary extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(7);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::BOX_SUMMARY));
	$this->addField(new X937FieldRoutingNumber(2, 'Destination',   X937Field::USAGE_MANDATORY, 03));
	$this->addField(new X937FieldGeneric(3, 'Box Sequence Number', X937Field::USAGE_MANDATORY, 12,  3, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(4, 'Box Bundle Count',    X937Field::USAGE_MANDATORY, 15,  4, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(5, 'Box Number ID',       X937Field::USAGE_MANDATORY, 19,  8, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(6, 'Box Total Amount',    X937Field::USAGE_MANDATORY, 27, 14, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldReserved(7, 41, 40));
    }
}

// Routing Number Summary - Type 85
class X937RecordRoutingNumberSummary extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(6);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::ROUTING_NUMBER_SUMMARY));
	$this->addField(new X937FieldRoutingNumber(2, 'Within Cash Letter',    X937Field::USAGE_MANDATORY,  3));
	$this->addField(new X937FieldGeneric(3, 'Routing Number Total Amount', X937Field::USAGE_MANDATORY, 12, 14, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(4, 'Routing Number Item Count',   X937Field::USAGE_MANDATORY, 26,  6, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldUser(5, 32, 24));
	$this->addField(new X937FieldReserved(6, 56, 25));
    }
}

// Cash Letter Control Record - Type 90
class X937RecordCashLetterControl extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CASH_LETTER_CONTROL));
	$this->addField(new X937FieldGeneric(2, 'Bundle Count',                    X937Field::USAGE_MANDATORY,    3,  6, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(3, 'Items Within Cash Letter Count',  X937Field::USAGE_MANDATORY,    9, 16, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(4, 'Cash Letter Total Amount',        X937Field::USAGE_MANDATORY,   17, 14, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(5, 'Images Within Cash Letter Count', X937Field::USAGE_CONDITIONAL, 31,  9, X937Field::TYPE_ALPHABETIC));
	$this->addField(new X937FieldInstitutionName(6, 'ECE Institution',         X937Field::USAGE_CONDITIONAL, 40));
	$this->addField(new X937FieldDate(7, 'Settlement Date',                    X937Field::USAGE_CONDITIONAL, 58));
	$this->addField(new X937FieldReserved(8, 66, 15));
    }
}

// File Control Record - Type 99
class X937RecordFileControl extends X937Record {
    protected function addFields() {
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::FILE_CONTROL));
	$this->addField(new X937FieldGeneric(2, 'Cash Letter Count',                 X937Field::USAGE_MANDATORY,    3,  6, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(3, 'Total Record Count',                X937Field::USAGE_MANDATORY,    9,  6, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(4, 'Total Item Count',                  X937Field::USAGE_MANDATORY,   17,  8, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(5, 'File Total Amount',                 X937Field::USAGE_MANDATORY,   25, 16, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldContactName(6, 'Immediate Origin Contact Name', X937Field::USAGE_CONDITIONAL, 41));
	$this->addField(new X937FieldPhoneNumber(7, 'Immediate Origin Contact',      X937Field::USAGE_CONDITIONAL, 55));
	$this->addField(new X937FieldReserved(8, 65, 16));
    }
}