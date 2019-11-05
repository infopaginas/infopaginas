<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Domain\BusinessBundle\Repository\BusinessProfilePhoneRepository;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

/**
 * Class BusinessProfilePhoneManager
 * @package Domain\BusinessBundle\Manager
 *
 * @method BusinessProfilePhoneRepository getRepository()
 */
class BusinessProfilePhoneManager extends Manager
{
    /**
     * @param $newBusinessPhone
     * @param array $oldBusinessPhones
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isNewPhoneValid($newBusinessPhone, array $oldBusinessPhones): bool
    {
        $oldPhonesIdsArray = BusinessProfileUtil::extractEntitiesId($oldBusinessPhones);

        return !(bool) $this->getRepository()->getSamePhonesCount($newBusinessPhone->getPhone(), $oldPhonesIdsArray);
    }
}
