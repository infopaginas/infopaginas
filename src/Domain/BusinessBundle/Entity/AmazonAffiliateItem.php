<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="amazon_affiliate_item")
 * @ORM\Entity()
 */
class AmazonAffiliateItem implements DefaultEntityInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use ChangeStateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="amazonAffiliateItems")
     */
    public $category;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @Assert\NotBlank()
     */
    public $embeddedHTML;

    public function getId()
    {
        return $this->id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getEmbeddedHTML()
    {
        return $this->embeddedHTML;
    }

    public function setEmbeddedHTML($embeddedHTML)
    {
        $this->embeddedHTML = $embeddedHTML;
    }
}
