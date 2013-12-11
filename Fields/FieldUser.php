<?php

namespace X937\Fields;

/**
 * A Field an Institution can put to its own use.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldUser extends Field
{
    public function __construct($fieldNumber, $position, $size)
    {
	parent::__construct($fieldNumber, 'User Field', Field::USAGE_CONDITIONAL, $position, $size, Field::TYPE_ALPHAMERICSPECIAL);
    }
}
