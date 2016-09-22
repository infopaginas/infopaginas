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
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\DfpBundle\Model\DataType\DateRangeVO;

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

    const KEYWORDS_PER_PAGE_COUNT = [5 => 5, 10 => 10, 15 => 15, 20=> 20, 25 => 25];

    const DEFAULT_KEYWORDS_COUNT = 15;

    /**
     * KeywordsReportManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getKeywordsData(array $params = [])
    {
        $businessProfile = $this->getBusinessProfilesRepository()->find($params['businessProfileId']);

        $stats = $this->getKeywordsDataFromRepo($businessProfile, $params);

        $keywordsData = [
            'results' => $stats,
            'keywords' => array_keys($stats),
            'searches' => array_values($stats),
        ];

        return $keywordsData;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param array $params
     * @return mixed
     */
    protected function getKeywordsDataFromRepo(BusinessProfile $businessProfile, array $params)
    {
        $start = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $params['date']['start']);
        $end = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $params['date']['end']);

        $limit = $params['limit'];

        return $this->getKeywordsRepository()->getTopKeywordsForBusinessProfile($businessProfile, $start, $end, $limit);
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
