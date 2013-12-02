<?php

require_once 'X937Record.php';
require_once 'X937RecordTypes.php';
require_once 'X937RecordVariableLength.php';
require_once 'X937FieldPredefined.php';

/**
 * A factor class to generate new X937Records from different sorts of input.
 *
 * @author astanley
 */
class X937RecordFactory {
    
    public static function handledRecordTypes() {
	$handledRecordTypes = X937FieldRecordType::defineValues();

	unset($handledRecordTypes[X937FieldRecordType::IMAGE_VIEW_ANALYSIS]);
	
	return $handledRecordTypes;
    }

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
	    case X937FieldRecordType::CHECK_DETAIL_ADDENDUM_B:
		return new X937RecordCheckDetailAddendumB($recordType, $recordData);
		break;
	    case X937FieldRecordType::CHECK_DETAIL_ADDENDUM_C:
		return new X937RecordCheckDetailAddendumC($recordType, $recordData);
		break;	    

	    case X937FieldRecordType::RETURN_RECORD:
		return new X937RecordReturnRecord($recordType, $recordData);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_A:
		return new X937RecordReturnAddendumA($recordType, $recordData);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_B:
		return new X937RecordReturnAddendumB($recordType, $recordData);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_C:
		return new X937RecordReturnAddendumC($recordType, $recordData);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_D:
		return new X937RecordReturnAddendumD($recordType, $recordData);
		break;
	    
	    case X937FieldRecordType::IMAGE_VIEW_DETAIL:
		return new X937RecordImageViewDetail($recordType, $recordData);
		break;
	    case X937FieldRecordType::IMAGE_VIEW_DATA:
		return new X937RecordImageViewData($recordType, $recordData);
		break;
	    
	    /*
	     * @todo implment Image View Analysis - Type 54
	     */

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
		return new X937RecordGeneric($recordType, $recordData);
		break;
	}
    }
}