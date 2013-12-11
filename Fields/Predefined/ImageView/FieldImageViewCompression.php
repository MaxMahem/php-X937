<?php

namespace X937\Fields\Predefined\ImageView;

/**
 * Field indicating the image view compression algorythm.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FieldImageViewCompression extends FieldImageView
{
    // no agreement required
    const VALUE_GROUP_4       = '0';
    const VALUE_JPEG_BASELINE = '1';
    const VALUE_ABIC          = '2';
    
    // agreement required
    const VALUE_PNG           = '21';
    const VALUE_JBIG          = '22';
    const VALUE_JPEG_2000     = '23';
    
    public function __construct()
    {
	parent::__construct(6, 'Compression Algorithm Indicator', 23, 2);
    }

    public static function defineValues()
    {
	$definedValues = array(
	    self::VALUE_GROUP_4       => 'Group 4 Facsimile Compression',
	    self::VALUE_JPEG_BASELINE => 'JPEG Baseline',
	    self::VALUE_ABIC          => 'ABIC',
    
	    self::VALUE_PNG           => 'PNG (Portable Network Graphics), Agreement Required.',
	    self::VALUE_JBIG          => 'JBIG, Agreement Required.',
	    self::VALUE_JPEG_2000     => 'JPEG 2000, Agreement Required.',
	);
	
	return $definedValues;
    }
    
    public static function translate($value) {
	return self::reservedTranslation($value, 3, 20, 24);
    }
}