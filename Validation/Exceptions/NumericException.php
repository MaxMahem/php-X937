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
            self::STANDARD => 'The value must contain only digits (0-9)',
            self::NAMED => '{{name}} must contain only digits (0-9)',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'The value must not must contain only digits (0-9)',
            self::NAMED => '{{name}} must must not contain only digits (0-9)',
        ],
    ];
}
