<?php namespace X937\Fields;

use X937\Records\Record;

use Respect\Validation\Validator;

Validator::with('X937\\Validation\\Rules');

/**
 * Contains a specific X937Field
 *
 * @author astanley
 */
class Field extends \X937\Container
{
    const PROP_NAME = 'name';

    // field properties names (leaf)
    const PROP_ORDER = 'order';
    const PROP_TYPE = 'type';
    const PROP_SUBTYPE = 'subtype';
    const PROP_USAGE = 'usage';
    const PROP_VALIDATION = 'validation';
    const PROP_LENGTH = 'length';
    const PROP_VARIABLELENGTH = 'variableLength';
    const PROP_POSITION = 'position';
    const PROP_VARIABLEPOSITION = 'variablePosition';
    const PROP_VALUEKEY = 'valueKey';
    const PROP_DICTONARY = 'dictonary';

    // field properties names (branch)
    const PROP_DICT_COVERAGE = 'comprehensive';
    const PARSED_PROPERTIES = [
        self::PROP_NAME,
        self::PROP_TYPE,
        self::PROP_SUBTYPE,
        self::PROP_USAGE,
        self::PROP_VALIDATION,
        self::PROP_LENGTH,
        self::PROP_VARIABLELENGTH,
        self::PROP_POSITION,
        self::PROP_VARIABLEPOSITION,
        self::PROP_VALUEKEY,
    ];

    // field property array
    const PROPERTIES = self::PARSED_PROPERTIES + [self::PROP_DICTONARY, self::PROP_ORDER];
    /**
     * template that defines how the field should be laid out.
     *
     * @var array
     */
    protected $template;
    /**
     * A reference back to the parent record.
     *
     * @var \X937\Records\Record
     */
    protected $parent;

    /**
     * Field Validator used to validate the field.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * The value of the field, always ASCII data.
     *
     * @var string
     */
    protected $value;

    /**
     * Field constructor.
     * @param array $fieldTemplate
     * @param Record|null $parent The parent record of this field
     */
    public function __construct(array $fieldTemplate, Record $parent = null)
    {
        $this->template = $fieldTemplate;
        $this->parent = $parent;

        $validators = array();
        $validators[] = Field::getTypeValidators($this->type);
        $validators[] = Field::getSubTypeValidator($this->subtype);

        // add dictonary validator
        if (isset($this->dictonary) && $this->template[self::PROP_DICT_COVERAGE] === 'true') {
            $dictonary = array_keys($this->dictonary);
            $validators[] = Validator::in($dictonary);
        }

        // remove null validators
        $validators = array_filter($validators);

        // add validator based on usage.
        switch ($this->usage) {
            case Field::USAGE_MANDATORY:
                $validators[] = Validator::required();
                $this->validator = Validator::allOf(...$validators);
                break;
            case Field::USAGE_CONDITIONAL:
            case Field::USAGE_OPTIONAL:
                $validators[] = Validator::blank();
                $this->validator = Validator::oneOf(...$validators);
                break;
            case Field::USAGE_FORBIDDEN:
                $validators[] = Validator::blank();
                $this->validator = Validator::allOf(...$validators);
                break;
        }
    }

    /**
     * @param string $type the type of this field
     * @return null|Validator A validator for this type
     */
    protected static function getTypeValidators(string $type): ?Validator
    {
        switch ($type) {
            case Type::ALPHABETIC:
                return Validator::alpha();
            case Type::NUMERIC:
                return Validator::numeric();
            case Type::BLANK:
                return Validator::blank();
            case Type::ALPHAMERIC:
                return Validator::alnum();
            case Type::NUMERICBLANK:
                return Validator::digit();
            case Type::NUMERICBLANKSPECIALMICR:
                return Validator::digit('-*');
            case Type::NUMERICBLANKSPECIALMICRONUS:
                return Validator::digit('-*/');
            case Type::SPECIAL:
            case Type::BINARY:
            case Type::ALPHAMERICSPECIAL:
                // delebriate fall through
                // no validation is possible on these fields, so do nothing.
                return null;
            default:
                trigger_error("Field type $type is unhandled by validation.");
                return null;
        }
    }

    /**
     * @param null|string $subtype The subtype of this field
     * @return null|Validator A validator for this subtype, or null if none
     */
    protected static function getSubTypeValidator(?string $subtype): ?Validator
    {
        switch ($subtype) {
            case SubType::ROUTINGNUMBER:
                return Validator::routingNumber();
            case SubType::DATE:
                return Validator::date('Ymd');
            case SubType::TIME:
                return Validator::date('Hm');
            case SubType::AMOUNT:
                // no aditional validation necessary
                return null;
            case SubType::PHONENUMBER:
                return Validator::phone();
            case SubType::BLANK:
                return Validator::blank();
            case null:
                return null;
            default:
                trigger_error("Field subtype $subtype is unhandled by validation");
                return null;
        }
    }

    public function set(string $value, bool $updateParent = true)
    {
        $valueLen = strlen($value);

        // if our length is variable we need to reset our field length when we set
        if (isset($this->variableLength)) {
            $this->template[self::PROP_LENGTH] = $valueLen;

            if (isset($this->parent) && $updateParent) {
                $key = $this->variableLength;
                $this->parent->updateAssociatedField($key, $valueLen);
            }
        }

        // check to see if our variable exceeds our field length. If so, exception.
        $fixedLen = $this->length;
        if ($valueLen > $fixedLen) {
            throw new \InvalidArgumentException("Value '$value' length of $valueLen does not match the field length of $fixedLen.");
        }

        // length might be short, so pad accordingly.
        $pad = ($this->type === Type::NUMERIC) ? '0' : ' ';
        $valuePadded = str_pad($value, $fixedLen, $pad, STR_PAD_LEFT);

        $this->value = $valuePadded;
    }

    /**
     * Validates our items and returns a string of our errors. An empty string
     * indicates no errors.
     *
     * @return array Errors.
     */
    public function validate(): ?string
    {
        $error = '';
        switch ($this->template[self::PROP_VALIDATION]) {
            case self::VALIDATION_PRESENT:
            case self::VALIDATION_REQUIRED:
                // deliberate fall through
                try {
                    $this->validator->assert($this->value);
                } catch (\Respect\Validation\Exceptions\ValidationException $exception) {
                    $name = $this->name;
                    $order = $this->order;

                    $error = "Field $order $name: " . implode(' and ', $exception->getMessages());
                }
            default:
                // do nothing;
                break;
        }

        return $error;
    }

    /**
     * Return the value.
     *
     * @return string
     */
    public function getValue(string $dataType = \X937\Util::DATA_ASCII): string
    {
        switch ($dataType) {
            case \X937\Util::DATA_ASCII:
                return $this->value;
            case \X937\Util::DATA_EBCDIC:
                if ($this->template[self::PROP_TYPE] === Type::BINARY) {
                    return $this->value;
                } else {
                    return \X937\Util::a2e($this->value);
                }
            default:
                throw new \InvalidArgumentException("getValue called with invalid data type, $dataType");
        }
    }

    /**
     * Return the value, after calling the classes formating function. Nothing
     * if blank.
     * @return string
     */
    public function getValueFormated()
    {
        if ($this->type === Type::BINARY) {
            return 'Binary Data';
        }

        $value = $this->getValueSignifigant();

        // if value is blank we don't want to return that an not call the other
        // format functions.
        if ($value === '') {
            return '';
        }

        return static::formatValue($value);
    }

    /**
     * Return the signifigant parts of the value, but in raw. Generally leading
     * 0's are not signifigant and are stripped. Binary data is stubbed.
     * @return string
     */
    public function getValueSignifigant()
    {
        if ($this->type === Type::BINARY) {
            return 'Binary Data';
        }

        $value = trim($this->value);
        return ltrim($value, '0');
    }

    /**
     * Returns the value formated. By default just a trim, but classes override.
     * @param string $value
     * @return string
     */
    protected static function formatValue($value)
    {
        return trim($value);
    }

    /**
     * Returns the raw value.
     * @return string
     */
    public function getValueRaw()
    {
        return $this->value;
    }
}