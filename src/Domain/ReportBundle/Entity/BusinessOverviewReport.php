<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Domain\ReportBundle\Model\ReportInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;

/**
 * BusinessOverviewReport
 *
 * @ORM\Table(name="business_overview_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\BusinessOverviewReportRepository")
 */
class BusinessOverviewReport implements DefaultEntityInterface, ReportInterface
{
    use DefaultEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * @var int
     * @ORM\Column(name="views", type="integer")
     */
    protected $views = 0;

    /**
     * @var int
     * @ORM\Column(name="impressions", type="integer")
     */
    protected $impressions = 0;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="businessViews",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id")
     */
    protected $businessProfile;

    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_BUSINESS_OVERVIEW_REPORT   => self::FORMAT_PDF,
            self::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT => self::FORMAT_EXCEL,
        ];
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return BusinessOverviewReport
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return BusinessOverviewReport
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set impressions
     *
     * @param integer $impressions
     *
     * @return BusinessOverviewReport
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;

        return $this;
    }

    /**
     * Get impressions
     *
     * @return integer
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return BusinessOverviewReport
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
}
