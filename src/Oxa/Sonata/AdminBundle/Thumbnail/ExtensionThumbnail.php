<?php

namespace Oxa\Sonata\AdminBundle\Thumbnail;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Thumbnail\FormatThumbnail;

class ExtensionThumbnail extends FormatThumbnail
{
    /**
     * @var string
     */
    protected $defaultFormat;

    /**
     * @param string $defaultFormat
     */
    public function __construct($defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
        parent::__construct($defaultFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        $formatSettings = $provider->getFormat($format);

        if ($format == 'reference') {
            $path = $provider->getReferenceImage($media);
        } else {
            $path = sprintf(
                '%s/thumb_%s_%s.%s',
                $provider->generatePath($media),
                $media->getId(),
                $format,
                $this->getExtension($media, $formatSettings)
            );
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrivateUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        $formatSettings = $provider->getFormat($format);

        if ('reference' === $format) {
            return $provider->getReferenceImage($media);
        }

        return sprintf(
            '%s/thumb_%s_%s.%s',
            $provider->generatePath($media),
            $media->getId(),
            $format,
            $this->getExtension($media, $formatSettings)
        );
    }

    /**
     * @param MediaInterface $media
     * @param array $formatSettings
     *
     * @return string the file extension for the $media, or the $defaultExtension if not available
     */
    protected function getExtension(MediaInterface $media, $formatSettings = [])
    {
        $extension = (empty($formatSettings['format']) || $formatSettings['format'] == 'reference') ?
            $media->getExtension() : $formatSettings['format'];

        if (!is_string($extension) || strlen($extension) < 3) {
            $extension = $this->defaultFormat;
        }

        return $extension;
    }
}