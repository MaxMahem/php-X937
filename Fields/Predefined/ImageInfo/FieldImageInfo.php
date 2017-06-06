<?php

namespace X937\Fields\Predefined\ImageInfo;

/**
 * Abstract base clase for Image Info fields. They share some const's in common.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
abstract class FieldImageInfo extends \X937\Fields\Predefined\FieldPredefined
{
    const TEST_NOT_DONE = 0;
    
    public function __construct($fieldNumber, $fieldName, $position)
    {
    parent::__construct($fieldNumber, $fieldName, self::USAGE_CONDITIONAL, $position, 1, self::TYPE_NUMERIC);
    }
}