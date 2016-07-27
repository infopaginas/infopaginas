<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/26/16
 * Time: 6:45 PM
 */

namespace Domain\ReportBundle\Entity;

use Domain\ReportBundle\Model\BusinessOverviewReportTypeInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;

/**
 * BusinessOverviewReportBusinessProfile
 *
 * @ORM\Table(name="business_overview_report")
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
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Domain\ReportBundle\Entity\BusinessOverviewReport",
     *     inversedBy="businessOverviewReportBusinessProfile"
     * )
     * @ORM\JoinColumn(name="business_overview_report_id", referencedColumnName="id")
     */
    protected $businessOverviewReport;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id")
     */
    protected $businessProfile;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    protected $type;

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

    public function getTypeValue()
    {
//        $types = self::getTypes();
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

}