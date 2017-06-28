<?php
/**
 * Description of X937RecordWriter
 *
 * @author astanley
 */

namespace X937\Writer;

use X937\Fields;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * Our resource for writing.
     * @var resource
     */
    protected $resource;

    /**
     *
     * @var \X937\Writer\Format\TextFormatInterface
     */
    protected $textWriter;

    /**
     *
     * @var \X937\Writer\Format\BinaryFormatInterface
     */
    protected $binaryWriter;

    public function __construct(
        $resource,
        \X937\Writer\Format\TextFormatInterface $textWriter,
        \X937\Writer\Format\BinaryFormatInterface $binaryWriter
    )
    {
        $this->resource = $resource;
        $this->textWriter = $textWriter;
        $this->binaryWriter = $binaryWriter;
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

    abstract public function writeRecord(\X937\Records\Record $record);

    /**
     * Calls the member fieldWriters to write the field as appropriate for its
     * data type.
     * @param \X937\Fields\Field $field the Field to be writen
     * @return string the field data formated appropriately.
     */
    protected function writeField(Fields\Field $field)
    {
        if ($field->type === Fields\Type::BINARY) {
            return $this->binaryWriter->format($field);
        } else {
            return $this->textWriter->format($field);
        }
    }
}