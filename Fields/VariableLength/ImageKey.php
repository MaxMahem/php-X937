<?php

namespace X937\Fields\VariableLength;

/**
 * Contains an ImageKey, this could possibly be a URL. Stub for later handling.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class ImageKey extends VariableLength
{
    public function __construct($fieldNumber, $filedName, $position, $size) {
	parent::__construct($fieldNumber, $filedName, self::USAGE_CONDITIONAL, $position, $size, self::TYPE_ALPHAMERICSPECIAL);
    }
}