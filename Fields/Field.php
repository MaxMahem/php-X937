<?php namespace X937\Fields;

use X937\Validator as Validator;

/**
 * Contains a specific X937Field
 *
 * @author astanley
 */
class Field
{
    // Usage Types
    const USAGE_CONDITIONAL = 'C';
    const USAGE_MANDATORY   = 'M';
    const USAGE_OPTIONAL    = 'O';
    const USAGE_FORBIDDEN   = 'F';
    
    const USAGES = array(
        self::USAGE_CONDITIONAL => 'Conditional',
        self::USAGE_MANDATORY   => 'Mandatory',
        self::USAGE_OPTIONAL    => 'Optional',
        self::USAGE_FORBIDDEN   => 'Forbidden',
    );
    
    // Validation Types
    const VALIDATION_REQUIRED = 'Required';
    const VALIDATION_PRESENT  = 'Required if Present';
    const VALIDATION_NONE     = 'None';
    
    const VALIDATION = array(
        self::VALIDATION_REQUIRED => 'Required',
        self::VALIDATION_PRESENT  => 'Required if Present',
        self::VALIDATION_NONE     => 'None',
    );
    
    // field types
    const TYPE_ALPHABETIC                  = 'A';
    const TYPE_NUMERIC                     = 'N';
    const TYPE_BLANK                       = 'B';
    const TYPE_SPECIAL                     = 'S';
    const TYPE_ALPHAMERIC                  = 'AN';
    const TYPE_ALPHAMERICSPECIAL           = 'ANS';
    const TYPE_NUMERICBLANK                = 'NB';
    const TYPE_NUMERICSPECIAL              = 'NS';
    const TYPE_NUMERICBLANKSPECIALMICR     = 'NBSM';
    const TYPE_NUMERICBLANKSPECIALMICRONUS = 'NBSMOS';
    const TYPE_BINARY                      = 'Binary';
    
    const TYPES = array(
        self::TYPE_ALPHABETIC                  => 'Alphabetic characters (A-Z, a-z) and space.',
        self::TYPE_NUMERIC                     => 'Numeric characters (0-9)',
        self::TYPE_BLANK                       => 'Blank character, space (ASCII 0x20, EBCDIC 0x40)',
        self::TYPE_SPECIAL                     => 'Any printable character (ASCII > 0x1F, EBCIDC > 0x3F',
        self::TYPE_ALPHAMERIC                  => 'Any Alphabetic or Numeric character',
        self::TYPE_ALPHAMERICSPECIAL           => 'Any Alphabetic, Numeric, or Special character.',
        self::TYPE_NUMERICBLANK                => 'Any Numeric or Blank character',
        self::TYPE_NUMERICSPECIAL              => 'Any Numeric of Special character',
        self::TYPE_NUMERICBLANKSPECIALMICR     => 'Any Numeric Character, Dash (-), or Asterisk (*)',
        self::TYPE_NUMERICBLANKSPECIALMICRONUS => 'Any Numeric Character, Dash (-), Asterisk (*), or Forward Slash (/)',
        self::TYPE_BINARY                      => 'Binary Data',
    );
    
    const SUBTYPE_ROUTINGNUMBER = 'Routing Number';
    const SUBTYPE_DATE          = 'Date';
    const SUBTYPE_TIME          = 'Time';
    const SUBTYPE_PHONENUMBER   = 'Phone Number';
    const SUBTYPE_AMOUNT        = 'Amount';
    
    const SUBTYPES = array(
        self::SUBTYPE_ROUTINGNUMBER => 'Routing Number (with check digit)',
        self::SUBTYPE_DATE          => 'Date, YYYYMMDD',
        self::SUBTYPE_TIME          => 'Time, HHMM',
        self::SUBTYPE_PHONENUMBER   => 'Phone Number',
        self::SUBTYPE_AMOUNT        => 'Amount',
    );
    
    protected $fieldTemplate;
    
    // field properties names (leaf)
    const PROP_NAME             = 'name';
    const PROP_TYPE             = 'type';
    const PROP_SUBTYPE          = 'subtype';
    const PROP_USAGE            = 'usage';
    const PROP_VALIDATION       = 'validation';
    const PROP_LENGTH           = 'length';
    const PROP_VARIABLELENGTH   = 'variableLength';
    const PROP_POSITION         = 'position';
    const PROP_VARIABLEPOSITION = 'variablePosition';
    const PROP_VALUEKEY         = 'valueKey';
    
    // field properties names (branch)
    const PROP_DICTONARY        = 'dictonary';

    // field property array
    const LEAF_PROPERTIES = [
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
    
    const PROPERTIES = self::LEAF_PROPERTIES + [self::PROP_DICTONARY];
    
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
    
    public function __construct(array $fieldTemplate) {
        $this->fieldTemplate = $fieldTemplate;
        
        $this->addValidators();
    }

    /**
     * Adds the base Validators to the field, based on attributes we can pre-determine.
     */
    protected function addValidators() {
        // initialize validator
        $this->validator = new Validator\Validator();

        // add validator based on usage.
        if ($this->fieldTemplate['usage'] === Field::USAGE_MANDATORY) {
            $this->validator->addValidator(new Validator\ValidatorUsageManditory());
        }

        // add validator based on size.
        $this->validator->addValidator(new Validator\ValidatorSize((int) $this->fieldTemplate['length']));

        // add validator based on type.
        switch ($this->fieldTemplate['type']) {
            case Field::TYPE_ALPHABETIC:
                $this->validator->addValidator(new Validator\ValidatorTypeAlphabetic());
                break;
            case Field::TYPE_NUMERIC:
                $this->validator->addValidator(new Validator\ValidatorTypeNumeric());
                break;
            case Field::TYPE_BLANK:
                $this->validator->addValidator(new Validator\ValidatorTypeBlank());
                break;
            case Field::TYPE_SPECIAL:
                // insert validators
                break;
            case Field::TYPE_ALPHAMERIC:
                $this->validator->addValidator(new Validator\ValidatorTypeAlphameric());
                break;
            /**
             * @todo add rest of validators.
             */
            default:
                // possibly throw error here?
                break;
        }
        
        // add validator based on subtype.
        if (isset($this->fieldTemplate['subtype'])) {
            switch ($this->fieldTemplate['subtype']) {
                case Field::SUBTYPE_ROUTINGNUMBER:
                    /**
                     * @todo handle it.
                     */
                    break;
                default:
                    // do nothing
                    break;
            }
        }
    }
    
    public function set(string $value): bool {
        switch ($this->fieldTemplate['validation']) {
            case self::VALIDATION_PRESENT:
            case self::VALIDATION_REQUIRED:
                // deliberate fall through
                $validationResult = $this->validator->validate($value);
                if ($validationResult) {
                    $this->value = $value;
                } else {
                    return false;
                }
                break;
            default:
                $this->value = $value;
        }
        
        $this->fieldTemplate[self::PROP_LENGTH] = strlen($value);
        return true;
    }

    // validate
    public function validate() {
        return $this->validator->validate($this->value);
    }

    // getters
    public function getTemplate()   { return $this->fieldTemplate; }
    
    public function __get($name) {
        if (isset($this->fieldTemplate[$name])) {
            return $this->fieldTemplate[$name];
        } else {
            trigger_error("Attempted to get property $name which is undefined.");
            return null;
        }
    }
    
    public function __isset($name) {
        return isset($this->recordTemplate[$name]);
    }
    
    /**
     * Return the value.
     *
     * @return string
     */
    public function getValue(string $dataType = \X937\Util::DATA_ASCII): string {
        switch ($dataType) {
            case \X937\Util::DATA_ASCII:
                return $this->value;
            case \X937\Util::DATA_EBCDIC:
                if ($this->fieldTemplate[self::PROP_TYPE] === self::TYPE_BINARY) {
                    return $this->value;
                } else {
                    return \X937\Util::a2e($this->value);
                }
            default:
                throw new \InvalidArgumentException("getValue called with invalid data type, $dataType");
        }
        
        return $this->value;
    }
    
    /**
     * Return the value, after calling the classes formating function. Nothing
     * if blank.
     * @return string
     */
    public function getValueFormated() {
        if ($this->type === self::TYPE_BINARY) {
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
    public function getValueSignifigant() {
        if ($this->type === self::TYPE_BINARY) {
            return 'Binary Data';
        }

        $value = trim($this->value);
        return ltrim($value, '0');
    }
    
    /**
     * Returns the raw value.
     * @return string
     */
    public function getValueRaw() {
        return $this->value;
    }
    
    /**
     * Returns the value formated. By default just a trim, but classes override.
     * @param string $value
     * @return string
     */
    protected static function formatValue($value) {
        return trim($value);
    }
}