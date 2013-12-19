<?php

namespace X937\Writer;

use X937\Fields\Field;
use X937\Record as Record;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Record' . DIRECTORY_SEPARATOR . 'Record.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fields' .  DIRECTORY_SEPARATOR . 'Field.php';

/**
 * Outputs record data in ASCII, with system line-endings at end of record.
 * Binary data is discarded.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Flat extends AbstractWriter
{
    public function writeRecord(Record\Record $record) {	
	$output = '';
	
	foreach ($record as $field) {
	    $output .= $this->writeField($field);
	}
	
	$output .= PHP_EOL;
	
	$this->resource->fwrite($output);
    }
}