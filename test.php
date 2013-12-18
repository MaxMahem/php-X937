<?php

require_once 'File.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'Factory.php';

$file = new X937\X937File("test.X937");

$count = 0;

$fileFormat  = \X937\Writer\Factory::FORMAT_FILE_FLAT;
$filename    = 'ascii.txt';
$imageFormat = \X937\Writer\Factory::FORMAT_BINARY_NONE;

$writerFlat = \X937\Writer\Factory::Generate($fileFormat, $filename, $imageFormat);

$timeStart = microtime(true);

foreach($file as $record) {
    $writerFlat->writeRecord($record);
    
    if ($count > 10) { break; }
}

$timeEnd  = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;