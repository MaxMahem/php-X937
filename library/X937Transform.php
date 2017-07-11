<?php

namespace X937;

/**
 * Wraps the XSLTProcessor class to automate X937 based transforms. 
 *
 * @author astanley
 */
class X937Transform {
    const TYPE_VALIDATION = 'xsd';
    const TYPE_TRANSFORM = 'xsl';
    
    const TYPE_RECORDS = 'records';
    const RECORDS_XSD = __DIR__ . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'X937Records.xsd';
    const RECORDS_XSL = __DIR__ . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'X937Records.xsl';
    
    const TYPE_STRUCTURE = 'structure';
    const STRUCTURE_XSD = __DIR__ . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'X937Structure.xsd';
    const STRUCTURE_XSL = __DIR__ . DIRECTORY_SEPARATOR . 'Records' . DIRECTORY_SEPARATOR . 'X937Structure.xsl';
    
    const TRANSFORMATIONS = array(
        self::TYPE_RECORDS => [self::TYPE_VALIDATION => self::RECORDS_XSD, self::TYPE_TRANSFORM => self::RECORDS_XSL],
        self::TYPE_STRUCTURE => [self::TYPE_VALIDATION => self::STRUCTURE_XSD, self::TYPE_TRANSFORM => self::STRUCTURE_XSL],
    );
    
    private static function transformXML(string $sourceXMLFilename, string $transformType) {
        if (!array_key_exists($transformType, self::TRANSFORMATIONS)) {
            throw new \InvalidArgumentException("TransformType $transformType is invalid");
        }
        $transformations = self::TRANSFORMATIONS[$transformType];
        
        $sourceXMLFilename = realpath($sourceXMLFilename);
        
        // load the XML source
        $sourceDOM = new \DOMDocument();
        $sourceDOM->load($sourceXMLFilename);
        
        if (!$sourceDOM) {
            throw new \InvalidArgumentException("Filename: $sourceXMLFilename failed loading.");
        }
        
        // validate 
        $validation = $sourceDOM->schemaValidate($transformations[self::TYPE_VALIDATION]);
        if (!$validation) {
            throw new \InvalidArgumentException("Filename: $sourceXMLFilename failed validation.");
        }
        
        // load the XSLT
        $transformDOM = new \DOMDocument();
        $transformDOM->load($transformations[self::TYPE_TRANSFORM]);
        
        // setup the transformer
        $processorXSL = new \XSLTProcessor();
        $processorXSL->importStylesheet($transformDOM);
        
        // populate our paramaters.
        $processorXSL->setParameter('', 'generated', date('Y-m-d H:m'));
        $processorXSL->setParameter('', 'source', $sourceXMLFilename);
        
        // transFOOOORM!
        // we do this kind of roundabout because XSLTProcessor kind of mangles our
        // whitespace otherwise.
        $newDOM = new \DOMDocument();
        $newRAW = $processorXSL->transformToXml($sourceDOM);
        $newDOM->preserveWhiteSpace = false;
        $newDOM->loadXML($newRAW);
        $newDOM->formatOutput = true;
        
        // get new filename replacing old extension
        $xmlBasename = pathinfo($sourceXMLFilename, PATHINFO_FILENAME);
        $newFilename = $xmlBasename . '.' . 'xsd';
        $newDOM->save($newFilename);
    }
    
    public static function transformRecordsXML(string $recordsXMLFilename) {
        self::transformXML($recordsXMLFilename, self::TYPE_RECORDS);
    }
    
    public static function transformStructureXML(string $structXMLFilename) {
        self::transformXML($structXMLFilename, self::TYPE_STRUCTURE);
    }
}
