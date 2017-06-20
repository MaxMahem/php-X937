<?php

namespace X937\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

/**
 * Description of NumericException
 *
 * @author astanley
 */
class NumericException extends ValidationException {
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'The value must not contain only digits (0-9)',
            self::NAMED => '{{name}} must not contain only digits (0-9)',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'The value must must contain only digits (0-9)',
            self::NAMED => '{{name}} must must contain only digits (0-9)',
        ],
    ];
}
