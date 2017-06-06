<?php

namespace X937\Fields;

class DepositAccountNumber extends Field
{
    public function __construct($fieldNumber, $position)
    {
    parent::__construct($fieldNumber, 'Deposit Account Number at BOFD', Field::USAGE_CONDITIONAL, $position, 18, Field::TYPE_ALPHAMERICSPECIAL);
    }    
}

class ItemSequenceNumber extends Field
{
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
    parent::__construct($fieldNumber, $fieldNamePrefix . ' ' . 'Item Sequence Number', $usage, $position, 15, Field::TYPE_NUMERICBLANK);
    }    
}