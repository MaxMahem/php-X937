<?php

namespace X937\Record;
/**
 *
 * @author astanley
 */
interface RecordInterface extends \ArrayAccess, \Countable, \IteratorAggregate {
    public function parse(string $data, string $dataType): bool;
}
