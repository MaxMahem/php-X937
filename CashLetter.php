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
        Records\RecordType::CASH_LETTER_HEADER,
        Records\RecordType::BUNDLE_HEADER,
        Records\RecordType::CHECK_DETAIL,
        Records\RecordType::CHECK_DETAIL_ADDENDUM_A,
        Records\RecordType::CHECK_DETAIL_ADDENDUM_B,
        Records\RecordType::CHECK_DETAIL_ADDENDUM_C,
        Records\RecordType::RETURN_RECORD,
        Records\RecordType::RETURN_ADDENDUM_A,
        Records\RecordType::RETURN_ADDENDUM_B,
        Records\RecordType::RETURN_ADDENDUM_C,
        Records\RecordType::RETURN_ADDENDUM_D,
        Records\RecordType::ACCOUNT_TOTALS_DETAIL,
        Records\RecordType::NON_HIT_TOTALS_DETAIL,
        Records\RecordType::IMAGE_VIEW_DETAIL,
        Records\RecordType::IMAGE_VIEW_DATA,
        Records\RecordType::IMAGE_VIEW_ANALYSIS,
        Records\RecordType::BUNDLE_CONTROL,
        Records\RecordType::BOX_SUMMARY,
        Records\RecordType::ROUTING_NUMBER_SUMMARY,
        Records\RecordType::CASH_LETTER_CONTROL,
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
