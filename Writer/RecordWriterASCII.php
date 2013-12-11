<?php

namespace X937\Writer;

use X937\Fields\Field;
use X937\Records as Records;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'Record.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fields' .  DIRECTORY_SEPARATOR . 'Field.php';

require_once 'RecordWriter.php';
/**
 * Outputs record data in ASCII, with system line-endings at end of record.
 * Binary data is discarded.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class RecordWriterASCII extends RecordWriter {    
    public function write() {
	$recordType = $this->record->getRecordType();
	
	// check for records we current haven't implemented.
	if (array_key_exists($recordType, Records\RecordFactory::handledRecordTypes()) === FALSE) {
	    return PHP_EOL;
	}
	
	$output = '';
	
	foreach ($this->record as $field) {
	    if ($field->getType() !== Field::TYPE_BINARY) {
		$output .= $field->getValue();
	    } else {
		$output .= '';
	    }
	}
	
	return $output;
    }
}