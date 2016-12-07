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
     * @param EventArgs $args
     * @return bool
     * @param \Doctrine\Common\EventArgs $args
     */
    public function postUpdate(EventArgs $args)
    {
logger(__METHOD__);
        if (!($provider = $this->getProvider($args))) {
logger('empty provider', 'error');
            return false;
        }

        $media = $this->getMedia($args);

logger($media->getContext());
        if (!$media->getBinaryContent() instanceof \SplFileInfo
            || $media->getBinaryContent() === null
        ) {
logger('empty binary content', 'error');
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
logger($filepath);
logger(' - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ');
        $contents = $media->getBinaryContent()->getRealPath();

        $file->setContent(file_get_contents($contents));

        $provider->generateThumbnails($media);

        return true;
    }
}
