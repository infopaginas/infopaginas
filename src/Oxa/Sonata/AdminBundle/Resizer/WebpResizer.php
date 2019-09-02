<?php

namespace Oxa\Sonata\AdminBundle\Resizer;

use Gaufrette\File;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\SimpleResizer;

class WebpResizer extends SimpleResizer
{
    const SUPPORTED_FORMATS = ['webp', 'jpg', 'png', 'gif', 'jpeg'];
    const DEFAULT_QUALITY = 80;

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
        $image = $this->adapter->load($in->getContent());
        $thumbnail = $image->thumbnail($this->getBox($media, $settings), $this->mode);

        $resource = $thumbnail->getGdResource();
        $quality = self::DEFAULT_QUALITY;

        ob_start();
        $format = strtolower($format);

        if (!$this->supported($format)) {
            throw new InvalidArgumentException(sprintf(
                'Saving image in "%s" format is not supported, please use one of the following extensions: "%s"',
                $format,
                implode('", "', $this->supported())
            ));
        }

        if (!isset($settings['webp_quality'])) {
            if (isset($settings['quality'])) {
                $settings['webp_quality'] = $settings['quality'];
            }
        }
        if (isset($settings['webp_quality'])) {
            if ($settings['webp_quality'] < 0 || $settings['webp_quality'] > 100) {
                throw new InvalidArgumentException('webp_quality option should be an integer from 0 to 100');
            }
            $quality = $settings['webp_quality'];
        }

        $this->setExceptionHandler();

        if (false === imagewebp($resource, null, $quality)) {
            throw new RuntimeException('Save operation failed');
        }

        $this->resetExceptionHandler();

        $content = ob_get_clean();

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