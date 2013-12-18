<?php

namespace X937\Writer;

require_once 'WriterInterface.php';
require_once 'AbstractWriter.php';

require_once 'FieldInterface.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Field' .  DIRECTORY_SEPARATOR . 'BinaryAbstract.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Field' .  DIRECTORY_SEPARATOR . 'Formated.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Field' .  DIRECTORY_SEPARATOR . 'None.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Field' .  DIRECTORY_SEPARATOR . 'Raw.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Field' .  DIRECTORY_SEPARATOR . 'Signifigant.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Field' .  DIRECTORY_SEPARATOR . 'Binary' . DIRECTORY_SEPARATOR . 'Base64.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Field' .  DIRECTORY_SEPARATOR . 'Binary' . DIRECTORY_SEPARATOR . 'Binary.php';

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
    // Writer type constants
    const FORMAT_FILE_FLAT  = 'Flat';
    const FORMAT_FILE_HUMAN = 'Human';
    const FORMAT_FILE_XML   = 'XML';
    
    const FORMAT_BINARY_BASE64 = 'Base64';
    const FORMAT_BINARY_NONE   = 'None';
    
    public static function defineFileFormats()
    {
	$legalTypes = array(
	    self::FORMAT_FILE_FLAT  => 'Flat File',
	    self::FORMAT_FILE_HUMAN => 'Human Readable File',
	    self::FORMAT_FILE_XML   => 'XML File',
	);
	
	return $legalTypes;
    }
    
    public static function defineBinaryFormats()
    {
	$legalTypes = array(
	    self::FORMAT_BINARY_BASE64 => 'Base64 encoded data',
	    self::FORMAT_BINARY_NONE   => 'Omit binary data'
	);
	return $legalTypes;
    }
    
    public static function Generate(
	$fileFormat,
	$filename,
	$binaryFormat = self::FORMAT_BINARY_NONE,
	$imagePath = null
    ) {
	if (array_key_exists($fileFormat, self::defineFileFormats()) === false) {
	    throw new \InvalidArgumentException("Invalid file format");
	}
	
	if (array_key_exists($binaryFormat, self::defineBinaryFormats()) === false) {
	    throw new \InvalidArgumentException("Invalid image format");
	}
	
	// build our binary handler.
	switch ($binaryFormat) {
	    case self::FORMAT_BINARY_BASE64;
		$binaryWriter = new \X937\Writer\Field\Binary\Base64();
		break;
	    case self::FORMAT_BINARY_NONE:
		$binaryWriter = new \X937\Writer\Field\None();
		break;
	}
	
	// build our normal field handler.
	switch ($fileFormat) {
	    case self::FORMAT_FILE_FLAT:
		$fieldWriter = new \X937\Writer\Field\Raw();
		break;
	    case self::FORMAT_FILE_HUMAN:
		$fieldWriter = new \X937\Writer\Field\Formated();
		break;
	    case self::FORMAT_FILE_XML:
		$fieldWriter = new \X937\Writer\Field\Signifigant();
		break;
	}
	
	// build our file object for the writer.
	switch ($fileFormat) {
	    case self::FORMAT_FILE_FLAT:
	    case self::FORMAT_FILE_HUMAN:
		// delebirate fall through.
		// SplObject will throw it's own exception if it can't write.
		$fileObject = new \SplFileObject($filename, 'wb');
		break;
	    case self::FORMAT_FILE_XML:
		$fileObject = new \XMLWriter();
		$fileValid  = $fileObject->openUri($filename);
		if ($fileValid === false) {
		    throw new \InvalidArgumentException("Error openining $filename for writing.");
		}
		break;
	}
	
	// finaly build our object
	switch ($fileFormat) {
	    case self::FORMAT_FILE_FLAT:
		return new Flat($fileObject, $fieldWriter, $binaryWriter);
	}
    }
    //put your code here
}
