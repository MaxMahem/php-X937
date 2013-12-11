<?php

require_once 'File.php';
require_once 'RecordWriter.php';

$file = new X937\X937File("test.X937");

$count = 0;
$options = array(
    X937\Writer\RecordWriterSimple::OPTION_TRANSLATE => TRUE,
    X937\Writer\RecordWriterSimple::OPTION_VALIDATE  => TRUE,
);

foreach($file as $record) {
    $recordWriter = new X937\Writer\RecordWriterSimple($record, $options);
    echo $recordWriter->write() . PHP_EOL;
    $count++;
    if ($count > 100) { break; }
}