<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\ReportInterface;

/**
 * CategoryReport
 *
 * @ORM\Table(name="category_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\CategoryReportRepository")
 */
class CategoryReport implements ReportInterface
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

    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_CATEGORY_REPORT      => self::FORMAT_PDF,
            self::CODE_EXCEL_CATEGORY_REPORT    => self::FORMAT_EXCEL,
        ];
    }
}
