<?php

require_once 'X937Record.php';
require_once 'X937Field.php';

class X937File {
	private $fileHandle;
	private $valid;
	private $fileInfo;
	private $records;

        /**
         * array of records position in the X937 File. Indexed the same as file
         * @var array
         */
        private $recordPosition;
	
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
		
//		if (strtoupper($this->fileInfo->getExtension()) !== 'X937') {
//			throw new InvalidArgumentException("X937File created with file with non X937 file extension, filename: $filename.");
//		}
		
		// open our file for reading in binary mode.
		$this->fileHandle = fopen($filename, 'rb');
		
		// read all our records
		// $this->readAllRecords();
	}
	
	public function getFileInfo()        { return $this->fileInfo; }
	public function getRecords()         { return $this->records; }
	public function getRecord($record)   { return $this->records[$record]; }
	
	public function getFileTotalAmount() { return $this->fileTotalAmount; }
	public function getFileItemCount()   { return $this->fileItemCount; }
	
	public function getRecordsByType($recordType) {
		foreach ($this->records as $record) {
			if ($record->getRecordType() === $recordType) {
				$records[] = $record;
			}
		}
		
		return $records;
	}
	
	public function readAllRecords() {
		while (!feof($this->fileHandle)) {
			$this->readRecord();
		}
		
		$fileControlRecords = $this->getRecordsByType(X937Record::FILE_CONTROL);
		$fileControlRecord  = array_shift($fileControlRecords);
		
		if ($fileControlRecord instanceof X937RecordFileControl) {
                    $this->fileTotalAmount = $fileControlRecord->getFieldByNumber(5)->getValue()/100;
                    $this->fileItemCount   = $fileControlRecord->getFieldByNumber(4)->getValue();
                }
	}
	
    public function readRecord() {
        // save our record position
        $this->recordPosition = ftell($this->fileHandle);
            
        // pull 4 bytes, this should contain our record length.
	$recordLengthData = fread($this->fileHandle, 4);
		
	// check to see if we have a value here. If not, we've reached the eof, and just return
	if (!$recordLengthData) { return false; }
		
	// unpack our data into an int, unpack should return an with a single value
	// i.e. array '['int']=>RECORDLENGTH so array shift will get us the raw value.
	$recordLength = array_shift(unpack("Nint", $recordLengthData));

	// read our record, it should be $recordLength long
	$recordData = fread($this->fileHandle, $recordLength);
		
	// build a record from the data
	$this->records[] = $this->newRecord($recordData);
                
        return true;
    }
	
	private function newRecord($recordData) {
		// the first two characters should be the record type, in EBCDIC. Cut them and convert them.
		$recordTypeEBCDIC = substr($recordData, 0, 2);
		$recordTypeASCII  = iconv('EBCDIC-US', 'ASCII', $recordTypeEBCDIC);
		
		switch ($recordTypeASCII) {
			case X937Record::FILE_HEADER:
				return new X937RecordFileHeader($recordTypeASCII, $recordData);
				break;
			case X937Record::CASH_LETTER_HEADER:
				return new X937RecordCashLetterHeader($recordTypeASCII, $recordData);
				break;
			case X937Record::BUNDLE_HEADER:
				return new X937RecordBundleHeader($recordTypeASCII, $recordData);
				break;
			case X937Record::CHECK_DETAIL:
				return new X937RecordCheckDetail($recordTypeASCII, $recordData);
				break;
			case X937Record::CHECK_DETAIL_ADDENDUM_A:
				return new X937RecordCheckDetailAddendumA($recordTypeASCII, $recordData);
				break;
			
			// more to be inserted
			
			case X937Record::BUNDLE_CONTROL:
				return new X937RecordBundleControl($recordTypeASCII, $recordData);
				break;
			case X937Record::BOX_SUMMARY:
				return new X937RecordBoxSummary($recordTypeASCII, $recordData);
				break;
			case X937Record::ROUTING_NUMBER_SUMMARY:
				return new X937RecordRoutingNumberSummary($recordTypeASCII, $recordData);
				break;
			case X937Record::CASH_LETTER_CONTROL:
				return new X937RecordCashLetterControl($recordTypeASCII, $recordData);
				break;
			case X937Record::FILE_CONTROL:
				return new X937RecordFileControl($recordTypeASCII, $recordData);
				break;
			default:
				return new X937Record($recordTypeASCII, $recordData);
				break;
		}
	}
	
	public function __destruct() {
		// close our file handle.
		fclose($this->fileHandle);
	}
}
