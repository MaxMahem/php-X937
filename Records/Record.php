<?php

namespace X937\Records;

use X937\Container;
use X937\Fields\Field;
use X937\Fields;
use X937\Util;

/**
 * X937Record represent a single variable length line of a X937 file.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley
 * @property-read string name The Name of the record
 * @property-read string type The Type of the record (two digit numeric)
 * @property-read string usage The Usage restriction of the record
 * @property-read string validation The Validation restriction of the record
 * @property-read string length The length of the record
 * @property-read string variableLength The varible length formulation, if present
 * @property-read string fieldCount The number of fields in the record
 */
class Record extends Container implements \ArrayAccess, \Countable, \IteratorAggregate
{
    const PROP_NAME = 'name';
    const PROP_TYPE = 'type';
    const PROP_USAGE = 'usage';

    // record properties names (leaf, parsed)
    const PROP_VALIDATION = 'validation';
    const PROP_LENGTH = 'length';
    const PROP_VARIABLELENGTH = 'variableLength';
    const PROP_FIELDCOUNT = 'fieldCount';
    protected const PROP_FIELDS = 'fields';
    const PARSED_PROPERTIES = [
        self::PROP_NAME,
        self::PROP_TYPE,
        self::PROP_USAGE,
        self::PROP_VALIDATION,
        self::PROP_LENGTH,
        self::PROP_VARIABLELENGTH,
        self::PROP_FIELDCOUNT,
    ];
    const PROPERTIES = self::PARSED_PROPERTIES;

    // protected const, we only need this during the constructor.
    /**
     * Contains all the field in the record.
     * @var array
     */
    protected $fields;

    // record properties
    /**
     * Reference array that links field name => field number.
     * @var array
     */
    protected $fieldsRef;
    /**
     * List of variable keys, for fields with variable lengths.
     * @var array
     */
    protected $keyArray;

    /**
     * Creates a X937Record.
     *
     * @param array $template a template with the 'bones' of the record.
     */
    public function __construct(array $template)
    {
        $this->template = $template;

        // create the records fields
        $fields = $template[self::PROP_FIELDS];
        foreach ($fields as $order => $fieldTemplate) {
            // since our fields are indexed by 1, and our array by 0, we need to subtract one            
            $fieldIndex = $order;
            $fieldName = $fieldTemplate[Field::PROP_NAME];
            $this->fields[$fieldIndex] = new Field($fieldTemplate, $this);

            // since objects are passed by reference, both indexes point to the same object
            $this->fieldsRef[$fieldIndex] = $this->fields[$fieldIndex];
            $this->fieldsRef[$fieldName] = $this->fields[$fieldIndex];

            // populate our keyArray if necessary.
            if (isset($fieldTemplate[Field::PROP_VALUEKEY])) {
                $key = $fieldTemplate[Field::PROP_VALUEKEY];
                $this->keyArray[$key] = $this->fields[$fieldIndex];
            }
        }

        unset($this->template[self::PROP_FIELDS]);
    }

    /**
     * Updates a field with a new value given the keys association.
     *
     * @param string $key The key of the field associated with this value.
     * @param string $value The new value for this field.
     * @throws \InvalidArgumentException If the matching key is not found in the array.
     */
    public function updateAssociatedField(string $key, string $value)
    {
        if (!array_key_exists($key, $this->keyArray)) {
            throw new \InvalidArgumentException("Key $key not found in association array");
        }

        $field = $this->keyArray[$key];
        $field->set($value);

        $this->calculateLength();
    }

    /**
     * recalculate the length of the record. Should only be called in the context
     * of variable length records.
     */
    protected function calculateLength()
    {
        // recalculation is only possible on variable length records.
        if (isset($this->variableLength)) {
            $length = 0;
            foreach ($this->fields as $field) {
                $length += $field->length;
            }
        } else {
            trigger_error("recalculateLength called on record type {$this->type} with static length, nothing done.");
        }

        $this->template[Record::PROP_LENGTH] = $length;
    }

    /**
     * Parses in a string of data into the record, populating all its records.
     *
     * @param string $data The data to be parsed
     * @param string $dataType The type of data, EBCDIC or ASCII
     * @return bool If the parse was sucesfull.
     * @throws \InvalidArgumentException If gets an invalid argument.
     */
    public function parse(string $data, string $dataType = Util::DATA_EBCDIC): bool
    {
        if (!array_key_exists($dataType, Util::DATA_TYPES)) {
            throw new \InvalidArgumentException("Invalid data type: $dataType");
        }

        // parse through all our fields
        foreach ($this->fields as $field) {
            $position = $this->getFieldPosition($field);
            $length = $this->getFieldLength($field);
            $rawValue = substr($data, $position, $length);

            // if our data is binary we also do not want to translate it.
            if (($dataType === Util::DATA_EBCDIC) &&
                ($field->type != Fields\Type::BINARY)
            ) {
                $asciiValue = Util::e2a($rawValue);
            } else {
                $asciiValue = $rawValue;
            }

            $field->set($asciiValue, false);
        }

        // if our record was a variable length one, we need to calculate its length.
        if (isset($this->variableLength)) {
            $this->calculateLength();
        }
        return true;
    }

    /**
     * Gets the field position, including calculations for variable position if
     * necessary.
     *
     * @param Field $field
     * @return int
     */
    protected function getFieldPosition(Field $field): int
    {
        // some fields may have a variableposition, if so, we need to parse
        // the variable position field which will contain a record in the format
        // ###+X(+Y) where ### is a static offset, and X (and possibly Y are
        // variables that the record should be offset.
        // these variables should already be populated into our $keyArray above
        if (isset($field->variablePosition)) {
            $positionArray = explode('+', $field->variablePosition);

            $position = 0;
            foreach ($positionArray as $positionItem) {
                if (is_numeric($positionItem)) {
                    $position += $positionItem;
                } else {
                    $position += (int)$this->keyArray[$positionItem]->getValue();
                }
            }

            $position -= 1;
        } else {
            // standard field
            $position = $field->position - 1;
        }

        return (int)$position;
    }

    /**
     * Gets the length of a given field, derived from other fields if necessary.
     *
     * @param Field $field
     * @return int
     */
    protected function getFieldLength(Field $field): int
    {
        // for fields with a variable length, the field Variblelength will
        // contain a variable which identifies what part of the records
        // variable length it composes. We populate the length with the value
        // of this variable
        if (isset($field->variableLength)) {
            $lengthVariable = $field->variableLength;
            $fieldLength = $this->keyArray[$lengthVariable]->getValue();
        } else {
            // normal field, just return length.
            $fieldLength = $field->length;
        }

        return (int)$fieldLength;
    }

    /**
     * Returns all the data for a given record.
     *
     * @param string $dataType Either Util::DATA_ASCII or Util::DATA_EBCDIC
     * @return string The record data.
     */
    public function getData(): string
    {
        $data = '';
        foreach ($this->fields as $field) {
            $data .= $field->getValue($dataType);
        }

        return $data;
    }
    
    public function getIterator() { return new \ArrayIterator($this->fields); }

    /**
     * Returns a count of the number of fields. For Countable.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->fields);
    }

    /**
     * Tells if a given field is set or not.
     *
     * @param type $offset Either the fields name or its order (1 indexed).
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->fieldsRef[$offset]);
    }

    /**
     * Does nothing, set access is not allowed.
     *
     * @param type $offset
     * @throws \InvalidArgumentException
     */
    public function offsetUnset($offset): void
    {
        throw new \InvalidArgumentException("Set access by constructor only");
    }

    /**
     * Does nothing, set access is not allowed.
     *
     * @param type $offset
     * @param type $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        throw new \InvalidArgumentException("Set access by constructor only");
    }

    /**
     * Returns the Field specified by offset.
     *
     * @param type $offset Either the fields name or its order (1 indexed).
     * @return Field or null
     */
    public function offsetGet($offset)
    {
        return $this->fieldsRef[$offset];
    }

    /**
     * Calls all the validation routines for the records fields and the record itself.
     *
     * @return string a complied string of all the errors, with newlines.
     */
    public function validate(): ?string
    {
        $fieldErrors = array();

        foreach ($this->fields as $order => $field) {
            $fieldErrors[] = $field->validate();
        }

        /**
         * @todo Do record level validation.
         */

        $fieldErrors = array_filter($fieldErrors);
        if (!empty($fieldErrors)) {
            $errorBase = "Error validating Record Type {$this->type}: {$this->name}:";
            $errorField = implode(PHP_EOL . '  ', $fieldErrors);
            $error = $errorBase . PHP_EOL . '  ' . $errorField . PHP_EOL;
        } else {
            $error = null;
        }

        return $error;
    }
}