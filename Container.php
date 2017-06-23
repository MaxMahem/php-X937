<?php

namespace X937;

/**
 * Description of Container
 *
 * @author astanley
 */
abstract class Container
{
    // Usage Types
    const USAGE_CONDITIONAL = 'C';
    const USAGE_MANDATORY = 'M';
    const USAGE_OPTIONAL = 'O';
    const USAGE_FORBIDDEN = 'F';

    const USAGES = array(
        self::USAGE_CONDITIONAL => 'Conditional',
        self::USAGE_MANDATORY => 'Mandatory',
        self::USAGE_OPTIONAL => 'Optional',
        self::USAGE_FORBIDDEN => 'Forbidden',
    );

    // Validation Types
    const VALIDATION_REQUIRED = 'R';
    const VALIDATION_PRESENT = 'P';
    const VALIDATION_NONE = 'N';

    const VALIDATION = array(
        self::VALIDATION_REQUIRED => 'Required',
        self::VALIDATION_PRESENT => 'Required if Present',
        self::VALIDATION_NONE => 'None',
    );

    const PROPERTIES = array();
    protected $template;

    abstract function validate(): ?string;

    public function __get($name)
    {
        if (array_key_exists($name, $this->template)) {
            return $this->template[$name];
        } else {
            trigger_error("Attempted to get property $name which is undefined.");
            return null;
        }
    }

    public function __isset($name)
    {
        return isset($this->template[$name]);
    }
}
