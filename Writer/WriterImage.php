<?php

namespace X937\Writer;

use X937\Fields as Fields;

/**
 * Writes images from a check file to disk.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class WriterImage extends Writer implements WriterInterface
{
    /**
     * Holds our X937File we are going to write.
     * @var X937File
     */
    private $file;
    
    /**
     * Full path to the folder to write our files.
     * @var string
     */
    private $folderPath;
    
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
     * @param \X937\X937File $file
     * @param string $folderPath Path to folder to write the image files in if
     * it does not exist, it is created.
     */
    public function __construct(\X937\X937File $file, $folderPath = '.')
    {
	$this->file       = $file;
	$this->folderPath = $folderPath;
	
	if (is_writable($folderPath) === FALSE) {
	    $result = mkdir($folderPath, 0777, true);
	    if ($result === FALSE) {
		throw new \InvalidArgumentException("Error writing to $folderPath");
	    }
	}
    }
    
    public function write(\X937\Records\Record $record)
    {
	switch ($record->getType()) {
	    case Fields\Predefined\FieldRecordType::IMAGE_VIEW_DETAIL:
		$this->imageExtension = $record->getFieldByNumber(5)->getExtension();
		$this->viewSide       = $record->getFieldByNumber(8)->getValue();

		/**
		 * @todo: validation checks here.
		 */
		
		break;
	    case Fields\Predefined\FieldRecordType::IMAGE_VIEW_DATA:
		return $this->writeImage($record) . PHP_EOL;
		break;
	}
    }
    
    private function writeImage(\X937\Records\RecordImageViewData $record)
    {
	$filename  = trim($record->getFieldByNumber(5)->getValue()); // base
	$filename .= '-' . $this->viewSide;                          // side
	$filename .= '.' . $this->imageExtension;                    // extension
	
	// should we use 'x' here to prevent overwrite?
	$file = fopen($this->folderPath . DIRECTORY_SEPARATOR . $filename, 'wb');
	
	if ($file === FALSE) {
	    throw new Exception("Unable to open $filename for writing");
	}
	
	fwrite($file, $record->getFieldByNumber(19)->getValue());
	
	fclose($file);
	
	return $filename . ' ' . 'written';
    }
}