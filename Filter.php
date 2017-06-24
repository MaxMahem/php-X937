<?php

namespace X937;

/**
 * Description of Filter
 *
 * @author astanley
 */
class Filter
{

    public static function getCashLetters(File $file)
    {
        $capture = false;

        foreach ($file as $record) {
            $recordType = $record->type;
            if ($record->type == Record\Type::CASH_LETTER_HEADER) {
                $capture = true;
                $working = array();
            }

            if ($capture && in_array($recordType, CashLetter::VALID_RECORD_TYPES)) {
                $working[] = $record;
            }

            if ($record->type == Record\Type::CASH_LETTER_CONTROL) {
                $capture = false;
                yield $working;
            }
        }
    }

    public static function getBundles(File $file)
    {
        $capture = false;

        foreach ($file as $record) {
            $recordType = $record->type;
            if ($record->type == Record\Type::BUNDLE_HEADER) {
                $capture = true;
                $working = array();
            }

            if ($capture && in_array($recordType, CashLetter::VALID_RECORD_TYPES)) {
                $working[] = $record;
            }

            if ($record->type == Record\Type::BUNDLE_CONTROL) {
                $capture = false;
                yield $working;
            }
        }
    }
}