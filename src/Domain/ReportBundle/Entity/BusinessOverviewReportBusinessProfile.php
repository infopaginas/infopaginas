<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/26/16
 * Time: 6:45 PM
 */

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Domain\ReportBundle\Model\BusinessOverviewReportTypeInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Domain\BusinessBundle\Entity\BusinessProfile;

/**
 * BusinessOverviewReportBusinessProfile
 *
 * @ORM\Table(name="business_overview_report_business_profile")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\BusinessOverviewReportBusinessProfileRepository")
 */
class BusinessOverviewReportBusinessProfile implements DefaultEntityInterface, BusinessOverviewReportTypeInterface
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
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="datetime", type="datetime")
     */
    protected $datetime;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Domain\ReportBundle\Entity\BusinessOverviewReport",
     *     inversedBy="businessOverviewReportBusinessProfiles"
     * )
     * @ORM\JoinColumn(name="business_overview_report_id", referencedColumnName="id", nullable=false)
     */
    protected $businessOverviewReport;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", nullable=false)
     */
    protected $businessProfile;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    protected $type;

    public function __toString()
    {
        return $this->getBusinessProfile() ? $this->getBusinessProfile()->__toString() : '';
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_CODE_IMPRESSION  => 'impression',
            self::TYPE_CODE_VIEW        => 'view',
        ];
    }

    /**
     * @return mixed
     */
    public function getTypeValue()
    {
        $types = self::getTypes();

        return $types[$this->getType()];
    }

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
     * Set type
     *
     * @param integer $type
     *
     * @return BusinessOverviewReportBusinessProfile
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set businessOverviewReport
     *
     * @param BusinessOverviewReport $businessOverviewReport
     *
     * @return BusinessOverviewReportBusinessProfile
     */
    public function setBusinessOverviewReport(BusinessOverviewReport $businessOverviewReport = null)
    {
        $this->businessOverviewReport = $businessOverviewReport;

        return $this;
    }

    /**
     * Get businessOverviewReport
     *
     * @return \Domain\ReportBundle\Entity\BusinessOverviewReport
     */
    public function getBusinessOverviewReport()
    {
        return $this->businessOverviewReport;
    }

    /**
     * Set businessProfile
     *
     * @param BusinessProfile $businessProfile
     *
     * @return BusinessOverviewReportBusinessProfile
     */
    public function setBusinessProfile(BusinessProfile $businessProfile = null)
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
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return BusinessOverviewReportBusinessProfile
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }
}
