<?php

namespace X937\Fields\Predefined;

/**
 * Field containing the type of Fed Work.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldFedWorkType extends FieldPredefined
{
    const CITY                      = '1';
    const CITY_GROUP                = '2';
    const CITY_FINE_SORT            = '3';
    const RCPC                      = '4';
    const RCPC_GROUP                = '5';
    const RCPC_FINE_SORT            = '6';
    const HIGH_DOLLAR_GROUP_SORT    = '7';
    const COUNTRY                   = '8';
    const COUNTRY_GROUP_SORT        = '9';
    const COUNTRY_FINE_SORT         = '0';
    const OTHER_DISTRICT            = 'A';
    const OTHER_DISTRICT_GROUP_SORT = 'B';
    const MIXED                     = 'C';
    const CITY_RCPC_MIXED           = 'D';
    const PAYOR_GROUP_SORT          = 'E';
    
    public function __construct()
    {
	parent::__construct(13, 'Fed Work Type', self::USAGE_CONDITIONAL, 77, 1, self::TYPE_ALPHAMERIC);
    }

    public static function defineValues()
    {
	$definedValues = array(
	    self::CITY                      => 'City',
	    self::CITY_GROUP                => 'City Group',
	    self::CITY_FINE_SORT            => 'City Fine Sort',
	    self::RCPC                      => 'RCPC',
	    self::RCPC_GROUP                => 'RCPC Group',
	    self::RCPC_FINE_SORT            => 'RCPC Fine Sort',
	    self::HIGH_DOLLAR_GROUP_SORT    => 'High Dollar Group Sort',
	    self::COUNTRY                   => 'Country',
	    self::COUNTRY_GROUP_SORT        => 'Country Group Sort',
	    self::COUNTRY_FINE_SORT         => 'Country Group Sort',
	    self::OTHER_DISTRICT            => 'Other District',
	    self::OTHER_DISTRICT_GROUP_SORT => 'Other District Group Sort',
	    self::MIXED                     => 'Mixed',
	    self::CITY_RCPC_MIXED           => 'City/RCPC Mixed',
	    self::PAYOR_GROUP_SORT          => 'Payor Group Sort',
	);
	
	return $definedValues;
    }
}