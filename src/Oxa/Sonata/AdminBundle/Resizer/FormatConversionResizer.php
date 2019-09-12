<?php

namespace Oxa\Sonata\AdminBundle\Resizer;

use Gaufrette\File;
use Imagick;
use Imagine\Imagick\Image;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\SimpleResizer;

class FormatConversionResizer extends SimpleResizer
{
    /**
     * {@inheritdoc}
     */
    public function resize(MediaInterface $media, File $in, File $out, $referenceFormat, array $settings)
    {
        if (!isset($settings['width'])) {
            throw new \RuntimeException(sprintf(
                'Width parameter is missing in context "%s" for provider "%s"',
                $media->getContext(),
                $media->getProviderName()
            ));
        }

        $format = (empty($settings['format']) || $settings['format'] == 'reference') ?
            $referenceFormat : strtolower($settings['format']);

        $image = $this->adapter->load($in->getContent());

        /** @var Image $thumbnail */
        $thumbnail = $image->thumbnail($this->getBox($media, $settings), $this->mode);

        $im = $thumbnail->getImagick();

        $im->setCompressionQuality($settings['quality']);

        if ($format != $referenceFormat) {
            $im->setImageFormat($format);
        }

        $out->setContent($im->getImageBlob(), $this->metadata->get($media, $out->getName()));
    }
}