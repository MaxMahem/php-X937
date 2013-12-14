<?php

namespace X937\Writer;

use X937\Fields as Fields;
use X937\Fields\VariableLength\Binary\BinaryData;

use X937\Record as Record;

/**
 * Writes images from a check file to disk.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Image extends Writer implements WriterInterface
{
    // the format we are going to write out in.
    const FORMAT_FILE   = 'file';
    const FORMAT_BASE64 = 'base64';
    const FORMAT_BINARY = 'binary';
    const FORMAT_NONE   = 'none';
    const FORMAT_STUB   = 'stub';
    
    /**
     * Image format for the associated record.
     * @var string
     */
    private $imageExtension = 'tif';
    
    /**
     *
     * @var string
     */
    private $viewSide = Fields\Predefined\FieldViewSide::VALUE_FRONT;
    
    /**
     * Create a WriterImage, for writing check images to disk.
     * @param string $optionFormat the format to write our image file in.
     * @param string $path Path to folder to write the image files in if
     * it does not exist, it is created.
     */
    public function __construct($format = self::FORMAT_BASE64, $path = '.')
    {
	if ($format === self::FORMAT_FILE) {
	    if (is_writable($path) === FALSE) {
		$result = mkdir($path, 0777, true);
		if ($result === FALSE) {
		    throw new \InvalidArgumentException("Error writing to $path");
		}
	    }
	}
	
	$options = array(
	    self::OPTION_FORMAT => $format,
	    self::OPTION_PATH   => $path
	);
	
	// generate a dummy resource for the parent.
	$dummyResource = fopen('php://output', 'wb');
	
	parent::__construct($dummyResource, $options, $this);
    }
    
    public function write(\X937\Record\Record $record)
    {
	switch ($record->getType()) {
	    case Fields\Predefined\RecordType::VALUE_IMAGE_VIEW_DETAIL:
		$this->imageExtension = $record->getFieldByNumber(5)->getExtension();
		$this->viewSide       = $record->getFieldByNumber(8)->getValue();

		/**
		 * @todo: validation checks here.
		 */
		
		break;
	    case Fields\Predefined\RecordType::VALUE_IMAGE_VIEW_DATA:
		return $this->writeImage($record);
		break;
	}
    }
    
    private function writeImage($record)
    {
	$imageDataField = $record->getFieldByName('Image Data');
	// var_dump($imageDataField);
	
	switch ($this->options['format']) {
	    case self::FORMAT_NONE:
		return '';
	    
	    case self::FORMAT_STUB:
		return $imageDataField->getValue(Fields\Field::FORMAT_RAW);
	    
	    case self::FORMAT_BASE64:
		return $imageDataField->getValue(BinaryData::FORMAT_BASE64);
	    
	    case self::FORMAT_BINARY:
		return $imageDataField->getValue(BinaryData::FORMAT_BINARY);
	    
	    case self::FORMAT_FILE:
		return $this->writeImageBinary($record);
	}
    }
    
    private function writeImageBinary(Record\VariableLength\ImageViewData $record)
    {
	$path      = $this->options['path'];
	$fileId    = trim($record->getFieldByNumber(5)->getValue());
	$side      = $this->viewSide;
	$extension = $this->imageExtension;
	
	$filename =  $path . DIRECTORY_SEPARATOR . $fileId . '-' . $side . '.' . $extension;
	
	$file = fopen($filename, 'wb');
	
	if ($file === false) {
	    throw new \Exception("Unable to open $filename for writing");
	}
	
	fwrite($file, $record->getFieldByNumber(19)->getValueBinary());
	
	fclose($file);
	
	return $filename;
    }
}