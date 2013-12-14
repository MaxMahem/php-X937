<?php

namespace X937\Writer;

use X937\Fields\Field;
use X937\Record as Record;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Record' . DIRECTORY_SEPARATOR . 'Record.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fields' .  DIRECTORY_SEPARATOR . 'Field.php';

require_once 'Writer.php';
/**
 * Outputs record data in ASCII, with system line-endings at end of record.
 * Binary data is discarded.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Flat extends Writer implements WriterInterface
{
    const FORMAT_ASCII_US = 'ASCII-US';
    
    public function __construct(
	    \SplFileObject $resource,
	    array $options                  = array(),
	    \X937\Writer\Image $imageWriter = NULL
	    )
    {	
	parent::__construct($resource, $options, $imageWriter);
    }

    public function write(Record\Record $record) {
	$recordType = $record->getType();
	
	// check for Record we current haven't implemented.
	if (array_key_exists($recordType, Record\Factory::handledRecordTypes()) === FALSE) {
	    return PHP_EOL;
	}
	
	$output = '';
	
	/**
	 * @todo image handling!
	 */
	// pass our data to the imageWriter.
	// $imageData = $this->imageWriter->write($record);
	
	foreach ($record as $field) {
	    $output .= ($field->getType() === Field::TYPE_BINARY) ? '' : $field->getValue();
	}
	
	$output .= PHP_EOL;
	
	$this->resource->fwrite($output);
    }
}