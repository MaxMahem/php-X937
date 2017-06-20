<?php namespace X937\Writer;

/**
 * Outputs record data in ASCII, with system line-endings at end of record.
 * Binary data is discarded.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class X937Writer extends AbstractWriter
{
    public function writeRecord(\X937\Record\Record $record) {    
    $output = '';
        
        $recordLengthData = pack('N', $record->length);
        
        $output .= $recordLengthData;
        
        $output .= $record->getData(\X937\Util::DATA_EBCDIC);
    
    $this->resource->fwrite($output);
    }
}