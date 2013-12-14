<?php

namespace X937\Record\VariableLength;

use X937\Fields as Fields;
use X937\Fields\Field;

/**
 * Check Detail Addendum B Record - Type 27
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class CheckDetailAddendumB extends VariableLength
{
    /**
     * Note that this record has variable length and so these field definition spacing may not be accurate.
     * @return array() array of X937Fields
     */
    public static function defineFields()
    {
	$fields = array();
		
	$fields[1] = new Fields\Predefined\RecordType(RecordType::VALUE_CHECK_DETAIL_ADDENDUM_B);
	$fields[2] = new Fields\Predefined\FieldVariableSize();
	$fields[3] = new Fields\FieldGeneric(3, 'Microfilm Archive Sequence Number',       Field::USAGE_CONDITIONAL,  4,                      15, Field::TYPE_NUMERICBLANK);
	$fields[4] = new Fields\FieldGeneric(4, 'Length of Image Archive Sequence Number', Field::USAGE_MANDATORY,   19,                       4, Field::TYPE_NUMERIC);
	$fields[5] = new Fields\FieldGeneric(5, 'Variable Name',                           Field::USAGE_CONDITIONAL, 23,   Field::LENGTH_VARIABLE, Field::TYPE_NUMERICBLANK);
	$fields[6] = new Fields\FieldGeneric(6, 'Description',                             Field::USAGE_CONDITIONAL, Field::POSITION_VARIABLE, 15, Field::TYPE_ALPHAMERICSPECIAL);
	$fields[7] = new Fields\FieldUser(7,     Field::POSITION_VARIABLE, 4);
	$fields[8] = new Fields\FieldReserved(8, Field::POSITION_VARIABLE, 5);
	
	return $fields;
    }
    
    protected function addFields()
    {
	$this->fields = new \SplFixedArray(8);
	$this->addField(new Fields\Predefined\RecordType(RecordType::VALUE_CHECK_DETAIL_ADDENDUM_B));
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
		$this->addField(new Fields\VariableLength\ImageKey(5, 'Image Archive Locator', 23, $variableFieldLength));
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