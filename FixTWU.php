<?php

// Program expect to get an input path, and an output path.

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$inputDirectory = new DirectoryIterator($argv[0]);
$outputDirectory = $argv[1];

function fixTWUOnus(string $filename, string $outputDirectory)
{
    $file = new X937\X937File($filename);

    $writerX937 = \X937\Writer\Factory::Generate(\X937\Writer\Factory::FORMAT_FILE_X937, $outputDirectory . $filename);

    // loop through each record, and correct record type 25s that have an onus 
    // field (6) that doesn't match our format. Writer everything back out.
    foreach ($file as $record) {
        if ($record->getType() == '25') {
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

                $newData = substr_replace($data, $onusPadded, $position - 1, 20);

                $record->setData($newData);
            }
        }

        $writerX937->writeRecord($record);
    }
}

$timeStart = microtime(true);

foreach ($inputDirectory as $fileInfo) {
    if ($fileinfo->getExtension == 'X937') {
        fixTWUOnus($fileinfo->getPathname, $outputDirectory);
    }
}

$timeEnd = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;