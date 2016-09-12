<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 06.09.16
 * Time: 11:54
 */

namespace Domain\ReportBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Admin\KeywordsReportAdmin;
use Domain\ReportBundle\Entity\Keyword;
use Domain\ReportBundle\Entity\SearchLog;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;

/**
 * Class KeywordsReportManager
 * @package Domain\ReportBundle\Manager
 */
class KeywordsReportManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * KeywordsReportManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $filterParams
     * @return array
     */
    public function getKeywordsDataByFilterParams(array $filterParams)
    {
        $businessProfile = $this->getBusinessProfilesRepository()->find($filterParams['businessProfile']['value']);
        $limit = KeywordsReportAdmin::KEYWORDS_PER_PAGE_COUNT[$filterParams['keywordsCount']['value']];

        return $this->getKeywordsData($businessProfile, $limit);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param int $limit
     * @return array
     */
    protected function getKeywordsData(BusinessProfile $businessProfile, int $limit)
    {
        return $this->getKeywordsRepository()->getTopKeywordsForBusinessProfile($businessProfile, $limit);
    }

    /**
     * @return \Domain\BusinessBundle\Repository\BusinessProfileRepository
     */
    protected function getBusinessProfilesRepository()
    {
        return $this->getEntityManager()->getRepository(BusinessProfile::class);
    }

    /**
     * @return \Domain\ReportBundle\Repository\KeywordRepository
     */
    protected function getKeywordsRepository()
    {
        return $this->getEntityManager()->getRepository(Keyword::class);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }
}
