<?php namespace X937\Fields\Predefined;

/**
 * Field indicating collection type. Valid values very on record type.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldCollectionType extends FieldPredefined
{
    const PRELIMINARY_FORWARD_INFORMATION         = '00';
    const FORWARD_PRESENTMENT                     = '01';
    const FORWARD_PRESENTMENT_SAME_DAY_SETTLEMENT = '02';
    const RETURN_CHECKS                           = '03';
    const RETURN_NOTIFICATION                     = '04';
    const PRELIMINARY_RETURN_NOTIFICATION         = '05';
    const FINAL_RETURN_NOTIFICATION               = '06';
    const ACCOUNT_TOTALS                          = '10';
    const NO_DETAIL                               = '20';
    const BUNDLES_NOT_SAME                        = '99';
    
    /**
     * The record type of this field, one of X937Field::CASH_LETTER_HEADER, or
     * X937Field::BUNDLE_HEADER
     * @var string 
     */
    private $recordType;
    
    public function __construct($recordType)
    {
    $this->recordType = $recordType;
    
    if (in_array($recordType, array(RecordType::VALUE_CASH_LETTER_HEADER, RecordType::VALUE_BUNDLE_HEADER)) === FALSE) {
        throw new \InvalidArgumentException('Bad record type');
    }
    
    parent::__construct(2, 'Collection Type Indicator', self::USAGE_MANDATORY, 3, 2, self::TYPE_NUMERIC);
    }

    public static function defineValues()
    {
    $definedValues = array(
        self::PRELIMINARY_FORWARD_INFORMATION         => 'Preliminary Forward Information',
        self::FORWARD_PRESENTMENT                     => 'Forward Presentment',
        self::FORWARD_PRESENTMENT_SAME_DAY_SETTLEMENT => 'Forward Presentment - Same-Day Settlement',
        self::RETURN_CHECKS                           => 'Return',
        self::RETURN_NOTIFICATION                     => 'Return Notification',
        self::PRELIMINARY_RETURN_NOTIFICATION         => 'Preliminary Return Notification',
        self::FINAL_RETURN_NOTIFICATION               => 'Final Return Notification',
        self::ACCOUNT_TOTALS                          => 'Account Totals',
        self::NO_DETAIL                               => 'No Detail',
        self::BUNDLES_NOT_SAME                        => 'Bundles not the same collection type',
    );
    
    return $definedValues;
    }
    
    /**
     * This is overridden because the valid values differs depending upon the record type.
     */
    protected function addClassValidators()
    {
    $legalValues = array_keys(self::defineValues());
    
    switch ($this->recordType) {
        // Check Letter Header can use the default.
        case RecordType::VALUE_CASH_LETTER_HEADER:
        break;
        
        // Bundle Header Record do not permit option 10, 20, and 99.
        case RecordType::VALUE_BUNDLE_HEADER:
        unset($legalValues[self::ACCOUNT_TOTALS]);
        unset($legalValues[self::NO_DETAIL]);
        unset($legalValues[self::BUNDLES_NOT_SAME]);
        break;
        
        // we would normaly error check here, but that should be handled in the constructor.
    }
    
    $legalValuesValidator = new \X937\Validator\ValidatorValueEnumerated($legalValues);
    $this->validator->addValidator($legalValuesValidator);
    }
}