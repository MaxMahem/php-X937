<?php

namespace X937\Fields\Predefined;

/**
 * A field containing the type of the record. This field is currently widly used
 * as it defines all the possible record types, which is checked elseware.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldRecordType extends FieldPredefined
{
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
    
    public function __construct($value)
    {
	parent::__construct(1, 'Record Type', self::USAGE_MANDATORY, 1, 2, self::TYPE_NUMERIC);
	
	$this->value = $value;
    }
    
    public static function defineValues()
    {
	$X937FieldRecordTypes = array(
	    self::FILE_HEADER             => 'File Header Record',
	    self::CASH_LETTER_HEADER      => 'Cash Letter Header Record',
	    self::BUNDLE_HEADER           => 'Bundle Header Record',
	    self::CHECK_DETAIL            => 'Check Detail Record',
            self::CHECK_DETAIL_ADDENDUM_A => 'Check Detail Addendum A Record',
	    self::CHECK_DETAIL_ADDENDUM_B => 'Check Detail Addendum B Record',
	    self::CHECK_DETAIL_ADDENDUM_C => 'Check Detail Addendum C Record',
	    self::RETURN_RECORD           => 'Return Record',
	    self::RETURN_ADDENDUM_A       => 'Retrun Addendum A Record',
	    self::RETURN_ADDENDUM_B       => 'Return Addendum B Record',
	    self::RETURN_ADDENDUM_C       => 'Return Addendum C Record',
	    self::RETURN_ADDENDUM_D       => 'Return Addendum D Record',
	    self::ACCOUNT_TOTALS_DETAIL   => 'Account Totals Detail Record',
	    self::NON_HIT_TOTALS_DETAIL   => 'Non-Hit Total Detail Record',
	    self::IMAGE_VIEW_DETAIL       => 'Image View Detail Record',
	    self::IMAGE_VIEW_DATA         => 'Image View Data Record',
	    self::IMAGE_VIEW_ANALYSIS     => 'Image View Analysis',
	    self::BUNDLE_CONTROL          => 'Bundle Control Record',
	    self::BOX_SUMMARY             => 'Box Summary Record',
	    self::ROUTING_NUMBER_SUMMARY  => 'Routing Number Summary Record',
	    self::CASH_LETTER_CONTROL     => 'Cash Letter Control Record',
	    self::FILE_CONTROL            => 'File Control Record',
	);
	
	return $X937FieldRecordTypes;
    }
}
