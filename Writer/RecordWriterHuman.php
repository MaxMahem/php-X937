<?php

namespace X937\Writer;

require_once 'RecordWriter.php';

/**
 * Simple X937 Writer class, parses though a record and prints a human readable
 * readout of all records.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class RecordWriterHuman extends RecordWriter {
    const OPTION_VALIDATE  = 'validate';
    const OPTION_TRANSLATE = 'translate';
    
    public function write() {
	$recordType = $this->record->getRecordType();
	
	// check for records we current haven't implemented.
	if (array_key_exists($recordType, Records\RecordFactory::handledRecordTypes()) === FALSE) {
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