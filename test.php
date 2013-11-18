<?php

require_once 'X937File.php';

$file = new X937File("TEST.X937");


foreach($file as $record) {
    echo $file->key() . ' ' . $record->getRecordData() . PHP_EOL;
    break;
}

var_dump($file);
