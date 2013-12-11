<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

namespace X937\Writer;

use X937\Records as Records;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'Record.php';

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