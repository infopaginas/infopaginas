<?php

namespace Oxa\Sonata\AdminBundle\Resizer;

use Gaufrette\File;
use Imagick;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Imagick\Image;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\SimpleResizer;

class FormatConversionResizer extends SimpleResizer
{
    const SUPPORTED_FORMATS = ['webp', 'jpg', 'png', 'gif', 'jpeg', 'jp2'];
    const DEFAULT_QUALITY = 80;
    const JP2_QUALITY = 35;

    /**
     * {@inheritdoc}
     */
    public function resize(MediaInterface $media, File $in, File $out, $format, array $settings)
    {
        if (!isset($settings['width'])) {
            throw new \RuntimeException(sprintf(
                'Width parameter is missing in context "%s" for provider "%s"',
                $media->getContext(),
                $media->getProviderName()
            ));
        }

        $format = isset($settings['format']) ? strtolower($settings['format']) : $format;
        if (!$this->supported($format)) {
            throw new InvalidArgumentException(sprintf(
                'Saving image in "%s" format is not supported, please use one of the following extensions: "%s"',
                $format,
                implode('", "', $this->supported())
            ));
        }

        $image = $this->adapter->load($in->getContent());

        /** @var Image $thumbnail */
        $thumbnail = $image->thumbnail($this->getBox($media, $settings), $this->mode);

        $im = $thumbnail->getImagick();
        $im->setFormat($format);

        if ($format == 'jp2') {
            $im->setImageCompressionQuality(self::JP2_QUALITY);
            $im->setCompression(imagick::COMPRESSION_JPEG2000);
        } else {
            $quality = isset($settings['quality']) ? $settings['quality'] : self::DEFAULT_QUALITY;
            $im->setImageCompressionQuality($quality);
        }

        $out->setContent($im->getImageBlob(), $this->metadata->get($media, $out->getName()));
    }

    private function supported($format = null)
    {
        if (null === $format) {
            return self::SUPPORTED_FORMATS;
        }

        return in_array($format, self::SUPPORTED_FORMATS);
    }
}