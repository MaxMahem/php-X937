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
    // type specific options.
    const OPTION_OMIT_BLANKS = 'Omit';
    
    public function defineOptions() {
    $parentOptions = parent::defineOptions();
    
    $legalOptions = array(
        self::OPTION_OMIT_BLANKS => 'Omit Blank Values: true/false',
    );
    
    return array_merge($parentOptions, $legalOptions);
    }
    
    public function setOptionOmitBlanks($value)
    {
    $this->setOption(self::OPTION_OMIT_BLANKS, (bool) $value);
    }
    
    public function writeRecord(\X937\Record\RecordInterface $record) {
    // initilise an array used for all the output.
    $outputArray = array();
    
    // parse over each field in the record.
    foreach ($record as $field) {
        // reset our field output array
        $fieldOutputArray = array();
        
        $fieldOutputArray['name']        = $field->name . ':';
            try {
                $fieldOutputArray['value']       = $this->writeField($field);
            } catch (\Exception $e) {
                echo "Error: Record Type#" . $record->getType() . " Field: " . $field->getName()
                        . ' ' . $e->getMessage() . PHP_EOL;
            }

        // adding to the output array is optional, we dont' want to do it
        // for blank fields if the OMIT_BLANKS option is set.
        /**
         * @todo Simply this logic, should be able to do it with one if.
         */
        if ($this->options[self::OPTION_OMIT_BLANKS] === true) {
        if (($fieldOutputArray['value'] !== '')) {
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