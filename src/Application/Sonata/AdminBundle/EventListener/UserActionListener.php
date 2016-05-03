<?php

namespace Application\Sonata\AdminBundle\EventListener;

use Application\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserActionListener
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

		array_map(function ($entity) {
			if ($entity instanceof DefaultEntityInterface) {
				$entity
					->setCreatedUser($this->user);
			}
		}, $uow->getScheduledEntityInsertions());

		array_map(function ($entity) use ($uow) {
			if ($entity instanceof DefaultEntityInterface) {
				$entity->setUpdatedUser($this->user);

				if (array_key_exists('isActive', $uow->getEntityChangeSet($entity))) {
					$entity->setIsActiveUser($this->user);
				}
			}
		}, $uow->getScheduledEntityUpdates());

		array_map(function ($entity) use ($uow) {
			if ($entity instanceof DefaultEntityInterface) {
				$entity->setDeletedUser($this->user);
				$uow->propertyChanged($entity, 'deletedUser', null, $this->user);
				$uow->scheduleExtraUpdate($entity, [
					'deletedUser' => [null, $this->user]
				]);
			}
		}, $uow->getScheduledEntityDeletions());
	}
}