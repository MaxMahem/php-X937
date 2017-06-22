<?php

namespace X937;

/**
 * Description of CashLetter
 *
 * @author astanley
 */
class CashLetter extends Container {
    private $records;
    
    public function __construct(array $records) {
        $this->records = $records;
    }
    
    public function getBundles(): Bundle {
        
    }
    
    public function validate(): string {
        $error = '';
        
        foreach($this->records as $record) {
            $error .= $record->validate();
        }
        
        return $error;
    }
}
