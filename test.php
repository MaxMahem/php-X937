<?php
$timeStart = microtime(true);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$file = new X937\File('C:\PHP-Projects\x937\test4.X937');

$fileFormat  = X937\Writer\Factory::FORMAT_FILE_HUMAN;
$filename    = '..\human.txt';
$imageFormat = X937\Writer\Factory::FORMAT_BINARY_STUB;
$writerHuman = X937\Writer\Factory::Generate($fileFormat, $filename, $imageFormat);
$writerHuman->setOptionOmitBlanks(true);
// $writerHuman->writeAll($file);

$fileFormat  = X937\Writer\Factory::FORMAT_FILE_FLAT;
$filename    = '..\flat.txt';
$imageFormat = X937\Writer\Factory::FORMAT_BINARY_NONE;
$writerFlat  = X937\Writer\Factory::Generate($fileFormat, $filename, $imageFormat);
// $writerFlat->writeAll($file);

$writerX937 = X937\Writer\Factory::Generate(\X937\Writer\Factory::FORMAT_FILE_X937, '..\new4.X937');

$count = 0;
foreach($file as $record) {
    $result = $record->validate();   
    echo $result;
        
    if ($record->type == '25') {
//        $record[4]->set('NOTANUME');
//        echo $record->validate();
        
//        try {
//            $record[4]->validate();
//        } catch (\Exception $exception) {
//            trigger_error(implode(PHP_EOL, $exception->getMessages()));
//        }
        
        $onusField = $record['6'];
        $onusValue = $onusField->getValue();
        
        // we want to catch all records ending with /### which may be suspect.
        if (preg_match('/\/[0-9]{1,3}$/', $onusValue)) {
            echo "'$onusValue' BOGUS";
            
            $onusNoSpace = str_replace(' ', '', $onusValue);
            
            // it's possible that the onus could have more than 1 onus symbol, 
            // in which case we wan't the *last* section of it. This does that.
            // the last part we will call the 'check number' and the first part
            // the account number.
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
            // delete it.
            if (preg_match("/^$onusCheckNum/", $onusAccountNum)) {
                echo ' - ' . "deleting checknum from start";
                $onusAccountNum = preg_replace("/^$onusCheckNum/", '', $onusAccountNum);
                
                // and pad the number out to 4 digits.
                $onusCheckNum = str_pad($onusCheckNum, 4, '0', STR_PAD_LEFT);
            } else {
                // check number is suspect, nuke it?
                echo ' - ' . "check number suspect, nuked.";
                $onusCheckNum = '';
            }
            
            // glue back together and pad out to field length
            $onusGlued = $onusAccountNum . '/' . $onusCheckNum;
            $onusPadded = str_pad($onusGlued, $onusField->length, ' ', STR_PAD_LEFT);
            
            $onusDataLength = strlen($onusPadded);
            
            if ($onusDataLength > $onusField->length) {
                echo "onus to long, aborting!" . PHP_EOL;
                continue;
            }
            
            echo ' - ' . "'$onusPadded' FIXED" . PHP_EOL;
            
            $onusField->set($onusPadded);
        }        
    }
    
    // write the file with our changes.
    // $writerX937->writeRecord($record);

    $count++;
}

$timeEnd  = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;