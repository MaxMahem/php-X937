<?php

require_once 'File.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'WriterHuman.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'WriterASCII.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'WriterImage.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'WriterXML.php';

$file = new X937\X937File("test.X937");

$count = 0;
$options = array(
    X937\Writer\WriterHuman::OPTION_TRANSLATE => TRUE,
    X937\Writer\WriterHuman::OPTION_VALIDATE  => TRUE,
);

$imageWriter = New X937\Writer\WriterImage($file, 'images');
$humanWriter = New X937\Writer\WriterHuman($options);
$asciiWriter = New X937\Writer\WriterASCII();
$xmlWriter   = New X937\Writer\WriterXML('test.xml');

$fileASCII = fopen('test.txt', 'w');

foreach($file as $record) {
    // write human readable.
    // echo $humanWriter->write($record) . PHP_EOL;
    
    // write ASCII.
    fwrite($fileASCII, $asciiWriter->write($record) . PHP_EOL);
    
    // write Images.
    // $imageWriter->write($record);
    
    // write XML
    $xmlWriter->write($record);
    
//    if ($count > 100) { break; }
}