<?php

namespace X937;

/**
 * Description of Util
 *
 * @author astanley
 */
class Util {
    
    const DATA_ASCII  = 'ASCII';
    const DATA_EBCDIC = 'EBCDIC-US';
    
    const DATA_TYPES = array(
        self::DATA_ASCII  => self::DATA_ASCII,
        self::DATA_EBCDIC => self::DATA_EBCDIC,
    );
    
    const EBCDIC_2_ASCII_TABLE = array(
        '40' => ' ',
        '4A' => '¢',
        '4B' => '.',
        '4C' => '<',
        '4D' => '(',
        '4E' => '+',
        '4F' => '|',
        '5A' => '!',
        '5B' => '$',
        '5C' => '*',
        '5D' => ')',
        '5E' => ';',
        '5F' => '¬',
        '60' => '-',
        '61' => '/',
        '6A' => '¦',
        '6B' => ',',
        '6C' => '%',
        '6D' => '_',
        '6E' => '>',
        '6F' => '?',
        '79' => '`',
        '7A' => ':',
        '7B' => '#',
        '7C' => '@',
        '7D' => '\'',
        '7E' => '=',
        '7F' => '\"',
        '81' => 'a',
        '82' => 'b',
        '83' => 'c',
        '84' => 'd',
        '85' => 'e',
        '86' => 'f',
        '87' => 'g',
        '88' => 'h',
        '89' => 'i',
        '91' => 'j',
        '92' => 'k',
        '93' => 'l',
        '94' => 'm',
        '95' => 'n',
        '96' => 'o',
        '97' => 'p',
        '98' => 'q',
        '99' => 'r',
        'A1' => '~',
        'A2' => 's',
        'A3' => 't',
        'A4' => 'u',
        'A5' => 'v',
        'A6' => 'w',
        'A7' => 'x',
        'A8' => 'y',
        'A9' => 'z',
        'C0' => '{',
        'C1' => 'A',
        'C2' => 'B',
        'C3' => 'C',
        'C4' => 'D',
        'C5' => 'E',
        'C6' => 'F',
        'C7' => 'G',
        'C8' => 'H',
        'C9' => 'I',
        'D0' => '}',
        'D1' => 'J',
        'D2' => 'K',
        'D3' => 'L',
        'D4' => 'M',
        'D5' => 'N',
        'D6' => 'O',
        'D7' => 'P',
        'D8' => 'Q',
        'D9' => 'R',
        'E0' => '\\',
        'E2' => 'S',
        'E3' => 'T',
        'E4' => 'U',
        'E5' => 'V',
        'E6' => 'W',
        'E7' => 'X',
        'E8' => 'Y',
        'E9' => 'Z',
        'F0' => '0',
        'F1' => '1',
        'F2' => '2',
        'F3' => '3',
        'F4' => '4',
        'F5' => '5',
        'F6' => '6',
        'F7' => '7',
        'F8' => '8',
        'F9' => '9',  
    );
    
    const ASCII_2_EBCDIC_TABLE = array(
        ' ' => '40',
        '¢' => '4A',
        '.' => '4B',
        '<' => '4C',
        '(' => '4D',
        '+' => '4E',
        '|' => '4F',
        '!' => '5A',
        '$' => '5B',
        '*' => '5C',
        ')' => '5D',
        ';' => '5E',
        '¬' => '5F',
        '-' => '60',
        '/' => '61',
        '¦' => '6A',
        ',' => '6B',
        '%' => '6C',
        '_' => '6D',
        '>' => '6E',
        '?' => '6F',
        '`' => '79',
        ':' => '7A',
        '#' => '7B',
        '@' => '7C',
        '\'' => '7D',
        '=' => '7E',
        '"' => '7F',
        'a' => '81',
        'b' => '82',
        'c' => '83',
        'd' => '84',
        'e' => '85',
        'f' => '86',
        'g' => '87',
        'h' => '88',
        'i' => '89',
        'j' => '91',
        'k' => '92',
        'l' => '93',
        'm' => '94',
        'n' => '95',
        'o' => '96',
        'p' => '97',
        'q' => '98',
        'r' => '99',
        '~' => 'A1',
        's' => 'A2',
        't' => 'A3',
        'u' => 'A4',
        'v' => 'A5',
        'w' => 'A6',
        'x' => 'A7',
        'y' => 'A8',
        'z' => 'A9',
        '{' => 'C0',
        'A' => 'C1',
        'B' => 'C2',
        'C' => 'C3',
        'D' => 'C4',
        'E' => 'C5',
        'F' => 'C6',
        'G' => 'C7',
        'H' => 'C8',
        'I' => 'C9',
        '}' => 'D0',
        'J' => 'D1',
        'K' => 'D2',
        'L' => 'D3',
        'M' => 'D4',
        'N' => 'D5',
        'O' => 'D6',
        'P' => 'D7',
        'Q' => 'D8',
        'R' => 'D9',
        '\\' => 'E0',
        'S' => 'E2',
        'T' => 'E3',
        'U' => 'E4',
        'V' => 'E5',
        'W' => 'E6',
        'X' => 'E7',
        'Y' => 'E8',
        'Z' => 'E9',
        '0' => 'F0',
        '1' => 'F1',
        '2' => 'F2',
        '3' => 'F3',
        '4' => 'F4',
        '5' => 'F5',
        '6' => 'F6',
        '7' => 'F7',
        '8' => 'F8',
        '9' => 'F9',
    );
    
    /**
     * Converts a string of ASCII into EBCDIC
     * 
     * @param string $aString ASCII string
     * @return string EBCDIC string
     */
    public static function a2e(string $aString): string {
        // loop though string converting to EBCDIC
        $eOut = "";    
        while(strlen($aString)>=1)
        {
            $thisASCII = substr($aString, 0, 1);

            if (array_key_exists($thisASCII, self::ASCII_2_EBCDIC_TABLE)) {
                $eOut .= hex2bin(self::ASCII_2_EBCDIC_TABLE[$thisASCII]);
            } else {
                $eOut .= hex2bin(self::ASCII_2_EBCDIC_TABLE[' ']);
                trigger_error("Unhandled ASCII Character " . bin2hex($thisASCII));
            }
            $aString = substr($aString, 1);

        }    

        return $eOut;
    }
    
    /**
     * Converts the dreaded EBCDIC to ASCII
     * 
     * @param string $eBinaryString raw EBCDIC hex string, in the format \xF0\xF1...
     * @return string Decoded ASCII data
     */
     public static function e2a(string $eBinaryString): string {
        // loop until there is no more conversion.
        $asciiOut = "";    
        while(strlen($eBinaryString)>=1)
        {
            $thisEBCDIC = strtoupper(bin2hex(substr($eBinaryString, 0, 1)));
            if (array_key_exists($thisEBCDIC, self::EBCDIC_2_ASCII_TABLE)) {
                $asciiOut .= self::EBCDIC_2_ASCII_TABLE[$thisEBCDIC];
            } else {
                $asciiOut .= ' ';
                trigger_error("Unhandled EBCDIC Character " . bin2hex($thisEBCDIC));
            }
            $eBinaryString = substr($eBinaryString, 1);
        }    

        return $asciiOut;
    }
}
