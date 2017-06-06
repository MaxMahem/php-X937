<?php

namespace X937\Record\VariableLength;

use X937\Fields as Fields;
use X937\Fields\Field;

/**
 *  Image View Data - Type 52
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class ImageViewData extends VariableLength
{       
    /**
     * Note that this record has variable length and so these field definition may not be accurate.
     * @return array() array of X937Fields
     */
    public static function defineFields() {
    $fields = array();
    
    $fields[1]  = new Fields\Predefined\RecordType(Fields\Predefined\RecordType::VALUE_IMAGE_VIEW_DATA);
    $fields[2]  = new Fields\FieldRoutingNumber(2, 'ECE Institution',                Field::USAGE_MANDATORY,     3);
    $fields[3]  = new Fields\DateTime\Date(3, 'Bundle Business Date',                Field::USAGE_MANDATORY,    12);
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
    $fields[15] = new Fields\VariableLength\ImageKey(15, 'Image Reference Key', 106, Field::LENGTH_VARIABLE);
    $fields[16] = new Fields\SizeBytes(16, 'Length of Digital Signature',            Field::USAGE_MANDATORY,   Field::POSITION_VARIABLE, 5);
    $fields[17] = new Fields\VariableLength\Binary\DigitalSignature(Field::USAGE_CONDITIONAL, Field::POSITION_VARIABLE, Field::LENGTH_VARIABLE);
    $fields[18] = new Fields\SizeBytes(18, 'Length of Image Data',                   Field::USAGE_MANDATORY,   Field::POSITION_VARIABLE, 7);
    $fields[19] = new Fields\VariableLength\Binary\ImageData(Field::POSITION_VARIABLE, Field::LENGTH_VARIABLE);
    
    return $fields;
    }
    
    protected function addFields()
    {
    $this->fields = new \SplFixedArray(19);
    $this->addField(new Fields\Predefined\RecordType(Fields\Predefined\RecordType::VALUE_IMAGE_VIEW_DATA));
    $this->addField(new Fields\FieldRoutingNumber(2, 'ECE Institution',                Field::USAGE_MANDATORY,     3));
    $this->addField(new Fields\DateTime\Date(3, 'Bundle Business Date',                Field::USAGE_MANDATORY,    12));
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
    $imageRefKeyLength = (int) $this->fields[13]->getValueRaw();
    
    $this->addField(new Fields\VariableLength\ImageKey(15, 'Image Reference Key',  106,  $imageRefKeyLength));
    $this->addField(new Fields\SizeBytes(16, 'Length of Digital Signature', Field::USAGE_MANDATORY,   106 + $imageRefKeyLength, 5));

    // get the size of the second variable key, field 16 - Length of Digital Signature
    $this->fields[15]->parseValue($this->recordData);
    $digitalSignatureLength = (int) $this->fields[15]->getValueRaw();
    
    $this->addField(new Fields\VariableLength\Binary\DigitalSignature($imageRefKeyLength, $digitalSignatureLength));
    $this->addField(new Fields\SizeBytes(18, 'Length of Image Data',        Field::USAGE_MANDATORY,   111 + $imageRefKeyLength + $digitalSignatureLength, 7));
    
    // get the size of the third variable key, field 18 - Length of Image Data
    $this->fields[17]->parseValue($this->recordData);
    $imageDataLength = (int) $this->fields[17]->getValue();
    
    $this->addField(new Fields\VariableLength\Binary\ImageData($imageRefKeyLength + $digitalSignatureLength, $imageDataLength));
    }
}