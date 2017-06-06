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
class RecordType extends FieldPredefined
{
    const VALUE_FILE_HEADER             = '01';
    const VALUE_CASH_LETTER_HEADER      = '10';
    const VALUE_BUNDLE_HEADER           = '20';
    const VALUE_CHECK_DETAIL            = '25';
    const VALUE_CHECK_DETAIL_ADDENDUM_A = '26';
    const VALUE_CHECK_DETAIL_ADDENDUM_B = '27';
    const VALUE_CHECK_DETAIL_ADDENDUM_C = '28';
    const VALUE_RETURN_RECORD           = '31';
    const VALUE_RETURN_ADDENDUM_A       = '32';
    const VALUE_RETURN_ADDENDUM_B       = '33';
    const VALUE_RETURN_ADDENDUM_C       = '34';
    const VALUE_RETURN_ADDENDUM_D       = '35';
    const VALUE_ACCOUNT_TOTALS_DETAIL   = '40';
    const VALUE_NON_HIT_TOTALS_DETAIL   = '41';
    const VALUE_IMAGE_VIEW_DETAIL       = '50';
    const VALUE_IMAGE_VIEW_DATA         = '52';
    const VALUE_IMAGE_VIEW_ANALYSIS     = '54';
    const VALUE_BUNDLE_CONTROL          = '70';
    const VALUE_BOX_SUMMARY             = '75';
    const VALUE_ROUTING_NUMBER_SUMMARY  = '85';
    const VALUE_CASH_LETTER_CONTROL     = '90';
    const VALUE_FILE_CONTROL            = '99';
    
    public function __construct($value)
    {
    parent::__construct(1, 'Record Type', self::USAGE_MANDATORY, 1, 2, self::TYPE_NUMERIC);
    
    $this->value = $value;
    }
    
    public static function defineValues()
    {
    $definedValues = array(
        self::VALUE_FILE_HEADER             => 'File Header Record',
        self::VALUE_CASH_LETTER_HEADER      => 'Cash Letter Header Record',
        self::VALUE_BUNDLE_HEADER           => 'Bundle Header Record',
        self::VALUE_CHECK_DETAIL            => 'Check Detail Record',
            self::VALUE_CHECK_DETAIL_ADDENDUM_A => 'Check Detail Addendum A Record',
        self::VALUE_CHECK_DETAIL_ADDENDUM_B => 'Check Detail Addendum B Record',
        self::VALUE_CHECK_DETAIL_ADDENDUM_C => 'Check Detail Addendum C Record',
        self::VALUE_RETURN_RECORD           => 'Return Record',
        self::VALUE_RETURN_ADDENDUM_A       => 'Retrun Addendum A Record',
        self::VALUE_RETURN_ADDENDUM_B       => 'Return Addendum B Record',
        self::VALUE_RETURN_ADDENDUM_C       => 'Return Addendum C Record',
        self::VALUE_RETURN_ADDENDUM_D       => 'Return Addendum D Record',
        self::VALUE_ACCOUNT_TOTALS_DETAIL   => 'Account Totals Detail Record',
        self::VALUE_NON_HIT_TOTALS_DETAIL   => 'Non-Hit Total Detail Record',
        self::VALUE_IMAGE_VIEW_DETAIL       => 'Image View Detail Record',
        self::VALUE_IMAGE_VIEW_DATA         => 'Image View Data Record',
        self::VALUE_IMAGE_VIEW_ANALYSIS     => 'Image View Analysis',
        self::VALUE_BUNDLE_CONTROL          => 'Bundle Control Record',
        self::VALUE_BOX_SUMMARY             => 'Box Summary Record',
        self::VALUE_ROUTING_NUMBER_SUMMARY  => 'Routing Number Summary Record',
        self::VALUE_CASH_LETTER_CONTROL     => 'Cash Letter Control Record',
        self::VALUE_FILE_CONTROL            => 'File Control Record',
    );
    
    return $definedValues;
    }
}
