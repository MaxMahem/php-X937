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
    
    /**
     * Returns an array of currently handled record types.
     * @return array An array of handled types.
     */
    public static function handledRecordTypes() {
	$handledRecordTypes = X937FieldRecordType::defineValues();

	unset($handledRecordTypes[X937FieldRecordType::IMAGE_VIEW_ANALYSIS]);
	
	return $handledRecordTypes;
    }

    /**
     * Generates an appropriate X937 Record from the raw record data.
     * @param string $recordData raw Record data as from the X937 file, generally EBCDIC format.
     * @param string $dataType   the type of the data a X937File const, either DATA_EBCDIC or DATA_ASCII
     * @return X937Record returns an X937Record of the appropriate type.
     */
    public static function newRecordFromRawData($recordData, $dataType) {
	$recordTypeRaw = substr($recordData, 0, 2);
	
	switch ($dataType) {
	    case X937File::DATA_ASCII:
		$recordType = $recordTypeRaw;
		break;
	    case X937File::DATA_EBCDIC:
		$recordType = iconv(X937File::DATA_EBCDIC, X937File::DATA_ASCII, $recordTypeRaw);
		break;
	    default:
		throw new InvalidArgumentException("Bad dataType passed.");
		break;
	}
    
	return self::newRecord($recordType, $recordData, $dataType);
    }

    /**
     * Creates an new X937Record from appropriate input.
     * @param string $recordType the type of the record, in ASCII
     * @param string $recordData the raw record data
     * @param string $dataType the type of the record, X937File const either DATA_EBCDIC or DATA_ASCII
     * @return X937Record an approriate record type
     * @throws InvalidArgumentException if given bad data
     */
    private static function newRecord($recordType, $recordData, $dataType = X937File::DATA_EBCDIC) {
	if (array_key_exists($recordType, X937FieldRecordType::defineValues()) === FALSE) {
	    throw new InvalidArgumentException("Bad record type passed.");
	}
	
	// convert the record data if necessary.
	/**
	 * @todo consider how to handle binary data here? Or push this conversion elseware.
	 */
	if ($dataType === X937File::DATA_EBCDIC) {
	    $recordDataASCII = iconv(X937File::DATA_EBCDIC, X937File::DATA_ASCII, $recordData);
	} else {
	    $recordDataASCII = $recordData;
	}

	switch ($recordType) {
	    // header records
	    case X937FieldRecordType::FILE_HEADER:
		return new X937RecordFileHeader($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::CASH_LETTER_HEADER:
		return new X937RecordCashLetterHeader($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::BUNDLE_HEADER:
		return new X937RecordBundleHeader($recordType, $recordDataASCII);
		break;

	    // check detail records
	    case X937FieldRecordType::CHECK_DETAIL:
		return new X937RecordCheckDetail($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::CHECK_DETAIL_ADDENDUM_A:
		return new X937RecordCheckDetailAddendumA($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::CHECK_DETAIL_ADDENDUM_B:
		return new X937RecordCheckDetailAddendumB($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::CHECK_DETAIL_ADDENDUM_C:
		return new X937RecordCheckDetailAddendumC($recordType, $recordDataASCII);
		break;	    
	    
	    // return detail records
	    case X937FieldRecordType::RETURN_RECORD:
		return new X937RecordReturnRecord($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_A:
		return new X937RecordReturnAddendumA($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_B:
		return new X937RecordReturnAddendumB($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_C:
		return new X937RecordReturnAddendumC($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::RETURN_ADDENDUM_D:
		return new X937RecordReturnAddendumD($recordType, $recordDataASCII);
		break;
	    
	    // image view records
	    case X937FieldRecordType::IMAGE_VIEW_DETAIL:
		return new X937RecordImageViewDetail($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::IMAGE_VIEW_DATA:
		/**
		 * @todo special data handling here for the binary data.
		 */
		return new X937RecordImageViewData($recordType, $recordDataASCII, $recordData);
		break;
	    case X937FieldRecordType::IMAGE_VIEW_ANALYSIS:
		return new X937RecordImageViewAnalysis($recordType, $recordDataASCII);
		break;

	    // control/summary records
	    case X937FieldRecordType::BUNDLE_CONTROL:
		return new X937RecordBundleControl($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::BOX_SUMMARY:
		return new X937RecordBoxSummary($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::ROUTING_NUMBER_SUMMARY:
		return new X937RecordRoutingNumberSummary($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::CASH_LETTER_CONTROL:
		return new X937RecordCashLetterControl($recordType, $recordDataASCII);
		break;
	    case X937FieldRecordType::FILE_CONTROL:
		return new X937RecordFileControl($recordType, $recordDataASCII);
		break;
	    default:
		return new X937RecordGeneric($recordType, $recordDataASCII);
		break;
	}
    }
}