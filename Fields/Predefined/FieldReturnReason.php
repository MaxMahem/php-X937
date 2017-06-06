<?php

namespace X937\Fields\Predefined;

/**
 * Description of FieldReturnReason
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldReturnReason extends FieldPredefined
{
    const VALUE_NSF                  = 'A';
    const VALUE_UCF                  = 'B';
    const VALUE_STOPPAY              = 'C';
    const VALUE_CLOSED_ACCOUNT       = 'D';
    const VALUE_UTLA                 = 'E';
    const VALUE_FROZEN               = 'F';
    const VALUE_STALE_DATE           = 'G';
    const VALUE_POST_DATE            = 'H';
    const VALUE_ENDORSEMENT_MISS     = 'I';
    const VALUE_ENDORSEMENT_IRREG    = 'J';
    const VALUE_SIGNATURE_MISS       = 'K';
    const VALUE_SIGNATURE_IRREG      = 'L';
    const VALUE_NON_CASH             = 'M';
    const VALUE_ALTERED_FICTIOUS     = 'N';
    const VALUE_UNABLE_TO_PROCESS    = 'O';
    const VALUE_EXCEEDS_LIMIT        = 'P';
    const VALUE_NOT_AUTHORIZED       = 'Q';
    const VALUE_ACCOUNT_SOLD         = 'R';
    const VALUE_REFER_TO_MAKER       = 'S';
    const VALUE_STOPPAY_SUSPECT      = 'T';
    const VALUE_UNUSABLE_IMAGE       = 'U';
    const VALUE_IMAGE_FAIL_SECURITY  = 'V';
    const VALUE_AMOUNT_INDETERMINATE = 'W';
    
    public static function defineValues()
    {
    $definedValues = array(
        self::VALUE_NSF                  => 'NSF - Not Sufficent Funds',
        self::VALUE_UCF                  => 'UCF - Uncollected Funds Hold',
        self::VALUE_STOPPAY              => 'Stop Payment',
        self::VALUE_CLOSED_ACCOUNT       => 'Closed Account',
        self::VALUE_UTLA                 => 'UTLA - Unable to Locate Account',
        self::VALUE_FROZEN               => 'Frozen/Blocked Account',
        self::VALUE_STALE_DATE           => 'Stale Dated',
        self::VALUE_POST_DATE            => 'Post Dated',
        self::VALUE_ENDORSEMENT_MISS     => 'Endorsement Missing',
        self::VALUE_ENDORSEMENT_IRREG    => 'Endorsement Irregular',
        self::VALUE_SIGNATURE_MISS       => 'Signature Missing',
        self::VALUE_SIGNATURE_IRREG      => 'Signature Irregular',
        self::VALUE_NON_CASH             => 'Non-Cash Item (Non-Negotiable)',
        self::VALUE_ALTERED_FICTIOUS     => 'Altered/Fictious Item',
        self::VALUE_UNABLE_TO_PROCESS    => 'Unable to Process (Mutilated Item)',
        self::VALUE_EXCEEDS_LIMIT        => 'Item Exceeds Dollar Limit',
        self::VALUE_NOT_AUTHORIZED       => 'Not Authorized',
        self::VALUE_ACCOUNT_SOLD         => 'Branch/Account Sold (Wrong Bank)',
        self::VALUE_REFER_TO_MAKER       => 'Refer to Maker',
        self::VALUE_STOPPAY_SUSPECT      => 'Stop Payment Suspect',
        self::VALUE_UNUSABLE_IMAGE       => 'Unusable Image',
        self::VALUE_IMAGE_FAIL_SECURITY  => 'Image Fails Security Check',
        self::VALUE_AMOUNT_INDETERMINATE => 'Cannot Determine Amount',
    );
    
    return $definedValues;
    }
    
    public function __construct($fieldNumber, $usage, $position)
    {
    parent::__construct($fieldNumber, 'Return Reason', $usage, $position, 1, self::TYPE_ALPHAMERIC);
    }
}