<?php

namespace X937\Writer;

use X937\Record as Record;
use X937\Fields  as Fields;

/**
 * Simple X937 Writer class, parses though a record and prints a human readable
 * readout of all Record.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Human extends AbstractWriter
{
    const OPTION_TRANSLATE = 'translate';
    
    public function writeRecord(Record\Record $record) {
	$outputArray = array();
	
	foreach ($record as $field) {
	    // reset our output array
	    $fieldOutputArray = array();
	    
	    // we build an array of what we want to output.
	    // These items are mandatory.
	    $fieldOutputArray[] = $field->getName() . ':';
	    $fieldOutputArray[] = $field->getValueFormated();
	    
	    // these items are conditional
	    if ($this->options[self::OPTION_TRANSLATE] === TRUE) {
		$fieldOutputArray[] = $field->translatedValue();
	    }

	    // implode the array to build our string, append it to the output.
	    $outputArray[] .= implode(' ', $fieldOutputArray);
	}
	
	$output = implode(PHP_EOL, $outputArray) . PHP_EOL;
	$this->resource->fwrite($output);
    }
}