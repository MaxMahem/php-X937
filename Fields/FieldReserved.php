<?php

namespace X937\Fields;

/**
 * Field Reserved for later use. Should always be blank.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldReserved extends Field
{
    public function __construct($fieldNumber, $position, $size)
    {
    parent::__construct($fieldNumber, 'Reserved', Field::USAGE_MANDATORY, $position, $size, Field::TYPE_BLANK);
    }
}
