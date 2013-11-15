<?php

require_once 'X937File.php';

$file = new X937File("PATHTOTESTFILE");

foreach($file as $record) {
    echo $file->key() . ' ' . $record->getRecordData() . PHP_EOL;
}
