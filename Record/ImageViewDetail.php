<?php

namespace X937\Record;

use X937\Fields as Fields;
use X937\Fields\Field;

/**
 * Image View Detail Record - Type 50
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class ImageViewDetail extends Record
{
    public static function defineFields()
    {
	$fields = array();
	
	$fields[1]  = new Fields\Predefined\RecordType(Fields\Predefined\RecordType::VALUE_IMAGE_VIEW_DETAIL);
	$fields[2]  = new Fields\FieldGeneric(2, 'Image Indicator',              Field::USAGE_MANDATORY,    3, 1, Field::TYPE_NUMERIC);
	$fields[3]  = new Fields\FieldRoutingNumber(3, 'Image Creator',          Field::USAGE_MANDATORY,    4);
	$fields[4]  = new Fields\DateTime\Date(4, 'Image Creator',         Field::USAGE_MANDATORY,   13);
	$fields[5]  = new Fields\Predefined\ImageView\Format();
	$fields[6]  = new Fields\Predefined\ImageView\Compression();
	$fields[7]  = new Fields\SizeBytes( 7, 'Image View Data Size',           Field::USAGE_CONDITIONAL, 25, 7);
	$fields[8]  = new Fields\Predefined\ViewSide();
	$fields[9]  = new Fields\FieldGeneric( 9, 'View Descriptor',             Field::USAGE_MANDATORY,   33, 2, Field::TYPE_NUMERIC);
	$fields[10] = new Fields\FieldGeneric(10, 'Digital Signature Indicator', Field::USAGE_MANDATORY,   35, 1, Field::TYPE_NUMERICBLANK);
	$fields[11] = new Fields\FieldGeneric(11, 'Digital Signature Method',    Field::USAGE_MANDATORY,   36, 2, Field::TYPE_NUMERIC);
	$fields[12] = new Fields\FieldGeneric(12, 'Security Key Size',           Field::USAGE_MANDATORY,   38, 5, Field::TYPE_NUMERIC);
	$fields[13] = new Fields\FieldGeneric(13, 'Start of Protected Data',     Field::USAGE_CONDITIONAL, 43, 7, Field::TYPE_NUMERIC);
	$fields[14] = new Fields\SizeBytes(14, 'Length of Protected Data',       Field::USAGE_CONDITIONAL, 50, 7);
	$fields[15] = new Fields\FieldGeneric(15, 'Image Recreate Indicator',    Field::USAGE_CONDITIONAL, 57, 1, Field::TYPE_NUMERIC);
	$fields[16] = new Fields\FieldUser(16, 58, 8);
	$fields[17] = new Fields\FieldReserved(17, 66, 15);
	
	return $fields;
    }
}