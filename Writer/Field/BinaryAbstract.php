<?php

namespace X937\Writer\Field;

use X937\Fields as Fields;

/**
 * Abstract class for child fields Binary Fields in Base64.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
abstract class BinaryAbstract implements \X937\Writer\FieldInterface {
    /**
     * Returns a field formated appropriately.
     * @param \X937\Fields\Field $field the field to write.
     * @return string formated field
     */
    public function writeField(Fields\Field $field)
    {
	return $this->writeBinary($field);
    }
    
    abstract public function writeBinary(Fields\VariableLength\Binary\BinaryData $field);
}
