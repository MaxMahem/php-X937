<?php

namespace X937\Validation\Rules;

use Respect\Validation\Rules;

/**
 * Description of numeric
 *
 * @author astanley
 */
class Blank extends Rules\Not
{
    public function __construct()
    {
        parent::__construct(
            new Required()
        );
    }
}
