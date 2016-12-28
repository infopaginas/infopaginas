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
                    'abogados',
                    'librerias',
                    'mueblerias',
                    'pizzerias',
                    'restaurantes',
                    'video-juegos',
                ],
            ],
            [
                'slug' => 'ponce',
                'categories' => [
                    'abogados',
                    'medicos',
                    'pizzerias',
                    'restaurantes',
                    'salones-de-actividades',
                    'automobiles-alternators',  //todo no such category at mapping
                ],
            ],
            [
                'slug' => 'caguas',
                'categories' => [
                    'gimnasios',
                    'librerias',
                    'pizzerias',
                    'restaurantes',
                    'veterinarios',
                    'automobiles-alternators',  //todo no such category at mapping
                ],
            ],
            [
                'slug' => 'mayaguez',
                'categories' => [
                    'gimnasios',
                    'abogados',
                    'agencias-de-empleos',
                    'librerias',
                    'pizzerias',
                    'veterinarios',
                ],
            ],
            [
                'slug' => 'arecibo',
                'categories' => [
                    'abogados',
                    'medicos-especialistas-en-obstetricia-y-ginecologia',
                    'pizzerias',
                    'agencias-de-empleoss',
                    'automobiles-alternators',  //todo no such category at mapping
                    'veterinarios',
                ],
            ],
        ];

        return $data;
    }
}
