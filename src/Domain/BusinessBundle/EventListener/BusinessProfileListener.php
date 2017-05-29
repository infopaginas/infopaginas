<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\VideoBundle\Entity\VideoMedia;

class BusinessProfileListener implements EventSubscriber
{
    private $businessProfileManager;

    public function setBusinessProfileManager($businessProfileManager)
    {
        $this->businessProfileManager = $businessProfileManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();

        if ($entity instanceof BusinessProfile) {
            $this->businessProfileManager->removeBusinessFromElastic($entity->getId());

            if ($entity->getVideo()) {
                $media = $entity->getVideo();

                if ($media->getYoutubeSupport() and $media->getYoutubeId()) {
                    $media->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_REMOVE);
                } else {
                    $em->remove($media);
                }
            }
        }
    }
}