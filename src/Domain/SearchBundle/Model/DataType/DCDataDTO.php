<?php

namespace Domain\SearchBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;

class DCDataDTO extends AbstractDTO
{
    /**
     * @var array $searchKeywords
     */
    public $searchKeywords;

    /**
     * @var string $locationName
     */
    public $locationName;

    /**
     * @var array $categories
     */
    public $categories;

    /**
     * @var string|null $slug
     */
    public $slug;

    /**
     * @param array         $searchKeywords
     * @param string        $locationName
     * @param array         $categories
     * @param string|null   $slug
     */
    public function __construct(array $searchKeywords = [], string $locationName = '', $categories = [], $slug = null)
    {
        $this->searchKeywords  = $searchKeywords;
        $this->locationName    = $locationName;
        $this->categories      = $categories;
        $this->slug            = $slug;
    }
}
