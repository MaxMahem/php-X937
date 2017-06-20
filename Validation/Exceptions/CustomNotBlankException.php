<?php
/**
 * Description of customNotBlank
 *
 * @author astanley
 */
namespace X937\Validation\Exceptions;

use Respect\Validation\Exceptions;

class CustomNotBlankException extends Exceptions\ValidationException
{
    const STANDARD = 0;
    const NAMED = 1;

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'The value must not be blank',
            self::NAMED => '{{name}} must not be blank',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'The value must be blank',
            self::NAMED => '{{name}} must be blank',
        ],
    ];

    public function chooseTemplate()
    {
        return $this->getName() == '' ? static::STANDARD : static::NAMED;
    }
}
