<?php

namespace X937\Fields;

use Respect\Validation\Validator;

Validator::with('X937\\Validation\\Rules');

/**
 * A factor class to generate new Fields from different sorts of input.
 *
 * @author astanley
 */
class FieldFactory
{
    /**
     * Contains a template of reference record structures, parsed from the
     * specification file. Used to create record objects.
     * @todo consider using an array of template objects for this instead?
     *
     * @var array
     */
    protected $fieldTypes;

    protected $globalPredefines;

    public function __construct(string $specXMLFile = 'FieldTypes.xsd')
    {
        // guard input
        $specDOM = new \DOMDocument();
        if (!$specDOM->load($specXMLFile)) {
            throw new \InvalidArgumentException("Loading of XML file $specXMLFile failed.");
        }
        if (!$specDOM->schemaValidate('https://www.w3.org/2001/XMLSchema.xsd')) {
            throw new \InvalidArgumentException("$specXMLFile failed schema validation.");
        }

        // create our XPath
        $specXPath = new \DOMXPath($specDOM);

        // parse each record
        $fieldTypeDOMList = $specXPath->query('/xs:schema/xs:simpleType');
        foreach ($fieldTypeDOMList as $fieldTypeDOM) {
            $type = $fieldTypeDOM->getAttribute('name');
            $patternDOM = $specXPath->query('xs:restriction/xs:pattern', $fieldTypeDOM);
            $pattern = $patternDOM->item(0)->getAttribute('value');
            $this->fieldTypes[$type]['pattern'] = $pattern;
        }
    }

    public function generateField(array $fieldTemplate): Field
    {
        $type = $fieldTemplate[Field::PROP_TYPE];
        
        if (isset($this->fieldTypes[$type])) {
            $typeValidator = Validator::regex($this->fieldTypes[$type]['pattern']);
        }
    }
}