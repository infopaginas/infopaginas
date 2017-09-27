<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\PostponeExportInterface;
use Domain\ReportBundle\Model\ReportInterface;

/**
 * FeedbackReport
 *
 * @ORM\Table(name="feedback_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\FeedbackReportRepository")
 */
class FeedbackReport implements ReportInterface, PostponeExportInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
            self::FORMAT_CSV => self::FORMAT_CSV,
        ];
    }
}
