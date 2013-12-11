<?php

namespace X937\Records;

use X937\Fields as Fields;

use X937\Fields\RecordType;
use X937\Fields\Field;

/**
 * This class is just a stub for record types we haven't implemented yet.
 */
class Generic extends Record 
{
    public static function defineFields() {
	return array();
    }
}

/**
 * File Header Record - Type 01
 */
class FileHeader extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::FILE_HEADER);
	$fields[2]  = new Fields\SpecificationLevel();
	$fields[3]  = new Fields\TestFile();
	$fields[4]  = new Fields\RoutingNumber(4, 'Immediate Destination',    Field::USAGE_MANDATORY,    6);
	$fields[5]  = new Fields\RoutingNumber(5, 'Immediate Origin',         Field::USAGE_MANDATORY,   15);
	$fields[6]  = new Fields\Date(6, 'File Creation',                     Field::USAGE_MANDATORY,   24);
	$fields[7]  = new Fields\Time(7, 'File Creation',                     Field::USAGE_MANDATORY,   32);
	$fields[8]  = new Fields\Resend();
	$fields[9]  = new Fields\NameInstitution( 9, 'Immediate Destination', 37);
	$fields[10] = new Fields\NameInstitution(10, 'Immediate Origin',      55);
	$fields[11] = new Fields\Generic(11, 'File ID Modifer',               Field::USAGE_CONDITIONAL, 73,  1, Field::TYPE_ALPHAMERIC);
	$fields[12] = new Fields\Generic(12, 'Country Code',                  Field::USAGE_CONDITIONAL, 74,  2, Field::TYPE_ALPHABETIC);
	$fields[13] = new Fields\User(13, 76,  4);
	$fields[14] = new Fields\Reserved(14, 80,  1);
	
	return $fields;
    }
}

/**
 *  Cash Letter Header Record - Type 10
 */
class CashLetterHeader  extends Record 
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::CASH_LETTER_HEADER);
	$fields[2]  = new Fields\CollectionType(RecordType::CASH_LETTER_HEADER);
	$fields[3]  = new Fields\RoutingNumber(3, 'Destination',            Field::USAGE_MANDATORY,    5);
	$fields[4]  = new Fields\RoutingNumber(4, 'ECE Instituion',         Field::USAGE_MANDATORY,   14);
	$fields[5]  = new Fields\Date(5, 'Cash Letter Business',            Field::USAGE_MANDATORY,   23);
	$fields[6]  = new Fields\Date(6, 'Cash Letter Creation',            Field::USAGE_MANDATORY,   31);
	$fields[7]  = new Fields\Time(7, 'Cash Letter Creation',            Field::USAGE_MANDATORY,   39);
	$fields[8]  = new Fields\CashLetterType();
	$fields[9]  = new Fields\DocType(RecordType::CASH_LETTER_HEADER);
	$fields[10] = new Fields\Generic(10, 'Cash Letter ID',              Field::USAGE_CONDITIONAL, 45,  8, Field::TYPE_ALPHAMERIC);
	$fields[11] = new Fields\NameContact(11, 'Originator', 53, 14);
	$fields[12] = new Fields\PhoneNumber(12, 'Originator Contact',      Field::USAGE_CONDITIONAL, 67);
	$fields[13] = new Fields\FedWorkType();
	$fields[14] = new Fields\User(14, 78,  2);
	$fields[15] = new Fields\Reserved(15, 80,  1);
	
	return $fields;
    }
}

/**
 * Bundle Header Record - Type 20
 */
class BundleHeader extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::BUNDLE_HEADER);
	$fields[2]  = new Fields\CollectionType(RecordType::BUNDLE_HEADER);
	$fields[3]  = new Fields\RoutingNumber( 3, 'Destination',      Field::USAGE_MANDATORY,    5);
	$fields[4]  = new Fields\RoutingNumber( 4, 'ECE Institution',  Field::USAGE_MANDATORY,   14);
	$fields[5]  = new Fields\Date(5, 'Bundle Business',            Field::USAGE_MANDATORY,   23);
	$fields[6]  = new Fields\Date(6, 'Bundle Creation',            Field::USAGE_MANDATORY,   31);
	$fields[7]  = new Fields\Generic( 7, 'Bundle ID',              Field::USAGE_CONDITIONAL, 39, 10, Field::TYPE_ALPHAMERIC);
	$fields[8]  = new Fields\Generic( 8, 'Bundle Sequence Number', Field::USAGE_CONDITIONAL, 49,  4, Field::TYPE_NUMERICBLANK);
	$fields[9]  = new Fields\Generic( 9, 'Cycle Number',           Field::USAGE_CONDITIONAL, 53,  2, Field::TYPE_ALPHAMERIC);
	$fields[10] = new Fields\RoutingNumber(10, 'Return Location',  Field::USAGE_CONDITIONAL, 55);
	$fields[11] = new Fields\User(11, 64,  5);
	$fields[12] = new Fields\Reserved(12, 69, 12);
	
	return $fields;
    }
}

/**
 * Check Detail Record - Type 25
 */
class CheckDetail extends Record {
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::CHECK_DETAIL);
	$fields[2]  = new Fields\Generic( 2, 'Auxiliary On-Us',                       Field::USAGE_CONDITIONAL,  3, 15, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\Generic( 3, 'External Processing Code',              Field::USAGE_CONDITIONAL, 18,  1, Field::TYPE_ALPHAMERICSPECIAL);
	$fields[4]  = new Fields\Generic( 4, 'Payor Bank Routing Number',             Field::USAGE_MANDATORY,   19,  8, Field::TYPE_NUMERIC);
	$fields[5]  = new Fields\Generic( 5, 'Payor Bank Routing Number Check Digit', Field::USAGE_CONDITIONAL, 27,  1, Field::TYPE_NUMERICBLANKSPECIALMICR);
	$fields[6]  = new Fields\Generic( 6, 'On-Us',                                 Field::USAGE_MANDATORY,   28, 20, Field::TYPE_NUMERICBLANKSPECIALMICRONUS);
	$fields[7]  = new Fields\Amount(  7, 'Item', 10, 48);
	$fields[8]  = new Fields\ItemSequenceNumber(8, 'ECE Institution',             Field::USAGE_MANDATORY, 58);
	$fields[9]  = new Fields\DocType(RecordType::CHECK_DETAIL);
	$fields[10] = new Fields\Generic(10, 'Return Acceptance Indicator',           Field::USAGE_CONDITIONAL, 74,  1, Field::TYPE_ALPHAMERIC);
	$fields[11] = new Fields\Generic(11, 'MICR Valid Indicator',                  Field::USAGE_CONDITIONAL, 75,  1, Field::TYPE_NUMERIC);
	$fields[12] = new Fields\Generic(12, 'BOFD Indicator',                        Field::USAGE_MANDATORY,   76,  1, Field::TYPE_ALPHABETIC);
	$fields[13] = new Fields\Generic(13, 'Check Detail Record Addendum Count',    Field::USAGE_MANDATORY,   77,  2, Field::TYPE_NUMERIC);
	$fields[14] = new Fields\Generic(14, 'Correction Indicator',                  Field::USAGE_CONDITIONAL, 79,  1, Field::TYPE_NUMERIC);
	$fields[15] = new Fields\Generic(15, 'Archive Type Indicator',                Field::USAGE_CONDITIONAL, 80,  1, Field::TYPE_ALPHAMERIC);
	
	return $fields;
    }
}

/**
 * Check Detail Record - Type 26
 */
class CheckDetailAddendumA extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::CHECK_DETAIL_ADDENDUM_A);
	$fields[2]  = new Fields\Generic( 2, 'Check Detail Addendum A Record Number', Field::USAGE_MANDATORY,    3,  1, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\RoutingNumber(3, 'BOFD',                             Field::USAGE_CONDITIONAL,  4);
	$fields[4]  = new Fields\Date(4, 'BOFD Endorsement',                          Field::USAGE_MANDATORY, 13);
	$fields[5]  = new Fields\ItemSequenceNumber( 5, 'BOFD',                       Field::USAGE_CONDITIONAL, 21);
	$fields[6]  = new Fields\DepositAccountNumber(6,                              Field::USAGE_CONDITIONAL, 36);
	$fields[7]  = new Fields\Generic( 7, 'BOFD Deposit Branch',                   Field::USAGE_CONDITIONAL, 54,  5, Field::TYPE_ALPHAMERICSPECIAL);
	$fields[8]  = new Fields\NamePayee();
	$fields[9]  = new Fields\Generic( 9, 'Truncation Indicator',                  Field::USAGE_CONDITIONAL, 74,  1, Field::TYPE_ALPHAMERIC);
	$fields[10] = new Fields\Generic(10, 'BOFD Conversion Indicator',             Field::USAGE_CONDITIONAL, 75,  1, Field::TYPE_ALPHAMERIC);
	$fields[11] = new Fields\Generic(11, 'BOFD Correction Indicator',             Field::USAGE_CONDITIONAL, 76,  1, Field::TYPE_NUMERIC);
	$fields[12] = new Fields\User(12, 77,  1);
	$fields[13] = new Fields\Reserved(13, 78, 3);
	
	return $fields;
    }
}

// note Check Detail AddendumB is variable length and in a seperate file.

/**
 * Check Detail Record - Type 28
 */
class CheckDetailAddendumC extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::CHECK_DETAIL_ADDENDUM_C);
	$fields[2]  = new Fields\Generic(2, 'Check Detail Addendum C Record Number', Field::USAGE_MANDATORY,    3,  2, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\RoutingNumber(3, 'Endorsing Bank',                  Field::USAGE_CONDITIONAL,  5);
	$fields[4]  = new Fields\Date(4, 'Endorsing Bank Endorsement',               Field::USAGE_CONDITIONAL, 14);
	$fields[5]  = new Fields\ItemSequenceNumber(5, 'Endorsing Bank',             Field::USAGE_CONDITIONAL, 22);
	$fields[6]  = new Fields\Generic(6, 'Truncation Indicator',                  Field::USAGE_CONDITIONAL, 37,  1, Field::TYPE_ALPHABETIC);
	$fields[7]  = new Fields\Generic(7, 'Endorsing Bank Conversion Indicator',   Field::USAGE_CONDITIONAL, 38,  1, Field::TYPE_ALPHAMERIC);
	$fields[8]  = new Fields\Generic(8, 'Endorsing Bank Correction Indicator',   Field::USAGE_CONDITIONAL, 39,  1, Field::TYPE_NUMERIC);
	$fields[9]  = new Fields\ReturnReason(9, Field::USAGE_CONDITIONAL, 40);
	$fields[10] = new Fields\User(10, 41,  15);
	$fields[11] = new Fields\Reserved(11, 56, 15);
	
	return $fields;
    }
}

/**
 * Return Record - Type 31
 */
class ReturnRecord extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::RETURN_RECORD);
	$fields[2]  = new Fields\Generic( 2, 'Payor Bank Routing Number',             Field::USAGE_MANDATORY,    3,  8, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\Generic( 3, 'Payor Bank Routing Number Check Digit', Field::USAGE_CONDITIONAL, 11,  1, Field::TYPE_NUMERICBLANKSPECIALMICR);
	$fields[4]  = new Fields\Generic( 4, 'On-Us Return Record',                   Field::USAGE_CONDITIONAL, 12, 20, Field::TYPE_NUMERICBLANKSPECIALMICRONUS);
	$fields[5]  = new Fields\Item(    5, 'Item',                                  Field::USAGE_MANDATORY,   32, 10);
	$fields[6]  = new Fields\ReturnReason(6, Field::USAGE_MANDATORY, 42);
	$fields[7]  = new Fields\Generic( 7, 'Return Reason Addendum Count',          Field::USAGE_MANDATORY,   43,  2, Field::TYPE_NUMERIC);
	$fields[8]  = new Fields\DocType(RecordType::RETURN_RECORD);
	$fields[9]  = new Fields\Date(9, 'Forward Bundle',                            Field::USAGE_CONDITIONAL, 46);
	$fields[10] = new Fields\ItemSequenceNumber(10, 'ECE Institution',            Field::USAGE_CONDITIONAL, 54);
	$fields[11] = new Fields\Generic(11, 'External Processing Code',              Field::USAGE_CONDITIONAL, 69,  1, Field::TYPE_ALPHAMERICSPECIAL);
	$fields[12] = new Fields\Generic(12, 'Return Notification Indicator',         Field::USAGE_CONDITIONAL, 70,  1, Field::TYPE_NUMERIC);
	$fields[13] = new Fields\Generic(13, 'Return Archive Type Indicator',         Field::USAGE_CONDITIONAL, 71,  1, Field::TYPE_ALPHAMERIC);
	$fields[14] = new Fields\Reserved(14, 72, 9);
	
	return $fields;
    }
}

/**
 * Return Addendum A - Type 32
 */
class ReturnAddendumA extends Record
{
    public static function defineFields()
    {
	$fields = array();

	$fields[1]  = new Fields\RecordType(RecordType::RETURN_ADDENDUM_A);
	$fields[2]  = new Fields\Generic( 2, 'Return Addendum A Record Number', Field::USAGE_MANDATORY,    3,  1, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\RoutingNumber(3, 'BOFD',                       Field::USAGE_CONDITIONAL,  4);
	$fields[4]  = new Fields\Date(4, 'BOFD Endorsement',                    Field::USAGE_CONDITIONAL, 13); // This field has some additional data maybe?
	$fields[5]  = new Fields\ItemSequenceNumber(5, 'BOFD',                  Field::USAGE_CONDITIONAL, 21);
	$fields[6]  = new Fields\DepositAccountNumber(6,                        Field::USAGE_CONDITIONAL, 36);
	$fields[7]  = new Fields\Generic( 7, 'BOFD Deposit Branch',             Field::USAGE_CONDITIONAL, 54,  5, Field::TYPE_ALPHAMERICSPECIAL);
	$fields[8]  = new Fields\NamePayee();
	$fields[9]  = new Fields\Generic( 9, 'Truncation Indicator',            Field::USAGE_CONDITIONAL, 74,  1, Field::TYPE_ALPHAMERIC);
	$fields[10] = new Fields\Generic(10, 'BOFD Conversion Indicator',       Field::USAGE_CONDITIONAL, 75,  1, Field::TYPE_ALPHAMERIC);
	$fields[11] = new Fields\Generic(11, 'BOFD Correction Indicator',       Field::USAGE_CONDITIONAL, 76,  1, Field::TYPE_NUMERIC);
	$fields[12] = new Fields\User(12, 77,  1);
	$fields[13] = new Fields\Reserved(13, 78, 3);
	
	return $fields;
    }
}

/**
 * Return Addendum B - Type 33
 */
class ReturnAddendumB extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::RETURN_ADDENDUM_B);
	$fields[2] = new Fields\NameInstitution(2, 'Payor Bank', 3);
	$fields[3] = new Fields\Generic(3, 'Auxiliary On-Us',       Field::USAGE_CONDITIONAL, 21, 15, Field::TYPE_NUMERICBLANKSPECIALMICR);
	$fields[4] = new Fields\ItemSequenceNumber(4, 'Payor Bank', Field::USAGE_CONDITIONAL, 36);
	$fields[5] = new Fields\Date(5, 'Payor Bank Business',      Field::USAGE_CONDITIONAL, 51,  8, Field::TYPE_NUMERIC);
	$fields[6] = new Fields\NamePayorAccount();
	
	return $fields;
    }
}

// Note: Return Addendum C - Type 34 is variable length and on seperate page.

/**
 * Return Addendum D - Type 35
 */
class ReturnAddendumD extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::RETURN_ADDENDUM_D);
	$fields[2]  = new Fields\Generic(2, 'Return Addendum D Record Number',     Field::USAGE_MANDATORY,    3,  2, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\RoutingNumber(3, 'Endorsing Bank',                Field::USAGE_CONDITIONAL,  5);
	$fields[4]  = new Fields\Date(4, 'Endorsing Bank Endorsement',             Field::USAGE_CONDITIONAL, 14);
	$fields[5]  = new Fields\ItemSequenceNumber(5, 'Endorsing Bank',           Field::USAGE_CONDITIONAL, 22);
	$fields[6]  = new Fields\Generic(6, 'Truncation Indicator',                Field::USAGE_CONDITIONAL, 37,  1, Field::TYPE_ALPHABETIC);
	$fields[7]  = new Fields\Generic(7, 'Endorsing Bank Conversion Indicator', Field::USAGE_CONDITIONAL, 38,  1, Field::TYPE_ALPHAMERIC);
	$fields[8]  = new Fields\Generic(8, 'Endorsing Bank Correction Indicator', Field::USAGE_CONDITIONAL, 39,  1, Field::TYPE_NUMERIC);
	$fields[9]  = new Fields\ReturnReason(9, Field::USAGE_CONDITIONAL, 40);
	$fields[10] = new Fields\User(10, 41,  15);
	$fields[11] = new Fields\Reserved(11, 56, 15);
	
	return $fields;
    }
}

/**
 * Account Totals Detail - Type 40
 */
class AccountTotalsDetail extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::ACCOUNT_TOTALS_DETAIL);
	$fields[2] = new Fields\RoutingNumber(2, 'Destination',                   Field::USAGE_MANDATORY,  5);
	$fields[3] = new Fields\Generic(3, 'Key Account / Low Account in Range',  Field::USAGE_MANDATORY, 12, 18, Field::TYPE_NUMERIC);
	$fields[4] = new Fields\Generic(4, 'Key Account / High Account in Range', Field::USAGE_MANDATORY, 30, 18, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\Generic(5, 'Total Item Count',                    Field::USAGE_MANDATORY, 48, 12, Field::TYPE_NUMERIC);
	$fields[6] = new Fields\Amount( 6, 'Total Item', 60, 14);
	$fields[7] = new Fields\User(7, 74, 4);
	$fields[8] = new Fields\Reserved(8, 78, 3);
	
	return $fields;
    }
}

/**
 * Non-Hit Totals Detail - Type 41
 */
class NonHitTotalsDetail extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::NON_HIT_TOTALS_DETAIL);
	$fields[2] = new Fields\RoutingNumber(2, 'Destination', Field::USAGE_MANDATORY,  5);
	$fields[3] = new Fields\Generic(3, 'Non-Hit Indicator', Field::USAGE_MANDATORY, 12, 01, Field::TYPE_ALPHAMERIC);
	$fields[4] = new Fields\Generic(4, 'Total Item Count',  Field::USAGE_MANDATORY, 13, 12, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\Amount( 5, 'Total Item', 25, 14);
	$fields[6] = new Fields\User(6, 39, 12);
	$fields[7] = new Fields\Reserved(7, 51, 30);
	
	return $fields;
    }
}

/**
 * Image View Detail Record - Type 50
 */
class ImageViewDetail extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::IMAGE_VIEW_DETAIL);
	$fields[2]  = new Fields\Generic(2, 'Image Indicator',                             Field::USAGE_MANDATORY,    3, 1, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\RoutingNumber(3, 'Image Creator',                         Field::USAGE_MANDATORY,    4);
	$fields[4]  = new Fields\Date(4, 'Image Creator',                                  Field::USAGE_MANDATORY,   13);
	$fields[5]  = new Fields\Generic( 5, 'Image View Format Indicator',                Field::USAGE_MANDATORY,   21, 2, Field::TYPE_NUMERICBLANK);
	$fields[6]  = new Fields\Generic( 6, 'Image View Compression Algorithm Identifer', Field::USAGE_MANDATORY,   23, 2, Field::TYPE_NUMERICBLANK);
	$fields[7]  = new Fields\Generic( 7, 'Image View Data Size',                       Field::USAGE_CONDITIONAL, 25, 7, Field::TYPE_NUMERIC);
	$fields[8]  = new Fields\Generic( 8, 'View Side Indicator',                        Field::USAGE_MANDATORY,   32, 1, Field::TYPE_NUMERIC);
	$fields[9]  = new Fields\Generic( 9, 'View Descriptor',                            Field::USAGE_MANDATORY,   33, 2, Field::TYPE_NUMERIC);
	$fields[10] = new Fields\Generic(10, 'Digital Signature Indicator',                Field::USAGE_MANDATORY,   35, 1, Field::TYPE_NUMERICBLANK);
	$fields[11] = new Fields\Generic(11, 'Digital Signature Method',                   Field::USAGE_MANDATORY,   36, 2, Field::TYPE_NUMERIC);
	$fields[12] = new Fields\Generic(12, 'Security Key Size',                          Field::USAGE_CONDITIONAL, 38, 5, Field::TYPE_NUMERIC);
	$fields[13] = new Fields\Generic(13, 'Start of Protected Data',                    Field::USAGE_CONDITIONAL, 43, 7, Field::TYPE_NUMERIC);
	$fields[14] = new Fields\Generic(14, 'Length of Protected Data',                   Field::USAGE_CONDITIONAL, 50, 7, Field::TYPE_NUMERIC);
	$fields[15] = new Fields\Generic(15, 'Image Recreate Indicator',                   Field::USAGE_CONDITIONAL, 57, 1, Field::TYPE_NUMERIC);
	$fields[16] = new Fields\User(16, 58, 8);
	$fields[17] = new Fields\Reserved(17, 66, 15);
	
	return $fields;
    }
}

/**
 * Image View Analysis Record - Type 54
 */
class ImageViewAnalysis extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\RecordType(RecordType::IMAGE_VIEW_ANALYSIS);
	$fields[2]  = new Fields\Generic(2, 'Global Image Qualilty',      Field::USAGE_MANDATORY, 3, 1, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\Generic(3, 'Global Image Usability',     Field::USAGE_MANDATORY, 4, 1, Field::TYPE_NUMERIC);
	$fields[4]  = new Fields\Generic(4, 'Imaging Bank Specific Test', Field::USAGE_MANDATORY, 5, 1, Field::TYPE_NUMERIC);

	// Image Quality Information (Fields 5-24)
	$fields[5]  = new Fields\ImageQualityInfo( 5, 'Partial Image',               6);
	$fields[6]  = new Fields\ImageInfoQuality( 6, 'Excessive Image Skew',        7);
	$fields[7]  = new Fields\ImageInfoQuality( 7, 'Piggyback Image',             8);
	$fields[8]  = new Fields\ImageInfoQuality( 8, 'Too Light or Too Dark',       9);
	$fields[9]  = new Fields\ImageInfoQuality( 9, 'Streaks and/or Bands',       10);
	$fields[10] = new Fields\ImageInfoQuality(10, 'Below Minimum Image Size',   11);
	$fields[11] = new Fields\ImageInfoQuality(11, 'Exceeds Maximum Image Size', 12);
	$fields[12] = new Fields\Reserved(12, 13, 1);
	$fields[13] = new Fields\Reserved(13, 14, 1);
	$fields[14] = new Fields\Reserved(14, 15, 1);
	$fields[15] = new Fields\Reserved(15, 16, 1);
	$fields[16] = new Fields\Reserved(16, 17, 1);
	$fields[17] = new Fields\Reserved(17, 18, 1);
	$fields[18] = new Fields\Reserved(18, 19, 1);
	$fields[19] = new Fields\Reserved(19, 20, 1);
	$fields[20] = new Fields\Reserved(20, 21, 1);
	$fields[21] = new Fields\Reserved(21, 22, 1);
	$fields[22] = new Fields\Reserved(22, 23, 1);
	$fields[23] = new Fields\Reserved(23, 24, 1);
	$fields[24] = new Fields\Reserved(24, 25, 1);

	// Image Usability Information (Fields 25-44)
	$fields[25] = new Fields\Generic(25, 'Image-Enabled POD',   Field::USAGE_CONDITIONAL, 26, 1, Field::TYPE_NUMERIC);
	$fields[26] = new Fields\Generic(26, 'Source Document Bad', Field::USAGE_CONDITIONAL, 27, 1, Field::TYPE_NUMERIC);
	$fields[27] = new Fields\ImageInfoUsability(27, 'Date Usability',                        28);
	$fields[28] = new Fields\ImageInfoUsability(28, 'Payee Usability',                       29);
	$fields[29] = new Fields\ImageInfoUsability(29, 'Convenience Amount Usability',          30);
	$fields[30] = new Fields\ImageInfoUsability(30, 'Legal Amount Usability',                31);
	$fields[31] = new Fields\ImageInfoUsability(31, 'Signature Usability',                   32);
	$fields[32] = new Fields\ImageInfoUsability(32, 'Payor Name and Address Usability',      33);
	$fields[33] = new Fields\ImageInfoUsability(33, 'MICR Line Usability',                   34);
	$fields[34] = new Fields\ImageInfoUsability(34, 'Memo Line Usability',                   35);
	$fields[35] = new Fields\ImageInfoUsability(35, 'Payor Bank Name and Address Usability', 36);
	$fields[36] = new Fields\ImageInfoUsability(36, 'Payee Endorsement Usability',           37);
	$fields[37] = new Fields\ImageInfoUsability(37, 'BOFD Endorsement Usability',            38);
	$fields[38] = new Fields\Reserved(38, 39, 1);
	$fields[39] = new Fields\Reserved(39, 40, 1);
	$fields[40] = new Fields\Reserved(40, 41, 1);
	$fields[41] = new Fields\Reserved(41, 42, 1);
	$fields[42] = new Fields\Reserved(42, 43, 1);
	$fields[43] = new Fields\Reserved(43, 44, 1);
	$fields[44] = new Fields\Reserved(44, 45, 1);
	
	// Image Analysis User Information (Field 45)
	$fields[45] = new Fields\User(45, 46, 20);
	$fields[46] = new Fields\Reserved(46, 66, 80);
	
	return $fields;
    }
}

/**
 * Bundle Control Record - Type 70
 */
class BundleControl extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::BUNDLE_CONTROL);
	$fields[2] = new Fields\Generic(2, 'Items Within Bundle Count',  Field::USAGE_MANDATORY,    3,  4, Field::TYPE_NUMERIC);
	$fields[3] = new Fields\Amount( 3, 'Bundle Total',      7, 12);
	$fields[4] = new Fields\Amount( 4, 'MICR Valid Total', 19, 12,   Field::USAGE_CONDITIONAL);
	$fields[5] = new Fields\Generic(5, 'Images within Bundle Count', Field::USAGE_CONDITIONAL, 31,  5, Field::TYPE_NUMERIC);
	$fields[6] = new Fields\User(6, 36, 20);
	$fields[7] = new Fields\Reserved(7, 56, 25);
	
	return $fields;
    }
}

/**
 * Box Summary - Type 75
 */
class BoxSummary extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::BOX_SUMMARY);
	$fields[2] = new Fields\RoutingNumber(2, 'Destination',   Field::USAGE_MANDATORY, 03);
	$fields[3] = new Fields\Generic(3, 'Box Sequence Number', Field::USAGE_MANDATORY, 12,  3, Field::TYPE_NUMERIC);
	$fields[4] = new Fields\Generic(4, 'Box Bundle Count',    Field::USAGE_MANDATORY, 15,  4, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\Generic(5, 'Box Number ID',       Field::USAGE_MANDATORY, 19,  8, Field::TYPE_NUMERIC);
	$fields[6] = new Fields\Amount( 6, 'Box Total', 27, 14);
	$fields[7] = new Fields\Reserved(7, 41, 40);
	
	return $fields;
    }
}

/**
 * Routing Number Summary - Type 85
 */
class RoutingNumberSummary extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::ROUTING_NUMBER_SUMMARY);
	$fields[2] = new Fields\RoutingNumber(2, 'Within Cash Letter',    Field::USAGE_MANDATORY,  3);
	$fields[3] = new Fields\Amount( 3, 'Routing Number Total', 12, 14);
	$fields[4] = new Fields\Generic(4, 'Routing Number Item Count',   Field::USAGE_MANDATORY, 26,  6, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\User(5, 32, 24);
	$fields[6] = new Fields\Reserved(6, 56, 25);
	
	return $fields;
    }
}

/**
 * Cash Letter Control Record - Type 90
 */
class CashLetterControl extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::CASH_LETTER_CONTROL);
	$fields[2] = new Fields\Generic(2, 'Bundle Count',                    Field::USAGE_MANDATORY,    3,  6, Field::TYPE_NUMERIC);
	$fields[3] = new Fields\Generic(3, 'Items Within Cash Letter Count',  Field::USAGE_MANDATORY,    9, 16, Field::TYPE_NUMERIC);
	$fields[4] = new Fields\Amount( 4, 'Cash Letter Total', 17, 14);
	$fields[5] = new Fields\Generic(5, 'Images Within Cash Letter Count', Field::USAGE_CONDITIONAL, 31,  9, Field::TYPE_ALPHABETIC);
	$fields[6] = new Fields\NameInstitution(6, 'ECE', 40);
	$fields[7] = new Fields\Date(7, 'Settlement',                         Field::USAGE_CONDITIONAL, 58);
	$fields[8] = new Fields\Reserved(8, 66, 15);
	
	return $fields;
    }
}

/**
 * File Control Record - Type 99
 */
class FileControl extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1] = new Fields\RecordType(RecordType::FILE_CONTROL);
	$fields[2] = new Fields\Generic(2, 'Cash Letter Count',                 Field::USAGE_MANDATORY,    3,  6, Field::TYPE_NUMERIC);
	$fields[3] = new Fields\Generic(3, 'Total Record Count',                Field::USAGE_MANDATORY,    9,  6, Field::TYPE_NUMERIC);
	$fields[4] = new Fields\Generic(4, 'Total Item Count',                  Field::USAGE_MANDATORY,   17,  8, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\Amount( 5, 'File Total', 25, 16);
	$fields[6] = new Fields\NameContact(6, 'Immediate Origin', 41, 14);
	$fields[7] = new Fields\PhoneNumber(7, 'Immediate Origin Contact',      Field::USAGE_CONDITIONAL, 55);
	$fields[8] = new Fields\Reserved(8, 65, 16);
	
	return $fields;
    }
}