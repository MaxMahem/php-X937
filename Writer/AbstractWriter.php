<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

namespace X937\Writer;

use X937\Record as Record;
use X937\Fields as Fields;

abstract class AbstractWriter implements WriterInterface {
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
     *
     * @var \X937\Writer\FieldInterface
     */
    protected $fieldWriter;
    
    protected $binaryFieldWriter;
    
    /**
     * Format for binary data.
     * @var stromg
     */
    protected $binaryFormat;

    public function __construct(
        \SplFileObject $resource,
        FieldInterface $fieldWriter,
        FieldInterface $binaryWriter,
        $options = array()
    ) {    
    $this->resource          = $resource;
    $this->fieldWriter       = $fieldWriter;
    $this->binaryFieldWriter = $binaryWriter;
    $this->options           = $options;
    }
    
    public function getOptions()
    {
    return $this->options;
    }
    
    /**
     * Define universal options. None currently.
     * @return array
     */
    public function defineOptions()
    {
    return array();
    }

    abstract public function writeRecord(Record\RecordInterface $record);
    
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

    /**
     * Calls the member fieldWriters to write the field as appropriate for its
     * data type.
     * @param \X937\Fields\Field $field the Field to be writen
     * @return string the field data formated appropriately.
     */
    protected function writeField(Fields\Field $field)
    {
    if ($field->type === Fields\Field::TYPE_BINARY) {
        return $this->binaryFieldWriter->writeField($field);
    } else {
        return $this->fieldWriter->writeField($field);
    }
    }
    
    /**
     * Set an option.
     * @param type $optionKey the option to set
     * @param type $value the option value
     */
    protected function setOption($optionKey, $value)
    {
    if (array_key_exists($optionKey, static::defineOptions()) === false) {
        trigger_error('Set undefined option', E_USER_WARNING);
    }
    $this->options[$optionKey] = $value;
    }
}