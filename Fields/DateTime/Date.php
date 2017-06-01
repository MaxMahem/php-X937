<?php namespace X937\Fields\DateTime;

/**
 * Field containing a date, YYYYMMDD format.
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Date extends \X937\Fields\DateTime
{
    const X937_DATETIME_FORMAT   = 'Ymd';
    const OUTPUT_DATETIME_FORMAT = 'Y-m-d';
    
    const FIELD_SIZE = 8;
    
    const FIELD_NAME_SUFIX = 'Date';
    
    protected function addClassValidators()
    {
	$this->validator->addValidator(new \X937\Validator\ValidatorDate(self::X937_DATETIME_FORMAT));
    }
}