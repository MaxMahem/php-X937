<?php

namespace X937\Fields\VariableLength\Binary;

/**
 * Contains a digital signature. Stub for later handling.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class DigitalSignature extends BinaryData
{
    public function __construct($offset, $size)
    {
    parent::__construct(17, 'Digital Signature', self::USAGE_CONDITIONAL, 111 + $offset, $size);
    }
}