<?php

namespace X937\Writer;

/**
 * Interface for Writer objects
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
interface WriterInterface {
    public function writeRecord(\X937\Record\RecordInterface $record);
    public function writeAll(\X937\File $file);
}