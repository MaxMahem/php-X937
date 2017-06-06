<?php

namespace X937\Fields\Predefined\ImageView;

/**
 * Field indicating the image view format.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Format extends ImageView
{
    // no agreement required
    const VALUE_TIFF_6     = '00';
    const VALUE_IOCA_FS_11 = '01';
    
    // agreement required
    const VALUE_PNG        = '20';
    const VALUE_JFIF       = '21';
    const VALUE_SPIFF      = '22';
    const VALUE_JBIG       = '23';
    const VALUE_JPEG_2000  = '24';
    
    public function __construct()
    {
    parent::__construct(5, 'Format Indicator', 21, 2);
    }

    public static function defineValues()
    {
    $definedValues = array(
        self::VALUE_TIFF_6     => 'TIFF 6',
        self::VALUE_IOCA_FS_11 => 'IOCA FS 11',
    
        self::VALUE_PNG        => 'PNG (Portable Network Graphics), Agreement Required.',
        self::VALUE_JFIF       => 'JFIF (JPEG File Interchange Format), Agreement Required.',
        self::VALUE_SPIFF      => 'SPIFF (Still Picture Interchange File Format), Agreement Required.',
        self::VALUE_JBIG       => 'JBIG data stream, Agreement Required.',
        self::VALUE_JPEG_2000  => 'JPEG 2000, Agreement Required.',
    );
    
    return $definedValues;
    }
    
    /**
     * Returns an array of appropriate extenions for data associated with this record type.
     * @return array
     */
    public static function defineExtension()
    {
    $definedValues = array(
        self::VALUE_TIFF_6     => 'TIF',
        self::VALUE_IOCA_FS_11 => 'ICA',
        self::VALUE_PNG        => 'PNG',
        self::VALUE_JFIF       => 'JPG',
        self::VALUE_SPIFF      => 'SPF',
        self::VALUE_JBIG       => 'JBG',
        self::VALUE_JPEG_2000  => 'JP2',
    );
    
    return $definedValues;
    }

    public static function translate($value) {
    return self::reservedTranslation($value, 2, 19, 25);
    }
    
    public static function translateExtensions($value)
    {
    $legalValues = self::defineExtension();
    
    if (array_key_exists($value, $legalValues)) {
        $translatedValue = $legalValues[$value];
    } else {
        $translatedValue = 'Undefined';
    }
    
    return $translatedValue;
    }
    
    public function getExtension() {
    return self::translateExtensions($this->value);
    }
}