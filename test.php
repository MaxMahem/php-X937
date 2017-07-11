<?php
$time = microtime(true);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$file = new X937\File('..\test.X937', '.\ANSI-X9-100-187-Records.xml');

X937\X937Transform::transformRecordsXML('ANSI-X9-100-187-Records.xml');
X937\X937Transform::transformStructureXML('ANSI-X9-100-187-Structure.xml');
die();

//$fieldFactory = new X937\Fields\FieldFactory();
//
//$fieldTemplate['type'] = 'ANS';
//$field = $fieldFactory->generateField($fieldTemplate);

//$humanFile = new SplFileObject('..\Human.txt', 'wb');
//$writerHuman = new X937\Writer\HumanFileWriter($humanFile, true);
//$writerHuman->writeAll($file);
//echo "Human written: ", microtime(true) - $time, PHP_EOL;
//$time = microtime(true);
//
//$flatFile = new SplFileObject('..\Flat.txt', 'wb');
//$writerFlat = new X937\Writer\FlatFileWriter($flatFile);
//$writerFlat->writeAll($file);
//echo "Flat written: ", microtime(true) - $time, PHP_EOL;
//$time = microtime(true);

$filename = '..\xml.xml';
$xmlObject = new \XMLWriter();
$xmlObject->openUri($filename);
$writerXML = new X937\Writer\XMLFileWriter($xmlObject, [X937\Writer\XMLFileWriter::OPTION_STUB => true]);
$writerXML->writeAll($file);
echo "XML written: ", microtime(true) - $time, PHP_EOL;
$time = microtime(true);
die();

$X937File = new SplFileObject('..\X937.x937', 'wb');
$writerX937 = new X937\Writer\X937FileWriter($X937File);
$writerX937->writeAll($file);
echo "X937 written: ", microtime(true) - $time, PHP_EOL;
die();

gc_collect_cycles();
$startMem = memory_get_usage() / 1024;
echo "Starting Memory: $startMem" . PHP_EOL;

//foreach(\X937\Filter::getCashLetters($file) as $cashletter) {
//    $string[] = round(memory_get_usage()/1024 - $startMem);
//    $string[] = count($cashletter);
//    $string[] = round($string[0]/$string[1]);
//    echo implode(' ', $string) . PHP_EOL;
//    $string = [];
//    gc_collect_cycles();
//
////    var_dump($cashletter);
//}
//
//die();

foreach ($file as $record) {
    echo $record->validate();

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
    $writerX937->writeRecord($record);
}

$timeEnd = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;