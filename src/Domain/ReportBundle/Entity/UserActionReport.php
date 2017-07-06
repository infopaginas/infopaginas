<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\PostponeExportInterface;
use Domain\ReportBundle\Model\ReportInterface;

/**
 * Visitor
 *
 * @ORM\Table(name="user_action_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\UserActionReportRepository")
 */
class UserActionReport implements ReportInterface, PostponeExportInterface
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

    public static function getExportFormats()
    {
        return [
            self::CODE_EXCEL_CATEGORY_REPORT    => self::FORMAT_EXCEL,
        ];
    }
}
