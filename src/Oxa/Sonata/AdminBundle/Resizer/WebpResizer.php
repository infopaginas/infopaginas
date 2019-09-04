<?php

namespace Oxa\Sonata\AdminBundle\Resizer;

use Gaufrette\File;
use Imagick;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Imagick\Image;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\SimpleResizer;

class WebpResizer extends SimpleResizer
{
    const SUPPORTED_FORMATS = ['webp', 'jpg', 'png', 'gif', 'jpeg', 'jp2'];
    const DEFAULT_QUALITY = 80;
    const DEFAULT_FORMAT = 'webp';

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

        $format = isset($settings['format']) ? strtolower($settings['format']) : self::DEFAULT_FORMAT;
        if (!$this->supported($format)) {
            throw new InvalidArgumentException(sprintf(
                'Saving image in "%s" format is not supported, please use one of the following extensions: "%s"',
                $format,
                implode('", "', $this->supported())
            ));
        }

        $image = $this->adapter->load($in->getContent());

//        $content = $image->thumbnail($this->getBox($media, $settings), $this->mode)->get($format, $settings);

        /** @var Image $thumbnail */
        $thumbnail = $image->thumbnail($this->getBox($media, $settings), $this->mode);

        $format = 'jp2';
        $im = $thumbnail->getImagick();

        $im->setFormat($format);
        $im->setCompressionQuality(50);
        $im->setImageFormat($format);

        $content = $im->getImageBlob();

        $out->setContent($content, $this->metadata->get($media, $out->getName()));
    }

    private function setExceptionHandler()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (0 === error_reporting()) {
                return;
            }

            throw new RuntimeException($errstr, $errno, new \ErrorException($errstr, 0, $errno, $errfile, $errline));
        }, E_WARNING | E_NOTICE);
    }

    private function resetExceptionHandler()
    {
        restore_error_handler();
    }

    private function supported($format = null)
    {
        if (null === $format) {
            return self::SUPPORTED_FORMATS;
        }

        return in_array($format, self::SUPPORTED_FORMATS);
    }
}