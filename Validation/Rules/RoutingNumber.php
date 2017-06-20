<?php

namespace X937\Validation\Rules;

use Respect\Validation\Rules;

/**
 * Description of RoutingNumber
 *
 * @author astanley
 */
class RoutingNumber extends Rules\AbstractRule {
    
    public function validate($input) {
        // guard function, can't validate on non-numeric values.
        if (!is_numeric($input)) { return false; }
        
        // calculate the routing number
        $sum  = 3 * ($input[0]+$input[3]+$input[6]);
        $sum += 7 * ($input[1]+$input[4]+$input[7]);
        $sum += 1 * ($input[2]+$input[5]+$input[8]);
        
        return !($sum % 10);
    }
}
