<?php

namespace Domain\SiteBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\SiteBundle\Repository\SearchRepository;

/**
 * Class SearchManager
 * Search management entry point
 *
 * @package Domain\SiteBundle\Manager
 */
class SearchManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * SearchManager constructor.
     *
     * @access public
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Provides a list of quick search phares and properties for hexagonal menu on FE
     *
     * @return array();
     */
    public function getQuickSearchMenuLinksList()
    {
        return array(
            1 => array(
                'sectionClass' => 'even',
                'items' => array(
                    array(
                        'liClass' => '',
                        'aClass'  => 'green-bk sol-icon',
                        'data'    => 'Solicitors'
                    ),
                    array(
                        'liClass' => 'photo-hex',
                        'aClass'  => 'photo-bk-build',
                        'data'    => 'Builders'
                    )
                )
            ),
            2 => array(
                'sectionClass' => 'odd',
                'items' => array(
                    array(
                        'liClass' => 'photo-hex',
                        'aClass'  => 'photo-bk-pharma',
                        'data'    => 'Pharmacies'
                    ),
                    array(
                        'liClass' => '',
                        'aClass'  => 'grey-bk el-icon',
                        'data'    => 'Electricians'
                    ),
                    array(
                        'liClass' => '',
                        'aClass'  => 'blue-bk plum-icon',
                        'data'    => 'Plumbers'
                    )
                )
            ),
            3 => array(
                'sectionClass' => 'odd',
                'items' => array(
                    array(
                        'liClass' => '',
                        'aClass'  => 'blue-bk mech-icon',
                        'data'    => 'Mechanics'
                    ),
                    array(
                        'liClass' => 'photo-hex',
                        'aClass'  => 'photo-bk-dent',
                        'data'    => 'Dentists'
                    ),
                    array(
                        'liClass' => '',
                        'aClass'  => 'green-bk rest-icon',
                        'data'    => 'Restaurants'
                    )
                )
            ),
            4 => array(
                'sectionClass' => 'odd',
                'items' => array(
                    array(
                        'liClass' => 'photo-hex',
                        'aClass'  => 'photo-bk-flor',
                        'data'    => 'Florists'
                    ),
                    array(
                        'liClass' => '',
                        'aClass'  => 'green-bk salon-icon',
                        'data'    => 'Beauty Salons'
                    ),
                    array(
                        'liClass' => 'photo-hex',
                        'aClass'  => 'photo-bk-hair',
                        'data'    => 'Hairdressers'
                    ),
                    array(
                        'liClass' => '',
                        'aClass'  => 'blue-bk doc-icon',
                        'data'    => 'Doctors'
                    )
                )
            )
        );
    }
}
