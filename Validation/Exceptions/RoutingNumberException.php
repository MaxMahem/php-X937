<?php

namespace X937\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

/**
 * Description of RoutingException
 *
 * @author astanley
 */
class RoutingNumberException extends ValidationException {
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be a valid routing number',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be a valid routing number',
        ],
    ];
}
