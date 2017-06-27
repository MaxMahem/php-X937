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
        $fieldWriter = new Formater\Text\Translate();
        $binaryWriter = new Formater\Binary\Stub();
        parent::__construct($resource, $fieldWriter, $binaryWriter);
    }

    public function writeRecord(\X937\Records\Record $record)
    {
        // initilise an array used for all the output.
        $outputArray = array();

        // parse over each field in the record.
        foreach ($record as $field) {
            // reset our field output array
            $fieldOutputArray = array();

            $fieldOutputArray['name'] = $field->name . ':';
            $fieldOutputArray['value'] = $this->writeField($field);

            // adding to the output array is optional, we dont' want to do it
            // for blank fields if the OMIT_BLANKS option is set.
            /**
             * @todo Simply this logic, should be able to do it with one if.
             */
            if ($this->omitBlanks === true) {
                if ((trim($fieldOutputArray['value']) !== '')) {
                    $outputArray[] = implode(' ', $fieldOutputArray);
                }
            } else {
                $outputArray[] = implode(' ', $fieldOutputArray);
            }
        }

        $output = implode(PHP_EOL, $outputArray) . PHP_EOL . PHP_EOL;
        $this->resource->fwrite($output);
    }
}