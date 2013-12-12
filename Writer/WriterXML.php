<?php

namespace X937\Writer;

use X937\Fields\Field;
use X937\Fields\Predefined\FieldRecordType;

use X937\Records as Records;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'Record.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fields' .  DIRECTORY_SEPARATOR . 'Field.php';

require_once 'Writer.php';
/**
 * Outputs record data as an XML file.
 * Binary data is discarded.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class WriterXML extends Writer implements WriterInterface
{
    const CONTROL_ELEMENT_FILE        = 'File';
    const CONTROL_ELEMENT_CASH_LETTER = 'Cash_Letter';
    const CONTROL_ELEMENT_BUNDLE      = 'Bundle';
    /**
     * XMLWriter object for writing XML!
     * @var XMLWriter
     */
    private $XML;
    
    /**
     * URI (basically filepath) to our output resource. Default to user output.
     * @var string
     */
    private $xmlURI = 'php://output';
    
    /**
     * keeps track of the currently open elements.
     * @var array
     */
    private $openElements = array();
    
    /**
     * Create a new XML writer. Initialises a XML file.
     * @param string $xmlURI URI to write to. Possibly a file path.
     * @param array $options array of options (currently none).
     */
    public function __construct($xmlURI = 'php://output', array $options = array())
    {
	$this->XML = new \XMLWriter();
	
	$this->xmlURI = $xmlURI;
	
	// open our file for writing. Default just puts it to output.
	$this->XML->openURI($this->xmlURI);
	
	$this->XML->startDocument('1.0', 'UTF-8');
	$this->XML->setIndent(TRUE);
	
	parent::__construct($options);
    }
    
    public function __destruct() {	
	$this->XML->endDocument();
    }
    
    public function write(Records\Record $record)
    {
	$recordType = $record->getType();
	
	// check for records we current haven't implemented.
	if (array_key_exists($recordType, Records\RecordFactory::handledRecordTypes()) === FALSE) {
	    return PHP_EOL;
	}
	
	switch ($recordType) {
	    // Header records. Open the control element first and then write the
	    // record elements.
	    case FieldRecordType::FILE_HEADER:
		$this->openElement(self::CONTROL_ELEMENT_FILE);
		$this->writeElement($record);
		break;
	    case FieldRecordType::CASH_LETTER_HEADER:
		$this->openElement(self::CONTROL_ELEMENT_CASH_LETTER);
		$this->writeElement($record);
		break;
	    case FieldRecordType::BUNDLE_HEADER:
		$this->openElement(self::CONTROL_ELEMENT_BUNDLE);
		$this->writeElement($record);
		break;
	    
	    /**
	     * @todo Special handling for checks.
	     */
	    
	    // Control record. Write the record element first and then close the
	    // control element.
	    case FieldRecordType::BUNDLE_CONTROL:
		$this->writeElement($record);
		$this->closeElement(self::CONTROL_ELEMENT_BUNDLE);
		break;
	    case FieldRecordType::CASH_LETTER_CONTROL:
		$this->writeElement($record);
		$this->closeElement(self::CONTROL_ELEMENT_CASH_LETTER);
		break;
	    case FieldRecordType::FILE_CONTROL:
		$this->writeElement($record);
		$this->closeElement(self::CONTROL_ELEMENT_FILE);
		break;
	    default:
		$this->writeElement($record);
	}
	
	$output = '';
	
	foreach ($record as $field) {
	    // ignore binary data.
	    $output .= ($field->getType() === Field::TYPE_BINARY) ? '' : $field->getValue();
	}
	
	return $output;
    }
    
    private function writeElement(Records\Record $record)
    {
	// get record name, turn space to underscores.
	/**
	 * @todo change this function to a getName function... on record class
	 */
	$recordName  = FieldRecordType::translate($record->getType());
	$elementName = str_replace(' ', '_', $recordName);
	
	// start the record element
	$this->XML->startElement($elementName);
	
	// write all fields as element
	foreach ($record as $field) {
	    // get name turn spaces to underscore
	    $fieldName = $field->getName();
	    $elementName = str_replace(' ', '_', $fieldName);
	    
	    if($field->getType() === Field::TYPE_BINARY) {
		$value = 'Binary Data';
	    } else {
		$value = trim($field->getValue());
	    }
	    
	    /**
	     * @todo make this optional
	     */
	    // if after trimming we have no data, then ommit the field.
	    if ($value !== '') {
		$this->XML->writeElement($elementName, $value);
	    }
	}
	
	$this->XML->endElement();
    }
    
    private function openElement($element)
    {
	// push our element onto the end of our stack.
	array_push($this->openElements, $element);
	
	// open our element
	$this->XML->startElement($element);
    }
    
    private function closeElement($element)
    {
	// pop our element of of the end of our stack.
	$elementFromStack = array_pop($this->openElements);
	
	// check to make sure we are closing elements in proper order.
	if ($elementFromStack !== $element) {
	    throw new \Exception("Element closed out of order, possibly bad record data. Closing $element, last element on stack $elementFromStack");
	}
	
	$this->XML->endElement();
    }
}
