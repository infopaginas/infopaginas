<?php

namespace Oxa\Sonata\AdminBundle\EventListener;

use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DeleteableEntityInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Set object crud action user
 *
 * Class UserCRUDActionListener
 * @package Oxa\Sonata\AdminBundle\EventListener
 */
class UserCRUDActionListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private $user;

    public function __construct(TokenStorageInterface $token)
    {
        $this->tokenStorage = $token;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        if (is_null($this->tokenStorage->getToken())) {
            return;
        }

        $this->user = $this->tokenStorage->getToken()->getUser();
        if (!is_object($this->user)) {
            return;
        }

        $uow = $args->getEntityManager()->getUnitOfWork();

        // set user to created object
        array_map(function ($entity) use ($uow) {
            if ($entity instanceof DefaultEntityInterface) {
                $entity->setCreatedUser($this->user);
                $entity->setUpdatedUser($this->user);

                $uow->propertyChanged(
                    $entity,
                    DefaultEntityInterface::CREATE_USER_PROPERTY_NAME,
                    null,
                    $this->user
                );
                $uow->scheduleExtraUpdate($entity, [
                    DefaultEntityInterface::CREATE_USER_PROPERTY_NAME => [null, $this->user]
                ]);

                $uow->propertyChanged(
                    $entity,
                    DefaultEntityInterface::UPDATE_USER_PROPERTY_NAME,
                    null,
                    $this->user
                );
                $uow->scheduleExtraUpdate($entity, [
                    DefaultEntityInterface::UPDATE_USER_PROPERTY_NAME=> [null, $this->user]
                ]);
            }
        }, $uow->getScheduledEntityInsertions());

        // set user to updated object
        array_map(function ($entity) use ($uow) {
            if ($entity instanceof DefaultEntityInterface) {
                $entity->setUpdatedUser($this->user);

                if (array_key_exists(
                    DefaultEntityInterface::IS_ACTIVE_PROPERTY_NAME,
                    $uow->getEntityChangeSet($entity)
                )) {
                    $entity->setIsActiveUser($this->user);
                }
            }
        }, $uow->getScheduledEntityUpdates());

        // set user to deleted object
        array_map(function ($entity) use ($uow) {
            if ($entity instanceof DefaultEntityInterface) {
                $entity->setDeletedUser($this->user);
                $uow->propertyChanged(
                    $entity,
                    DeleteableEntityInterface::DELETED_USER_PROPERTY_NAME,
                    null,
                    $this->user
                );
                $uow->scheduleExtraUpdate($entity, [
                    DeleteableEntityInterface::DELETED_USER_PROPERTY_NAME => [null, $this->user]
                ]);
            }
        }, $uow->getScheduledEntityDeletions());
    }
}
