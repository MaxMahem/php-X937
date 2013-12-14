<?php

namespace X937\Fields\Predefined;

/**
 * Field indicating the work type. Allowed values vary based on the where the
 * field is located.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldDocType extends FieldPredefined
{
    const NO_IMAGE_PAPER_SEPERATE                        = 'A';
    const NO_IMAGE_PAPER_SEPERATE_IMAGE_ON_REQUEST       = 'B';
    const IMAGE_SEPERATE_NO_PAPER                        = 'C';
    const IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       = 'D';
    const IMAGE_AND_PAPER_SEPERATE                       = 'E';
    const IMAGE_AND_PAPER_SEPERATE_IMAGE_ON_REQUEST      = 'F';
    const IMAGE_INCLUDED_NO_PAPER                        = 'G';
    const IMAGE_INCLUDED_NO_PAPER_IMAGE_ON_REQUEST       = 'H';
    const IMAGE_INCLUDED_PAPER_SEPERATE                  = 'I';
    const IMAGE_INCLUDED_PAPER_SEPERATE_IMAGE_ON_REQUEST = 'J';
    const NO_IMAGE_NO_PAPER                              = 'K';
    const NO_IMAGE_NO_PAPER_IMAGE_ON_REQUEST             = 'L';
    const NO_IMAGE_NO_PAPER_ELECTRONIC_CHECK_SEPERATE    = 'M';
    const NOT_SAME_TYPE                                  = 'Z';
    
    /**
     * The record type of this field, one of X937Field::CASH_LETTER_HEADER,
     * X937Field::CHECK_DETAIL, or X937Field::RETURN_RECORD
     * @var string 
     */
    private $recordType;
    
    /**
     * Creates new X937FieldDocType
     * @param  string $recordType The record type of this field: X937Field::CASH_LETTER_HEADER,
     * X937Field::CHECK_DETAIL, or X937Field::RETURN_RECORD
     * @throws InvalidArgumentException if Given a bad record type
     */
    public function __construct($recordType)
    {
	$this->recordType = $recordType;
	
	switch ($recordType) {
	    case RecordType::VALUE_CASH_LETTER_HEADER:
		$fieldNumber = 9;
		$fieldName   = 'Cash Letter Documentation Type Indicator';
		$position    = 44;
		$usage       = self::USAGE_MANDATORY;
		break;
	    case RecordType::VALUE_CHECK_DETAIL:
		$fieldNumber = 9;
		$fieldName   = 'Documentation Type Indicator';
		$position    = 73;
		$usage       = self::USAGE_CONDITIONAL;
		break;
	    case RecordType::VALUE_RETURN_RECORD:
		$fieldNumber = 8;
		$fieldName   = 'Return Documentation Type Indicator';
		$position    = 45;
		$usage       = self::USAGE_CONDITIONAL;
		break;
	    default:
		throw new InvalidArgumentException('Bad record type.');
		break;
	}
	
	parent::__construct($fieldNumber, $fieldName, $usage, $position, 1, self::TYPE_ALPHAMERIC);
    }

    public static function defineValues()
    {
	$legalValues = array(
	    self::NO_IMAGE_PAPER_SEPERATE                        => 'No image provided, paper provided separately',
	    self::NO_IMAGE_PAPER_SEPERATE_IMAGE_ON_REQUEST       => 'No image provided, paper provided separetly, image upon request',
	    self::IMAGE_SEPERATE_NO_PAPER                        => 'Image provided separetly, no paper provided',
	    self::IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       => 'Image provided separetly, no paper provided, image upon request',
	    self::IMAGE_AND_PAPER_SEPERATE                       => 'Image and paper provided separetly',
	    self::IMAGE_AND_PAPER_SEPERATE_IMAGE_ON_REQUEST      => 'Image and paper provided separetly, image upon request',
	    self::IMAGE_INCLUDED_NO_PAPER                        => 'Image included, no paper provided',
	    self::IMAGE_SEPERATE_NO_PAPER_IMAGE_ON_REQUEST       => 'Image included, no paper provided, image upon request',
	    self::IMAGE_INCLUDED_PAPER_SEPERATE                  => 'Image included, paper provided separetly',
	    self::IMAGE_INCLUDED_PAPER_SEPERATE_IMAGE_ON_REQUEST => 'Image included, paper provided separetly',
	    self::NO_IMAGE_NO_PAPER                              => 'No image provided, no paper provided',
	    self::NO_IMAGE_NO_PAPER_IMAGE_ON_REQUEST             => 'No image provided, no paper provided, image upon request',
	    self::NO_IMAGE_NO_PAPER_ELECTRONIC_CHECK_SEPERATE    => 'No image provided, no paper provided, Electronic Check provided seperately',
	    self::NOT_SAME_TYPE                                  => 'Not Same Type',
	);
	
	return $legalValues;
    }
    
    /**
     * This is overridden because the valid values differs depending upon the record type.
     */
    protected function addClassValidators()
    {
	$legalValues = array_keys(self::defineValues());
	
	switch ($this->recordType) {
	    // Check Detail Record can use the default.
	    case RecordType::VALUE_CASH_LETTER_HEADER:
		break;
	    
	    // Check Detail Record and Return Record do not permit option Z.
	    case RecordType::VALUE_CHECK_DETAIL:
		unset($legalValues[self::NOT_SAME_TYPE]);
		break;
	    case RecordType::VALUE_RETURN_RECORD:
		unset($legalValues[self::NOT_SAME_TYPE]);
		break;
		
	    // we would normaly error check here, but that should be handled in the constructor.
	}
	
	$legalValuesValidator = new \ValidatorValueEnumerated($legalValues);
	$this->validator->addValidator($legalValuesValidator);
    }
}