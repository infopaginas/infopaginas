<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Translation\AreaTranslation;
use Domain\BusinessBundle\Entity\Locality;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAreaData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ObjectManager
     */
    protected $manager;

    private $areaAndLocalities = [
            [
                'name' => 'Aguadilla',
                'localities' => [
                    [
                        'name' => 'Aguadilla',
                    ]
                ]
            ],
            [
                'name' => 'Arecibo',
                'localities' => [
                    [
                        'name' => 'Arecibo'
                    ]
                ]
            ],
            [
                'name' => 'Bayamón',
                'localities' => [
                    [
                        'name' => 'Bayamón'
                    ]
                ]
            ],
            [
                'name' => 'Caguas',
                'localities' => [
                    [
                        'name' => 'Caguas'
                    ]
                ]
            ],
            [
                'name' => 'Carolina',
                'localities' => [
                    [
                        'name' => 'Carolina'
                    ]
                ]
            ],
            [
                'name' => 'Cayey',
                'localities' => [
                    [
                        'name' => 'Cayey'
                    ]
                ]
            ],
            [
                'name' => 'Fajardo',
                'localities' => [
                    [
                        'name' => 'Fajardo'
                    ]
                ]
            ],
            [
                'name' => 'Guayama',
                'localities' => [
                    [
                        'name' => 'Guayama'
                    ]
                ]
            ],
            [
                'name' => 'Humacao',
                'localities' => [
                    [
                        'name' => 'Humacao'
                    ]
                ]
            ],
            [
                'name' => 'Juana Díaz',
                'localities' => [
                    [
                        'name' => 'Juana Díaz'
                    ]
                ]
            ],
            [
                'name' => 'Manatí',
                'localities' => [
                    [
                        'name' => 'Manatí'
                    ]
                ]
            ],
            [
                'name' => 'Mayagüez',
                'localities' => [
                    [
                        'name' => 'Mayagüez'
                    ]
                ]
            ],
            [
                'name' => 'Ponce',
                'localities' => [
                    [
                        'name' => 'Ponce'
                    ]
                ]
            ],
            [
                'name' => 'San Germán',
                'localities' => [
                    [
                        'name' => 'San Germán'
                    ]
                ]
            ],
            [
                'name' => 'San Sebastián',
                'localities' => [
                    [
                        'name' => 'San Sebastián'
                    ]
                ]
            ],
            [
                'name' => 'San Juan',
                'localities' => [
                    [
                        'name' => 'San Juan'
                    ]
                ]
            ],
            [
                'name' => 'Toa Baja',
                'localities' => [
                    [
                        'name' => 'Toa Baja'
                    ]
                ]
            ],
            [
                'name' => 'Utuado',
                'localities' => [
                    [
                        'name' => 'Utuado'
                    ]
                ]
            ],
            [
                'name' => 'Vega Baja',
                'localities' => [
                    [
                        'name' => 'Vega Baja'
                    ]
                ]
            ],
            [
                'name' => 'Yauco',
                'localities' => [
                    [
                        'name' => 'Yauco'
                    ]
                ]
            ],
        ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        foreach ($this->areaAndLocalities as $areaItem) {
            $area = new Area();
            $area->setName($areaItem['name']);

            $translation = new AreaTranslation();
            $translation->setContent(sprintf('Spain %s', $areaItem['name']));
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($area);

            $this->manager->persist($translation);
            $this->manager->persist($area);

            $this->addReference('area.' . str_replace(' ', '', $areaItem['name']), $area);
        }

        $manager->flush();
    }



    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 8;
    }

    /**
     * @param ContainerInterface|null $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }
}
