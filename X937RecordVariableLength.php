<?php
/**
 * Description of X937FieldVariableLength
 *
 * @author astanley
 */

require_once 'X937FieldVariableLength.php';

/**
 * just a holder for now.
 */
abstract class X937RecordVariableLength extends X937Record {
    const LENGTH_VARIABLE = -1;
    const POSITION_VARIABLE = -1;
    // nothing for now
}

class X937RecordCheckDetailAddendumB extends X937RecordVariableLength
{
    /**
     * Note that this record has variable length and so these field definition spacing may not be accurate.
     * @return array() array of X937Fields
     */
    public static function defineFields()
    {
	$fields = array();
		
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_B);
	$fields[2] = new X937FieldVariableSizeIndicator();
	$fields[3] = new X937FieldGeneric(3, 'Microfilm Archive Sequence Number',       X937Field::USAGE_CONDITIONAL,  4,                      15, X937Field::TYPE_NUMERICBLANK);
	$fields[4] = new X937FieldGeneric(4, 'Length of Image Archive Sequence Number', X937Field::USAGE_MANDATORY,   19,                       4, X937Field::TYPE_NUMERIC);
	$fields[5] = new X937FieldGeneric(5, 'Variable Name',                           X937Field::USAGE_CONDITIONAL, 23,   self::LENGTH_VARIABLE, X937Field::TYPE_NUMERICBLANK);
	$fields[6] = new X937FieldGeneric(6, 'Description',                             X937Field::USAGE_CONDITIONAL, self::POSITION_VARIABLE, 15, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[7] = new X937FieldUser(7,     self::POSITION_VARIABLE, 4);
	$fields[8] = new X937FieldReserved(8, self::POSITION_VARIABLE, 5);
	
	return $fields;
    }
    
    protected function addFields()
    {
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_B));
	$this->addField(new X937FieldVariableSizeIndicator());
	$this->addField(new X937FieldGeneric(3, 'Microfilm Archive Sequence Number', X937Field::USAGE_CONDITIONAL,  4, 15, X937Field::TYPE_NUMERICBLANK));

	$this->fields[1]->parseValue($this->recordData);
	$variableSizeIndicator = $this->fields[1]->getValue();

	switch ($variableSizeIndicator) {
	    case X937FieldVariableSizeIndicator::FIXED:
		// $variableSizeIndicator should ALWAYS be 34 for FIXED length field, but we reset it here just in case.
		$variableSizeIndicator = 34;
		
		$this->addField(new X937FieldGeneric(4, 'Length of Image Archive Sequence Number', X937Field::USAGE_MANDATORY,   19,  4, X937Field::TYPE_NUMERIC));
		$this->addField(new X937FieldGeneric(5, 'Image Archive Sequence Number',           X937Field::USAGE_CONDITIONAL, 23, 34, X937Field::TYPE_NUMERICBLANK));
		break;
	    case X937FieldVariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new X937FieldGeneric(4, 'Length of Image Archive Locator', X937Field::USAGE_MANDATORY,   19,  4,                        X937Field::TYPE_NUMERIC));
		$this->addField(new X937FieldGeneric(5, 'Image Archive Locator',           X937Field::USAGE_CONDITIONAL, 23, $variableFieldLength,      X937Field::TYPE_NUMERICBLANK));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
	
	$this->addField(new X937FieldGeneric(6, 'Description', X937Field::USAGE_CONDITIONAL, $variableFieldLength + 57, 15, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldUser(7,     72 + $variableFieldLength, 4));
	$this->addField(new X937FieldReserved(8, 76 + $variableFieldLength, 5));
    }
}

class X937RecordReturnAddendumC extends X937RecordVariableLength
{
    /**
     * Note that this record has variable length and so these field definition may not be accurate.
     * @return array() array of X937Fields
     */
    public static function defineFields()
    {
	$fields = array();
		
	$fields[1] = new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_C);
	$fields[2] = new X937FieldVariableSizeIndicator();
	$fields[3] = new X937FieldGeneric(3, 'Microfilm Archive Sequence Number',       X937Field::USAGE_CONDITIONAL,  4,                      15, X937Field::TYPE_NUMERICBLANK);
	$fields[4] = new X937FieldGeneric(4, 'Length of Image Archive Sequence Number', X937Field::USAGE_MANDATORY,   19,                       4, X937Field::TYPE_NUMERIC);
	$fields[5] = new X937FieldGeneric(5, 'Variable Name',                           X937Field::USAGE_CONDITIONAL, 23,   self::LENGTH_VARIABLE, X937Field::TYPE_NUMERICBLANK);
	$fields[6] = new X937FieldGeneric(6, 'Description',                             X937Field::USAGE_CONDITIONAL, self::POSITION_VARIABLE, 15, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[7] = new X937FieldUser(7,     self::POSITION_VARIABLE, 4);
	$fields[8] = new X937FieldReserved(8, self::POSITION_VARIABLE, 5);
	
	return $fields;
    }
    
    protected function addFields()
	{
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_C));
	$this->addField(new X937FieldVariableSizeIndicator());
	$this->addField(new X937FieldGeneric(3, 'Microfilm Archive Sequence Number', X937Field::USAGE_CONDITIONAL,  4, 15, X937Field::TYPE_NUMERICBLANK));
	$this->addField(new X937FieldGeneric(4, 'Length of Image Archive Sequence',  X937Field::USAGE_MANDATORY,   19,  4, X937Field::TYPE_NUMERIC));

	$this->fields[1]->parseValue($this->recordData);
	$variableSizeIndicator = $this->fields[1]->getValue();
	
	switch ($variableSizeIndicator) {
	    case X937FieldVariableSizeIndicator::FIXED:
		// $variableSizeIndicator should ALWAYS be 34 for FIXED length field, but we reset it here just in case.
		$variableSizeIndicator = 34;
		
		$this->addField(new X937FieldGeneric(4, 'Length of Image Archive Sequence Number', X937Field::USAGE_MANDATORY,   19,  4, X937Field::TYPE_NUMERIC));
		$this->addField(new X937FieldGeneric(5, 'Image Archive Sequence Number',           X937Field::USAGE_CONDITIONAL, 23, 34, X937Field::TYPE_NUMERICBLANK));
		break;
	    case X937FieldVariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new X937FieldGeneric(4, 'Length of Image Archive Locator', X937Field::USAGE_MANDATORY,   19,  4,                        X937Field::TYPE_NUMERIC));
		$this->addField(new X937FieldGeneric(5, 'Image Archive Locator',           X937Field::USAGE_CONDITIONAL, 23, $variableFieldLength,      X937Field::TYPE_NUMERICBLANK));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
	
	$this->addField(new X937FieldGeneric(6, 'Description',                     X937Field::USAGE_CONDITIONAL, $variableFieldLength + 57, 15, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldUser(7,     72 + $variableFieldLength, 4));
	$this->addField(new X937FieldReserved(8, 76 + $variableFieldLength, 5));
    }
}

class X937RecordImageViewData extends X937RecordVariableLength
{
    /**
     * The raw record data, preserved so we can have it for binary fields.
     * @var string
     */
    private $recordDataRaw;
    
    public function __construct($recordType, $recordDataASCII, $recordDataRaw)
    {
	$this->recordDataRaw = $recordDataRaw;
	
	parent::__construct($recordType, $recordDataASCII);
    }
    
    /**
     * Get the raw Record data
     * @return string Raw (untranslated) Record Data
     */
    public function getRawRecordData() { return $this->recordDataRaw; }
    
    /**
     * Note that this record has variable length and so these field definition may not be accurate.
     * @return array() array of X937Fields
     */
    public static function defineFields() {
	$fields = array();
	
	$fields[1]  = new X937FieldRecordType(X937FieldRecordType::IMAGE_VIEW_DATA);
	$fields[2]  = new X937FieldRoutingNumber(2, 'ECE Institution',                X937Field::USAGE_MANDATORY,     3);
	$fields[3]  = new X937FieldDate(3, 'Bundle Bsiness Date',                     X937Field::USAGE_MANDATORY,    12);
	$fields[4]  = new X937FieldGeneric(4,  'Cycle Number',                        X937Field::USAGE_MANDATORY,    20,  2, X937Field::TYPE_ALPHAMERIC);
	$fields[5]  = new X937FieldGeneric(5,  'ECE Instituion Item Sequence Number', X937Field::USAGE_MANDATORY,    22, 15, X937Field::TYPE_NUMERICBLANK);
	$fields[6]  = new X937FieldGeneric(6,  'Security Originator Name',            X937Field::USAGE_CONDITIONAL,  37, 16, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[7]  = new X937FieldGeneric(7,  'Security Authenticator Name',         X937Field::USAGE_CONDITIONAL,  53, 16, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[8]  = new X937FieldGeneric(8,  'Security Key Name',                   X937Field::USAGE_CONDITIONAL,  69, 16, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[9]  = new X937FieldGeneric(9,  'Clipping Origin',                     X937Field::USAGE_MANDATORY,    85,  1, X937Field::TYPE_NUMERIC);
	$fields[10] = new X937FieldGeneric(10, 'Clipping Coordinate H1',              X937Field::USAGE_CONDITIONAL,  86,  4, X937Field::TYPE_NUMERIC);
	$fields[11] = new X937FieldGeneric(11, 'Clipping Coordinate H2',              X937Field::USAGE_CONDITIONAL,  90,  4, X937Field::TYPE_NUMERIC);
	$fields[12] = new X937FieldGeneric(12, 'Clipping Coordinate V1',              X937Field::USAGE_CONDITIONAL,  94,  4, X937Field::TYPE_NUMERIC);
	$fields[13] = new X937FieldGeneric(13, 'Clipping Coordinate V2',              X937Field::USAGE_CONDITIONAL,  98,  4, X937Field::TYPE_NUMERIC);
	$fields[14] = new X937FieldGeneric(14, 'Length of Image Reference Key',       X937Field::USAGE_MANDATORY,   102,  4, X937Field::TYPE_NUMERICBLANK);	
	$fields[15] = new X937FieldVariableLength(15, 'Image Reference Key',          X937Field::USAGE_CONDITIONAL, 106,                     self::LENGTH_VARIABLE, X937Field::TYPE_ALPHAMERICSPECIAL);
	$fields[16] = new X937FieldGeneric(16, 'Length of Digital Signature',         X937Field::USAGE_MANDATORY,   self::POSITION_VARIABLE, 5,                     X937Field::TYPE_NUMERICBLANK);
	$fields[17] = new X937FieldGeneric(17, 'Digital Signature',                   X937Field::USAGE_CONDITIONAL, self::POSITION_VARIABLE, self::LENGTH_VARIABLE, X937Field::TYPE_BINARY);
	$fields[18] = new X937FieldGeneric(18, 'Length of Image Data',                X937Field::USAGE_MANDATORY,   self::POSITION_VARIABLE, 7,                     X937Field::TYPE_NUMERICBLANK);
	$fields[19] = new X937FieldGeneric(19, 'Image Data',                          X937Field::USAGE_MANDATORY,   self::POSITION_VARIABLE, self::LENGTH_VARIABLE, X937Field::TYPE_BINARY);
	
	return $fields;
    }
    
    protected function addFields()
    {
	$this->fields = new SplFixedArray(19);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::IMAGE_VIEW_DATA));
	$this->addField(new X937FieldRoutingNumber(2, 'ECE Institution',                X937Field::USAGE_MANDATORY,     3));
	$this->addField(new X937FieldDate(3, 'Bundle Bsiness Date',                     X937Field::USAGE_MANDATORY,    12));
	$this->addField(new X937FieldGeneric(4,  'Cycle Number',                        X937Field::USAGE_MANDATORY,    20,  2, X937Field::TYPE_ALPHAMERIC));
	$this->addField(new X937FieldGeneric(5,  'ECE Instituion Item Sequence Number', X937Field::USAGE_MANDATORY,    22, 15, X937Field::TYPE_NUMERICBLANK));
	$this->addField(new X937FieldGeneric(6,  'Security Originator Name',            X937Field::USAGE_CONDITIONAL,  37, 16, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric(7,  'Security Authenticator Name',         X937Field::USAGE_CONDITIONAL,  53, 16, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric(8,  'Security Key Name',                   X937Field::USAGE_CONDITIONAL,  69, 16, X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric(9,  'Clipping Origin',                     X937Field::USAGE_MANDATORY,    85,  1, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(10, 'Clipping Coordinate H1',              X937Field::USAGE_CONDITIONAL,  86,  4, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(11, 'Clipping Coordinate H2',              X937Field::USAGE_CONDITIONAL,  90,  4, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(12, 'Clipping Coordinate V1',              X937Field::USAGE_CONDITIONAL,  94,  4, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(13, 'Clipping Coordinate V2',              X937Field::USAGE_CONDITIONAL,  98,  4, X937Field::TYPE_NUMERIC));
	$this->addField(new X937FieldGeneric(14, 'Length of Image Reference Key',       X937Field::USAGE_MANDATORY,   102,  4, X937Field::TYPE_NUMERICBLANK));
	
	// get the size of the first variable key, field 14 - Length of Image Reference Key
	$this->fields[13]->parseValue($this->recordData);
	$imageRefKeyLength = (int) $this->fields[13]->getValue();
	
	$this->addField(new X937FieldVariableLength(15, 'Image Reference Key',  X937Field::USAGE_CONDITIONAL, 106,  $imageRefKeyLength,     X937Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new X937FieldGeneric(16, 'Length of Digital Signature', X937Field::USAGE_MANDATORY,   106 + $imageRefKeyLength, 5, X937Field::TYPE_NUMERICBLANK));

	// get the size of the second variable key, field 16 - Length of Digital Signature
	$this->fields[15]->parseValue($this->recordData);
	$digitalSignatureLength = (int) $this->fields[15]->getValue();
	
	$this->addField(new X937FieldDigitalSignature($this, $imageRefKeyLength, $digitalSignatureLength));
	$this->addField(new X937FieldGeneric(18, 'Length of Image Data',        X937Field::USAGE_MANDATORY,   111 + $imageRefKeyLength + $digitalSignatureLength, 7, X937Field::TYPE_NUMERICBLANK));
	
	// get the size of the third variable key, field 18 - Length of Image Data
	$this->fields[17]->parseValue($this->recordData);
	$imageDataLength = (int) $this->fields[17]->getValue();
	
	$this->addField(new X937FieldImageData($this, $imageRefKeyLength + $digitalSignatureLength, $imageDataLength));
    }
}