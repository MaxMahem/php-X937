<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

namespace X937\Writer;

use X937\Records as Records;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'Record.php';

interface WriterInterface {
    public function write(Records\Record $record);
}

abstract class Writer implements WriterInterface {     
    /**
     * Options array
     * @var array
     */
    protected $options;

    public function __construct(array $options = array()) {
	$this->options = $options;
    }
    
    public function setOptions(array $options) {
	$this->options = array_merge($this->options, $options);
    }
    
    public function getOptions() {
	return $this->options;
    }

    public abstract function write(Records\Record $record);
}