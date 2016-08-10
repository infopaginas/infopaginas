<?php

namespace Domain\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\MenuBundle\Model\MenuModel;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Menu
 *
 * @ORM\Table(name="menu")
 * @UniqueEntity("code")
 * @ORM\Entity(repositoryClass="Domain\MenuBundle\Repository\MenuRepository")
 */
class Menu extends MenuModel implements DefaultEntityInterface
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
     * @var integer
     *
     * @ORM\Column(name="code", type="integer")
     */
    protected $code;

    /**
     * @ORM\OneToOne(targetEntity="Domain\BusinessBundle\Entity\Category", inversedBy="menu")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    protected $category;

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
     * @return string
     */
    public function __toString()
    {
        return ($this->getCategory()) ? $this->getCategory()->getName() : 'New menu';
    }

    /**
     * Set category
     *
     * @param \Domain\BusinessBundle\Entity\Category $category
     *
     * @return Menu
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
