<?php

namespace X937\Validation\Rules;

use Respect\Validation\Rules;

/**
 * Description of numeric
 *
 * @author astanley
 */
class Numeric extends Rules\AllOf
{
    public function __construct() {
    parent::__construct(
            new Rules\StringType(),
            new Rules\NoWithespace(),
            new Rules\intVal()
        );
    }
}
