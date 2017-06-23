<?php

namespace X937\Writer;

/**
 * Writes images from a check file to disk.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Image extends AbstractWriter implements WriterInterface
{
    // the format we are going to write out in.
    const FORMAT_FILE = 'file';
    const FORMAT_BASE64 = 'base64';
    const FORMAT_BINARY = 'binary';
    const FORMAT_NONE = 'none';
    const FORMAT_STUB = 'stub';

    /**
     * Image format for the associated record.
     * @var string
     */
    private $imageExtension = 'tif';

    /**
     * The current side of our image.
     * @var string
     */
    private $viewSide = 'front';

    /**
     * Create a Image, for writing check images to disk.
     * @param string $optionFormat the format to write our image file in.
     * @param string $path Path to folder to write the image files in if
     * it does not exist, it is created.
     */
    public function __construct($format = self::FORMAT_BASE64, $path = null)
    {
        if (array_key_exists($format, self::defineFormats()) === false) {
            throw new \InvalidArgumentException('Format is not a defined type.');
        }

        if ($format === self::FORMAT_FILE) {
            if (is_writable($path) === FALSE) {
                $result = mkdir($path, 0777, true);
                if ($result === FALSE) {
                    throw new \InvalidArgumentException('Error writing to path.');
                }
            }
        }

        $this->options = array(
            self::OPTION_FORMAT => $format,
            self::OPTION_PATH => $path
        );
    }

    public static function defineFormats()
    {
        $legalFormats = array(
            self::FORMAT_BASE64 => 'Base 64 Encoded',
            self::FORMAT_BINARY => 'Binary Data',
            self::FORMAT_FILE => 'Write to seperate file',
            self::FORMAT_NONE => 'Ignore Image Data',
            self::FORMAT_STUB => 'Return Image Stub',
        );

        return $legalFormats;
    }

    public function writeRecord(\X937\Records\Record $record)
    {
        // we only handle these two record types.
        switch ($record->getType()) {
            case Fields\Predefined\RecordType::VALUE_IMAGE_VIEW_DETAIL:
                $this->imageExtension = $record->getFieldByName('Image View Format Indicator')->getExtension();
                $this->viewSide = $record->getFieldByName('View Side Indicator')->getValue();

                /**
                 * @todo: validation checks here.
                 */

                break;
            case Fields\Predefined\RecordType::VALUE_IMAGE_VIEW_DATA:
                return $this->writeBinaryField($record);
                break;
        }
    }

    protected function writeBinaryField($record)
    {
        $imageDataField = $record->getFieldByName('Image Data');

        switch ($this->options[self::OPTION_FORMAT]) {
            case self::FORMAT_NONE:
                return '';

            case self::FORMAT_STUB:
                $value = $imageDataField->getValue(Fields\Field::FORMAT_RAW);
                echo $value;
                return $value;

            case self::FORMAT_BASE64:
                return $imageDataField->getValue(BinaryData::FORMAT_BASE64);

            case self::FORMAT_BINARY:
                return $imageDataField->getValue(BinaryData::FORMAT_BINARY);

            case self::FORMAT_FILE:
                return $this->writeImageBinary($record);
        }
    }

    private function writeImageBinary(Record\VariableLength\ImageViewData $record)
    {
        $path = $this->options['path'];
        $fileId = trim($record->getFieldByNumber(5)->getValue());
        $side = $this->viewSide;
        $extension = $this->imageExtension;

        $filename = $path . DIRECTORY_SEPARATOR . $fileId . '-' . $side . '.' . $extension;

        $file = fopen($filename, 'wb');

        if ($file === false) {
            throw new \Exception("Unable to open $filename for writing");
        }

        fwrite($file, $record->getFieldByNumber(19)->getValueBinary());

        fclose($file);

        return $filename;
    }
}