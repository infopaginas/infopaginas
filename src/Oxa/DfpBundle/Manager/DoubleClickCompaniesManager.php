<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 11.09.16
 * Time: 15:44
 */

namespace Oxa\DfpBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\DfpBundle\Entity\DoubleClickCompany;
use Oxa\DfpBundle\Service\Google\CompanyService;

/**
 * Class DoubleClickCompaniesManager
 * @package Oxa\DfpBundle\Manager]
 */
class DoubleClickCompaniesManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CompanyService
     */
    protected $companyService;

    /**
     * DoubleClickCompaniesManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param CompanyService $companyService
     */
    public function __construct(EntityManagerInterface $entityManager, CompanyService $companyService)
    {
        $this->entityManager = $entityManager;

        $this->companyService = $companyService;
    }

    public function getCompaniesIndexedByDcCompanyId()
    {
        return $this->getDoubleClickCompaniesRepo()->getCompaniesIndexedByDcCompanyId();
    }

    public function synchronizeBusinessProfilesDoubleClickCompanies()
    {
        $businessProfiles = $this->getBusinessProfilesRepo()->getBusinessProfilesWithAllowedAdUnits();
        $businessProfiles = $this->mapBusinessProfilesViaExternalIds($businessProfiles);

        $externalIds = array_keys($businessProfiles);

        $doubleClickCompanyIds = $this->getCompanyService()->getAdvertiserIdsByBusinessProfileExternalIds($externalIds);

        $alreadySynchronizedCompanyIds = array_keys($this->getCompaniesIndexedByDcCompanyId());

        foreach ($doubleClickCompanyIds as $externalId => $companyId) {
            //skip already synchronized companies
            if (in_array($companyId, $alreadySynchronizedCompanyIds)) {
                continue;
            }

            $doubleClickCompany = new DoubleClickCompany();
            $doubleClickCompany->setBusinessProfile($businessProfiles[$externalId]);
            $doubleClickCompany->setDoubleClickCompanyId($companyId);

            $this->getEntityManager()->persist($doubleClickCompany);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param array $businessProfiles
     * @return array
     */
    protected function mapBusinessProfilesViaExternalIds(array $businessProfiles)
    {
        $externalIds = [];

        /** @var BusinessProfile $businessProfile */
        foreach ($businessProfiles as $businessProfile) {
            $externalIds[$businessProfile->getDoubleClickExternalId()] = $businessProfile;
        }

        return $externalIds;
    }

    /**
     * @return CompanyService
     */
    protected function getCompanyService()
    {
        return $this->companyService;
    }

    /**
     * @return \Oxa\DfpBundle\Repository\DoubleClickCompanyRepository
     */
    protected function getDoubleClickCompaniesRepo()
    {
        return $this->getEntityManager()->getRepository(DoubleClickCompany::class);
    }

    /**
     * @return \Domain\BusinessBundle\Repository\BusinessProfileRepository
     */
    protected function getBusinessProfilesRepo()
    {
        return $this->getEntityManager()->getRepository(BusinessProfile::class);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }
}
