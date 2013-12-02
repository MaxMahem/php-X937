<?php

require_once 'X937Record.php';
require_once 'X937FieldPredefined.php';

/**
 * A factor class to generate new X937Records from different sorts of input.
 *
 * @author astanley
 */
class X937RecordFactory {
    /**
     * Generates an appropriate X937 Record from the raw record data.
     * @param type $recordData raw Record data as from the X937 file, generally EBCDIC format.
     * @return X937Record returns an X937Record of the appropriate type.
     */
    public static function newRecordFromRawData($recordData) {
        $recordTypeEBCDIC = substr($recordData, 0, 2);
        $recordTypeASCII  = iconv('EBCDIC-US', 'ASCII', $recordTypeEBCDIC);
    
	return self::newRecord($recordTypeASCII, $recordData);
    }

    private static function newRecord($recordType, $recordData) {
	if (array_key_exists($recordType, X937FieldRecordType::defineValues()) === FALSE) {
	    throw new InvalidArgumentException("Bad record type passed.");
	}

	switch ($recordType) {
	    case X937FieldRecordType::FILE_HEADER:
		return new X937RecordFileHeader($recordType, $recordData);
		break;
	    case X937FieldRecordType::CASH_LETTER_HEADER:
		return new X937RecordCashLetterHeader($recordType, $recordData);
		break;
	    case X937FieldRecordType::BUNDLE_HEADER:
		return new X937RecordBundleHeader($recordType, $recordData);
		break;
	    case X937FieldRecordType::CHECK_DETAIL:
		return new X937RecordCheckDetail($recordType, $recordData);
		break;
	    case X937FieldRecordType::CHECK_DETAIL_ADDENDUM_A:
		return new X937RecordCheckDetailAddendumA($recordType, $recordData);
		break;

	    // more to be inserted

	    case X937FieldRecordType::BUNDLE_CONTROL:
		return new X937RecordBundleControl($recordType, $recordData);
		break;
	    case X937FieldRecordType::BOX_SUMMARY:
		return new X937RecordBoxSummary($recordType, $recordData);
		break;
	    case X937FieldRecordType::ROUTING_NUMBER_SUMMARY:
		return new X937RecordRoutingNumberSummary($recordType, $recordData);
		break;
	    case X937FieldRecordType::CASH_LETTER_CONTROL:
		return new X937RecordCashLetterControl($recordType, $recordData);
		break;
	    case X937FieldRecordType::FILE_CONTROL:
		return new X937RecordFileControl($recordType, $recordData);
		break;
	    default:
		return new X937Record($recordType, $recordData);
		break;
	}
    }
}