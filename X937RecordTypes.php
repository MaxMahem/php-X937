<?php

require_once 'X937Field.php';
require_once 'X937FieldPredefined.php';
require_once 'X937FieldTypeName.php';

/**
 * This class is just a stub for record types we haven't implemented yet.
 */
class X937RecordGeneric extends X937Record 
{
    public static function defineFields() {
	return array();
    }
}

/**
 * File Header Record - Type 01
 */
class X937RecordFileHeader  extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::FILE_HEADER);
	$fields[2]  = new X937FieldSpecificationLevel();
	$fields[3]  = new X937FieldTestFile();
	$fields[4]  = new X937FieldRoutingNumber(4, 'Immediate Destination',    X937Field::USAGE_MANDATORY,    6);
	$fields[5]  = new X937FieldRoutingNumber(5, 'Immediate Origin',         X937Field::USAGE_MANDATORY,   15);
	$fields[6]  = new X937FieldDate(6, 'File Creation',                     X937Field::USAGE_MANDATORY,   24);
	$fields[7]  = new X937FieldTime(7, 'File Creation',                     X937Field::USAGE_MANDATORY,   32);
	$fields[8]  = new X937FieldResend();
	$fields[9]  = new X937FieldNameInstitution( 9, 'Immediate Destination', 37);
	$fields[10] = new X937FieldNameInstitution(10, 'Immediate Origin',      55);
	$fields[11] = new X937FieldGeneric(11, 'File ID Modifer',               X937Field::USAGE_CONDITIONAL, 73,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[12] = new X937FieldGeneric(12, 'Country Code',                  X937Field::USAGE_CONDITIONAL, 74,  2, X937Field::TYPE_ALPHABETIC);
	$fields[13] = new X937FieldUser(13, 76,  4);
	$fields[14] = new X937FieldReserved(14, 80,  1);
	
	return $fields;
    }
}

/**
 *  Cash Letter Header Record - Type 10
 */
class X937RecordCashLetterHeader  extends X937Record 
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::CASH_LETTER_HEADER);
	$fields[2]  = new X937FieldCollectionType(X937FieldRecordType::CASH_LETTER_HEADER);
	$fields[3]  = new X937FieldRoutingNumber(3, 'Destination',            X937Field::USAGE_MANDATORY,    5);
	$fields[4]  = new X937FieldRoutingNumber(4, 'ECE Instituion',         X937Field::USAGE_MANDATORY,   14);
	$fields[5]  = new X937FieldDate(5, 'Cash Letter Business',            X937Field::USAGE_MANDATORY,   23);
	$fields[6]  = new X937FieldDate(6, 'Cash Letter Creation',            X937Field::USAGE_MANDATORY,   31);
	$fields[7]  = new X937FieldTime(7, 'Cash Letter Creation',            X937Field::USAGE_MANDATORY,   39);
	$fields[8]  = new X937FieldCashLetterType();
	$fields[9]  = new X937FieldDocType(X937FieldRecordType::CASH_LETTER_HEADER);
	$fields[10] = new X937FieldGeneric(10, 'Cash Letter ID',              X937Field::USAGE_CONDITIONAL, 45,  8, X937Field::TYPE_ALPHAMERIC);
	$fields[11] = new X937FieldName(11, 'Originator Contact', 53, 14);
	$fields[12] = new X937FieldPhoneNumber(12, 'Originator Contact',      X937Field::USAGE_CONDITIONAL, 67);
	$fields[13] = new X937FieldFedWorkType();
	$fields[14] = new X937FieldUser(14, 78,  2);
	$fields[15] = new X937FieldReserved(15, 80,  1);
	
	return $fields;
    }
}

/**
 * Bundle Header Record - Type 20
 */
class X937RecordBundleHeader extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::BUNDLE_HEADER);
	$fields[2]  = new X937FieldCollectionType(X937FieldRecordType::BUNDLE_HEADER);
	$fields[3]  = new X937FieldRoutingNumber( 3, 'Destination',      X937Field::USAGE_MANDATORY,    5);
	$fields[4]  = new X937FieldRoutingNumber( 4, 'ECE Institution',  X937Field::USAGE_MANDATORY,   14);
	$fields[5]  = new X937FieldDate(5, 'Bundle Business',            X937Field::USAGE_MANDATORY,   23);
	$fields[6]  = new X937FieldDate(6, 'Bundle Creation',            X937Field::USAGE_MANDATORY,   31);
	$fields[7]  = new X937FieldGeneric( 7, 'Bundle ID',              X937Field::USAGE_CONDITIONAL, 39, 10, X937Field::TYPE_ALPHAMERIC);
	$fields[8]  = new X937FieldGeneric( 8, 'Bundle Sequence Number', X937Field::USAGE_CONDITIONAL, 49,  4, X937Field::TYPE_NUMERICBLANK);
	$fields[9]  = new X937FieldGeneric( 9, 'Cycle Number',           X937Field::USAGE_CONDITIONAL, 53,  2, X937Field::TYPE_ALPHAMERIC);
	$fields[10] = new X937FieldRoutingNumber(10, 'Return Location',  X937Field::USAGE_CONDITIONAL, 55);
	$fields[11] = new X937FieldUser(11, 64,  5);
	$fields[12] = new X937FieldReserved(12, 69, 12);
	
	return $fields;
    }
}

/**
 * Check Detail Record - Type 25
 */
class X937RecordCheckDetail extends X937Record {
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL);
	$fields[2]  = new X937FieldGeneric( 2, 'Auxiliary On-Us',                       X937Field::USAGE_CONDITIONAL,  3, 15, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldGeneric( 3, 'External Processing Code',              X937Field::USAGE_CONDITIONAL, 18,  1, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[4]  = new X937FieldGeneric( 4, 'Payor Bank Routing Number',             X937Field::USAGE_MANDATORY,   19,  8, X937Field::TYPE_NUMERIC);
	$fields[5]  = new X937FieldGeneric( 5, 'Payor Bank Routing Number Check Digit', X937Field::USAGE_CONDITIONAL, 27,  1, X937Field::TYPE_NUMERICBLANKSPECIALMICR);
	$fields[6]  = new X937FieldGeneric( 6, 'On-Us',                                 X937Field::USAGE_MANDATORY,   28, 20, X937Field::TYPE_NUMERICBLANKSPECIALMICRONUS);
	$fields[7]  = new X937FieldAmount(  7, 'Item', 10, 48);
	$fields[8]  = new X937FieldItemSequenceNumber(8, 'ECE Institution',             X937Field::USAGE_MANDATORY, 58);
	$fields[9]  = new X937FieldDocType(X937FieldRecordType::CHECK_DETAIL);
	$fields[10] = new X937FieldGeneric(10, 'Return Acceptance Indicator',           X937Field::USAGE_CONDITIONAL, 74,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[11] = new X937FieldGeneric(11, 'MICR Valid Indicator',                  X937Field::USAGE_CONDITIONAL, 75,  1, X937Field::TYPE_NUMERIC);
	$fields[12] = new X937FieldGeneric(12, 'BOFD Indicator',                        X937Field::USAGE_MANDATORY,   76,  1, X937Field::TYPE_ALPHABETIC);
	$fields[13] = new X937FieldGeneric(13, 'Check Detail Record Addendum Count',    X937Field::USAGE_MANDATORY,   77,  2, X937Field::TYPE_NUMERIC);
	$fields[14] = new X937FieldGeneric(14, 'Correction Indicator',                  X937Field::USAGE_CONDITIONAL, 79,  1, X937Field::TYPE_NUMERIC);
	$fields[15] = new X937FieldGeneric(15, 'Archive Type Indicator',                X937Field::USAGE_CONDITIONAL, 80,  1, X937Field::TYPE_ALPHAMERIC);
	
	return $fields;
    }
}

/**
 * Check Detail Record - Type 26
 */
class X937RecordCheckDetailAddendumA extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_A);
	$fields[2]  = new X937FieldGeneric( 2, 'Check Detail Addendum A Record Number', X937Field::USAGE_MANDATORY,    3,  1, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldRoutingNumber(3, 'BOFD',                             X937Field::USAGE_CONDITIONAL,  4);
	$fields[4]  = new X937FieldDate(4, 'BOFD Endorsement',                          X937Field::USAGE_MANDATORY, 13);
	$fields[5]  = new X937FieldItemSequenceNumber( 5, 'BOFD',                       X937Field::USAGE_CONDITIONAL, 21);
	$fields[6]  = new X937FieldDepositAccountNumber(6,                              X937Field::USAGE_CONDITIONAL, 36);
	$fields[7]  = new X937FieldGeneric( 7, 'BOFD Deposit Branch',                   X937Field::USAGE_CONDITIONAL, 54,  5, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[8]  = new X937FieldName(    8, 'Payee', 59, 15);
	$fields[9]  = new X937FieldGeneric( 9, 'Truncation Indicator',                  X937Field::USAGE_CONDITIONAL, 74,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[10] = new X937FieldGeneric(10, 'BOFD Conversion Indicator',             X937Field::USAGE_CONDITIONAL, 75,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[11] = new X937FieldGeneric(11, 'BOFD Correction Indicator',             X937Field::USAGE_CONDITIONAL, 76,  1, X937Field::TYPE_NUMERIC);
	$fields[12] = new X937FieldUser(12, 77,  1);
	$fields[13] = new X937FieldReserved(13, 78, 3);
	
	return $fields;
    }
}

// note Check Detail AddendumB is variable length and in a seperate file.

/**
 * Check Detail Record - Type 28
 */
class X937RecordCheckDetailAddendumC extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_C);
	$fields[2]  = new X937FieldGeneric(2, 'Check Detail Addendum C Record Number', X937Field::USAGE_MANDATORY,    3,  2, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldRoutingNumber(3, 'Endorsing Bank',                  X937Field::USAGE_CONDITIONAL,  5);
	$fields[4]  = new X937FieldDate(4, 'Endorsing Bank Endorsement',               X937Field::USAGE_CONDITIONAL, 14);
	$fields[5]  = new X937FieldItemSequenceNumber(5, 'Endorsing Bank',             X937Field::USAGE_CONDITIONAL, 22);
	$fields[6]  = new X937FieldGeneric(6, 'Truncation Indicator',                  X937Field::USAGE_CONDITIONAL, 37,  1, X937Field::TYPE_ALPHABETIC);
	$fields[7]  = new X937FieldGeneric(7, 'Endorsing Bank Conversion Indicator',   X937Field::USAGE_CONDITIONAL, 38,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[8]  = new X937FieldGeneric(8, 'Endorsing Bank Correction Indicator',   X937Field::USAGE_CONDITIONAL, 39,  1, X937Field::TYPE_NUMERIC);
	$fields[9]  = new X937FieldReturnReason(9, X937Field::USAGE_CONDITIONAL, 40);
	$fields[10] = new X937FieldUser(10, 41,  15);
	$fields[11] = new X937FieldReserved(11, 56, 15);
	
	return $fields;
    }
}

/**
 * Return Record - Type 31
 */
class X937RecordReturnRecord extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::RETURN_RECORD);
	$fields[2]  = new X937FieldGeneric( 2, 'Payor Bank Routing Number',             X937Field::USAGE_MANDATORY,    3,  8, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldGeneric( 3, 'Payor Bank Routing Number Check Digit', X937Field::USAGE_CONDITIONAL, 11,  1, X937Field::TYPE_NUMERICBLANKSPECIALMICR);
	$fields[4]  = new X937FieldGeneric( 4, 'On-Us Return Record',                   X937Field::USAGE_CONDITIONAL, 12, 20, X937Field::TYPE_NUMERICBLANKSPECIALMICRONUS);
	$fields[5]  = new X937FieldItem(    5, 'Item',                                  X937Field::USAGE_MANDATORY,   32, 10);
	$fields[6]  = new X937FieldReturnReason(6, X937Field::USAGE_MANDATORY, 42);
	$fields[7]  = new X937FieldGeneric( 7, 'Return Reason Addendum Count',          X937Field::USAGE_MANDATORY,   43,  2, X937Field::TYPE_NUMERIC);
	$fields[8]  = new X937FieldDocType(X937FieldRecordType::RETURN_RECORD);
	$fields[9]  = new X937FieldDate(9, 'Forward Bundle',                            X937Field::USAGE_CONDITIONAL, 46);
	$fields[10] = new X937FieldItemSequenceNumber(10, 'ECE Institution',            X937Field::USAGE_CONDITIONAL, 54);
	$fields[11] = new X937FieldGeneric(11, 'External Processing Code',              X937Field::USAGE_CONDITIONAL, 69,  1, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[12] = new X937FieldGeneric(12, 'Return Notification Indicator',         X937Field::USAGE_CONDITIONAL, 70,  1, X937Field::TYPE_NUMERIC);
	$fields[13] = new X937FieldGeneric(13, 'Return Archive Type Indicator',         X937Field::USAGE_CONDITIONAL, 71,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[14] = new X937FieldReserved(14, 72, 9);
	
	return $fields;
    }
}

/**
 * Return Addendum A - Type 32
 */
class X937RecordReturnAddendumA extends X937Record
{
    public static function defineFields()
    {
	$fields = array();

	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_A);
	$fields[2]  = new X937FieldGeneric( 2, 'Return Addendum A Record Number', X937Field::USAGE_MANDATORY,    3,  1, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldRoutingNumber(3, 'BOFD',                       X937Field::USAGE_CONDITIONAL,  4);
	$fields[4]  = new X937FieldDate(4, 'BOFD Endorsement',                    X937Field::USAGE_CONDITIONAL, 13); // This field has some additional data maybe?
	$fields[5]  = new X937FieldItemSequenceNumber(5, 'BOFD',                  X937Field::USAGE_CONDITIONAL, 21);
	$fields[6]  = new X937FieldDepositAccountNumber(6,                        X937Field::USAGE_CONDITIONAL, 36);
	$fields[7]  = new X937FieldGeneric( 7, 'BOFD Deposit Branch',             X937Field::USAGE_CONDITIONAL, 54,  5, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[8]  = new X937FieldName(    8, 'Payee', 59, 15);
	$fields[9]  = new X937FieldGeneric( 9, 'Truncation Indicator',            X937Field::USAGE_CONDITIONAL, 74,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[10] = new X937FieldGeneric(10, 'BOFD Conversion Indicator',       X937Field::USAGE_CONDITIONAL, 75,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[11] = new X937FieldGeneric(11, 'BOFD Correction Indicator',       X937Field::USAGE_CONDITIONAL, 76,  1, X937Field::TYPE_NUMERIC);
	$fields[12] = new X937FieldUser(12, 77,  1);
	$fields[13] = new X937FieldReserved(13, 78, 3);
	
	return $fields;
    }
}

/**
 * Return Addendum B - Type 33
 */
class X937RecordReturnAddendumB extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_B);
	$fields[2] = new X937FieldNameInstitution(2, 'Payor Bank', 3);
	$fields[3] = new X937FieldGeneric(3, 'Auxiliary On-Us',       X937Field::USAGE_CONDITIONAL, 21, 15, X937Field::TYPE_NUMERICBLANKSPECIALMICR);
	$fields[4] = new X937FieldItemSequenceNumber(4, 'Payor Bank', X937Field::USAGE_CONDITIONAL, 36);
	$fields[5] = new X937FieldDate(5, 'Payor Bank Business',      X937Field::USAGE_CONDITIONAL, 51,  8, X937Field::TYPE_NUMERIC);
	$fields[6] = new X937FieldName(6, 'Payor Account', 59, 22);
	
	return $fields;
    }
}

// Note: Return Addendum C - Type 34 is variable length and on seperate page.

/**
 * Return Addendum D - Type 35
 */
class X937RecordReturnAddendumD extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_D);
	$fields[2]  = new X937FieldGeneric(2, 'Return Addendum D Record Number',     X937Field::USAGE_MANDATORY,    3,  2, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldRoutingNumber(3, 'Endorsing Bank',                X937Field::USAGE_CONDITIONAL,  5);
	$fields[4]  = new X937FieldDate(4, 'Endorsing Bank Endorsement',             X937Field::USAGE_CONDITIONAL, 14);
	$fields[5]  = new X937FieldItemSequenceNumber(5, 'Endorsing Bank',           X937Field::USAGE_CONDITIONAL, 22);
	$fields[6]  = new X937FieldGeneric(6, 'Truncation Indicator',                X937Field::USAGE_CONDITIONAL, 37,  1, X937Field::TYPE_ALPHABETIC);
	$fields[7]  = new X937FieldGeneric(7, 'Endorsing Bank Conversion Indicator', X937Field::USAGE_CONDITIONAL, 38,  1, X937Field::TYPE_ALPHAMERIC);
	$fields[8]  = new X937FieldGeneric(8, 'Endorsing Bank Correction Indicator', X937Field::USAGE_CONDITIONAL, 39,  1, X937Field::TYPE_NUMERIC);
	$fields[9]  = new X937FieldReturnReason(9, X937Field::USAGE_CONDITIONAL, 40);
	$fields[10] = new X937FieldUser(10, 41,  15);
	$fields[11] = new X937FieldReserved(11, 56, 15);
	
	return $fields;
    }
}

/**
 * Account Totals Detail - Type 40
 */
class X937RecordAccountTotalsDetail extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::ACCOUNT_TOTALS_DETAIL);
	$fields[2] = new X937FieldRoutingNumber(2, 'Destination',                   X937Field::USAGE_MANDATORY,  5);
	$fields[3] = new X937FieldGeneric(3, 'Key Account / Low Account in Range',  X937Field::USAGE_MANDATORY, 12, 18, X937Field::TYPE_NUMERIC);
	$fields[4] = new X937FieldGeneric(4, 'Key Account / High Account in Range', X937Field::USAGE_MANDATORY, 30, 18, X937Field::TYPE_NUMERIC);
	$fields[5] = new X937FieldGeneric(5, 'Total Item Count',                    X937Field::USAGE_MANDATORY, 48, 12, X937Field::TYPE_NUMERIC);
	$fields[6] = new X937FieldAmount( 6, 'Total Item', 60, 14);
	$fields[7] = new X937FieldUser(7, 74, 4);
	$fields[8] = new X937FieldReserved(8, 78, 3);
	
	return $fields;
    }
}

/**
 * Non-Hit Totals Detail - Type 41
 */
class X937RecordNonHitTotalsDetail extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::NON_HIT_TOTALS_DETAIL);
	$fields[2] = new X937FieldRoutingNumber(2, 'Destination', X937Field::USAGE_MANDATORY,  5);
	$fields[3] = new X937FieldGeneric(3, 'Non-Hit Indicator', X937Field::USAGE_MANDATORY, 12, 01, X937Field::TYPE_ALPHAMERIC);
	$fields[4] = new X937FieldGeneric(4, 'Total Item Count',  X937Field::USAGE_MANDATORY, 13, 12, X937Field::TYPE_NUMERIC);
	$fields[5] = new X937FieldAmount( 5, 'Total Item', 25, 14);
	$fields[6] = new X937FieldUser(6, 39, 12);
	$fields[7] = new X937FieldReserved(7, 51, 30);
	
	return $fields;
    }
}

/**
 * Image View Detail Record - Type 50
 */
class X937RecordImageViewDetail extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::IMAGE_VIEW_DETAIL);
	$fields[2]  = new X937FieldGeneric(2, 'Image Indicator',                             X937Field::USAGE_MANDATORY,    3, 1, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldRoutingNumber(3, 'Image Creator',                         X937Field::USAGE_MANDATORY,    4);
	$fields[4]  = new X937FieldDate(4, 'Image Creator',                                  X937Field::USAGE_MANDATORY,   13);
	$fields[5]  = new X937FieldGeneric( 5, 'Image View Format Indicator',                X937Field::USAGE_MANDATORY,   21, 2, X937Field::TYPE_NUMERICBLANK);
	$fields[6]  = new X937FieldGeneric( 6, 'Image View Compression Algorithm Identifer', X937Field::USAGE_MANDATORY,   23, 2, X937Field::TYPE_NUMERICBLANK);
	$fields[7]  = new X937FieldGeneric( 7, 'Image View Data Size',                       X937Field::USAGE_CONDITIONAL, 25, 7, X937Field::TYPE_NUMERIC);
	$fields[8]  = new X937FieldGeneric( 8, 'View Side Indicator',                        X937Field::USAGE_MANDATORY,   32, 1, X937Field::TYPE_NUMERIC);
	$fields[9]  = new X937FieldGeneric( 9, 'View Descriptor',                            X937Field::USAGE_MANDATORY,   33, 2, X937Field::TYPE_NUMERIC);
	$fields[10] = new X937FieldGeneric(10, 'Digital Signature Indicator',                X937Field::USAGE_MANDATORY,   35, 1, X937Field::TYPE_NUMERICBLANK);
	$fields[11] = new X937FieldGeneric(11, 'Digital Signature Method',                   X937Field::USAGE_MANDATORY,   36, 2, X937Field::TYPE_NUMERIC);
	$fields[12] = new X937FieldGeneric(12, 'Security Key Size',                          X937Field::USAGE_CONDITIONAL, 38, 5, X937Field::TYPE_NUMERIC);
	$fields[13] = new X937FieldGeneric(13, 'Start of Protected Data',                    X937Field::USAGE_CONDITIONAL, 43, 7, X937Field::TYPE_NUMERIC);
	$fields[14] = new X937FieldGeneric(14, 'Length of Protected Data',                   X937Field::USAGE_CONDITIONAL, 50, 7, X937Field::TYPE_NUMERIC);
	$fields[15] = new X937FieldGeneric(15, 'Image Recreate Indicator',                   X937Field::USAGE_CONDITIONAL, 57, 1, X937Field::TYPE_NUMERIC);
	$fields[16] = new X937FieldUser(16, 58, 8);
	$fields[17] = new X937FieldReserved(17, 66, 15);
	
	return $fields;
    }
}

/**
 * Image View Analysis Record - Type 54
 */
class X937RecordImageViewAnalysis extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::IMAGE_VIEW_ANALYSIS);
	$fields[2]  = new X937FieldGeneric(2, 'Global Image Qualilty',      X937Field::USAGE_MANDATORY, 3, 1, X937Field::TYPE_NUMERIC);
	$fields[3]  = new X937FieldGeneric(3, 'Global Image Usability',     X937Field::USAGE_MANDATORY, 4, 1, X937Field::TYPE_NUMERIC);
	$fields[4]  = new X937FieldGeneric(4, 'Imaging Bank Specific Test', X937Field::USAGE_MANDATORY, 5, 1, X937Field::TYPE_NUMERIC);

	// Image Quality Information (Fields 5-24)
	$fields[5]  = new X937FieldImageQualityInfo( 5, 'Partial Image',               6);
	$fields[6]  = new X937FieldImageInfoQuality( 6, 'Excessive Image Skew',        7);
	$fields[7]  = new X937FieldImageInfoQuality( 7, 'Piggyback Image',             8);
	$fields[8]  = new X937FieldImageInfoQuality( 8, 'Too Light or Too Dark',       9);
	$fields[9]  = new X937FieldImageInfoQuality( 9, 'Streaks and/or Bands',       10);
	$fields[10] = new X937FieldImageInfoQuality(10, 'Below Minimum Image Size',   11);
	$fields[11] = new X937FieldImageInfoQuality(11, 'Exceeds Maximum Image Size', 12);
	$fields[12] = new X937FieldReserved(12, 13, 1);
	$fields[13] = new X937FieldReserved(13, 14, 1);
	$fields[14] = new X937FieldReserved(14, 15, 1);
	$fields[15] = new X937FieldReserved(15, 16, 1);
	$fields[16] = new X937FieldReserved(16, 17, 1);
	$fields[17] = new X937FieldReserved(17, 18, 1);
	$fields[18] = new X937FieldReserved(18, 19, 1);
	$fields[19] = new X937FieldReserved(19, 20, 1);
	$fields[20] = new X937FieldReserved(20, 21, 1);
	$fields[21] = new X937FieldReserved(21, 22, 1);
	$fields[22] = new X937FieldReserved(22, 23, 1);
	$fields[23] = new X937FieldReserved(23, 24, 1);
	$fields[24] = new X937FieldReserved(24, 25, 1);

	// Image Usability Information (Fields 25-44)
	$fields[25] = new X937FieldGeneric(25, 'Image-Enabled POD',   X937Field::USAGE_CONDITIONAL, 26, 1, X937Field::TYPE_NUMERIC);
	$fields[26] = new X937FieldGeneric(26, 'Source Document Bad', X937Field::USAGE_CONDITIONAL, 27, 1, X937Field::TYPE_NUMERIC);
	$fields[27] = new X937FieldImageInfoUsability(27, 'Date Usability',                        28);
	$fields[28] = new X937FieldImageInfoUsability(28, 'Payee Usability',                       29);
	$fields[29] = new X937FieldImageInfoUsability(29, 'Convenience Amount Usability',          30);
	$fields[30] = new X937FieldImageInfoUsability(30, 'Legal Amount Usability',                31);
	$fields[31] = new X937FieldImageInfoUsability(31, 'Signature Usability',                   32);
	$fields[32] = new X937FieldImageInfoUsability(32, 'Payor Name and Address Usability',      33);
	$fields[33] = new X937FieldImageInfoUsability(33, 'MICR Line Usability',                   34);
	$fields[34] = new X937FieldImageInfoUsability(34, 'Memo Line Usability',                   35);
	$fields[35] = new X937FieldImageInfoUsability(35, 'Payor Bank Name and Address Usability', 36);
	$fields[36] = new X937FieldImageInfoUsability(36, 'Payee Endorsement Usability',           37);
	$fields[37] = new X937FieldImageInfoUsability(37, 'BOFD Endorsement Usability',            38);
	$fields[38] = new X937FieldReserved(38, 39, 1);
	$fields[39] = new X937FieldReserved(39, 40, 1);
	$fields[40] = new X937FieldReserved(40, 41, 1);
	$fields[41] = new X937FieldReserved(41, 42, 1);
	$fields[42] = new X937FieldReserved(42, 43, 1);
	$fields[43] = new X937FieldReserved(43, 44, 1);
	$fields[44] = new X937FieldReserved(44, 45, 1);
	
	// Image Analysis User Information (Field 45)
	$fields[45] = new X937FieldUser(45, 46, 20);
	$fields[46] = new X937FieldReserved(46, 66, 80);
	
	return $fields;
    }
}

/**
 * Bundle Control Record - Type 70
 */
class X937RecordBundleControl extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::BUNDLE_CONTROL);
	$fields[2] = new X937FieldGeneric(2, 'Items Within Bundle Count',  X937Field::USAGE_MANDATORY,    3,  4, X937Field::TYPE_NUMERIC);
	$fields[3] = new X937FieldAmount( 3, 'Bundle Total',      7, 12);
	$fields[4] = new X937FieldAmount( 4, 'MICR Valid Total', 19, 12,   X937Field::USAGE_CONDITIONAL);
	$fields[5] = new X937FieldGeneric(5, 'Images within Bundle Count', X937Field::USAGE_CONDITIONAL, 31,  5, X937Field::TYPE_NUMERIC);
	$fields[6] = new X937FieldUser(6, 36, 20);
	$fields[7] = new X937FieldReserved(7, 56, 25);
	
	return $fields;
    }
}

/**
 * Box Summary - Type 75
 */
class X937RecordBoxSummary extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::BOX_SUMMARY);
	$fields[2] = new X937FieldRoutingNumber(2, 'Destination',   X937Field::USAGE_MANDATORY, 03);
	$fields[3] = new X937FieldGeneric(3, 'Box Sequence Number', X937Field::USAGE_MANDATORY, 12,  3, X937Field::TYPE_NUMERIC);
	$fields[4] = new X937FieldGeneric(4, 'Box Bundle Count',    X937Field::USAGE_MANDATORY, 15,  4, X937Field::TYPE_NUMERIC);
	$fields[5] = new X937FieldGeneric(5, 'Box Number ID',       X937Field::USAGE_MANDATORY, 19,  8, X937Field::TYPE_NUMERIC);
	$fields[6] = new X937FieldAmount( 6, 'Box Total', 27, 14);
	$fields[7] = new X937FieldReserved(7, 41, 40);
	
	return $fields;
    }
}

/**
 * Routing Number Summary - Type 85
 */
class X937RecordRoutingNumberSummary extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::ROUTING_NUMBER_SUMMARY);
	$fields[2] = new X937FieldRoutingNumber(2, 'Within Cash Letter',    X937Field::USAGE_MANDATORY,  3);
	$fields[3] = new X937FieldAmount( 3, 'Routing Number Total', 12, 14);
	$fields[4] = new X937FieldGeneric(4, 'Routing Number Item Count',   X937Field::USAGE_MANDATORY, 26,  6, X937Field::TYPE_NUMERIC);
	$fields[5] = new X937FieldUser(5, 32, 24);
	$fields[6] = new X937FieldReserved(6, 56, 25);
	
	return $fields;
    }
}

/**
 * Cash Letter Control Record - Type 90
 */
class X937RecordCashLetterControl extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::CASH_LETTER_CONTROL);
	$fields[2] = new X937FieldGeneric(2, 'Bundle Count',                    X937Field::USAGE_MANDATORY,    3,  6, X937Field::TYPE_NUMERIC);
	$fields[3] = new X937FieldGeneric(3, 'Items Within Cash Letter Count',  X937Field::USAGE_MANDATORY,    9, 16, X937Field::TYPE_NUMERIC);
	$fields[4] = new X937FieldAmount( 4, 'Cash Letter Total', 17, 14);
	$fields[5] = new X937FieldGeneric(5, 'Images Within Cash Letter Count', X937Field::USAGE_CONDITIONAL, 31,  9, X937Field::TYPE_ALPHABETIC);
	$fields[6] = new X937FieldNameInstitution(6, 'ECE', 40);
	$fields[7] = new X937FieldDate(7, 'Settlement',                         X937Field::USAGE_CONDITIONAL, 58);
	$fields[8] = new X937FieldReserved(8, 66, 15);
	
	return $fields;
    }
}

/**
 * File Control Record - Type 99
 */
class X937RecordFileControl extends X937Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::FILE_CONTROL);
	$fields[2] = new X937FieldGeneric(2, 'Cash Letter Count',                 X937Field::USAGE_MANDATORY,    3,  6, X937Field::TYPE_NUMERIC);
	$fields[3] = new X937FieldGeneric(3, 'Total Record Count',                X937Field::USAGE_MANDATORY,    9,  6, X937Field::TYPE_NUMERIC);
	$fields[4] = new X937FieldGeneric(4, 'Total Item Count',                  X937Field::USAGE_MANDATORY,   17,  8, X937Field::TYPE_NUMERIC);
	$fields[5] = new X937FieldAmount( 5, 'File Total', 25, 16);
	$fields[6] = new X937FieldName(   6, 'Immediate Origin Contact', 41, 14);
	$fields[7] = new X937FieldPhoneNumber(7, 'Immediate Origin Contact',      X937Field::USAGE_CONDITIONAL, 55);
	$fields[8] = new X937FieldReserved(8, 65, 16);
	
	return $fields;
    }
}