<?php

namespace X937\Record;

use X937\X937File;
use X937\Fields\Predefined\RecordType;

require_once 'RecordTypes.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'VariableLength' . DIRECTORY_SEPARATOR . 'VariableLength.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'VariableLength' . DIRECTORY_SEPARATOR . 'CheckDetailAddendumB.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'VariableLength' . DIRECTORY_SEPARATOR . 'ImageViewData.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'VariableLength' . DIRECTORY_SEPARATOR . 'ReturnAddendumC.php';

require_once 'CashLetterHeader.php';
require_once 'ImageViewDetail.php';

/**
 * A factor class to generate new X937Record from different sorts of input.
 *
 * @author astanley
 */
class Factory {
    
    // record types
    const RECORD_TYPE_FILE_HEADER             = '01';
    const RECORD_TYPE_CASH_LETTER_HEADER      = '10';
    const RECORD_TYPE_BUNDLE_HEADER           = '20';
    const RECORD_TYPE_CHECK_DETAIL            = '25';
    const RECORD_TYPE_CHECK_DETAIL_ADDENDUM_A = '26';
    const RECORD_TYPE_CHECK_DETAIL_ADDENDUM_B = '27';
    const RECORD_TYPE_CHECK_DETAIL_ADDENDUM_C = '28';
    const RECORD_TYPE_RETURN_RECORD           = '31';
    const RECORD_TYPE_RETURN_ADDENDUM_A       = '32';
    const RECORD_TYPE_RETURN_ADDENDUM_B       = '33';
    const RECORD_TYPE_RETURN_ADDENDUM_C       = '34';
    const RECORD_TYPE_RETURN_ADDENDUM_D       = '35';
    const RECORD_TYPE_ACCOUNT_TOTALS_DETAIL   = '40';
    const RECORD_TYPE_NON_HIT_TOTALS_DETAIL   = '41';
    const RECORD_TYPE_IMAGE_VIEW_DETAIL       = '50';
    const RECORD_TYPE_IMAGE_VIEW_DATA         = '52';
    const RECORD_TYPE_IMAGE_VIEW_ANALYSIS     = '54';
    const RECORD_TYPE_BUNDLE_CONTROL          = '70';
    const RECORD_TYPE_BOX_SUMMARY             = '75';
    const RECORD_TYPE_ROUTING_NUMBER_SUMMARY  = '85';
    const RECORD_TYPE_CASH_LETTER_CONTROL     = '90';
    const RECORD_TYPE_FILE_CONTROL            = '99';
    
    /**
     * 
     * @return array List of defined Record Types.
     */
    public static function defineRecordTypes()
    {
	$definedValues = array(
	    self::RECORD_TYPE_FILE_HEADER             => 'File Header Record',
	    self::RECORD_TYPE_CASH_LETTER_HEADER      => 'Cash Letter Header Record',
	    self::RECORD_TYPE_BUNDLE_HEADER           => 'Bundle Header Record',
	    self::RECORD_TYPE_CHECK_DETAIL            => 'Check Detail Record',
            self::RECORD_TYPE_CHECK_DETAIL_ADDENDUM_A => 'Check Detail Addendum A Record',
	    self::RECORD_TYPE_CHECK_DETAIL_ADDENDUM_B => 'Check Detail Addendum B Record',
	    self::RECORD_TYPE_CHECK_DETAIL_ADDENDUM_C => 'Check Detail Addendum C Record',
	    self::RECORD_TYPE_RETURN_RECORD           => 'Return Record',
	    self::RECORD_TYPE_RETURN_ADDENDUM_A       => 'Retrun Addendum A Record',
	    self::RECORD_TYPE_RETURN_ADDENDUM_B       => 'Return Addendum B Record',
	    self::RECORD_TYPE_RETURN_ADDENDUM_C       => 'Return Addendum C Record',
	    self::RECORD_TYPE_RETURN_ADDENDUM_D       => 'Return Addendum D Record',
	    self::RECORD_TYPE_ACCOUNT_TOTALS_DETAIL   => 'Account Totals Detail Record',
	    self::RECORD_TYPE_NON_HIT_TOTALS_DETAIL   => 'Non-Hit Total Detail Record',
	    self::RECORD_TYPE_IMAGE_VIEW_DETAIL       => 'Image View Detail Record',
	    self::RECORD_TYPE_IMAGE_VIEW_DATA         => 'Image View Data Record',
	    self::RECORD_TYPE_IMAGE_VIEW_ANALYSIS     => 'Image View Analysis',
	    self::RECORD_TYPE_BUNDLE_CONTROL          => 'Bundle Control Record',
	    self::RECORD_TYPE_BOX_SUMMARY             => 'Box Summary Record',
	    self::RECORD_TYPE_ROUTING_NUMBER_SUMMARY  => 'Routing Number Summary Record',
	    self::RECORD_TYPE_CASH_LETTER_CONTROL     => 'Cash Letter Control Record',
	    self::RECORD_TYPE_FILE_CONTROL            => 'File Control Record',
	);
	
	return $definedValues;
    }

    /**
     * Generates an appropriate X937 Record from the raw record data.
     * @param string $recordData raw Record data as from the X937 file, generally EBCDIC format.
     * @param string $dataType   the type of the data a X937File const, either DATA_EBCDIC or DATA_ASCII
     * @return Record returns an X937Record of the appropriate type.
     */
    public static function GenerateFromRawData($recordData, $dataType) {
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
     * @return Record an approriate record type
     * @throws InvalidArgumentException if given bad data
     */
    private static function newRecord($recordType, $recordData, $dataType = X937File::DATA_EBCDIC) {
	if (array_key_exists($recordType, self::defineRecordTypes()) === FALSE) {
	    throw new InvalidArgumentException("Bad record type passed.");
	}
	
	// convert the record data if necessary.
	/**
	 * @todo consider how to handle binary data here? Or push this elsware.
	 * Currently it will warn on binary data, which we suppress.
	 */
	if ($dataType === X937File::DATA_EBCDIC) {
	    $recordDataASCII = @iconv(X937File::DATA_EBCDIC, X937File::DATA_ASCII, $recordData);
	} else {
	    $recordDataASCII = $recordData;
	}

	switch ($recordType) {
	    // header Record
	    case RecordType::VALUE_FILE_HEADER:
		return new FileHeader($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_CASH_LETTER_HEADER:
		return new CashLetterHeader($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_BUNDLE_HEADER:
		return new BundleHeader($recordType, $recordDataASCII);
		break;

	    // check detail Record
	    case RecordType::VALUE_CHECK_DETAIL:
		return new CheckDetail($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_CHECK_DETAIL_ADDENDUM_A:
		return new CheckDetailAddendumA($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_CHECK_DETAIL_ADDENDUM_B:
		return new VariableLength\CheckDetailAddendumB($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_CHECK_DETAIL_ADDENDUM_C:
		return new CheckDetailAddendumC($recordType, $recordDataASCII);
		break;	    
	    
	    // return detail Record
	    case RecordType::VALUE_RETURN_RECORD:
		return new ReturnRecord($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_RETURN_ADDENDUM_A:
		return new ReturnAddendumA($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_RETURN_ADDENDUM_B:
		return new ReturnAddendumB($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_RETURN_ADDENDUM_C:
		return new VariableLength\ReturnAddendumC($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_RETURN_ADDENDUM_D:
		return new ReturnAddendumD($recordType, $recordDataASCII);
		break;
	    
	    // image view Record
	    case RecordType::VALUE_IMAGE_VIEW_DETAIL:
		return new ImageViewDetail($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_IMAGE_VIEW_DATA:
		/**
		 * @todo special data handling here for the binary data.
		 */
		return new VariableLength\ImageViewData($recordType, $recordDataASCII, $recordData);
		break;
	    case RecordType::VALUE_IMAGE_VIEW_ANALYSIS:
		return new ImageViewAnalysis($recordType, $recordDataASCII);
		break;

	    // control/summary Record
	    case RecordType::VALUE_BUNDLE_CONTROL:
		return new BundleControl($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_BOX_SUMMARY:
		return new BoxSummary($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_ROUTING_NUMBER_SUMMARY:
		return new RoutingNumberSummary($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_CASH_LETTER_CONTROL:
		return new CashLetterControl($recordType, $recordDataASCII);
		break;
	    case RecordType::VALUE_FILE_CONTROL:
		return new FileControl($recordType, $recordDataASCII);
		break;
	    default:
		// emmit notice, we shouldn't get any of these.
		trigger_error('Invalid record passed, data is unhandled.');
		return new Generic($recordType, $recordDataASCII);
		break;
	}
    }
}