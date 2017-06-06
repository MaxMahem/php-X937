<?php
$timeStart = microtime(true);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$file = new X937\X937File('C:\PHP-Projects\x937\test4.X937');

$fileFormat  = X937\Writer\Factory::FORMAT_FILE_HUMAN;
$filename    = 'human.txt';
$imageFormat = X937\Writer\Factory::FORMAT_BINARY_STUB;

$writerFlat = X937\Writer\Factory::Generate($fileFormat, $filename, $imageFormat);
// $writerFlat->setOptionOmitBlanks(true);
// $writerFlat->writeAll($file);

$writerX937 = X937\Writer\Factory::Generate(\X937\Writer\Factory::FORMAT_FILE_X937, 'C:\PHP-Projects\x937\new4.X937');
//

$count = 0;
foreach($file as $record) {
    if ($record->getType() == '25') {
//        echo $record->getData() . PHP_EOL;
        $onus = $record->getFieldByNumber('6')->getValue();
        $position = $record->getFieldByNumber('6')->getPosition();
        
        if (preg_match('/\/[0-9]{1,3}$/', $onus)) {
            echo "'$onus' BOGUS";

            $rawData = $record->getDataRaw();
            $data = $record->getData();
            
            $onusNoSpace = str_replace(' ', '', $onus);
            
            // split the string into an array at the /
            $onusExploded = explode('/', $onusNoSpace);
            // we want the last part which we pop off the array.
            $onusCheckNum = array_pop($onusExploded);
            // we now glue the other parts back together.
            $onusAccountNum = implode('/', $onusExploded);
            
            // 'check number' 500 is a valid trancode so we abort on it.
            if ($onusCheckNum == '500') {
                echo ' - ' . "Actually valid, aborting." . PHP_EOL;
                continue;
            }
            
            // if the first part of the account number matches the check number,
            // delete it and continue.
            if (preg_match("/^$onusCheckNum/", $onusAccountNum)) {
                echo ' - ' . "deleting checknum from start";
                $onusAccountNum = preg_replace("/^$onusCheckNum/", '', $onusAccountNum);
                
                // pad the number out to 4 digits.
                $onusCheckNum = str_pad($onusCheckNum, 4, '0', STR_PAD_LEFT);
            } else {
                // check number is suspect, nuke it?
                echo ' - ' . "check number suspect, nuked.";
                $onusCheckNum = '';
            }
            
            
            // glue back together and pad.
            $onusGlued = $onusAccountNum . '/' . $onusCheckNum;
            $onusPadded = str_pad($onusGlued, 20, ' ', STR_PAD_LEFT);
            
            $onusDataLength = strlen($onusPadded);
            
            if ($onusDataLength > 20) {
                echo "onus to long, aborting!" . PHP_EOL;
                continue;
            }
            
            echo ' - ' . "'$onusPadded' FIXED" . PHP_EOL;
            
            $newData = substr_replace($data, $onusPadded, $position-1, 20);
            
            $record->setData($newData);
        }        
    }
    
    $writerX937->writeRecord($record);
    
    
//    if ($count > 100) { break; }
    $count++;
}

$timeEnd  = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;