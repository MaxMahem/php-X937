<?php

require_once 'File.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'Human.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'Flat.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'Image.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Writer' .  DIRECTORY_SEPARATOR . 'XML.php';

$file = new X937\X937File("test.X937");

$count = 0;
$options = array(
    X937\Writer\Human::OPTION_TRANSLATE => TRUE,
);

$fileASCII = New \SplFileObject('ascii.txt', 'w');
$fileHuman = New \SplFileObject('human.txt', 'w');
$fileXML   = New \XMLWriter();
$fileXML->openURI('xml.xml');

$imageWriter = New X937\Writer\Image(X937\Writer\Image::FORMAT_FILE, 'image');
$imageWriterBase64 = New X937\Writer\Image(X937\Writer\Image::FORMAT_BASE64);
$humanWriter = New X937\Writer\Human($fileHuman, $options);
$flatWriter  = New X937\Writer\Flat($fileASCII);
$xmlWriter   = New X937\Writer\XML($fileXML, array(), $imageWriter);

$timeStart = microtime(true);

foreach($file as $record) {
    // write human readable.
    $humanWriter->write($record);
    
    // write ASCII.
    $flatWriter->write($record);
    
    // write Images.
//    $imageWriter->write($record);
    
    // write XML
    $xmlWriter->write($record);
    
    if ($count > 10) { break; }
}

$timeEnd  = microtime(true);
echo $timeEnd - $timeStart . PHP_EOL;