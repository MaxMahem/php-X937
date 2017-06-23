<?php

namespace X937\Writer\Field\Binary;

/**
 * Writes Binary Fields in Binary.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Binary extends \X937\Writer\Field\BinaryAbstract
{

    /**
     * Returns the Field Binary data Binary encoded.
     * @param \X937\Fields\VariableLength\Binary\BinaryData $field
     * @return string The binary data encoded in Binary
     */
    public function writeBinary(\X937\Fields\VariableLength\Binary\BinaryData $field)
    {
        return $field->getValue(\X937\Fields\VariableLength\Binary\BinaryData::FORMAT_BINARY);
    }
}