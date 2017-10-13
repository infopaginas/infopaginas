<?php

namespace Domain\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PageLink
 *
 * @ORM\Table(name="page_link")
 * @ORM\Entity(repositoryClass="Domain\PageBundle\Repository\PageLinkRepository")
 */
class PageLink
{
    const PAGE_LINK_TYPE_LINK  = 'link';
    const PAGE_LINK_TYPE_OFFER = 'offer';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - link
     *
     * @ORM\Column(name="link", type="string", length=1000)
     * @Assert\Url()
     * @Assert\Length(max=1000)
     * @Assert\NotBlank()
     */
    protected $link;

    /**
     * @var string - name
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\Length(max=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, options={"default": PageLink::PAGE_LINK_TYPE_LINK})
     * @Assert\Choice(callback = "getTypesAssert", multiple = false)
     * @Assert\NotBlank()
     */
    protected $type;

    /**
     * @var Page
     * @ORM\ManyToOne(targetEntity="Domain\PageBundle\Entity\Page",
     *     cascade={"persist"},
     *     inversedBy="links",
     * )
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;

    public function __construct()
    {
        $this->type = self::PAGE_LINK_TYPE_LINK;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? : '';
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
     * Set page
     *
     * @param Page|null $page
     *
     * @return PageLink
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return Page|null
     */
    public function getBusiness()
    {
        return $this->page;
    }

    /**
     * @param string $link
     *
     * @return PageLink
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $name
     *
     * @return PageLink
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return PageLink
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public static function getTypesAssert()
    {
        return array_keys(self::getTypes());
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::PAGE_LINK_TYPE_LINK   => 'page_link.type.link',
            self::PAGE_LINK_TYPE_OFFER  => 'page_link.type.offer',
        ];
    }
}
