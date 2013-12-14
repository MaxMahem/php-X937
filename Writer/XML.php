<?php

namespace X937\Writer;

use X937\Fields\Field;
use X937\Fields\Predefined\RecordType;

use X937\Record as Record;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Record' . DIRECTORY_SEPARATOR . 'Record.php';
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
class XML extends Writer implements WriterInterface
{
    // control elements
    const CONTROL_ELEMENT_FILE        = 'File';
    const CONTROL_ELEMENT_CASH_LETTER = 'Cash_Letter';
    const CONTROL_ELEMENT_BUNDLE      = 'Bundle';
    const CONTROL_ELEMENT_ITEM        = 'Item';
    const CONTROL_ELEMENT_VIEW        = 'View';

    /**
     * XMLWriter object for writing XML!
     * @var XMLWriter
     */
    private $XML;
    
    /**
     * keeps track of the currently open elements.
     * @var array
     */
    private $openElements = array();
    
    /**
     * Create a new XML writer. Initialises a XML file.
     * @param \XMLWriter $xmlWriter an XML writer for writing, should be empty.
     * @param string $path URI to write to. Possibly a file path.
     * @param boolean $indent to indent the file or not.
     */
    public function __construct(
	    \XMLWriter $xmlWriter,
	    array $options = array(),
	    \X937\Writer\Image $imageWriter = NULL
	    )
    {	
	$xmlWriter->startDocument('1.0', 'UTF-8');
	$xmlWriter->setIndent(true);
	
	parent::__construct($xmlWriter, $options, $imageWriter);
    }
    
    public function __destruct() {	
	$this->resource->endDocument();
    }
    
    public function write(Record\Record $record)
    {
	$recordType = $record->getType();
	
	// check for Record we current haven't implemented.
	if (array_key_exists($recordType, Record\Factory::handledRecordTypes()) === FALSE) {
	    return PHP_EOL;
	}
	
	switch ($recordType) {
	    // Header Record. Open the control element first and then write the
	    // record elements.
	    case RecordType::VALUE_FILE_HEADER:
		$this->openElement(self::CONTROL_ELEMENT_FILE);
		$this->writeElement($record);
		break;
	    case RecordType::VALUE_CASH_LETTER_HEADER:
		$idValue = $record->getFieldByName('Cash Letter ID')->getValue();
		$this->openElement(self::CONTROL_ELEMENT_CASH_LETTER, $idValue);
		$this->writeElement($record);
		break;
	    case RecordType::VALUE_BUNDLE_HEADER:
		$idValue = $record->getFieldByName('Bundle ID')->getValue();
		$this->openElement(self::CONTROL_ELEMENT_BUNDLE, $idValue);
		$this->writeElement($record);
		break;
	    
	    // Item Record. There should only be one item detail record per 
	    // item group. Each new record marks the start of a new group.
	    case RecordType::VALUE_CHECK_DETAIL:
	    case RecordType::VALUE_RETURN_RECORD:
		// fall through
		$idValue = $record->getFieldByName('ECE Institution Item Sequence Number')->getValue();
		$this->openElement(self::CONTROL_ELEMENT_ITEM, $idValue);
		$this->writeElement($record);
		break;
	    
	    // Image view Record. There should only be one Image View Detail 
	    // each image view set (front and back).
	    case RecordType::VALUE_IMAGE_VIEW_DETAIL:
		$this->openElement(self::CONTROL_ELEMENT_VIEW);
		$this->writeElement($record);

		// imageWriter needs these for to get the extension/side.
		$this->imageWriter->write($record);
		break;
	    
	    // Control record. Write the record element first and then close the
	    // control element.
	    case RecordType::VALUE_BUNDLE_CONTROL:
		$this->closeBinaryElement();
		$this->writeElement($record);
		$this->closeElement(self::CONTROL_ELEMENT_BUNDLE);
		break;
	    case RecordType::VALUE_CASH_LETTER_CONTROL:
		$this->closeBinaryElement();
		$this->writeElement($record);
		$this->closeElement(self::CONTROL_ELEMENT_CASH_LETTER);
		break;
	    case RecordType::VALUE_FILE_CONTROL:
		$this->closeBinaryElement();
		$this->writeElement($record);
		$this->closeElement(self::CONTROL_ELEMENT_FILE);
		break;
	    default:
		$this->writeElement($record);
	}
    }
    
    private function writeElement(Record\Record $record)
    {
	// get record name, turn space to underscores.
	/**
	 * @todo change this function to a getName function... on record class
	 */
	$recordName  = RecordType::translate($record->getType());
	$elementName = str_replace(' ', '_', $recordName);
	
	// start the record element
	$this->resource->startElement($elementName);
	
	// write all fields as element
	foreach ($record as $field) {
	    // get name turn spaces to underscore
	    $fieldName = $field->getName();
	    $elementName = str_replace(' ', '_', $fieldName);
	    
	    if($field->getName() === 'Image Data') {
		$this->imageWriter->write($record);
	    } else {
		$value = $field->getValue(\X937\Fields\Field::FORMAT_SIGNIFIGANT);
	    }
	    
	    /**
	     * @todo make this optional
	     */
	    // if after trimming we have no data, then ommit the field.
	    if ($value !== '') {
		$this->resource->writeElement($elementName, $value);
	    }
	}
	
	$this->resource->endElement();
	$this->resource->flush();
    }
    
    private function openElement($element, $id = NULL)
    {
	// check to see if the current open element is a check, if it is, close it.
	$this->closeBinaryElement();
	
	// push our element onto the end of our stack.
	array_push($this->openElements, $element);
	
	// open our element
	$this->resource->startElement($element);
	
	// write an ID atribute
	$id = ltrim(trim($id), '0');
	if ($id !== '') {
	    $this->resource->writeAttribute('id', $id);
	}
    }
    /**
     * Item and view Record have no closing Record so closing them is tricky.
     * Call this function before any potentially non item/view record is open or
     * closed.
     */
    private function closeBinaryElement() {
	$openElement = array_pop($this->openElements);
	
	if (($openElement === self::CONTROL_ELEMENT_ITEM) 
		|| $openElement === self::CONTROL_ELEMENT_VIEW) {
	    // end our check element
	    $this->resource->endElement();
	} else {
	    // wasn't a check, push our elements back on the array.
	    array_push($this->openElements, $openElement);
	}
    }
    
    private function closeElement($element)
    {
	// check to see if we need to close a check element first.
	$this->closeBinaryElement();

	// pop our element of of the end of our stack.
	$elementFromStack = array_pop($this->openElements);
	
	// check to make sure we are closing elements in proper order.
	if ($elementFromStack !== $element) {
	    throw new \Exception("Element closed out of order, possibly bad record data. Closing $element, last element on stack $elementFromStack");
	}
	
	$this->resource->endElement();
    }
}