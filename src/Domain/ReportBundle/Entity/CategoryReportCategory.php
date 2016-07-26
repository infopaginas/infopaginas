<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CategoryReportCategory
 *
 * @ORM\Table(name="category_report_category")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\CategoryReportCategoryRepository")
 */
class CategoryReportCategory implements DefaultEntityInterface
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
     * @ORM\ManyToOne(
     *     targetEntity="Domain\ReportBundle\Entity\CategoryReport",
     *     inversedBy="categoryReportCategories"
     * )
     * @ORM\JoinColumn(name="category_report_id", referencedColumnName="id")
     */
    protected $categoryReport;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->getId() ? $this->getDate()->format(AdminHelper::DATE_FORMAT) : 'new report category';
    }

    /**
     * Set categoryReport
     *
     * @param \Domain\ReportBundle\Entity\CategoryReport $categoryReport
     *
     * @return CategoryReportCategory
     */
    public function setCategoryReport(\Domain\ReportBundle\Entity\CategoryReport $categoryReport = null)
    {
        $this->categoryReport = $categoryReport;

        return $this;
    }

    /**
     * Get categoryReport
     *
     * @return \Domain\ReportBundle\Entity\CategoryReport
     */
    public function getCategoryReport()
    {
        return $this->categoryReport;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return CategoryReportCategory
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
}
