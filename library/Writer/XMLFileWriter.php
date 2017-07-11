<?php namespace X937\Writer;

use X937\Records;

/**
 * Outputs record data as an XML file.
 * Binary data is discarded.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class XMLFileWriter implements WriterInterface
{
    // control elements
    const CONTROL_ELEMENT_FILE = 'File';
    const CONTROL_ELEMENT_CASH_LETTER = 'CashLetter';
    const CONTROL_ELEMENT_BUNDLE = 'Bundle';
    const CONTROL_ELEMENT_ITEM = 'Item';
    const CONTROL_ELEMENT_VIEW = 'View';

    const OPTION_SKIP_BLANKS = 'skipBlanks';
    const OPTION_STUB = 'stub';
    const OPTION_INDENT = 'indent';
    const OPTION_INDENT_STRING = 'indentString';
    const OPTION_XSD = 'xsd';
    
    const DEFAULT_OPTIONS = array(
        self::OPTION_SKIP_BLANKS => true,
        self::OPTION_STUB => false,
        self::OPTION_INDENT => true,
        self::OPTION_INDENT_STRING => '    ',
        self::OPTION_XSD => 'ANSI-X9-100-187-Structure.xsd',
    );
    
    /**
     * @var array
     */
    private $options;
    
    /**
     * @var \XMLWriter
     */
    private $XMLWriter;

    /**
     * keeps track of the currently open elements.
     * @var array
     */
    private $openElements = array();
    
    /**
     * @var \DOMElement
     */
    private $openDOM;
    
    /**
     * @var \DOMDocument
     */
    private $documentDOM;
    
    /**
     * @var \X937\Fields\Format\TextFormatInterface
     */
    protected $textWriter;

    /**
     * @var \X937\Fields\Format\BinaryFormatInterface
     */
    protected $binaryWriter;

    /**
     * Create a new XML writer. Initialises a XML file.
     * @param \XMLWriter $xmlWriter an XML writer for writing, should be empty.
     * @param string $path URI to write to. Possibly a file path.
     * @param boolean $indent to indent the file or not.
     */
    public function __construct(
        \XMLWriter $xmlWriter,
        array $options = array()
    )
    {
        // merge default and user options, user options override defaults.
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
        
        $this->XMLWriter = $xmlWriter;
        
        $this->XMLWriter->startDocument('1.0', 'UTF-8');
        $this->XMLWriter->setIndent($this->options[self::OPTION_INDENT]);
        $this->XMLWriter->setIndentString($this->options[self::OPTION_INDENT_STRING]);
        
        $this->textWriter = new \X937\Fields\Format\FormatSignifigant();
        $this->binaryWriter = new \X937\Fields\Format\FormatByteCount();
    }

    public function __destruct()
    {
        $this->XMLWriter->endDocument();
    }
    
    /**
     * Shortcut function, write's all records in the file.
     * @param \X937\X937File $file
     */
    public function writeAll(\X937\File $file)
    {
        foreach ($file as $record) {
            $this->writeRecord($record);
        }
    }
    
    public static function camelCase(string $string)
    {
        // non-alpha and non-numeric characters become spaces
        $string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
        $string = trim($string);
        // uppercase the first character of each word
        $string = ucwords($string);
        $string = str_replace(" ", "", $string);

        return $string;
    }

    public function writeRecord(Records\Record $record)
    {
        $lastOpenElement = end($this->openElements);
        switch ($record->type) {
            // Header Record. Open the control element first and then write the
            // record elements.
            case Records\RecordType::FILE_HEADER:
                $this->openELement(self::CONTROL_ELEMENT_FILE);
                $this->XMLWriter->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $this->XMLWriter->writeAttribute('xsi:noNamespaceSchemaLocation', $this->options[self::OPTION_XSD]);
                $this->writeElement($record);
                break;
            case Records\RecordType::CASH_LETTER_HEADER:
                $idValue = $record['Cash Letter ID']->getValue();
                $this->openElement(self::CONTROL_ELEMENT_CASH_LETTER, $idValue);
                $this->writeElement($record);
                break;
            case Records\RecordType::BUNDLE_HEADER:
//                $idValue = $record['Bundle ID']->getValue();
                $this->openElement(self::CONTROL_ELEMENT_BUNDLE);
                $this->writeElement($record);
                break;

            // Item Record. There should only be one item detail record per
            // item group. Each new record marks the start of a new group.
            case Records\RecordType::CHECK_DETAIL:
            case Records\RecordType::RETURN_RECORD:
                // fall through
                $this->closeIfOpen([self::CONTROL_ELEMENT_VIEW, self::CONTROL_ELEMENT_ITEM]);
                
                $idValue = $record['ECE Institution Item Sequence Number']->getValue();
                $this->openElement(self::CONTROL_ELEMENT_ITEM, $idValue, $this->openDOM);
                $this->writeElement($record);
                break;

            // Image view Record. There should only be one Image View Detail
            // each image view set (front and back).
            case Records\RecordType::IMAGE_VIEW_DETAIL:
                $this->closeIfOpen([self::CONTROL_ELEMENT_VIEW]);
                $this->openElement(self::CONTROL_ELEMENT_VIEW);
                $this->writeElement($record);
                break;
            
            case Records\RecordType::IMAGE_VIEW_DATA:
                $idValue = $record['ECE Institution Item Sequence Number']->getValue();
                $this->writeElement($record);
                break;

            // Control record. Write the record element first and then close the
            // control element.
            case Records\RecordType::BUNDLE_CONTROL:
                $this->closeIfOpen([self::CONTROL_ELEMENT_VIEW, self::CONTROL_ELEMENT_ITEM]);
                $this->writeElement($record);
                $this->closeElement(self::CONTROL_ELEMENT_BUNDLE);
                break;
            case Records\RecordType::CASH_LETTER_CONTROL:
                $this->writeElement($record);
                $this->closeElement(self::CONTROL_ELEMENT_CASH_LETTER);
                break;
            case Records\RecordType::FILE_CONTROL:
                $this->writeElement($record);
                $this->closeElement(self::CONTROL_ELEMENT_FILE);
                break;
            default:
                $this->writeElement($record);
        }
    }
    
    private function openElement(string $openElement, string $id = NULL)
    {
        // push our element onto the end of our stack.
        array_push($this->openElements, $openElement);
        
        $this->XMLWriter->startElement($openElement);
        
        // write an ID atribute
        $id = ltrim(trim($id), '0');
        if ($id !== '') {
            $this->XMLWriter->writeAttribute('id', $id);
        }
    }
    
    private function closeIfOpen(array $elementsToClose) {
        while (in_array(end($this->openElements), $elementsToClose)) {
            $this->closeElement(end($this->openElements));
        }
    }

    private function writeElement(Records\Record $record)
    {
        $recordName = self::camelCase($record->name);
        // if we are writing stubs, append that.
        $recordName .= ($this->options[self::OPTION_STUB]) ? 'Stub' : '';
        
        // start the record element        
        $this->XMLWriter->startElement($recordName);
        $this->XMLWriter->writeAttribute(Records\Record::PROP_TYPE, $record->type);
        
        // if our option is stub, we write no fields.
        if (!$this->options[self::OPTION_STUB]) {
            // write all fields as element
            foreach ($record as $field) {
                $fieldName = self::camelCase($field->name);

                if ($field->type === \X937\Fields\FieldType::BINARY) {
                    $value = $this->binaryWriter->format($field);
                } else {
                    $value = $this->textWriter->format($field);
                }

                // if after trimming we have no data, then ommit the field.
                if ((trim($value) === '')
                        && ($this->options[self::OPTION_SKIP_BLANKS])
                        && ($field->usage !== \X937\Fields\Field::USAGE_MANDATORY))
                {
                    // omit field.
                } else {
                    $this->XMLWriter->writeElement($fieldName, $value);
                }
            }
        }

        $this->XMLWriter->endElement();
        $this->XMLWriter->flush();
    }

    private function closeElement($element)
    {
        // pop our element of of the end of our stack.
        $elementFromStack = array_pop($this->openElements);

        // check to make sure we are closing elements in proper order.
        if ($elementFromStack !== $element) {
            throw new \Exception("Element closed out of order, possibly bad record data. Closing $element, last element on stack $elementFromStack");
        }

        $this->XMLWriter->endElement();
        $this->XMLWriter->flush();
    }
    
    private function flatten_array(array $array) {
       $flattenedArray = array();
       array_walk_recursive($array, function($item) use (&$flattenedArray) { $flattened_array[] = $item; });
       return $flattenedArray;
   }
}