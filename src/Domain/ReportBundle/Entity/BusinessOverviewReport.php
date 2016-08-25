<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/26/16
 * Time: 6:45 PM
 */

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\BusinessOverviewReportTypeInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Domain\ReportBundle\Model\ReportInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Domain\ReportBundle\Entity\BusinessOverviewReportBusinessProfile;

/**
 * BusinessOverviewReport
 *
 * @ORM\Table(name="business_overview_report")
 * @UniqueEntity("date")
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
     * @var BusinessOverviewReportBusinessProfile[] $businessOverviewReportBusinessProfile
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ReportBundle\Entity\BusinessOverviewReportBusinessProfile",
     *     mappedBy="businessOverviewReport",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $businessOverviewReportBusinessProfiles;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_BUSINESS_OVERVIEW_REPORT      => self::FORMAT_PDF,
            self::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT    => self::FORMAT_EXCEL,
        ];
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businessOverviewReportBusinessProfiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getViews(int $businessId = null)
    {
        $count = 0;
        foreach ($this->businessOverviewReportBusinessProfiles as $object) {
            if ($object->getType() == BusinessOverviewReportTypeInterface::TYPE_CODE_VIEW) {
                if ($businessId && $object->getBusinessProfile()->getId() != $businessId) {
                    continue;
                }

                $count++;
            }
        }

        return $count;
    }

    public function getImpressions(int $businessId = null)
    {
        $count = 0;
        foreach ($this->businessOverviewReportBusinessProfiles as $object) {
            if ($object->getType() == BusinessOverviewReportTypeInterface::TYPE_CODE_IMPRESSION) {
                if ($businessId && $object->getBusinessProfile()->getId() != $businessId) {
                    continue;
                }

                $count++;
            }
        }

        return $count;
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
     * Add businessOverviewReportBusinessProfile
     *
     * @param BusinessOverviewReportBusinessProfile $businessOverviewReportBusinessProfile
     *
     * @return BusinessOverviewReport
     */
    public function addBusinessOverviewReportBusinessProfile(
        BusinessOverviewReportBusinessProfile $businessOverviewReportBusinessProfile
    ) {
        $this->businessOverviewReportBusinessProfiles[] = $businessOverviewReportBusinessProfile;
        return $this;
    }

    /**
     * Remove businessOverviewReportBusinessProfile
     *
     * @param BusinessOverviewReportBusinessProfile $businessOverviewReportBusinessProfile
     */
    public function removeBusinessOverviewReportBusinessProfile(
        BusinessOverviewReportBusinessProfile $businessOverviewReportBusinessProfile
    ) {
        $this->businessOverviewReportBusinessProfiles->removeElement($businessOverviewReportBusinessProfile);
    }

    /**
     * Get businessOverviewReportBusinessProfile
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessOverviewReportBusinessProfile()
    {
        return $this->businessOverviewReportBusinessProfiles;
    }
}
