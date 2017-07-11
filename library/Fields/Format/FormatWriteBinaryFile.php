<?php

namespace X937\Fields\Format;

/**
 * Writes images from a check file to disk.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class FormatWriteBinaryFile implements BinaryFormatInterface
{
    /**
     * The path to write our images to.
     * @var string
     */
    private $path;

    /**
     * Create a Image, for writing check images to disk.
     * @param string $path Path to folder to write the image files in if
     * it does not exist, it is created.
     */
    public function __construct(string $path)
    {
        if (is_writable($path) === FALSE) {
            $result = mkdir($path, 0777, true);
            if ($result === FALSE) {
                throw new \InvalidArgumentException('Error writing to path.');
            }
        }
    }
    
    public function format(\X937\Fields\Field $field, ?string $filename) {
        // if there is no data in this field, or if we didn't get a filename,
        // we don't want to write it to disk.
        if (empty($field->getValue()) || empty($filename)) {
            return '';
        }
        
        $filename = $this->path . DIRECTORY_SEPARATOR . $filename;
        
        $file = fopen($filename, 'wb');

        if ($file === false) {
            throw new \Exception("Unable to open $filename for writing");
        }
        
        fwrite($file, $field->getValue());

        fclose($file);

        return $filename;
    }
}