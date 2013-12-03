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
    // nothing for now
}

class X937RecordCheckDetailAddendumB extends X937RecordVariableLength {
    protected function addFields() {
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_B));
	$this->addField(new X937FieldVariableSizeIndicator());
	$this->addField(new X937Field(3, 'Microfilm Archive Sequence Number', X937Field::CONDITIONAL,  4, 15, X937Field::NUMERICBLANK));

	$this->fields[1]->parseValue($this->recordASCII);
	$variableSizeIndicator = $this->fields[1]->getValue();

	switch ($variableSizeIndicator) {
	    case X937FieldVariableSizeIndicator::FIXED:
		// $variableSizeIndicator should ALWAYS be 34 for FIXED length field, but we reset it here just in case.
		$variableSizeIndicator = 34;
		
		$this->addField(new X937Field(4, 'Length of Image Archive Sequence Number', X937Field::MANDATORY,   19,  4, X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Image Archive Sequence Number',           X937Field::CONDITIONAL, 23, 34, X937Field::NUMERICBLANK));
		break;
	    case X937FieldVariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new X937Field(4, 'Length of Image Archive Locator', X937Field::MANDATORY,   19,  4,                        X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Image Archive Locator',           X937Field::CONDITIONAL, 23, $variableFieldLength,      X937Field::NUMERICBLANK));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
	
	$this->addField(new X937Field(6, 'Description',                     X937Field::CONDITIONAL, $variableFieldLength + 57, 15, X937Field::ALPHAMERICSPECIAL));
	$this->addField(new X937FieldUser(7,     72 + $variableFieldLength, 4));
	$this->addField(new X937FieldReserved(8, 76 + $variableFieldLength, 5));
    }
}

class X937RecordReturnAddendumC extends X937RecordVariableLength {
    protected function addFields() {
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::RETURN_ADDENDUM_C));
	$this->addField(new X937FieldVariableSizeIndicator());
	$this->addField(new X937Field(3, 'Microfilm Archive Sequence Number', X937Field::CONDITIONAL,  4, 15, X937Field::NUMERICBLANK));
	$this->addField(new X937Field(4, 'Length of Image Archive Sequence',  X937Field::MANDATORY,   19,  4, X937Field::NUMERIC));

	$this->fields[1]->parseValue($this->recordASCII);
	$variableSizeIndicator = $this->fields[1]->getValue();
	
	switch ($variableSizeIndicator) {
	    case X937FieldVariableSizeIndicator::FIXED:
		// $variableSizeIndicator should ALWAYS be 34 for FIXED length field, but we reset it here just in case.
		$variableSizeIndicator = 34;
		
		$this->addField(new X937Field(4, 'Length of Image Archive Sequence Number', X937Field::MANDATORY,   19,  4, X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Image Archive Sequence Number',           X937Field::CONDITIONAL, 23, 34, X937Field::NUMERICBLANK));
		break;
	    case X937FieldVariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new X937Field(4, 'Length of Image Archive Locator', X937Field::MANDATORY,   19,  4,                        X937Field::NUMERIC));
		$this->addField(new X937Field(5, 'Image Archive Locator',           X937Field::CONDITIONAL, 23, $variableFieldLength,      X937Field::NUMERICBLANK));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
	
	$this->addField(new X937Field(6, 'Description',                     X937Field::CONDITIONAL, $variableFieldLength + 57, 15, X937Field::ALPHAMERICSPECIAL));
	$this->addField(new X937FieldUser(7,     72 + $variableFieldLength, 4));
	$this->addField(new X937FieldReserved(8, 76 + $variableFieldLength, 5));
    }
}

class X937RecordImageViewData extends X937RecordVariableLength {
    private $digitalSignature;
    private $imageData;
    
    protected function addFields() {
	$this->fields = new SplFixedArray(19);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::IMAGE_VIEW_DATA));
	$this->addField(new X937FieldRoutingNumber(2, 'ECE Institution',         X937Field::MANDATORY,     3));
	$this->addField(new X937FieldDate(3, 'Bundle Bsiness Date',              X937Field::MANDATORY,    12));
	$this->addField(new X937Field(4,  'Cycle Number',                        X937Field::MANDATORY,    20,  2, X937Field::ALPHAMERIC));
	$this->addField(new X937Field(5,  'ECE Instituion Item Sequence Number', X937Field::MANDATORY,    22, 15, X937Field::NUMERICBLANK));
	$this->addField(new X937Field(6,  'Security Originator Name',            X937Field::CONDITIONAL,  37, 16, X937Field::ALPHAMERICSPECIAL));
	$this->addField(new X937Field(7,  'Security Authenticator Name',         X937Field::CONDITIONAL,  53, 16, X937Field::ALPHAMERICSPECIAL));
	$this->addField(new X937Field(8,  'Security Key Name',                   X937Field::CONDITIONAL,  69, 16, X937Field::ALPHAMERICSPECIAL));
	$this->addField(new X937Field(9,  'Clipping Origin',                     X937Field::MANDATORY,    85,  1, X937Field::NUMERIC));
	$this->addField(new X937Field(10, 'Clipping Coordinate H1',              X937Field::CONDITIONAL,  86,  4, X937Field::NUMERIC));
	$this->addField(new X937Field(11, 'Clipping Coordinate H2',              X937Field::CONDITIONAL,  90,  4, X937Field::NUMERIC));
	$this->addField(new X937Field(12, 'Clipping Coordinate V1',              X937Field::CONDITIONAL,  94,  4, X937Field::NUMERIC));
	$this->addField(new X937Field(13, 'Clipping Coordinate V2',              X937Field::CONDITIONAL,  98,  4, X937Field::NUMERIC));
	$this->addField(new X937Field(14, 'Length of Image Reference Key',       X937Field::MANDATORY,   102,  4, X937Field::NUMERICBLANK));
	
	// get the size of the first variable key, field 14 - Length of Image Reference Key
	$this->fields[13]->parseValue($this->recordData);
	$imageRefKeyLength = (int) $this->fields[13]->getValue();
	
	$this->addField(new X937FieldVariableLength(15, 'Image Reference Key',   X937Field::CONDITIONAL, 106,  $imageRefKeyLength,     X937Field::ALPHAMERICSPECIAL));
	$this->addField(new X937Field(16, 'Length of Digital Signature',         X937Field::MANDATORY,   106 + $imageRefKeyLength, 10, X937Field::NUMERICBLANK));

	// get the size of the second variable key, field 16 - Length of Digital Signature
	$this->fields[15]->parseValue($this->recordData);
	$digitalSignatureLength = (int) $this->fields[15]->getValue();
	
	$this->addField(new X937FieldDigitalSignature($imageRefKeyLength, $digitalSignatureLength));
	$this->addField(new X937Field(18, 'Length of Image Data',                X937Field::MANDATORY,   111 + $imageRefKeyLength + $digitalSignatureLength, 7, X937Field::NUMERICBLANK));
	
	// get the size of the third variable key, field 18 - Length of Image Data
	$this->fields[17]->parseValue($this->recordData);
	$imageDataLength = (int) $this->fields[17]->getValue();
	
	$this->addField(new X937FieldImageData($imageRefKeyLength + $digitalSignatureLength, $imageDataLength));
    }
}