<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * SearchLog
 *
 * @ORM\Table(name="search_logs")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\SearchLogRepository")
 */
class SearchLog
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="searchLogs",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id")
     */
    private $businessProfile;

    /**
     * @var Keyword
     * @ORM\ManyToOne(targetEntity="Domain\ReportBundle\Entity\Keyword",
     *     inversedBy="searchLogs",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="keyword_id", referencedColumnName="id")
     */
    private $keyword;

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
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return SearchLog
     */
    public function setBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * Set keyword
     *
     * @param \Domain\ReportBundle\Entity\Keyword $keyword
     *
     * @return SearchLog
     */
    public function setKeyword(\Domain\ReportBundle\Entity\Keyword $keyword = null)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return \Domain\ReportBundle\Entity\Keyword
     */
    public function getKeyword()
    {
        return $this->keyword;
    }
}
