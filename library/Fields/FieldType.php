<?php

namespace X937\Fields;

/**
 * Description of FieldType
 *
 * @author astanley
 */
class FieldType
{
    const ALPHABETIC = 'A';
    const NUMERIC = 'N';
    const BLANK = 'B';
    const SPECIAL = 'S';
    const ALPHAMERIC = 'AN';
    const ALPHAMERICSPECIAL = 'ANS';
    const NUMERICBLANK = 'NB';
    const NUMERICSPECIAL = 'NS';
    const NUMERICBLANKSPECIALMICR = 'NBSM';
    const NUMERICBLANKSPECIALMICRONUS = 'NBSMOS';
    const BINARY = 'Binary';

    const TYPES = array(
        self::ALPHABETIC => 'Alphabetic characters (A-Z, a-z) and space.',
        self::NUMERIC => 'Numeric characters (0-9)',
        self::BLANK => 'Blank character, space (ASCII 0x20, EBCDIC 0x40)',
        self::SPECIAL => 'Any printable character (ASCII > 0x1F, EBCIDC > 0x3F',
        self::ALPHAMERIC => 'Any Alphabetic or Numeric character',
        self::ALPHAMERICSPECIAL => 'Any Alphabetic, Numeric, or Special character.',
        self::NUMERICBLANK => 'Any Numeric or Blank character',
        self::NUMERICSPECIAL => 'Any Numeric of Special character',
        self::NUMERICBLANKSPECIALMICR => 'Any Numeric Character, Dash (-), or Asterisk (*)',
        self::NUMERICBLANKSPECIALMICRONUS => 'Any Numeric Character, Dash (-), Asterisk (*), or Forward Slash (/)',
        self::BINARY => 'Binary Data',
    );
}
