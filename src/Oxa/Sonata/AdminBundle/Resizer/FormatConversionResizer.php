<?php

namespace Oxa\Sonata\AdminBundle\Resizer;

use Gaufrette\File;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Imagine\Imagick\Image;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
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

        $format = (empty($settings['format']) || $settings['format'] == 'reference' || $settings['format'] == 'admin') ?
            $referenceFormat : strtolower($settings['format']);

        if (!(empty($settings['format']) || $settings['format'] === 'admin') &&
            $media->getContext() === OxaMediaInterface::CONTEXT_TESTIMONIAL
        ) {
            $this->mode = ManipulatorInterface::THUMBNAIL_OUTBOUND;
            $box = new Box($settings['width'], $settings['height']);
        } else {
            $box = $this->getBox($media, $settings);
        }

        $image = $this->adapter->load($in->getContent());

        /** @var Image $thumbnail */
        $thumbnail = $image->thumbnail($box, $this->mode);

        $im = $thumbnail->getImagick();

        $im->setCompressionQuality($settings['quality']);

        if ($format != $referenceFormat) {
            $im->setImageFormat($format);
        }

        $out->setContent($im->getImageBlob(), $this->metadata->get($media, $out->getName()));
    }
}