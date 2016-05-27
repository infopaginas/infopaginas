<?php

namespace Domain\BusinessBundle\Entity\Translation;

/**
 * BusinessReviewTranslation
 */
class BusinessReviewTranslation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \Domain\BusinessBundle\Entity\Review\BusinessReview
     */
    private $object;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return BusinessReviewTranslation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set field
     *
     * @param string $field
     *
     * @return BusinessReviewTranslation
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return BusinessReviewTranslation
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set object
     *
     * @param \Domain\BusinessBundle\Entity\Review\BusinessReview $object
     *
     * @return BusinessReviewTranslation
     */
    public function setObject(\Domain\BusinessBundle\Entity\Review\BusinessReview $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return \Domain\BusinessBundle\Entity\Review\BusinessReview
     */
    public function getObject()
    {
        return $this->object;
    }
}

