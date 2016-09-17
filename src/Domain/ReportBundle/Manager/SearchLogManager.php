<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 06.09.16
 * Time: 14:58
 */

namespace Domain\ReportBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Entity\Keyword;
use Domain\ReportBundle\Entity\SearchLog;

/**
 * Class SearchLogManager
 * @package Domain\ReportBundle\Manager
 */
class SearchLogManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var KeywordsManager
     */
    protected $keywordsManager;

    /**
     * SearchLogManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param KeywordsManager $keywordsManager
     */
    public function __construct(EntityManagerInterface $entityManager, KeywordsManager $keywordsManager)
    {
        $this->entityManager   = $entityManager;
        $this->keywordsManager = $keywordsManager;
    }

    /**
     * @param string $search
     * @param array $businessProfiles
     */
    public function saveProfilesDataSuggestedBySearchQuery(string $search, array $businessProfiles)
    {
        $keywords = $this->getKeywordsManager()->convertSearchStringToKeywordsCollection($search);

        foreach ($keywords as $keyword) {
            foreach ($businessProfiles as $businessProfile) {
                $searchLogEntry = $this->createSearchLogEntry($keyword, $businessProfile);
                $this->getEntityManager()->persist($searchLogEntry);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param Keyword $keyword
     * @param BusinessProfile $businessProfile
     * @return SearchLog
     */
    protected function createSearchLogEntry(Keyword $keyword, BusinessProfile $businessProfile) : SearchLog
    {
        $entry = new SearchLog();
        $entry->setBusinessProfile($businessProfile);
        $entry->setKeyword($keyword);

        return $entry;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager() : EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return KeywordsManager
     */
    protected function getKeywordsManager() : KeywordsManager
    {
        return $this->keywordsManager;
    }
}
