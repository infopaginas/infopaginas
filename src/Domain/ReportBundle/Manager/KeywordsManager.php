<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 06.09.16
 * Time: 12:01
 */

namespace Domain\ReportBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Domain\ReportBundle\Entity\Keyword;
use Domain\ReportBundle\Service\StemmerService;

/**
 * Class KeywordsManager
 * @package Domain\ReportBundle\Manager
 */
class KeywordsManager
{
    protected $entityManager;

    protected $stemmer;

    public function __construct(EntityManagerInterface $entityManager, StemmerService $stemmer)
    {
        $this->entityManager = $entityManager;

        $this->stemmer = $stemmer;
    }

    public function convertSearchStringToKeywordsCollection(string $search)
    {
        $searchValuesArray = $this->getStemmer()->getWordsArrayFromString($search);
        $keywordsCollection = $this->getRepository()->getKeywordsCollectionFromValuesArray($searchValuesArray);

        return $keywordsCollection;
    }

    public function findOrCreate(string $keywordValue) : Keyword
    {
        $keyword = $this->getRepository()->findKeywordByValue($keywordValue);

        if ($keyword === null) {
            $keyword = $this->create($keywordValue);
        }

        return $keyword;
    }

    public function create($value)
    {
        $keyword = new Keyword();
        $keyword->setValue($value);

        $this->commit($keyword);

        return $keyword;
    }

    protected function commit(Keyword $keyword)
    {
        $this->getEntityManager()->persist($keyword);
        $this->getEntityManager()->flush();
    }

    protected function getEntityManager() : EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function getRepository() : EntityRepository
    {
        return $this->getEntityManager()->getRepository(Keyword::class);
    }

    protected function getStemmer() : StemmerService
    {
        return $this->stemmer;
    }
}