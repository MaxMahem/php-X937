<?php

namespace X937\Fields\VariableLength\Binary;

/**
 * Contains a check image. Stub for later handling.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class ImageData extends BinaryData
{
    public function __construct($offset, $size)
    {
    parent::__construct(19, 'Image Data', self::USAGE_MANDATORY, 118 + $offset, $size);
    }
}