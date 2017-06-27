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
class XML extends AbstractWriter
{
    // control elements
    const CONTROL_ELEMENT_FILE = 'File';
    const CONTROL_ELEMENT_CASH_LETTER = 'Cash_Letter';
    const CONTROL_ELEMENT_BUNDLE = 'Bundle';
    const CONTROL_ELEMENT_ITEM = 'Item';
    const CONTROL_ELEMENT_VIEW = 'View';

    /**
     * XMLWriter object for writing XML!
     * @var \XMLWriter
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
        \XMLWriter $xmlWriter
    )
    {
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->setIndent(true);
        
        $fieldWriter = new Formater\Text\Formated();
        $binaryWriter = new Formater\Binary\Stub();

        parent::__construct($xmlWriter, $fieldWriter, $binaryWriter);
    }

    public function __destruct()
    {
        $this->resource->endDocument();
    }

    public function writeRecord(Records\Record $record)
    {
        switch ($record->type) {
            // Header Record. Open the control element first and then write the
            // record elements.
            case Records\Type::FILE_HEADER:
                $this->openElement(self::CONTROL_ELEMENT_FILE);
                $this->writeElement($record);
                break;
            case Records\Type::CASH_LETTER_HEADER:
                $idValue = $record['Cash Letter ID']->getValue();
                $this->openElement(self::CONTROL_ELEMENT_CASH_LETTER, $idValue);
                $this->writeElement($record);
                break;
            case Records\Type::BUNDLE_HEADER:
                $idValue = $record['Bundle ID']->getValue();
                $this->openElement(self::CONTROL_ELEMENT_BUNDLE, $idValue);
                $this->writeElement($record);
                break;

            // Item Record. There should only be one item detail record per
            // item group. Each new record marks the start of a new group.
            case Records\Type::CHECK_DETAIL:
            case Records\Type::RETURN_RECORD:
                // fall through
                $idValue = $record['Institution Item Sequence Number']->getValue();
                $this->openElement(self::CONTROL_ELEMENT_ITEM, $idValue);
                $this->writeElement($record);
                break;

            // Image view Record. There should only be one Image View Detail
            // each image view set (front and back).
            case Records\Type::IMAGE_VIEW_DETAIL:
                $this->openElement(self::CONTROL_ELEMENT_VIEW);
                $this->writeElement($record);

                // imageWriter needs these for to get the extension/side.
                // $this->imageWriter->write($record);
                break;

            // Control record. Write the record element first and then close the
            // control element.
            case Records\Type::BUNDLE_CONTROL:
                $this->closeBinaryElement();
                $this->writeElement($record);
                $this->closeElement(self::CONTROL_ELEMENT_BUNDLE);
                break;
            case Records\Type::CASH_LETTER_CONTROL:
                $this->closeBinaryElement();
                $this->writeElement($record);
                $this->closeElement(self::CONTROL_ELEMENT_CASH_LETTER);
                break;
            case Records\Type::FILE_CONTROL:
                $this->closeBinaryElement();
                $this->writeElement($record);
                $this->closeElement(self::CONTROL_ELEMENT_FILE);
                break;
            default:
                $this->writeElement($record);
        }
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
     * Item and view Records have no closing Records so closing them is tricky.
     * Call this function before any potentially non item/view record is open or
     * closed.
     */
    private function closeBinaryElement()
    {
        $openElement = array_pop($this->openElements);

        if (($openElement === self::CONTROL_ELEMENT_ITEM)
            || $openElement === self::CONTROL_ELEMENT_VIEW
        ) {
            // end our check element
            $this->resource->endElement();
        } else {
            // wasn't a check, push our elements back on the array.
            array_push($this->openElements, $openElement);
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

    private function writeElement(Records\Record $record)
    {
        // get record name, turn space to underscores.
        /**
         * @todo change this function to a getName function... on record class
         */
        $recordName = self::camelCase($record->name);

        // start the record element
        $this->resource->startElement($recordName);

        // write all fields as element
        foreach ($record as $field) {
            // get name turn spaces to underscore
            $fieldName = self::camelCase($field->name);

            if ($field->type === \X937\Fields\Type::BINARY) {
//                $this->imageWriter->write($record);
            } else {
                $value = trim($field->getValue());
            }

            /**
             * @todo make this optional
             */
            // if after trimming we have no data, then ommit the field.
            if ($value !== '') {
                $this->resource->writeElement($fieldName, $value);
            }
        }

        $this->resource->endElement();
        $this->resource->flush();
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