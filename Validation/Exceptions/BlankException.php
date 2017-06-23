<?php
/**
 * Description of customNotBlank
 *
 * @author astanley
 */

namespace X937\Validation\Exceptions;

use Respect\Validation\Exceptions;

class BlankException extends Exceptions\ValidationException
{
    const STANDARD = 0;
    const NAMED = 1;

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'The value must be 0 or blank',
            self::NAMED => '{{name}} must have 0 or blank',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'The value must not be 0 or blank',
            self::NAMED => '{{name}} must not have 0 or blank',
        ],
    ];

    public function chooseTemplate()
    {
        return $this->getName() == '' ? static::STANDARD : static::NAMED;
    }
}
