<?php

require_once 'File.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'Factory.php';

$file = new X937\X937File('C:\PHP-Projects\x937\test.X937');

// $fileFormat  = \X937\Writer\Factory::FORMAT_FILE_HUMAN;
$fileFormat  = \X937\Writer\Factory::FORMAT_FILE_HUMAN;
$filename    = 'human.txt';
$imageFormat = \X937\Writer\Factory::FORMAT_BINARY_STUB;

$writerFlat = \X937\Writer\Factory::Generate($fileFormat, $filename, $imageFormat);

$writerX937 = \X937\Writer\Factory::Generate(\X937\Writer\Factory::FORMAT_FILE_X937, 'new.X937');

// $writerFlat->setOptionOmitBlanks(true);

$timeStart = microtime(true);

$count = 0;
//foreach($file as $record) {
//    $writerFlat->writeRecord($record);
    
//    if ($count > 10) { break; }
//    $count++;
//}

$count = 0;
foreach($file as $record) {
    if ($record->getType() == '25') {
//        echo $record->getData() . PHP_EOL;
        $onus = $record->getFieldByNumber('6')->getValue();
        $position = $record->getFieldByNumber('6')->getPosition();
        
        if (preg_match('/\//', $onus)) {
//            echo "'$onus' VALID" . PHP_EOL;
        } else {
            $rawData = $record->getDataRaw();
            $data = $record->getData();
            
            $onusNoSpace = str_replace(' ', '', $onus);            
            $onusSlashAdded = $onusNoSpace . '/';            
            $onusPadded = str_pad($onusSlashAdded, 20, ' ', STR_PAD_LEFT);
            
            $onusDataLength = strlen($onusPadded);
            
            if ($onusDataLength > 20) {
                echo "onus to long, aborting!";
                break;
            }
            
            $newData = substr_replace($data, $onusPadded, $position-1, 20);
            
            $record->setData($newData);
        }        
    }
    
    $writerX937->writeRecord($record);
    
 //   if ($record->getLength() != $GLOBALS['length']) {
        echo "Read: " . $GLOBALS['length'] . " Written: " . $record->getLength() . " Type: " . $record->getType() . PHP_EOL;
  //  }
    
//    if ($count > 100) { break; }
    $count++;
}

$timeEnd  = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;