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
     * @var Page
     * @ORM\ManyToOne(targetEntity="Domain\PageBundle\Entity\Page",
     *     cascade={"persist"},
     *     inversedBy="links",
     * )
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;

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
}
