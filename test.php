<?php

require_once 'File.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'Factory.php';

$file = new X937\X937File("test.X937");

$count = 0;

$writerFlat = \X937\Writer\Factory::Generate(\X937\Writer\Factory::TYPE_FLAT, 'ascii.txt', \X937\Writer\Image::FORMAT_BASE64);

$timeStart = microtime(true);

foreach($file as $record) {
    $writerFlat->write($record);
    
    if ($count > 10) { break; }
}

$timeEnd  = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;