<?php
namespace X937\Writer;
/**
 * Outputs record data in ASCII, with system line-endings at end of record.
 * Binary data is discarded.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Flat extends AbstractWriter
{
    public function __construct($resource) {
        $textWriter = new \X937\Fields\Format\FormatRaw();
        $binaryWriter = new \X937\Fields\Format\FormatNone();
        parent::__construct($resource, $textWriter, $binaryWriter);
    }
    
    public function writeRecord(\X937\Records\Record $record)
    {
        $output = '';
        foreach ($record as $field) {
            $output .= $this->writeField($field);
        }
        $output .= PHP_EOL;
        $this->resource->fwrite($output);
    }
}