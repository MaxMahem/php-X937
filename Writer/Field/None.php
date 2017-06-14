<?php

namespace X937\Writer\Field;

/**
 * Doesn't write anything! Behavior for when we do NOT want to write field data.
 * (Mainly for binary data).
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class None implements \X937\Writer\FieldInterface {
    /**
     * Returns nothing. Does nothing.
     * @param \X937\Fields\Field $field the field to write.
     * @return void
     */
    public function writeField(\X937\Fields\Field $field)
    {
    // do nothing!
    }
}