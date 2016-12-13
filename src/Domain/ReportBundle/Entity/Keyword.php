<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Keyword
 *
 * @ORM\Table(name="keywords")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\KeywordRepository")
 */
class Keyword
{
    const KEYWORD_MAX_LENGTH = 50;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Related to KEYWORD_MAX_LENGTH const
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=50, nullable=true)
     */
    private $value;

    /**
     * @var SearchLog[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ReportBundle\Entity\SearchLog",
     *     mappedBy="keyword",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $searchLogs;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Keyword
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->searchLogs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add searchLog
     *
     * @param \Domain\ReportBundle\Entity\SearchLog $searchLog
     *
     * @return Keyword
     */
    public function addSearchLog(\Domain\ReportBundle\Entity\SearchLog $searchLog)
    {
        $this->searchLogs[] = $searchLog;

        return $this;
    }

    /**
     * Remove searchLog
     *
     * @param \Domain\ReportBundle\Entity\SearchLog $searchLog
     */
    public function removeSearchLog(\Domain\ReportBundle\Entity\SearchLog $searchLog)
    {
        $this->searchLogs->removeElement($searchLog);
    }

    /**
     * Get searchLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSearchLogs()
    {
        return $this->searchLogs;
    }
}
