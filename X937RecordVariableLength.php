<?php
/**
 * Description of X937FieldVariableLength
 *
 * @author astanley
 */

// not sure how to handle this one. Don't know the length untill we parse SOME
// of the data which we currently don't do at creation...
class X937RecordVariableLength extends X937Record {
    //put your code here
}

class X937RecordCheckDetailAddendumB extends X937RecordVariableLength {
    protected function addFields() {
	$this->fields = new SplFixedArray(8);
	$this->addField(new X937FieldRecordType(X937FieldRecordType::CHECK_DETAIL_ADDENDUM_B));
	$this->addField(new X937FieldVariableSizeIndicator());
	$this->addField(new X937Field(3, 'Microfilm Archive Sequence Number', X937Field::CONDITIONAL,  4, 15, X937Field::NUMERICBLANK));
	$this->addField(new X937Field(4, 'Length of Image Archive Sequence',  X937Field::MANDATORY,   19,  4, X937Field::NUMERIC));


	$this->fields[1]->parseValue($this->recordASCII);
	$variableSizeIndicator = $this->fields[1]->getValue();
	
	// we could simplfy this, but I think repeating the fields is more readable.
	switch ($variableSizeIndicator) {
	    case X937FieldVariableSizeIndicator::FIXED:
		$this->addField(new X937Field(5, 'Image Archive Sequence Number', X937Field::CONDITIONAL, 23, 34, X937Field::NUMERICBLANK));
		$this->addField(new X937Field(6, 'Description',                   X937Field::CONDITIONAL, 57, 15, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937FieldUser(7, 72, 4));
		$this->addField(new X937FieldReserved(8, 76, 5));
		break;
	    case X937FieldVariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new X937Field(5, 'Image Archive Sequence Number', X937Field::CONDITIONAL, 23,                        $variableFieldLength, X937Field::NUMERICBLANK));
		$this->addField(new X937Field(6, 'Description',                   X937Field::CONDITIONAL, $variableFieldLength + 57, 15,                   X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937FieldUser(7,     72 + $variableFieldLength, 4));
		$this->addField(new X937FieldReserved(8, 76 + $variableFieldLength, 5));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
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
	
	// we could simplfy this, but I think repeating the fields is more readable.
	switch ($variableSizeIndicator) {
	    case X937FieldVariableSizeIndicator::FIXED:
		$this->addField(new X937Field(5, 'Image Archive Sequence Number', X937Field::CONDITIONAL, 23, 34, X937Field::NUMERICBLANK));
		$this->addField(new X937Field(6, 'Description',                   X937Field::CONDITIONAL, 57, 15, X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937FieldUser(7, 72, 4));
		$this->addField(new X937FieldReserved(8, 76, 5));
		break;
	    case X937FieldVariableSizeIndicator::VARIABLE:
		// get the length of our variable field
		$variableFieldLength = $this->fields[4]->getValue();
		
		$this->addField(new X937Field(5, 'Image Archive Sequence Number', X937Field::CONDITIONAL, 23,                        $variableFieldLength, X937Field::NUMERICBLANK));
		$this->addField(new X937Field(6, 'Description',                   X937Field::CONDITIONAL, $variableFieldLength + 57, 15,                   X937Field::ALPHAMERICSPECIAL));
		$this->addField(new X937FieldUser(7,     72 + $variableFieldLength, 4));
		$this->addField(new X937FieldReserved(8, 76 + $variableFieldLength, 5));
		break;
	    default:
		// if it's not either of the field types, do nothing.
		break;
	}
    }
}