<?php

namespace X937\Records;

use X937\X937File;
use X937\Fields\Predefined\FieldRecordType;

require_once 'RecordTypes.php';
require_once 'RecordTypesVariableLength.php';

/**
 * A factor class to generate new X937Records from different sorts of input.
 *
 * @author astanley
 */
class Factory {
    
    /**
     * Returns an array of currently handled record types.
     * @return array An array of handled types.
     */
    public static function handledRecordTypes() {
	$handledRecordTypes = FieldRecordType::defineValues();

	unset($handledRecordTypes[FieldRecordType::IMAGE_VIEW_ANALYSIS]);
	
	return $handledRecordTypes;
    }

    /**
     * Generates an appropriate X937 Record from the raw record data.
     * @param string $recordData raw Record data as from the X937 file, generally EBCDIC format.
     * @param string $dataType   the type of the data a X937File const, either DATA_EBCDIC or DATA_ASCII
     * @return Record returns an X937Record of the appropriate type.
     */
    public static function newRecordFromRawData($recordData, $dataType) {
	$recordTypeRaw = substr($recordData, 0, 2);
	
	switch ($dataType) {
	    case X937File::DATA_ASCII:
		$recordType = $recordTypeRaw;
		break;
	    case X937File::DATA_EBCDIC:
		/**
		 * @todo: Better handling here so we don't have to supress errors.
		 * currently it will warn when hitting some binary data.
		 */
		$recordType = @iconv(X937File::DATA_EBCDIC, X937File::DATA_ASCII, $recordTypeRaw);
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
     * @return Record an approriate record type
     * @throws InvalidArgumentException if given bad data
     */
    private static function newRecord($recordType, $recordData, $dataType = X937File::DATA_EBCDIC) {
	if (array_key_exists($recordType, FieldRecordType::defineValues()) === FALSE) {
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
	    case FieldRecordType::FILE_HEADER:
		return new FileHeader($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::CASH_LETTER_HEADER:
		return new CashLetterHeader($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::BUNDLE_HEADER:
		return new BundleHeader($recordType, $recordDataASCII);
		break;

	    // check detail records
	    case FieldRecordType::CHECK_DETAIL:
		return new CheckDetail($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::CHECK_DETAIL_ADDENDUM_A:
		return new CheckDetailAddendumA($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::CHECK_DETAIL_ADDENDUM_B:
		return new X937RecordCheckDetailAddendumB($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::CHECK_DETAIL_ADDENDUM_C:
		return new CheckDetailAddendumC($recordType, $recordDataASCII);
		break;	    
	    
	    // return detail records
	    case FieldRecordType::RETURN_RECORD:
		return new ReturnRecord($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::RETURN_ADDENDUM_A:
		return new ReturnAddendumA($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::RETURN_ADDENDUM_B:
		return new ReturnAddendumB($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::RETURN_ADDENDUM_C:
		return new X937RecordReturnAddendumC($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::RETURN_ADDENDUM_D:
		return new ReturnAddendumD($recordType, $recordDataASCII);
		break;
	    
	    // image view records
	    case FieldRecordType::IMAGE_VIEW_DETAIL:
		return new ImageViewDetail($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::IMAGE_VIEW_DATA:
		/**
		 * @todo special data handling here for the binary data.
		 */
		return new X937RecordImageViewData($recordType, $recordDataASCII, $recordData);
		break;
	    case FieldRecordType::IMAGE_VIEW_ANALYSIS:
		return new ImageViewAnalysis($recordType, $recordDataASCII);
		break;

	    // control/summary records
	    case FieldRecordType::BUNDLE_CONTROL:
		return new BundleControl($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::BOX_SUMMARY:
		return new BoxSummary($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::ROUTING_NUMBER_SUMMARY:
		return new RoutingNumberSummary($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::CASH_LETTER_CONTROL:
		return new CashLetterControl($recordType, $recordDataASCII);
		break;
	    case FieldRecordType::FILE_CONTROL:
		return new FileControl($recordType, $recordDataASCII);
		break;
	    default:
		return new Generic($recordType, $recordDataASCII);
		break;
	}
    }
}