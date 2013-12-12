<?php
/**
 * Description of X937FieldVariableLength
 *
 * @author astanley
 */

namespace X937\Records;

use X937\Fields as Fields;

use X937\Fields\Predefined\FieldRecordType;
use X937\Fields\Field;

/**
 * just a holder for now.
 */
abstract class X937RecordVariableLength extends Record {
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
		
	$fields[1] = new Fields\Predefined\FieldRecordType(FieldRecordType::CHECK_DETAIL_ADDENDUM_B);
	$fields[2] = new Fields\Predefined\FieldVariableSize();
	$fields[3] = new Fields\FieldGeneric(3, 'Microfilm Archive Sequence Number',       Field::USAGE_CONDITIONAL,  4,                      15, Field::TYPE_NUMERICBLANK);
	$fields[4] = new Fields\FieldGeneric(4, 'Length of Image Archive Sequence Number', Field::USAGE_MANDATORY,   19,                       4, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\FieldGeneric(5, 'Variable Name',                           Field::USAGE_CONDITIONAL, 23,   self::LENGTH_VARIABLE, Field::TYPE_NUMERICBLANK);
	$fields[6] = new Fields\FieldGeneric(6, 'Description',                             Field::USAGE_CONDITIONAL, self::POSITION_VARIABLE, 15, Field::TYPE_ALPHAMERICSPECIAL);
	$fields[7] = new Fields\FieldUser(7,     self::POSITION_VARIABLE, 4);
	$fields[8] = new Fields\FieldReserved(8, self::POSITION_VARIABLE, 5);
	
	return $fields;
    }
    
    protected function addFields()
    {
	$this->fields = new \SplFixedArray(8);
	$this->addField(new Fields\Predefined\FieldRecordType(FieldRecordType::CHECK_DETAIL_ADDENDUM_B));
	$this->addField(new Fields\Predefined\FieldVariableSize());
	$this->addField(new Fields\FieldGeneric(3, 'Microfilm Archive Sequence Number', Field::USAGE_CONDITIONAL,  4, 15, Field::TYPE_NUMERICBLANK));

	$this->fields[1]->parseValue($this->recordData);
	$variableSizeIndicator = $this->fields[1]->getValue();

	switch ($variableSizeIndicator) {
	    case Fields\VariableSizeIndicator::FIXED:
		// $variableSizeIndicator should ALWAYS be 34 for FIXED length field, but we reset it here just in case.
		$variableSizeIndicator = 34;
		
		$this->addField(new Fields\FieldGeneric(4, 'Length of Image Archive Sequence Number', Field::USAGE_MANDATORY,   19,  4, Field::TYPE_NUMERIC));
		$this->addField(new Fields\FieldGeneric(5, 'Image Archive Sequence Number',           Field::USAGE_CONDITIONAL, 23, 34, Field::TYPE_NUMERICBLANK));
		break;
	    case Fields\VariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new Fields\FieldGeneric(4, 'Length of Image Archive Locator', Field::USAGE_MANDATORY,   19,  4,                        Field::TYPE_NUMERIC));
		$this->addField(new Fields\ImageKey(5, 'Image Archive Locator', 23, $variableFieldLength));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
	
	$this->addField(new Fields\FieldGeneric(6, 'Description', Field::USAGE_CONDITIONAL, $variableFieldLength + 57, 15, Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new Fields\FieldUser(7,     72 + $variableFieldLength, 4));
	$this->addField(new Fields\FieldReserved(8, 76 + $variableFieldLength, 5));
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
		
	$fields[1] = new Fields\Predefined\FieldRecordType(FieldRecordType::RETURN_ADDENDUM_C);
	$fields[2] = new Fields\VariableSizeIndicator();
	$fields[3] = new Fields\FieldGeneric(3, 'Microfilm Archive Sequence Number',       Field::USAGE_CONDITIONAL,  4,                      15, Field::TYPE_NUMERICBLANK);
	$fields[4] = new Fields\FieldGeneric(4, 'Length of Image Archive Sequence Number', Field::USAGE_MANDATORY,   19,                       4, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\FieldGeneric(5, 'Variable Name',                           Field::USAGE_CONDITIONAL, 23,   self::LENGTH_VARIABLE, Field::TYPE_NUMERICBLANK);
	$fields[6] = new Fields\FieldGeneric(6, 'Description',                             Field::USAGE_CONDITIONAL, self::POSITION_VARIABLE, 15, Field::TYPE_ALPHAMERICSPECIAL);
	$fields[7] = new Fields\FieldUser(7,     self::POSITION_VARIABLE, 4);
	$fields[8] = new Fields\FieldReserved(8, self::POSITION_VARIABLE, 5);
	
	return $fields;
    }
    
    protected function addFields()
	{
	$this->fields = new \SplFixedArray(8);
	$this->addField(new Fields\Predefined\FieldRecordType(FieldRecordType::RETURN_ADDENDUM_C));
	$this->addField(new Fields\VariableSizeIndicator());
	$this->addField(new Fields\FieldGeneric(3, 'Microfilm Archive Sequence Number', Field::USAGE_CONDITIONAL,  4, 15, Field::TYPE_NUMERICBLANK));
	$this->addField(new Fields\FieldGeneric(4, 'Length of Image Archive Sequence',  Field::USAGE_MANDATORY,   19,  4, Field::TYPE_NUMERIC));

	$this->fields[1]->parseValue($this->recordData);
	$variableSizeIndicator = $this->fields[1]->getValue();
	
	switch ($variableSizeIndicator) {
	    case Fields\VariableSizeIndicator::FIXED:
		// $variableSizeIndicator should ALWAYS be 34 for FIXED length field, but we reset it here just in case.
		$variableSizeIndicator = 34;
		
		$this->addField(new Fields\FieldGeneric(4, 'Length of Image Archive Sequence Number', Field::USAGE_MANDATORY,   19,  4, Field::TYPE_NUMERIC));
		$this->addField(new Fields\FieldGeneric(5, 'Image Archive Sequence Number',           Field::USAGE_CONDITIONAL, 23, 34, Field::TYPE_NUMERICBLANK));
		break;
	    case Fields\VariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new Fields\FieldGeneric(4, 'Length of Image Archive Locator', Field::USAGE_MANDATORY,   19,  4,                        Field::TYPE_NUMERIC));
		$this->addField(new Fields\ImageKey(5, 'Image Archive Locator', 23, $variableFieldLength));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
	
	$this->addField(new Fields\FieldGeneric(6, 'Description',                     Field::USAGE_CONDITIONAL, $variableFieldLength + 57, 15, Field::TYPE_ALPHAMERICSPECIAL));
	$this->addField(new Fields\FieldUser(7,     72 + $variableFieldLength, 4));
	$this->addField(new Fields\FieldReserved(8, 76 + $variableFieldLength, 5));
    }
}

class RecordImageViewData extends X937RecordVariableLength
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
	
	$fields[1]  = new Fields\Predefined\FieldRecordType(FieldRecordType::IMAGE_VIEW_DATA);
	$fields[2]  = new Fields\FieldRoutingNumber(2, 'ECE Institution',                Field::USAGE_MANDATORY,     3);
	$fields[3]  = new Fields\FieldDate(3, 'Bundle Bsiness Date',                     Field::USAGE_MANDATORY,    12);
	$fields[4]  = new Fields\FieldGeneric(4,  'Cycle Number',                        Field::USAGE_MANDATORY,    20,  2, Field::TYPE_ALPHAMERIC);
	$fields[5]  = new Fields\FieldGeneric(5,  'ECE Instituion Item Sequence Number', Field::USAGE_MANDATORY,    22, 15, Field::TYPE_NUMERICBLANK);
	$fields[6]  = new Fields\NameSecurity(6, 'Originator',    37);
	$fields[7]  = new Fields\NameSecurity(7, 'Authenticator', 53);
	$fields[8]  = new Fields\NameSecurity(8, 'Key',           69);
	$fields[9]  = new Fields\FieldGeneric(9,  'Clipping Origin',                     Field::USAGE_MANDATORY,    85,  1, Field::TYPE_NUMERIC);
	$fields[10] = new Fields\FieldGeneric(10, 'Clipping Coordinate H1',              Field::USAGE_CONDITIONAL,  86,  4, Field::TYPE_NUMERIC);
	$fields[11] = new Fields\FieldGeneric(11, 'Clipping Coordinate H2',              Field::USAGE_CONDITIONAL,  90,  4, Field::TYPE_NUMERIC);
	$fields[12] = new Fields\FieldGeneric(12, 'Clipping Coordinate V1',              Field::USAGE_CONDITIONAL,  94,  4, Field::TYPE_NUMERIC);
	$fields[13] = new Fields\FieldGeneric(13, 'Clipping Coordinate V2',              Field::USAGE_CONDITIONAL,  98,  4, Field::TYPE_NUMERIC);
	$fields[14] = new Fields\FieldGeneric(14, 'Length of Image Reference Key',       Field::USAGE_MANDATORY,   102,  4, Field::TYPE_NUMERICBLANK);	
	$fields[15] = new Fields\ImageKey(15, 'Image Reference Key', 106, self::LENGTH_VARIABLE);
	$fields[16] = new Fields\FieldGeneric(16, 'Length of Digital Signature',         Field::USAGE_MANDATORY,   self::POSITION_VARIABLE, 5,                     Field::TYPE_NUMERICBLANK);
	$fields[17] = new Fields\FieldGeneric(17, 'Digital Signature',                   Field::USAGE_CONDITIONAL, self::POSITION_VARIABLE, self::LENGTH_VARIABLE, Field::TYPE_BINARY);
	$fields[18] = new Fields\FieldGeneric(18, 'Length of Image Data',                Field::USAGE_MANDATORY,   self::POSITION_VARIABLE, 7,                     Field::TYPE_NUMERICBLANK);
	$fields[19] = new Fields\FieldGeneric(19, 'Image Data',                          Field::USAGE_MANDATORY,   self::POSITION_VARIABLE, self::LENGTH_VARIABLE, Field::TYPE_BINARY);
	
	return $fields;
    }
    
    protected function addFields()
    {
	$this->fields = new \SplFixedArray(19);
	$this->addField(new Fields\Predefined\FieldRecordType(FieldRecordType::IMAGE_VIEW_DATA));
	$this->addField(new Fields\FieldRoutingNumber(2, 'ECE Institution',                Field::USAGE_MANDATORY,     3));
	$this->addField(new Fields\FieldDate(3, 'Bundle Bsiness Date',                     Field::USAGE_MANDATORY,    12));
	$this->addField(new Fields\FieldGeneric(4,  'Cycle Number',                        Field::USAGE_MANDATORY,    20,  2, Field::TYPE_ALPHAMERIC));
	$this->addField(new Fields\FieldGeneric(5,  'ECE Instituion Item Sequence Number', Field::USAGE_MANDATORY,    22, 15, Field::TYPE_NUMERICBLANK));
	$this->addField(new Fields\NameSecurity(6, 'Originator',    37));
	$this->addField(new Fields\NameSecurity(7, 'Authenticator', 53));
	$this->addField(new Fields\NameSecurity(8, 'Key',           69));
	$this->addField(new Fields\FieldGeneric(9,  'Clipping Origin',                     Field::USAGE_MANDATORY,    85,  1, Field::TYPE_NUMERIC));
	$this->addField(new Fields\FieldGeneric(10, 'Clipping Coordinate H1',              Field::USAGE_CONDITIONAL,  86,  4, Field::TYPE_NUMERIC));
	$this->addField(new Fields\FieldGeneric(11, 'Clipping Coordinate H2',              Field::USAGE_CONDITIONAL,  90,  4, Field::TYPE_NUMERIC));
	$this->addField(new Fields\FieldGeneric(12, 'Clipping Coordinate V1',              Field::USAGE_CONDITIONAL,  94,  4, Field::TYPE_NUMERIC));
	$this->addField(new Fields\FieldGeneric(13, 'Clipping Coordinate V2',              Field::USAGE_CONDITIONAL,  98,  4, Field::TYPE_NUMERIC));
	$this->addField(new Fields\FieldGeneric(14, 'Length of Image Reference Key',       Field::USAGE_MANDATORY,   102,  4, Field::TYPE_NUMERICBLANK));
	
	// get the size of the first variable key, field 14 - Length of Image Reference Key
	$this->fields[13]->parseValue($this->recordData);
	$imageRefKeyLength = (int) $this->fields[13]->getValue();
	
	$this->addField(new Fields\ImageKey(15, 'Image Reference Key',  106,  $imageRefKeyLength));
	$this->addField(new Fields\FieldGeneric(16, 'Length of Digital Signature', Field::USAGE_MANDATORY,   106 + $imageRefKeyLength, 5, Field::TYPE_NUMERICBLANK));

	// get the size of the second variable key, field 16 - Length of Digital Signature
	$this->fields[15]->parseValue($this->recordData);
	$digitalSignatureLength = (int) $this->fields[15]->getValue();
	
	$this->addField(new Fields\DigitalSignature($this, $imageRefKeyLength, $digitalSignatureLength));
	$this->addField(new Fields\FieldGeneric(18, 'Length of Image Data',        Field::USAGE_MANDATORY,   111 + $imageRefKeyLength + $digitalSignatureLength, 7, Field::TYPE_NUMERICBLANK));
	
	// get the size of the third variable key, field 18 - Length of Image Data
	$this->fields[17]->parseValue($this->recordData);
	$imageDataLength = (int) $this->fields[17]->getValue();
	
	$this->addField(new Fields\ImageData($this, $imageRefKeyLength + $digitalSignatureLength, $imageDataLength));
    }
}