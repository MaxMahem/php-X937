<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace X937\Fields;

/**
 * Stub class for Field Date and Time classes. DateTime name should be fine
 * because of namespaces.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
abstract class DateTime extends Field {
    const X937_DATETIME_FORMAT   = 'invalid';
    const OUTPUT_DATETIME_FORMAT = 'invalid';
    
    const FIELD_SIZE = -1;
    
    const FIELD_NAME_SUFIX = 'invalid';
    
    public function __construct($fieldNumber, $fieldNamePrefix, $usage, $position)
    {
	parent::__construct(
	    $fieldNumber,
	    $fieldNamePrefix . ' ' . static::FIELD_NAME_SUFIX,
	    $usage,
	    $position,
	    static::FIELD_SIZE,
	    Field::TYPE_NUMERIC
	);
    }
    
    /**
     * Returns the date as a DateTime object (if possible). Returns false if unable to parse
     * @return mixed \DateTime date time object from the date, or false if unable to convert
     */
    public function getDateTime() {
	$dateTime = \DateTime::createFromFormat(static::X937_DATE_FORMAT, $this->value);
	
	// check if we got a good dateTime. If not, emit a notice, return false.
	if ($dateTime === false) {
	    $errorArray = date_get_last_errors();

	    $errors = implode(', ', $errorArray['errors']);
	    
	    $errorMessage = "Problem creating date: Errors: $errors";
	    trigger_error($errorMessage, E_USER_NOTICE);
	    
	    return false;
	}
	
	return $dateTime;
    }
    
    /**
     * Retuns the date Formated YYYY-MM-DD, or nothing if blank. Overrident to
     * avoid double calling formatValue (which causes problems).
     * @return string Formated Date YYYY-MM-DD
     */
    public function getValueFormated() {
	if (trim($this->value === '')) {
	    return '';
	} else {
	    return static::formatValue($this->value);
	}
    }
    
    /**
     * Returns the dateTime, formated appropriately leading 0's are signifigant.
     * @return string
     */
    public function getValueSignifigant() {
	return static::formatValue($this->value);
    }
    
    protected static function formatValue($value)
    {
	$dateTime = \DateTime::createFromFormat(static::X937_DATETIME_FORMAT, $value);
        
        if ($dateTime == FALSE) {
            throw new \InvalidArgumentException("Unable to format date: $value");
        }
	
	return $dateTime->format(static::OUTPUT_DATETIME_FORMAT);
    }
}
