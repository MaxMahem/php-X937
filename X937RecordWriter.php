<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

require_once 'X937Record.php';

/**
 * Simple X937 Writer class, parses though a record and prints a human readable readout of all records.
 */
class X937RecordWriter {
    /**
     * The X937Record we are writing.
     * @var X937Record
     */
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
	    $outputArray = array($field->getFieldName() . ':', $field->getValue(), $field->translatedValue());
   	    $output     .= implode(' ', $outputArray) . PHP_EOL;
	}
	
	return $output;
    }
}