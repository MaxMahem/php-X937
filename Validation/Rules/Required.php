<?php

namespace X937\Validation\Rules;

use Respect\Validation\Rules;
use stdClass;

/**
 * Description of customNotBlank
 * derived from 
 *
 * @author astanley
 */
class Required extends Rules\AbstractRule
{
    public function validate($input) {
        // 0's are valid, so we return false if we get a numeric value.
        if (is_numeric($input)) {
            return true;
        }
        
        if (is_string($input)) {
            $input = trim($input);
        }

        if ($input instanceof stdClass) {
            $input = (array) $input;
        }

        if (is_array($input)) {
            $input = array_filter($input, __METHOD__);
        }

        return !empty($input);
    }
}