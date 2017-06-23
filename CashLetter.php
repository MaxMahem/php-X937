<?php

namespace X937;

/**
 * Description of CashLetter
 *
 * @author astanley
 */
class CashLetter extends Container
{
    const VALID_RECORD_TYPES = array(
        Records\Type::CASH_LETTER_HEADER,
        Records\Type::BUNDLE_HEADER,
        Records\Type::CHECK_DETAIL,
        Records\Type::CHECK_DETAIL_ADDENDUM_A,
        Records\Type::CHECK_DETAIL_ADDENDUM_B,
        Records\Type::CHECK_DETAIL_ADDENDUM_C,
        Records\Type::RETURN_RECORD,
        Records\Type::RETURN_ADDENDUM_A,
        Records\Type::RETURN_ADDENDUM_B,
        Records\Type::RETURN_ADDENDUM_C,
        Records\Type::RETURN_ADDENDUM_D,
        Records\Type::ACCOUNT_TOTALS_DETAIL,
        Records\Type::NON_HIT_TOTALS_DETAIL,
        Records\Type::IMAGE_VIEW_DETAIL,
        Records\Type::IMAGE_VIEW_DATA,
        Records\Type::IMAGE_VIEW_ANALYSIS,
        Records\Type::BUNDLE_CONTROL,
        Records\Type::BOX_SUMMARY,
        Records\Type::ROUTING_NUMBER_SUMMARY,
        Records\Type::CASH_LETTER_CONTROL,
    );

    private $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function getBundles(): Bundle
    {

    }

    public function validate(): string
    {
        $error = '';

        foreach ($this->records as $record) {
            $error .= $record->validate();
        }

        return $error;
    }
}
