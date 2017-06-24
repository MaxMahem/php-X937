<?php

namespace X937\Fields;

/**
 * Description of SubType
 *
 * @author astanley
 */
class SubType
{
    const ROUTINGNUMBER = 'Routing';
    const DATE = 'Date';
    const TIME = 'Time';
    const PHONENUMBER = 'Phone';
    const AMOUNT = 'Amount';
    const BLANK = 'Blank';

    const SUBTYPES = array(
        self::ROUTINGNUMBER => 'Routing Number (with check digit)',
        self::DATE => 'Date, YYYYMMDD',
        self::TIME => 'Time, HHMM',
        self::PHONENUMBER => 'Phone Number',
        self::AMOUNT => 'Amount',
        self::BLANK => 'Blank',
    );
}
