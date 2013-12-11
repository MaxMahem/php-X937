<?php

require_once 'File.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'RecordWriterHuman.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'RecordWriterASCII.php';

$file = new X937\X937File("test.X937");

$count = 0;
$options = array(
    X937\Writer\RecordWriterHuman::OPTION_TRANSLATE => TRUE,
    X937\Writer\RecordWriterHuman::OPTION_VALIDATE  => TRUE,
);

foreach($file as $record) {
    $recordWriter = new X937\Writer\RecordWriterASCII($record);
    $recordWriter = new X937\Writer\RecordWriterHuman($record, $options);
    echo $recordWriter->write() . PHP_EOL;
    $count++;
    if ($count > 100) { break; }
}