<?php

namespace X937\Writer;

require_once 'Flat.php';
require_once 'Human.php';
require_once 'Image.php';
require_once 'XML.php';

/**
 * A Factor for building writers.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Factory {
    /**
     * Any user added types.
     * @var array
     */
    private static $additionalTypes = array();

    // Writer type constants
    const TYPE_FLAT  = 'Flat';
    const TYPE_HUMAN = 'Human';
    const TYPE_XML   = 'XML';
    
    public static function defineTypes()
    {
	$baseTypes = array(
	    self::TYPE_FLAT  => 'Flat File',
	    self::TYPE_HUMAN => 'Human Readable File',
	    self::TYPE_XML   => 'XML File',
	);
	
	$legalTypes = array_merge($baseTypes, self::$additionalTypes);
	
	return $legalTypes;
    }
    
    public static function Generate($fileFormat, $filename, $imageFormat = Image::FORMAT_NONE, $imagePath = null)
    {
	if (array_key_exists($fileFormat, self::defineTypes()) === false) {
	    throw new \InvalidArgumentException("Invalid file format");
	}
	
	if (array_key_exists($imageFormat, Image::defineFormats()) === false) {
	    throw new \InvalidArgumentException("Invalid image format");
	}
	
	// build our image handler.
	switch ($imageFormat) {
	    case Image::FORMAT_BASE64;
	    case Image::FORMAT_BINARY:
	    case Image::FORMAT_NONE:
	    case Image::FORMAT_STUB:
		// delebirate fall through here.
		$imageHandler = new Image($imageFormat, null);
		break;
	    
	    case Image::FORMAT_FILE:
		if (is_string($imagePath) === false) {
		    throw new \InvalidArgumentExecption("imagePath must be a string.");
		}
		$imageHandler = new Image(Image::FORMAT_FILE, $imagePath);
	}
	
	// build our file object for the writer.
	switch ($fileFormat) {
	    case self::TYPE_FLAT:
	    case self::TYPE_HUMAN:
		// delebirate fall through.
		// SplObject will throw it's own exception if it can't write.
		$fileObject = new \SplFileObject($filename, 'wb');
		break;
	    case self::TYPE_XML:
		$fileObject = new \XMLWriter();
		$fileValid  = $fileObject->openUri($filename);
		if ($fileValid === false) {
		    throw new \InvalidArgumentException("Error openining $filename for writing.");
		}
		break;
	}
	
	// finaly build our object
	switch ($fileFormat) {
	    case self::TYPE_FLAT:
		return new Flat($fileObject, $imageHandler);
	}
    }
    //put your code here
}
