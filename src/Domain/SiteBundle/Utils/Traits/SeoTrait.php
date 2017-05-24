<?php

namespace Domain\SiteBundle\Utils\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SeoTrait
 */
trait SeoTrait
{
    /**
     * @var string
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="seo_title", type="string", length=255, nullable=true)
     */
    protected $seoTitle;

    /**
     * @var string
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="seo_description", type="string", length=255, nullable=true)
     */
    protected $seoDescription;

    /**
     * Set seoTitle
     *
     * @param string $seoTitle
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    /**
     * Get seoTitle
     *
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * Set seoDescription
     *
     * @param string $seoDescription
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    /**
     * Get seoDescription
     *
     * @return string
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }
}
