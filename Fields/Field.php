<?php namespace X937\Fields;

use X937\Record\Record;

use Respect\Validation\Validator;
Validator::with('X937\\Validation\\Rules');

/**
 * Contains a specific X937Field
 *
 * @author astanley
 */
class Field extends \X937\Container
{   
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
    
    const SUBTYPE_ROUTINGNUMBER = 'Routing';
    const SUBTYPE_DATE          = 'Date';
    const SUBTYPE_TIME          = 'Time';
    const SUBTYPE_PHONENUMBER   = 'Phone';
    const SUBTYPE_AMOUNT        = 'Amount';
    const SUBTYPE_BLANK         = 'Blank';
    
    const SUBTYPES = array(
        self::SUBTYPE_ROUTINGNUMBER => 'Routing Number (with check digit)',
        self::SUBTYPE_DATE          => 'Date, YYYYMMDD',
        self::SUBTYPE_TIME          => 'Time, HHMM',
        self::SUBTYPE_PHONENUMBER   => 'Phone Number',
        self::SUBTYPE_AMOUNT        => 'Amount',
        self::SUBTYPE_BLANK         => 'Blank',
    );
    
    protected $template;
    
    // field properties names (leaf)
    const PROP_NAME             = 'name';
    const PROP_ORDER            = 'order';
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
    const PROP_DICT_COVERAGE    = 'comprehensive';
    
    // field properties names (infered)
    const PROP_VARIABLE         = 'variable';

    // field property array
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
    
    const PROPERTIES = self::PARSED_PROPERTIES + [self::PROP_DICTONARY, self::PROP_VARIABLE, self::PROP_ORDER];
    
    /**
     * A reference back to the parent record.
     * 
     * @var \X937\Record\Record
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
    
    public function __construct(array $fieldTemplate, Record $parent = null) {
        $this->template = $fieldTemplate;
        $this->parent   = $parent;
        
        // initialize validator
        $this->validator = new Validator;

        // add validator based on usage.
        if ($this->usage === Field::USAGE_MANDATORY) {
            $this->validator->addRule(Validator::required());
        }

        // add validator based on type.
        $type = $this->type;
        switch ($type) {
            case Field::TYPE_ALPHABETIC:
                $this->validator->addRule(Validator::alpha());
                break;
            case Field::TYPE_NUMERIC:
                $this->validator->addRule(Validator::numeric());
                break;
            case Field::TYPE_BLANK:
                $this->validator->addRule(Validator::not(Validator::required()));
                break;
            case Field::TYPE_ALPHAMERIC:
                $this->validator->addRule(Validator::alnum());
                break;
            case Field::TYPE_NUMERICBLANK:
                $this->validator->addRule(Validator::digit());
                break;
            case Field::TYPE_NUMERICBLANKSPECIALMICR:
                $this->validator->addRule(Validator::digit('-*'));
                break;
            case Field::TYPE_NUMERICBLANKSPECIALMICRONUS:
                $this->validator->addRule(Validator::digit('-*/'));
                break;
            case Field::TYPE_SPECIAL:
            case Field::TYPE_BINARY:
            case Field::TYPE_ALPHAMERICSPECIAL:
                // delebriate fall through
                // no validation is possible on these fields, so do nothing.
                break;
            default:
                trigger_error("Field type $type is unhandled by validation.");
                break;
        }
        
        // add validator based on subtype.
        if (isset($this->subtype)) {
            switch ($this->subtype) {
                case Field::SUBTYPE_ROUTINGNUMBER:
                    $this->validator->addRule(Validator::routingNumber());
                    break;
                case Field::SUBTYPE_DATE:
                    $this->validator->addRule(Validator::date('Ymd'));
                    break;
                case Field::SUBTYPE_TIME:
                    $this->validator->addRule(Validator::date('hm'));
                    break;
                case Field::SUBTYPE_AMOUNT:
                    // no aditional validation necessary
                    break;
                case Field::SUBTYPE_PHONENUMBER:
                    $this->validator->addRule(Validator::phone());
                    break;
                case Field::SUBTYPE_BLANK:
                    $this->validator->addRule(Validator::not(Validator::required()));
                    break;
                default:
                    $subtype = $this->subtype;
                    trigger_error("Field subtype $subtype is unhandled by validation");
                    break;
            }
        }
        
        // add dictonary validator
        if (isset($this->dictonary) && $this->template[self::PROP_DICT_COVERAGE] === 'true') {
            $dictonary = array_keys($this->dictonary);
            $this->validator->addRule(Validator::in($dictonary));
        }
    }
    
    public function set(string $value, bool $updateParent = true) {
        $valueLen = strlen($value);
        
        // if our length is variable we need to reset our field length when we set
        if ($this->variable) {        
            $this->template[self::PROP_LENGTH] = $valueLen;
            
            if (isset($this->parent) && $updateParent) {
                $key = $this->variableLength;
                $this->parent->updateAssociatedField($key, $valueLen);
            }
        }
        
        // check to see if our variable exceeds our field length. If so, exception.
        $fixedLen = $this->length;
        if($valueLen > $fixedLen) {
            throw new \InvalidArgumentException("Value '$value' length of $valueLen does not match the field length of $fixedLen.");
        }
        
        // length might be short, so pad accordingly.
        $pad         = ($this->type === Field::TYPE_NUMERIC) ? '0' : ' ';
        $valuePadded = str_pad($value, $fixedLen, $pad, STR_PAD_LEFT);
        
        $this->value = $valuePadded;
    }

    /**
     * Validates our items and returns a string of our errors. An empty string 
     * indicates no errors.
     * 
     * @return array Errors.
     */
    public function validate(): string {
        $error = '';
        switch ($this->template[self::PROP_VALIDATION]) {
            case self::VALIDATION_PRESENT:
            case self::VALIDATION_REQUIRED:                
                // deliberate fall through
                try {
                    $this->validator->assert($this->value);
                } catch (\Respect\Validation\Exceptions\ValidationException $exception) {
                    $name  = $this->template[self::PROP_NAME];
                    $order = $this->template[self::PROP_ORDER];
                    $value = $this->value;
                    
                    $errorBase  = "Field $order $name:";
                    $errorRules = implode(' and ', $exception->getMessages());
                    $error      = $errorBase . ' ' . $errorRules . '.';
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
    public function getValue(string $dataType = \X937\Util::DATA_ASCII): string {
        switch ($dataType) {
            case \X937\Util::DATA_ASCII:
                return $this->value;
            case \X937\Util::DATA_EBCDIC:
                if ($this->template[self::PROP_TYPE] === self::TYPE_BINARY) {
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