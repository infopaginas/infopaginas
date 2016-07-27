<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/26/16
 * Time: 6:45 PM
 */

namespace Domain\ReportBundle\Entity;

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
     * @ORM\GeneratedValue(strategy="AUTO")
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
    protected $businessOverviewReportBusinessProfile;

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
            self::CODE_PDF_CATEGORY_REPORT      => self::FORMAT_PDF,
            self::CODE_EXCEL_CATEGORY_REPORT    => self::FORMAT_EXCEL,
        ];
    }
}