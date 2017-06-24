<?php

namespace X937\Records;

/**
 * Description of dType
 *
 * @author astanley
 */
class Type
{
    const FILE_HEADER = '01';
    const CASH_LETTER_HEADER = '10';
    const BUNDLE_HEADER = '20';
    const CHECK_DETAIL = '25';
    const CHECK_DETAIL_ADDENDUM_A = '26';
    const CHECK_DETAIL_ADDENDUM_B = '27';
    const CHECK_DETAIL_ADDENDUM_C = '28';
    const RETURN_RECORD = '31';
    const RETURN_ADDENDUM_A = '32';
    const RETURN_ADDENDUM_B = '33';
    const RETURN_ADDENDUM_C = '34';
    const RETURN_ADDENDUM_D = '35';
    const ACCOUNT_TOTALS_DETAIL = '40';
    const NON_HIT_TOTALS_DETAIL = '41';
    const IMAGE_VIEW_DETAIL = '50';
    const IMAGE_VIEW_DATA = '52';
    const IMAGE_VIEW_ANALYSIS = '54';
    const BUNDLE_CONTROL = '70';
    const BOX_SUMMARY = '75';
    const ROUTING_NUMBER_SUMMARY = '85';
    const CASH_LETTER_CONTROL = '90';
    const FILE_CONTROL = '99';

    // record definitions
    const DEFINITIONS = [
        self::FILE_HEADER => 'File Header Records',
        self::CASH_LETTER_HEADER => 'Cash Letter Header Records',
        self::BUNDLE_HEADER => 'Bundle Header Records',
        self::CHECK_DETAIL => 'Check Detail Records',
        self::CHECK_DETAIL_ADDENDUM_A => 'Check Detail Addendum A Records',
        self::CHECK_DETAIL_ADDENDUM_B => 'Check Detail Addendum B Records',
        self::CHECK_DETAIL_ADDENDUM_C => 'Check Detail Addendum C Records',
        self::RETURN_RECORD => 'Return Records',
        self::RETURN_ADDENDUM_A => 'Retrun Addendum A Records',
        self::RETURN_ADDENDUM_B => 'Return Addendum B Records',
        self::RETURN_ADDENDUM_C => 'Return Addendum C Records',
        self::RETURN_ADDENDUM_D => 'Return Addendum D Records',
        self::ACCOUNT_TOTALS_DETAIL => 'Account Totals Detail Records',
        self::NON_HIT_TOTALS_DETAIL => 'Non-Hit Total Detail Records',
        self::IMAGE_VIEW_DETAIL => 'Image View Detail Records',
        self::IMAGE_VIEW_DATA => 'Image View Data Records',
        self::IMAGE_VIEW_ANALYSIS => 'Image View Analysis',
        self::BUNDLE_CONTROL => 'Bundle Control Records',
        self::BOX_SUMMARY => 'Box Summary Records',
        self::ROUTING_NUMBER_SUMMARY => 'Routing Number Summary Records',
        self::CASH_LETTER_CONTROL => 'Cash Letter Control Records',
        self::FILE_CONTROL => 'File Control Records',
    ];
}
