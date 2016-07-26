<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\ReportInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Domain\ReportBundle\Entity\CategoryReportCategory;

/**
 * CategoryReport
 *
 * @ORM\Table(name="category_report")
 * @UniqueEntity("category")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\CategoryReportRepository")
 */
class CategoryReport implements DefaultEntityInterface, ReportInterface
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
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var CategoryReportCategory[] $categoryReportCategories
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ReportBundle\Entity\CategoryReportCategory",
     *     mappedBy="categoryReport",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $categoryReportCategories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categoryReportCategories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_CATEGORY_REPORT      => self::FORMAT_PDF,
            self::CODE_EXCEL_CATEGORY_REPORT    => self::FORMAT_EXCEL,
        ];
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
     * Get total quantity per this day
     * @return int
     */
    public function getTotal()
    {
        return $this->getCategoryReportCategories()->count();
    }

    /**
     * Add categoryReportCategory
     *
     * @param CategoryReportCategory $categoryReportCategory
     *
     * @return CategoryReport
     */
    public function addCategoryReportCategory(CategoryReportCategory $categoryReportCategory)
    {
        $this->categoryReportCategories[] = $categoryReportCategory;

        return $this;
    }

    /**
     * Remove categoryReportCategory
     *
     * @param CategoryReportCategory $categoryReportCategory
     */
    public function removeCategoryReportCategory(CategoryReportCategory $categoryReportCategory)
    {
        $this->categoryReportCategories->removeElement($categoryReportCategory);
    }

    /**
     * Get categoryReportCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryReportCategories()
    {
        return $this->categoryReportCategories;
    }

    /**
     * Set category
     *
     * @param \Domain\BusinessBundle\Entity\Category $category
     *
     * @return CategoryReport
     */
    public function setCategory(\Domain\BusinessBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Domain\BusinessBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
