<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

namespace X937\Writer;

use X937\Record as Record;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Record' . DIRECTORY_SEPARATOR . 'Record.php';

interface WriterInterface {
    public function write(Record\Record $record);
}

abstract class Writer implements WriterInterface {
    const OPTION_PATH   = 'path';
    const OPTION_FORMAT = 'format';

    /**
     * Options array
     * @var array
     */
    protected $options;
    
    /**
     * Our resource for writing.
     * @var resource
     */
    protected $resource;


    /**
     * Our writer for writing images.
     * @var \X937\Writer\Image
     */
    protected $imageWriter;

    public function __construct(\SplFileObject $resource, \X937\Writer\Image $imageWriter, $options = array())
    {	
	$this->resource    = $resource;
	$this->imageWriter = $imageWriter;
	$this->options     = $options;
    }
    
    public function setOptions(array $options) {
	$this->options = array_merge($this->options, $options);
    }
    
    public function getOptions() {
	return $this->options;
    }

    public abstract function write(Record\Record $record);
    
    /**
     * The intent here is to handle the writing of 
     * @param \X937\Record\Record $record The record we are writing.
     */
    protected function writeImage(Record\Record $record) {
	// should we type check this?
	return $this->imageWriter->write($record);
    }
}