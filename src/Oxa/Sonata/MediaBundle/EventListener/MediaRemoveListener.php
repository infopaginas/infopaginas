<?php

namespace Oxa\Sonata\MediaBundle\EventListener;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Events;
use Sonata\MediaBundle\Listener\ORM\MediaEventSubscriber;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Set object crud action user
 *
 * Class MediaRemoveListener
 * @package Oxa\Sonata\AdminBundle\EventListener
 */
class MediaRemoveListener extends MediaEventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::postRemove,
            Events::postPersist,
            Events::postUpdate,
            Events::onClear,
        );
    }

    /**
     * @param EventArgs $args
     * @return bool
     * @param \Doctrine\Common\EventArgs $args
     */
    public function postUpdate(EventArgs $args)
    {
        if (!($provider = $this->getProvider($args))) {
            return false;
        }

        $media = $this->getMedia($args);

        if (!$media->getBinaryContent() instanceof \SplFileInfo
            || $media->getBinaryContent() === null
        ) {
            return false;
        }

        $oldMedia = clone $media;
        $oldMedia->setProviderReference($media->getPreviousProviderReference());

        // if the binary content is a filename => convert to a valid File
        if (!$media->getBinaryContent() instanceof File) {
            if (!is_file($media->getBinaryContent())) {
                throw new \RuntimeException('The file does not exist : '.$media->getBinaryContent());
            }

            $binaryContent = new File($media->getBinaryContent());

            $media->setBinaryContent($binaryContent);
        }

        $filepath = sprintf('%s/%s', $provider->generatePath($media), $media->getProviderReference());
        $file = $provider->getFilesystem()->get($filepath, true);

        $contents = $media->getBinaryContent()->getRealPath();

        $file->setContent(file_get_contents($contents));

        $provider->generateThumbnails($media);

        return true;
    }
}
