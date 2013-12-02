<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

require_once 'X937Record.php';

/**
 * 
 */
class X937RecordWriter {
    private $X937Record;
    
    public function __construct(X937Record $X937Record) {
	$this->X937Record = $X937Record;
    }
    
    public function write() {
	// check for records we current haven't implemented.
	if ($this->X937Record instanceof X937RecordGeneric) {
	    return "Record type" . ' ' . $this->X937Record->getRecordType() . ' ' . 'currently unhandled.';
	}
	
	$output = '';
	
	foreach ($this->X937Record as $field) {
   	    $output .= $field->getFieldName() . ': ' . $field->getValue() . PHP_EOL;
	}
	
	return $output;
    }
}