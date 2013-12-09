<?php

require_once 'X937Record.php';
require_once 'X937RecordFactory.php';
require_once 'X937Field.php';
/**
 * An X937File
 * 
 * @todo Test ASCII file codepaths. Currently untested as have no example work.
 */
class X937File implements Iterator {
    const DATA_ASCII  = 'ASCII';
    const DATA_EBCDIC = 'EBCDIC-US';
    
    // file signature constants.
    const FILE_SIGNATURE_ASCII  = '3031';
    const FILE_SIGNATURE_EBCDIC = 'f0f1';
    
    /**
     * Our FileHandle.
     * @var resource
     */
    private $fileHandle;
    
    /**
     * A SplFileInfo object for our file.
     * @var SplFileInfo
     */
    private $fileInfo;
    
    /**
     * The type of the data, either self::DATA_ASCII self::DATA_EBCDIC
     * @var string
     */
    private $dataType = self::DATA_ASCII;
    
    /**
     * File Header Record for the File
     * @var X937RecordFileHeader
     */
    private $fileHeaderRecord;
    
    /**
     * File Control Record for the File
     * @var X937RecordFileControl
     */
    private $fileControlRecord;
	
    /**
     * Creates a new X937File. It is immediatly validated for dataType (EBCDIC
     * vs ASCII) and for presence of the File Header Record at the start of the
     * file and File Control Record at the end of the file. Throws exception if
     * these two mandatory records are not present.
     * @param string $filename the path to the X937File
     * @throws InvalidArgumentException
     */
    public function __construct($filename) {
	// input validation		
	// check for existance of our file
	if (!file_exists($filename)) {
	    throw new InvalidArgumentException("X937File created with file that does not exist, filename: $filename");
	}
		
	// so we have a file, get info on it.
	$this->fileInfo = new SplFileInfo($filename);
	
	// open our file for reading in binary mode.
	$this->fileHandle = fopen($filename, 'rb');
	
	// read our file data type.
	$this->dataType = $this->readFileDataType();
		
	// pull the first record, this should always be the file header record.
	$this->fileHeaderRecord = $this->current();
	
	if (($this->fileHeaderRecord instanceof X937RecordFileHeader) === FALSE) {
	    throw new InvalidArgumentException('Bad file given, first record is not a File Header Record.');
	}

	// seek to 80 bytes from the EOF. This should be the File Control Record
	fseek($this->fileHandle, -80, SEEK_END);
	
	// read those 80 characters
	$fileControlRecordData = fread($this->fileHandle, 80);
	
	// build our file record from this data.
	$this->fileControlRecord = X937RecordFactory::newRecordFromRawData($fileControlRecordData, $this->dataType);
	if (($this->fileControlRecord instanceof X937RecordFileControl) === FALSE) {
	    throw new InvalidArgumentException('Bad file given, last record is not a File Control Record.');
	}
	
	// rewind to the beginning so users can start itteration.
	$this->rewind();
    }

    public function getFileInfo()        { return $this->fileInfo; }

    public function getFileTotalAmount() { 
	return $this->fileControlRecord->getFieldByNumber(5)->getValue()/100;
    }
    
    public function getFileItemCount(){ 
	return $this->fileControlRecord->getFieldByNumber(4)->getValue();
    }
    
    /**
     * Rewind to the first record.
     */
    public function rewind() {
	// rewind file pointer.
	rewind($this->fileHandle);
    }
    
    /**
     * Returns the current key for our record, which is the location of the 
     * beging of the record in the file, in bytes, with the data length record
     * field. I.E. 4 bytes before the actuall data's position.
     * @return int Key for the record, it's position in the file (in bytes)
     */
    public function key() {
	return ftell($this->fileHandle);
    }
    
    /**
     * Returns current record in the file.
     * @return X937Record The current x937Record
     */
    public function current() {
	// get the current record length.
	$recordLength = $this->readRecordLength();

	// read the data for our record. Build a record.
	$recordData = $this->readRecord($recordLength);	
	$record     = X937RecordFactory::newRecordFromRawData($recordData, $this->dataType);

	return $record;
    }
    
    /**
     * Advance to the next record in the file.
     */
    public function next() {
	// get the current record length
	$currentRecordLength = $this->readRecordLength();
	
	// seek to the next record position
	fseek($this->fileHandle, $currentRecordLength + 4, SEEK_CUR);
    }
    
    /**
     * Returns true if we are at the end of our record set, false otherwise.
     * @return bool True if end of record set. False if not.
     */
    public function valid() {
	// if we are at the last record our file handle should point to the eof.
	// feof will return true in this case, we wan't the opposite.
	$currentPosition = ftell($this->fileHandle);
	$size            = $this->fileInfo->getSize();
	
	if ($currentPosition >= $size) {
	    return false;
	} else {
	    return true;
	}
    }

    public function __destruct() {
	// close our file handle.
	fclose($this->fileHandle);
    }
    
    /**
     * Reads the current record length and returns it.
     * The file cursor is returned to its original position.
     * @return int Length of the record (in bytes)
     */
    private function readRecordLength() {
	// pull 4 bytes, this should contain our record length.
	$recordLengthData = fread($this->fileHandle, 4);
	
	// unpack our data into an int, unpack should return an with a single
	// value i.e. array '['int']=>RECORDLENGTH so array shift will get us
	// the raw value.
	$curentRecordLength = array_shift(unpack("Nint", $recordLengthData));
	
	// return the cursor to it's previous position.
	fseek($this->fileHandle, -4, SEEK_CUR);
	
	return $curentRecordLength;
    }
    
    /**
     * Reads the data for the current record.
     * The file cursor is returned to its original position.
     * @param int $recordLength
     * @return string the raw record data.
     */
    private function readRecord($recordLength) {
	// seek forward 4 bytes, our cursor should always be at the beginning of
	// a record, so we need to advance 4 bytes (past the record length part)
	// to get to our data.
	fseek($this->fileHandle, 4, SEEK_CUR);
	
	// read the data for our record.
	$recordData = fread($this->fileHandle, $recordLength);
	
	// return the cursor to it's previous position.
	fseek($this->fileHandle, -$recordLength, SEEK_CUR);
	fseek($this->fileHandle, -4,             SEEK_CUR);
	
	return $recordData;
    }
    
    /**
     * Read teh file data type. According to spec this is done by checking the
     * first field in the first record (that is the File Header Record - Type 01
     * Field 01 - Record Type). Should be 0xf0f1 for EBCDIC and 0x3031 for ASCII
     * If neither of those, we got bad data. The file pointer is rewinded by
     * this function.
     * @return string the file data type.
     * @throws InvalidArgumentException
     */
    private function readFileDataType() {
	// seek to the right location. 4 bytes from the start of the file.
	fseek($this->fileHandle, 4, SEEK_SET);
	
	// parse the first two bytes, which contain the appropriate record.
	$initialRecordDataTypeRaw = fread($this->fileHandle, 2);
	$initialRecordDataTypeHex = bin2hex($initialRecordDataTypeRaw);
	
	// switch on that data, comparing them to the known hex values.
	switch ($initialRecordDataTypeHex) {
	    case self::FILE_SIGNATURE_EBCDIC:
		$dataType = self::DATA_EBCDIC;
		break;
	    case self::FILE_SIGNATURE_ASCII:
		$dataType = self::DATA_ASCII;
		break;
	    default:
		throw new InvalidArgumentException('Unable to parse file, bad data given.');
		break;
	}
	
	// rewind to the start of the file.
	rewind($this->fileHandle);

	
	return $dataType;
    }
}
