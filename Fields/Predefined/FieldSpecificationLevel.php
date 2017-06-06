<?php

namespace X937\Fields\Predefined;

/**
 * Field defining the specification level.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldSpecificationLevel extends FieldPredefined
{
    const X9371994 = '01';
    const X9372001 = '02';
    const X9372003 = '03';
    const X9100180 = '20';
    
    public function __construct()
    {
    parent::__construct(2, 'Specification Level', self::USAGE_MANDATORY, 3, 2, self::TYPE_NUMERIC);
    }
    
    public static function defineValues()
    {
    $definedValues = array(
        self::X9371994 => 'X9.37-1994',
        self::X9372001 => 'X9.37-2001',
        self::X9372003 => 'X9.37-2003',
        self::X9100180 => 'X9.100-180',
    );
    
    return $definedValues;
    }
}