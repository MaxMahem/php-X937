<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

namespace X937\Writer;

use X937\Records as Records;
use X937\Fields  as Fields;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'Record.php';

interface Writer {
    public function write();
}

abstract class RecordWriter implements Writer {  
    /**
     * The X937Record we are going to write.
     * @var X937Record
     */
    protected $record;
    
    /**
     * The options for printing.
     * @var array
     */
    protected $options;

    public function __construct(Records\Record $record, array $options = array()) {
	$this->record  = $record;
	$this->options = $options;
    }
    
    public function setOptions(array $options) {
	$this->options = array_merge($this->options, $options);
    }
    
    public function getOptions() {
	return $this->options;
    }

    public abstract function write();
}

/**
 * Simple X937 Writer class, parses though a record and prints a human readable readout of all records.
 */
class RecordWriterSimple extends RecordWriter {
    const OPTION_VALIDATE  = 'validate';
    const OPTION_TRANSLATE = 'translate';
    
    public function write() {
	$recordType = $this->record->getRecordType();
	
	// check for records we current haven't implemented.
	if (array_key_exists($recordType, Records\Factory::handledRecordTypes()) === FALSE) {
	    return "Record type $recordType" . ' ' . Fields\RecordType::translate($recordType) . ' ' . 'currently unhandled.';
	}
	
	$fieldsFailedValidation = 0;
	$outputArray = array();
	
	foreach ($this->record as $field) {
	    // reset our output array
	    $fieldOutputArray = array();
	    
	    // we build an array of what we want to output.
	    // These items are mandatory.
	    $fieldOutputArray[] = $field->getFieldName() . ':';
	    $fieldOutputArray[] = $field->getValueFormated();
	    
	    // these items are conditional
	    if ($this->options[self::OPTION_TRANSLATE] === TRUE) { $fieldOutputArray[] = $field->translatedValue(); }
	    if ($this->options[self::OPTION_VALIDATE]  === TRUE) {
		$validation = $field->validate();
		$fieldOutputArray[] = ($validation) ? 'Validated' : 'Validation Failed';
		
		// if we fail validation, incrament the failed validatoin counter.
		if ($validation === FALSE) { $fieldsFailedValidation++; } 
	    }

	    // implode the array to build our string, append it to the output.
	    $outputArray[] .= implode(' ', $fieldOutputArray);
	}
	
	// display record summary if we are doing validation.
	if ($this->options[self::OPTION_VALIDATE]) {
	    $recordCount = count($this->record);
	    $recordValidation = ($fieldsFailedValidation === 0) ? 'The Record is Valid' : 'The Record is Invalid';
	    $outputArray[] .= "$fieldsFailedValidation of $recordCount fields failed validation. $recordValidation";
	}
	
	return implode(PHP_EOL, $outputArray) . PHP_EOL;
    }
}