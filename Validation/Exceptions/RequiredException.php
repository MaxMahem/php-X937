<?php
/**
 * Description of customNotBlank
 *
 * @author astanley
 */
namespace X937\Validation\Exceptions;

use Respect\Validation\Exceptions;

class RequiredException extends Exceptions\ValidationException
{
    const STANDARD = 0;
    const NAMED = 1;

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'The value must have a value',
            self::NAMED => '{{name}} must have a value',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'The value must not have a value',
            self::NAMED => '{{name}} must not have a value',
        ],
    ];

    public function chooseTemplate()
    {
        return $this->getName() == '' ? static::STANDARD : static::NAMED;
    }
}
