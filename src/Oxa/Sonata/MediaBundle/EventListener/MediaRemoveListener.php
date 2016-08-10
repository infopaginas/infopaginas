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
        // remove preRemove event to prevent thumbnail pictures deletion
        // thumbnail pictures has to exist when we restore media object
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::postRemove,
            Events::postPersist,
            Events::postUpdate,
        );
    }

    /**
     * @param \Doctrine\Common\EventArgs $args
     */
    public function postUpdate(EventArgs $args)
    {
        if (!($provider = $this->getProvider($args))) {
            return;
        }

        $media = $this->getMedia($args);

        if (!$media->getBinaryContent() instanceof \SplFileInfo) {
            return;
        }

        $oldMedia = clone $media;
        $oldMedia->setProviderReference($media->getPreviousProviderReference());

        if ($media->getBinaryContent() === null) {
            return;
        }

        // if the binary content is a filename => convert to a valid File
        if (!$media->getBinaryContent() instanceof File) {
            if (!is_file($media->getBinaryContent())) {
                throw new \RuntimeException('The file does not exist : '.$media->getBinaryContent());
            }

            $binaryContent = new File($media->getBinaryContent());

            $media->setBinaryContent($binaryContent);
        }

        $file = $provider->getFilesystem()->get(sprintf('%s/%s', $provider->generatePath($media), $media->getProviderReference()), true);

        $contents = $media->getBinaryContent()->getRealPath();

        $file->setContent(file_get_contents($contents));

        $provider->generateThumbnails($media);
    }
}
