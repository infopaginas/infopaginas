<?php

namespace Oxa\Sonata\MediaBundle\EventListener;

use Doctrine\ORM\Events;
use Sonata\MediaBundle\Listener\ORM\MediaEventSubscriber;

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
        );
    }
}
