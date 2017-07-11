<?php

namespace X937\Fields\Format;

/**
 * Doesn't write anything! Behavior for when we do NOT want to write field data.
 * (Mainly for binary data).
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FormatNone implements TextFormatInterface, BinaryFormatInterface
{
    /**
     * Returns nothing. Does nothing.
     * @param \X937\Fields\Field $field the field to write.
     * @return void
     */
    public function format(\X937\Fields\Field $field): string
    {
        return '';
    }
}