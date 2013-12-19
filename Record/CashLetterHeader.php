<?php

namespace X937\Record;

use X937\Fields as Fields;
use X937\Fields\Field;
use X937\Fields\Predefined\RecordType as FieldRecordType;

/**
 *  Cash Letter Header Record - Type 10
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class CashLetterHeader extends Record 
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\Predefined\RecordType(FieldRecordType::VALUE_CASH_LETTER_HEADER);
	$fields[2]  = new Fields\Predefined\FieldCollectionType(FieldRecordType::VALUE_CASH_LETTER_HEADER);
	$fields[3]  = new Fields\FieldRoutingNumber(3, 'Destination',       Field::USAGE_MANDATORY,    5);
	$fields[4]  = new Fields\FieldRoutingNumber(4, 'ECE Institution',   Field::USAGE_MANDATORY,   14);
	$fields[5]  = new Fields\FieldDate(5, 'Cash Letter Business',       Field::USAGE_MANDATORY,   23);
	$fields[6]  = new Fields\FieldDate(6, 'Cash Letter Creation',       Field::USAGE_MANDATORY,   31);
	$fields[7]  = new Fields\FieldTime(7, 'Cash Letter Creation',       Field::USAGE_MANDATORY,   39);
	$fields[8]  = new Fields\Predefined\FieldCashLetterType();
	$fields[9]  = new Fields\Predefined\FieldDocType(FieldRecordType::VALUE_CASH_LETTER_HEADER);
	$fields[10] = new Fields\FieldGeneric(10, 'Cash Letter ID',         Field::USAGE_CONDITIONAL, 45,  8, Field::TYPE_ALPHAMERIC);
	$fields[11] = new Fields\NameContact(11, 'Originator', 53, 14);
	$fields[12] = new Fields\FieldPhoneNumber(12, 'Originator Contact', Field::USAGE_CONDITIONAL, 67);
	$fields[13] = new Fields\Predefined\FieldFedWorkType();
	$fields[14] = new Fields\FieldUser(14, 78,  2);
	$fields[15] = new Fields\FieldReserved(15, 80,  1);
	
	return $fields;
    }
}
