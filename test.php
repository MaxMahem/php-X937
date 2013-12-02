<?php

require_once 'X937File.php';
require_once 'X937RecordWriter.php';

$file = new X937File("test.X937");

$count = 0;

foreach($file as $record) {
    $recordWriter = new X937RecordWriter($record);
    echo $recordWriter->write() . PHP_EOL;
    $count++;
    if ($count > 100) { break; }
}