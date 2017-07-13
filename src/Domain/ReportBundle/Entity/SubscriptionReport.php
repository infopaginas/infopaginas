<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\ReportInterface;

/**
 * SubscriptionReport
 *
 * @ORM\Table(name="subscription_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\SubscriptionReportRepository")
 */
class SubscriptionReport implements ReportInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @return array
     */
    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_SUBSCRIPTION_REPORT => self::FORMAT_PDF,
            self::CODE_EXCEL_SUBSCRIPTION_REPORT => self::FORMAT_EXCEL,
        ];
    }
}
