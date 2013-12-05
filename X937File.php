<?php

require_once 'X937Record.php';
require_once 'X937RecordFactory.php';
require_once 'X937Field.php';
/**
 * An X937File
 * 
 * @todo Test ASCII file codepaths. Currently untested as have no example work.
 */
class X937File implements Countable, Iterator {
    const DATA_ASCII  = 'ASCII';
    const DATA_EBCDIC = 'EBCDIC-US';
    
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
     * Current position of the itterator in the file.
     * @var int
     */
    private $currentRecordPosition;
    
    /**
     * Current record length
     * @var int
     */
    private $curentRecordLength;
    
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
     * array of records position in the X937 File. Indexed the same as file
     * @var array
     */
    private $recordPositions;
    
    /**
     * Count of the number of records in the file. Retrieved from File Control Record.
     * @var int
     */
    private $recordCount;
    
    private $fileTotalAmount;
    private $fileItemCount;
	
    public function __construct($filename) {
	// input validation		
	// check for existance of our file
	if (!file_exists($filename)) {
	    throw new InvalidArgumentException("X937File created with file that does not exist, filename: $filename");
	}
		
	// so we have a file, get info on it.
	$this->fileInfo = new SplFileInfo($filename);
		
//	if (strtoupper($this->fileInfo->getExtension()) !== 'X937') {
//	    throw new InvalidArgumentException("X937File created with file with non X937 file extension, filename: $filename.");
//	}
		
	// open our file for reading in binary mode.
	$this->fileHandle = fopen($filename, 'rb');
	
	// check file data type. According to doc this is done by checking the
	// first field in the first record (that is File Header Record - Type 01
	// Field 01 - Record Type). Should be 0xf0f1 for EBCDIC and 0x3031 for
	// ASCII. If neither of those, we got bad data probably.

	// seek to the right location. 4 bytes from the start of the file.
	fseek($this->fileHandle, 4, SEEK_SET);
	
	// parse the first two bytes, which contain the appropriate record.
	$initialRecordDataTypeRaw = fread($this->fileHandle, 2);
	$initialRecordDataTypeHex = bin2hex($initialRecordDataTypeRaw);
	
	// switch on that data, comparing them to the known hex values.
	switch ($initialRecordDataTypeHex) {
	    case self::FILE_SIGNATURE_EBCDIC:
		$this->dataType = self::DATA_EBCDIC;
		break;
	    case self::FILE_SIGNATURE_ASCII:
		$this->dataType = self::DATA_ASCII;
		break;
	    default:
		throw new InvalidArgumentException('Unable to parse file, bad data given.');
		break;
	}
	
	// rewind to the start for the other stuff.
	rewind($this->fileHandle);
	
	// pull the first record, this should always be the file header record.
	$this->fileHeaderRecord = $this->current();
	
	if (($this->fileHeaderRecord instanceof X937RecordFileHeader) === FALSE) {
	    throw new InvalidArgumentException('Bad file given, first record is not a File Header Record.');
	}
	
	/**
	 * @todo see if we can figure out how to parse records backwards elegantly, and use that here instead.
	 */

	// seek to 80 characters before the end of file. This should always be the File Control Record.
	fseek($this->fileHandle, -80, SEEK_END);
	
	// read those 80 characters
	$fileControlRecordData = fread($this->fileHandle, 80);
	
	// build our file record from this data.
	$this->fileControlRecord = X937RecordFactory::newRecordFromRawData($fileControlRecordData, $this->dataType);
	if ($this->fileControlRecord instanceof X937RecordFileControl) {
	    $this->fileTotalAmount = $this->fileControlRecord->getFieldByNumber(5)->getValue()/100;
	    $this->fileItemCount   = $this->fileControlRecord->getFieldByNumber(4)->getValue();
	    $this->recordCount     = $this->fileControlRecord->getFieldByNumber(3)->getValue();
	} else {
	    throw new InvalidArgumentException('Bad file given, last record is not a File Control Record.');
	}
	
	// rewind the file pointer;
	rewind($this->fileHandle);
	
	// set the position of our itterator at the beginging
	$this->currentRecordPosition = 0;
    }
	
    public function getFileInfo()        { return $this->fileInfo; }

    public function getFileTotalAmount() { return $this->fileTotalAmount; }
    public function getFileItemCount()   { return $this->fileItemCount; }
    
    /**
     * Implementation for countable. Returns the number of records from the file
     * control record. Be aware that if that count is wrong, this count is wrong.
     * @return int
     */
    public function count() {
	return $this->recordCount;
    }
    
    /**
     * Rewind to the first record.
     */
    public function rewind() {
	// set position back to 0, rewind file pointer.
	$this->currentRecordPosition = 0;
	rewind($this->fileHandle);
    }
    
    /**
     * Returns the current key for our record.
     * @return int Current position in the file.
     */
    public function key() {
	return $this->currentRecordPosition;
    }
    
    /**
     * Returns current record in the file.
     * @return X937Record The current x937Record
     */
    public function current() {
	        // save our record position
        $this->recordPositions = ftell($this->fileHandle);
            
        // pull 4 bytes, this should contain our record length.
	$recordLengthData = fread($this->fileHandle, 4);
		
	// check to see if we have a value here. If not, we've reached the eof, and just return
	// if (!$recordLengthData) { return false; }
		
	// unpack our data into an int, unpack should return an with a single value
	// i.e. array '['int']=>RECORDLENGTH so array shift will get us the raw value.
	$this->curentRecordLength = array_shift(unpack("Nint", $recordLengthData));

	// read the data for our record. Build a record.
	$recordData = fread($this->fileHandle, $this->curentRecordLength);	
	$record     = X937RecordFactory::newRecordFromRawData($recordData, $this->dataType);
	
	// seek back to the old position
	fseek($this->fileHandle, -$this->curentRecordLength, SEEK_CUR);

	return $record;
    }
    
    public function next() {
	// advance the index
	$this->currentRecordPosition++;
	
	// seek to the next record position
	fseek($this->fileHandle, $this->curentRecordLength, SEEK_CUR);
    }
    
    /**
     * Returns true if we are at the end of our record set, false otherwise.
     * @return bool True if end of record set. False if not.
     */
    public function valid() {
	// if we are at the last record our file handle should point to the eof.
	// feof will return true in this case, we wan't the opposite.
	$currentPosition = ftell($this->fileHandle);
	$size = $this->fileInfo->getSize();
	
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

}
