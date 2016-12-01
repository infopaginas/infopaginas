<?php

namespace Domain\MenuBundle\Manager;

use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class MenuManager extends Manager
{
    public function fetchAll()
    {
        return $this->em->getRepository('DomainMenuBundle:Menu')->getMenuItems();
    }

    public function getMenuItems()
    {
        $data = $this->getMenuItemsSlugs();
        $menu = [];

        foreach ($data as $item) {
            $locality = $this->em->getRepository('DomainBusinessBundle:Locality')
                ->getLocalityBySlug($item['slug']);

            $categories = $this->em->getRepository('DomainBusinessBundle:Category')
                ->getCategoriesBySlugs($item['categories']);

            $menu[] = [
                'locality'   => $locality,
                'categories' => $categories
            ];
        }

        return $menu;
    }

    protected function getMenuItemsSlugs()
    {
        $data = [
            [
                'slug' => 'san-juan',
                'categories' => [
                    'lawyers',
                    'bookstores',
                    'furniture',
                    'pizza',
                    'restaurants',
                    'video-games',
                ],
            ],
            [
                'slug' => 'ponce',
                'categories' => [
                    'lawyers',
                    'doctors',
                    'pizza',
                    'restaurants',
                    'Salones/Actividades',  //todo
                    'automobiles-alternators',
                ],
            ],
            [
                'slug' => 'caguas',
                'categories' => [
                    'sports-gymnasiums',
                    'bookstores',
                    'pizza',
                    'restaurants',
                    'veterinarians',
                    'automobiles-alternators',
                ],
            ],
            [
                'slug' => 'mayaguez',
                'categories' => [
                    'sports-gymnasiums',
                    'lawyers',
                    'Empleos/Agencias', //todo
                    'bookstores',
                    'pizza',
                    'veterinarians',
                ],
            ],
            [
                'slug' => 'arecibo',
                'categories' => [
                    'lawyers',
                    'Médicos Especialistas - Obstetricia Y Ginecología',    //todo
                    'pizza',
                    'Salones/Actividades',  //todo
                    'automobiles-alternators',
                    'veterinarians',
                ],
            ],
        ];

        return $data;
    }
}
