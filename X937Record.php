<?php
/**
 * X937Records represent a single variable length line of a X937 file.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3 (or later)
 * @copyright Copyright (c) 2013, Austin Stanley
 */
class X937Record {
    /**
     * The type of the record. Should be one of the class constants.
     * @var int
     */
    protected $recordType;

    /**
     * The raw record string. Generally EBCDIC data.
     * @var string
     */
    protected $recordData;
    
    /**
     * The length of the record. In bytes
     * @var int 
     */
    protected $recordLength;

    /**
     * The record string in ASCII format.
     * @var string
     */
    protected $recordASCII;

    /**
     * Contains all the field in the record. Indexed by field number, which
     * represents the fields position in the record. Starting at 1.
     * @var array contains all the fields in the record.
     */
    protected $fields;

    /**
     * Reference array that links field name => filed number.
     * @var array
     */
    protected $fieldsRef;

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

    /**
     * Creates a X937Record. Basic input validation, currently ignores TIFF data.
     * Calls addFields which should be overriden in a subclass to add all the
     * fields to the record. And then calls all those fields parseValue function
     * to parse in the data.
     * @param type $recordTypeASCII the type of the record, in ASCII. Should be
     * one of the class constants.
     * @param type $recordData the raw data for the record. In EBCDIC/Binary
     * @throws InvalidArgumentException If given bad input.
     */
    public function __construct($recordTypeASCII, $recordData) {
	// input validation
        if (!is_string($recordData)) { throw new InvalidArgumentException("Bad record: $recordData passed to new X937Record"); }

        $this->recordType = $recordTypeASCII;

        // check for the IMAGE_VIEW_DETAIL Record type. This is a TIFF record, and in this case we only want the first 117 bytes of EBCDIC data,
        // the rest is TIFF.
        if ($this->recordType == X937Record::IMAGE_VIEW_DATA) {
            $this->recordData  = substr($recordData, 0, 117);
            $this->recordASCII = iconv('EBCDIC-US', 'ASCII', substr($recordData, 0, 117));
	} else {
            $this->recordData  = $recordData;
            $this->recordASCII = iconv('EBCDIC-US', 'ASCII', $recordData);
	}

	$this->addFields();

        foreach ($this->fields as $field) {
            $field->parseValue($this->recordASCII);
	}
    }

    /**
     * Returns record type array.
     */
    public static function getRecordTypes() {
        $recordTypes[] = self::FILE_HEADER;
        $recordTypes[] = self::CASH_LETTER_HEADER;
        $recordTypes[] = self::BUNDLE_HEADER;
        $recordTypes[] = self::CHECK_DETAIL;
        $recordTypes[] = self::CHECK_DETAIL_ADDENDUM_A;
        $recordTypes[] = self::CHECK_DETAIL_ADDENDUM_B;
        $recordTypes[] = self::CHECK_DETAIL_ADDENDUM_C;
        $recordTypes[] = self::RETURN_RECORD;
        $recordTypes[] = self::RETURN_ADDENDUM_A;
        $recordTypes[] = self::RETURN_ADDENDUM_B;
        $recordTypes[] = self::RETURN_ADDENDUM_C;
        $recordTypes[] = self::RETURN_ADDENDUM_D;
        $recordTypes[] = self::ACCOUNT_TOTALS_DETAIL;
        $recordTypes[] = self::NON_HIT_TOTALS_DETAIL;
        $recordTypes[] = self::IMAGE_VIEW_DETAIL;
        $recordTypes[] = self::IMAGE_VIEW_DATA;
        $recordTypes[] = self::IMAGE_VIEW_ANALYSIS;
        $recordTypes[] = self::BUNDLE_CONTROL;
        $recordTypes[] = self::BOX_SUMMARY;
        $recordTypes[] = self::ROUTING_NUMBER_SUMMARY;
        $recordTypes[] = self::CASH_LETTER_CONTROL;
        $recordTypes[] = self::FILE_CONTROL;
	
	return $recordTypes;
    }

    /**
     * Get the Record Type, should be one of the class constents.
     * @return int The record type of the record.
     */
    public function getRecordType()  { return $this->recordType; }
    public function getRecordData()  { return $this->recordData; }
    public function getRecordASCII() { return $this->recordASCII; }
    public function getFields()      { return $this->fields; }

    public function getFieldByNumber($fieldNumber) { return $this->fields[$fieldNumber]; }
    public function getFieldByName($fieldName)     { return $this->fields[$this->fieldsRef[$fieldName]]; }

    protected function addFields() { $this->fields = array(); }

    /**
     * Adds a X937Field (or one of it's subclasses) to the Record.
     * @param X937Field $field
     */
    protected function addField(X937Field $field) {
        $this->fields[$field->getFieldNumber()]  = $field;
	
	// update fieldRef with pointer to correct position.
	$this->fieldsRef[$field->getFieldName()] = $field->getFieldNumber();
    }
}

// File Header Record - Type 01
class X937RecordFileHeader  extends X937Record {
    protected function addFields() {
	$this->addField(new X937FieldRecordType(X937Record::FILE_HEADER));
	$this->addField(new X937Field(2,  'Specification Level',                  X937Field::MANDATORY,    3,  2, X937Field::NUMERIC));
	$this->addField(new X937Field(3,  'Test File Indicator',                  X937Field::MANDATORY,    5,  1, X937Field::ALPHABETIC));
	$this->addField(new X937FieldRoutingNumber(4, 'Immediate Destination',    X937Field::MANDATORY,    6));
	$this->addField(new X937FieldRoutingNumber(5, 'Immediate Origin',         X937Field::MANDATORY,   15));
	$this->addField(new X937FieldDate(6, 'File Creation Date',                X937Field::MANDATORY,   24));
	$this->addField(new X937FieldTime(7, 'File Creation Time',                X937Field::MANDATORY,   32));
	$this->addField(new X937Field(8,  'Resend Indicator',                     X937Field::MANDATORY,   36,  1, X937Field::NUMERIC));
	$this->addField(new X937FieldInstitutionName( 9, 'Immediate Destination', X937Field::CONDITIONAL, 37));
	$this->addField(new X937FieldInstitutionName(10, 'Immediate Origin',      X937Field::CONDITIONAL, 55));
	$this->addField(new X937Field(11, 'File ID Modifer',                      X937Field::CONDITIONAL, 73,  1, X937Field::ALPHAMERIC));
	$this->addField(new X937Field(12, 'Country Code',                         X937Field::CONDITIONAL, 74,  2, X937Field::ALPHABETIC));
	$this->addField(new X937FieldUser(13, 76,  4));
	$this->addField(new X937FieldReserved(14, 80,  1));
    }
}

// Cash Letter Header Record - Type 10
class X937RecordCashLetterHeader  extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::CASH_LETTER_HEADER));
		$this->addField(new X937Field(2,  'Collection Type Indicator',                X937Field::MANDATORY,    3,  2, X937Field::NUMERIC));
		$this->addField(new X937FieldRoutingNumber(3, 'Destination',                  X937Field::MANDATORY,    5));
		$this->addField(new X937FieldRoutingNumber(4, 'ECE Instituion',               X937Field::MANDATORY,   14));
		$this->addField(new X937FieldDate(5, 'Cash Letter Business Date',             X937Field::MANDATORY,   23));
		$this->addField(new X937FieldDate(6, 'Cash Letter Creation Date',             X937Field::MANDATORY,   31));
		$this->addField(new X937FieldTime(7, 'Cash Letter Creation Time',             X937Field::MANDATORY,   39));
		$this->addField(new X937Field(8,  'Cash Letter Record Type Indicator',        X937Field::MANDATORY,   43,  1, X937Field::ALPHABETIC));
		$this->addField(new X937Field(9,  'Cash Letter Documentation Type Indicator', X937Field::CONDITIONAL, 44, 18, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(10, 'Cash Letter ID',                           X937Field::CONDITIONAL, 45,  8, X937Field::ALPHAMERIC));
		$this->addField(new X937FieldContactName(11, 'Originator Contact Name',       X937Field::CONDITIONAL, 53));
		$this->addField(new X937FieldPhoneNumber(12, 'Originator Contact',            X937Field::CONDITIONAL, 67));
		$this->addField(new X937Field(13, 'Fed Work Type',                            X937Field::CONDITIONAL, 77,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937FieldUser(14, 78,  2));
		$this->addField(new X937FieldReserved(14, 80,  1));
	}
}

// Bundle Header Record - Type 20
class X937RecordBundleHeader extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::BUNDLE_HEADER));
		$this->addField(new X937Field( 2, 'Collection Type Indicator',    X937Field::MANDATORY,    3,  2, X937Field::NUMERIC));
		$this->addField(new X937FieldRoutingNumber( 3, 'Destination',     X937Field::MANDATORY,    5));
		$this->addField(new X937FieldRoutingNumber( 4, 'ECE Institution', X937Field::MANDATORY,   14));
		$this->addField(new X937FieldDate(5, 'Bundle Business Date',      X937Field::MANDATORY,   23));
		$this->addField(new X937FieldDate(6, 'Bundle Creation Date',      X937Field::MANDATORY,   31));
		$this->addField(new X937Field( 7, 'Bundle ID',                    X937Field::CONDITIONAL, 39, 10, X937Field::ALPHAMERIC));
		$this->addField(new X937Field( 8, 'Bundle Sequence Number',       X937Field::CONDITIONAL, 49,  4, X937Field::NUMERICBLANK));
		$this->addField(new X937Field( 9, 'Cycle Number',                 X937Field::CONDITIONAL, 53,  2, X937Field::ALPHAMERIC));
		$this->addField(new X937FieldRoutingNumber(10, 'Return Location', X937Field::CONDITIONAL, 55));
		$this->addField(new X937FieldUser(11, 64,  5));
		$this->addField(new X937FieldReserved(12, 69, 12));
	}
}

// Check Detail Record - Type 25
class X937RecordCheckDetail extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::CHECK_DETAIL));
		$this->addField(new X937Field( 2, 'Auxiliary On-Us',                       X937Field::CONDITIONAL,  3, 15, X937Field::NUMERIC));
		$this->addField(new X937Field( 3, 'External Processing Code',              X937Field::CONDITIONAL, 18,  1, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937Field( 4, 'Payor Bank Routing Number',             X937Field::MANDATORY,   19,  8, X937Field::NUMERIC));
		$this->addField(new X937Field( 5, 'Payor Bank Routing Number Check Digit', X937Field::CONDITIONAL, 27,  1, X937Field::NUMERICBLANKSPECIALMICR));
		$this->addField(new X937Field( 6, 'On-Us',                                 X937Field::MANDATORY,   28, 20, X937Field::NUMERICBLANKSPECIALMICRONUS));
		$this->addField(new X937FieldItemAmount(7, 48));
		$this->addField(new X937FieldItemSequenceNumber(8, 'ECE Institution',      X937Field::MANDATORY, 58));
		$this->addField(new X937Field( 9, 'Documentation Type Indicator',          X937Field::CONDITIONAL, 73,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(10, 'Return Acceptance Indicator',           X937Field::CONDITIONAL, 74,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(11, 'MICR Valid Indicator',                  X937Field::CONDITIONAL, 75,  1, X937Field::NUMERIC));
		$this->addField(new X937Field(12, 'BOFD Indicator',                        X937Field::MANDATORY,   76,  1, X937Field::ALPHABETIC));
		$this->addField(new X937Field(13, 'Check Detail Record Addendum Count',    X937Field::MANDATORY,   77,  2, X937Field::NUMERIC));
		$this->addField(new X937Field(14, 'Correction Indicator',                  X937Field::CONDITIONAL, 79,  1, X937Field::NUMERIC));
		$this->addField(new X937Field(14, 'Archive Type Indicator',                X937Field::CONDITIONAL, 80,  1, X937Field::ALPHAMERIC));
	}
}

// Check Detail Record - Type 26
class X937RecordCheckDetailAddendumA extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::CHECK_DETAIL_ADDENDUM_A));
		$this->addField(new X937Field( 2, 'Check Detail Addendum A Record Number', X937Field::MANDATORY,    3,  1, X937Field::NUMERIC));
		$this->addField(new X937FieldRoutingNumber(3, 'BOFD',                      X937Field::CONDITIONAL,  4));
		$this->addField(new X937FieldDate(4, 'BOFD Endorsement Date',              X937Field::MANDATORY, 13));
		$this->addField(new X937FieldItemSequenceNumber( 5, 'BOFD',                X937Field::CONDITIONAL, 21));
		$this->addField(new X937FieldDepositAccountNumber(6,                       X937Field::CONDITIONAL, 36));
		$this->addField(new X937Field( 7, 'BOFD Deposit Branch',                   X937Field::CONDITIONAL, 54,  5, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937Field( 8, 'Payee Name',                            X937Field::CONDITIONAL, 59, 15, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937Field( 9, 'Truncation Indicator',                  X937Field::CONDITIONAL, 74,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(10, 'BOFD Conversion Indicator',             X937Field::CONDITIONAL, 75,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(11, 'BOFD Correction Indicator',             X937Field::CONDITIONAL, 76,  1, X937Field::NUMERIC));
		$this->addField(new X937FieldUser(12, 77,  1));
		$this->addField(new X937FieldReserved(13, 78, 3));
	}
}

// Check Detail Record - Type 28
class X937RecordCheckDetailAddendumC extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::CHECK_DETAIL_ADDENDUM_C));
		$this->addField(new X937Field(2, 'Check Detail Addendum C Record Number', X937Field::MANDATORY,    3,  2, X937Field::NUMERIC));
		$this->addField(new X937FieldRoutingNumber(3, 'Endorsing Bank',           X937Field::CONDITIONAL,  5));
		$this->addField(new X937FieldDate(4, 'Endorsing Bank Endorsement Date',   X937Field::CONDITIONAL, 14));
		$this->addField(new X937FieldItemSequenceNumber(5, 'Endorsing Bank',      X937Field::CONDITIONAL, 22));
		$this->addField(new X937Field(6, 'Truncation Indicator',                  X937Field::CONDITIONAL, 37,  1, X937Field::ALPHABETIC));
		$this->addField(new X937Field(7, 'Endorsing Bank Conversion Indicator',   X937Field::CONDITIONAL, 38,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(8, 'Endorsing Bank Correction Indicator',   X937Field::CONDITIONAL, 39,  1, X937Field::NUMERIC));
		$this->addField(new X937FieldReturnReason(9, X937Field::CONDITIONAL, 40));
		$this->addField(new X937FieldUser(10, 41,  15));
		$this->addField(new X937FieldReserved(11, 56, 15));
	}
}

// Return Record - Type 31
class X937RecordReturnRecord extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::RETURN_RECORD));
		$this->addField(new X937Field( 2, 'Payor Bank Routing Number',             X937Field::MANDATORY,    3,  8, X937Field::NUMERIC));
		$this->addField(new X937Field( 3, 'Payor Bank Routing Number Check Digit', X937Field::CONDITIONAL, 11,  1, X937Field::NUMERICBLANKSPECIALMICR));
		$this->addField(new X937Field( 4, 'On-Us Return Record',                   X937Field::CONDITIONAL, 12, 20, X937Field::NUMERICBLANKSPECIALMICRONUS));
		$this->addField(new X937FieldItemAmount(5, 32));
		$this->addField(new X937FieldReturnReason(6, X937Field::MANDATORY, 42));
		$this->addField(new X937Field( 7, 'Return Reason Addendum Count',          X937Field::MANDATORY,   43,  2, X937Field::NUMERIC));
		$this->addField(new X937Field( 8, 'Return Documentation Type Indicator',   X937Field::CONDITIONAL, 45,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937FieldDate(9, 'Forward Bundle Date',                X937Field::CONDITIONAL, 46));
		$this->addField(new X937FieldItemSequenceNumber(10, 'ECE Institution',     X937Field::CONDITIONAL, 54));
		$this->addField(new X937Field(11, 'External Processing Code',              X937Field::CONDITIONAL, 69,  1, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937Field(12, 'Return Notification Indicator',         X937Field::CONDITIONAL, 70,  1, X937Field::NUMERIC));
		$this->addField(new X937Field(13, 'Return Archive Type Indicator',         X937Field::CONDITIONAL, 71,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937FieldReserved(14, 72, 9));
	}
}

// Return Addendum A - Type 32
class X937RecordReturnAddendumA extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::RETURN_ADDENDUM_A));
		$this->addField(new X937Field( 2, 'Return Addendum A Record Number', X937Field::MANDATORY,    3,  1, X937Field::NUMERIC));
		$this->addField(new X937FieldRoutingNumber(3, 'BOFD',                X937Field::CONDITIONAL,  4));
		$this->addField(new X937FieldDate(4, 'BOFD Endorsement Date',        X937Field::CONDITIONAL, 13)); // This field has some additional data maybe?
		$this->addField(new X937FieldItemSequenceNumber(5, 'BOFD',           X937Field::CONDITIONAL, 21));
		$this->addField(new X937FieldDepositAccountNumber(6,                 X937Field::CONDITIONAL, 36));
		$this->addField(new X937Field( 7, 'BOFD Deposit Branch',             X937Field::CONDITIONAL, 54,  5, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937Field( 8, 'Payee Name',                      X937Field::CONDITIONAL, 59, 15, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937Field( 9, 'Truncation Indicator',            X937Field::CONDITIONAL, 74,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(10, 'BOFD Conversion Indicator',       X937Field::CONDITIONAL, 75,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(11, 'BOFD Correction Indicator',       X937Field::CONDITIONAL, 76,  1, X937Field::NUMERIC));
		$this->addField(new X937FieldUser(12, 77,  1));
		$this->addField(new X937FieldReserved(13, 78, 3));
	}
}

// Return Addendum B - Type 33
class X937RecordReturnAddendumB extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::RETURN_ADDENDUM_B));
		$this->addField(new X937FieldInstitutionName(2, 'Payor Bank',    X937Field::CONDITIONAL,  3));
		$this->addField(new X937Field(3, 'Auxiliary On-Us',              X937Field::CONDITIONAL, 21, 15, X937Field::NUMERICBLANKSPECIALMICR));
		$this->addField(new X937FieldItemSequenceNumber(4, 'Payor Bank', X937Field::CONDITIONAL, 36));
		$this->addField(new X937FieldDate(5, 'Payor Bank Business Date', X937Field::CONDITIONAL, 51,  8, X937Field::NUMERIC));
		$this->addField(new X937Field(6, 'Payor Account Name',           X937Field::CONDITIONAL, 59, 22, X937Field::ALPHAMERICSPECIAL));
	}
}

// Return Addendum D - Type 35
class X937RecordReturnAddendumD extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::RETURN_ADDENDUM_D));
		$this->addField(new X937Field(2, 'Return Addendum D Record Number',     X937Field::MANDATORY,    3,  2, X937Field::NUMERIC));
		$this->addField(new X937FieldRoutingNumber(3, 'Endorsing Bank',         X937Field::CONDITIONAL,  5));
		$this->addField(new X937FieldDate(4, 'Endorsing Bank Endorsement Date', X937Field::CONDITIONAL, 14));
		$this->addField(new X937FieldItemSequenceNumber(5, 'Endorsing Bank',    X937Field::CONDITIONAL, 22));
		$this->addField(new X937Field(6, 'Truncation Indicator',                X937Field::CONDITIONAL, 37,  1, X937Field::ALPHABETIC));
		$this->addField(new X937Field(7, 'Endorsing Bank Conversion Indicator', X937Field::CONDITIONAL, 38,  1, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(8, 'Endorsing Bank Correction Indicator', X937Field::CONDITIONAL, 39,  1, X937Field::NUMERIC));
		$this->addField(new X937FieldReturnReason(9, X937Field::CONDITIONAL, 40));
		$this->addField(new X937FieldUser(10, 41,  15));
		$this->addField(new X937FieldReserved(11, 56, 15));
	}
}

// Account Totals Detail - Type 40
class X937RecordAccountTotalsDetail extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::ACCOUNT_TOTALS_DETAIL));
		$this->addField(new X937FieldRoutingNumber(2, 'Destination',            X937Field::MANDATORY,  5));
		$this->addField(new X937Field(3, 'Key Account / Low Account in Range',  X937Field::MANDATORY, 12, 18, X937Field::NUMERIC));
		$this->addField(new X937Field(4, 'Key Account / High Account in Range', X937Field::MANDATORY, 30, 18, X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Total Item Count',                    X937Field::MANDATORY, 48, 12, X937Field::NUMERIC));
		$this->addField(new X937Field(6, 'Total Item Amount',                   X937Field::MANDATORY, 60, 14, X937Field::NUMERIC));
		$this->addField(new X937FieldUser(7, 74, 4));
		$this->addField(new X937FieldReserved(8, 78, 3));
	}
}

// Non-Hit Totals Detail - Type 41
class X937RecordNonHitTotalsDetail extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::NON_HIT_TOTALS_DETAIL));
		$this->addField(new X937FieldRoutingNumber(2, 'Destination', X937Field::MANDATORY,  5));
		$this->addField(new X937Field(3, 'Non-Hit Indicator',        X937Field::MANDATORY, 12, 01, X937Field::ALPHAMERIC));
		$this->addField(new X937Field(4, 'Total Item Count',         X937Field::MANDATORY, 13, 12, X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Total Item Amount',        X937Field::MANDATORY, 25, 14, X937Field::NUMERIC));
		$this->addField(new X937FieldUser(6, 39, 12));
		$this->addField(new X937FieldReserved(7, 51, 30));
	}
}

// Bundle Control Record - Type 70
class X937RecordBundleControl extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::BUNDLE_CONTROL));
		$this->addField(new X937Field(2, 'Items Within Bundle Count',  X937Field::MANDATORY,    3,  4, X937Field::NUMERIC));
		$this->addField(new X937Field(3, 'Bundle Total Amount',        X937Field::MANDATORY,    7, 12, X937Field::NUMERIC));
		$this->addField(new X937Field(4, 'MICR Valid Total Amount',    X937Field::CONDITIONAL, 19, 12, X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Images within Bundle Count', X937Field::CONDITIONAL, 31,  5, X937Field::NUMERIC));
		$this->addField(new X937FieldUser(6, 36, 20));
		$this->addField(new X937FieldReserved(7, 56, 25));
	}
}

// Box Summary - Type 75
class X937RecordBoxSummary extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::BOX_SUMMARY));
		$this->addField(new X937FieldRoutingNumber(2, 'Destination', X937Field::MANDATORY, 03));
		$this->addField(new X937Field(3, 'Box Sequence Number',      X937Field::MANDATORY, 12,  3, X937Field::NUMERIC));
		$this->addField(new X937Field(4, 'Box Bundle Count',         X937Field::MANDATORY, 15,  4, X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Box Number ID',            X937Field::MANDATORY, 19,  8, X937Field::NUMERIC));
		$this->addField(new X937Field(6, 'Box Total Amount',         X937Field::MANDATORY, 27, 14, X937Field::NUMERIC));
		$this->addField(new X937FieldReserved(7, 41, 40));
	}
}

// Routing Number Summary - Type 85
class X937RecordRoutingNumberSummary extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::ROUTING_NUMBER_SUMMARY));
		$this->addField(new X937FieldRoutingNumber(2, 'Within Cash Letter', X937Field::MANDATORY,  3));
		$this->addField(new X937Field(3, 'Routing Number Total Amount',     X937Field::MANDATORY, 12, 14, X937Field::NUMERIC));
		$this->addField(new X937Field(4, 'Routing Number Item Count',       X937Field::MANDATORY, 26,  6, X937Field::NUMERIC));
		$this->addField(new X937FieldUser(5, 32, 24));
		$this->addField(new X937FieldReserved(6, 56, 25));
	}
}

// Cash Letter Control Record - Type 90
class X937RecordCashLetterControl extends X937Record {
	protected function addFields() {
		$this->addField(new X937FieldRecordType(X937Record::CASH_LETTER_CONTROL));
		$this->addField(new X937Field(2, 'Bundle Count',                    X937Field::MANDATORY,    3,  6, X937Field::NUMERIC));
		$this->addField(new X937Field(3, 'Items Within Cash Letter Count',  X937Field::MANDATORY,    9, 16, X937Field::NUMERIC));
		$this->addField(new X937Field(4, 'Cash Letter Total Amount',        X937Field::MANDATORY,   17, 14, X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Images Within Cash Letter Count', X937Field::CONDITIONAL, 31,  9, X937Field::ALPHABETIC));
		$this->addField(new X937FieldInstitutionName(6, 'ECE Institution',  X937Field::CONDITIONAL, 40));
		$this->addField(new X937FieldDate(7, 'Settlement Date',             X937Field::CONDITIONAL, 58));
		$this->addField(new X937FieldReserved(8, 66, 15));
	}
}

// File Control Record - Type 99
class X937RecordFileControl extends X937Record {
    protected function addFields() {
	$this->addField(new X937FieldRecordType(X937Record::FILE_CONTROL));
	$this->addField(new X937Field(2, 'Cash Letter Count',                        X937Field::MANDATORY,    3,  6, X937Field::NUMERIC));
	$this->addField(new X937Field(3, 'Total Record Count',                       X937Field::MANDATORY,    9,  6, X937Field::NUMERIC));
	$this->addField(new X937Field(4, 'Total Item Count',                         X937Field::MANDATORY,   17,  8, X937Field::NUMERIC));
	$this->addField(new X937Field(5, 'File Total Amount',                        X937Field::MANDATORY,   25, 16, X937Field::NUMERIC));
	$this->addField(new X937FieldContactName(6, 'Immediate Origin Contact Name', X937Field::CONDITIONAL, 41));
	$this->addField(new X937FieldPhoneNumber(7, 'Immediate Origin Contact',      X937Field::CONDITIONAL, 55));
	$this->addField(new X937FieldReserved(8, 65, 16));
    }
}
