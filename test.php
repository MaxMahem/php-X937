<?php

require_once 'X937File.php';

$file = new X937File("PATHTOX937FILE");
$file->readAllRecords();

$record = $file->getRecord(1);

print_r($record);

print_r($file->getRecordsByType(X937Record::FILE_HEADER));