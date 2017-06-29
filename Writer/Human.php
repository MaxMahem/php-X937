<?php

namespace X937\Writer;

/**
 * Simple X937 Writer class, parses though a record and prints a human readable
 * readout of all Record.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Human extends AbstractWriter
{
    private $omitBlanks;
    
    public function __construct($resource, bool $omitBlanks) {
        $this->omitBlanks = $omitBlanks;
        $textWriter = new \X937\Fields\Format\Formated();
        $binaryWriter = new \X937\Fields\Format\FormatByteCount();
        parent::__construct($resource, $textWriter, $binaryWriter);
    }

    public function writeRecord(\X937\Records\Record $record)
    {
        // parse over each field in the record.
        foreach ($record as $field) {
            $value = $this->writeField($field);

            // adding to the output array is optional, we dont' want to do it
            // for blank fields if the OMIT_BLANKS option is set.
            if (($this->omitBlanks === true) && empty(trim($value))) {
                // do nothing
            } else {
                $outputArray[] = "{$field->name}: $value";
            }
        }

        $output = implode(PHP_EOL, $outputArray) . PHP_EOL . PHP_EOL;
        $this->resource->fwrite($output);
    }
}