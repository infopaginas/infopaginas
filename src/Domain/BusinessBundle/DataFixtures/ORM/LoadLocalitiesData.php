<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Locality;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLocalitiesData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ObjectManager
     */
    protected $manager;

    private $localityData = [
            [
                'name' => 'Aguadilla',
                'area' => 'Aguadilla',
                'latitude' => '18.427445',
                'longitude' => '-67.154070'
            ],
            [
                'name' => 'Arecibo',
                'area' => 'Arecibo',
                'latitude' => '18.459605',
                'longitude' => '-66.744172'
            ],
            [
                'name' => 'Bayamón',
                'area' => 'Bayamón',
                'latitude' => '18.369196',
                'longitude' => '-66.165967'
            ],
            [
                'name' => 'Caguas',
                'area' => 'Caguas',
                'latitude' => '18.238799',
                'longitude' => '-66.035249'
            ],
            [
                'name' => 'Carolina',
                'area' => 'Carolina',
                'latitude' => '18.380782',
                'longitude' => '-65.957387'
            ],
            [
                'name' => 'Cayey',
                'area' => 'Cayey',
                'latitude' => '18.111905',
                'longitude' => '-66.166000'
            ],
            [
                'name' => 'Fajardo',
                'area' => 'Fajardo',
                'latitude' => '18.325787',
                'longitude' => '-65.652384'
            ],
            [
                'name' => 'Guayama',
                'area' => 'Guayama',
                'latitude' => '17.984133',
                'longitude' => '-66.113777'
            ],
            [
                'name' => 'Humacao',
                'area' => 'Humacao',
                'latitude' => '18.149683',
                'longitude' => '-65.827385'
            ],
            [
                'name' => 'Juana Díaz',
                'area' => 'Juana Díaz',
                'latitude' => '18.053437',
                'longitude' => '-66.507508'
            ],
            [
                'name' => 'Manatí',
                'area' => 'Manatí',
                'latitude' => '18.429933',
                'longitude' => '-66.488464'
            ],
            [
                'name' => 'Mayagüez',
                'area' => 'Mayagüez',
                'latitude' => '18.201346',
                'longitude' => '-67.145152'
            ],
            [
                'name' => 'Ponce',
                'area' => 'Ponce',
                'latitude' => '18.011077',
                'longitude' => '-66.614062'
            ],
            [
                'name' => 'San Germán',
                'area' => 'San Germán',
                'latitude' => '18.080708',
                'longitude' => '-67.041110'
            ],
            [
                'name' => 'San Sebastián',
                'area' => 'San Sebastián',
                'latitude' => '18.333521',
                'longitude' => '-66.990992'
            ],
            [
                'name' => 'San Juan',
                'area' => 'San Juan',
                'latitude' => '18.465539',
                'longitude' => '-66.105735'
            ],
            [
                'name' => 'Toa Baja',
                'area' => 'Toa Baja',
                'latitude' => '18.444471',
                'longitude' => '-66.254329'
            ],
            [
                'name' => 'Utuado',
                'area' => 'Utuado',
                'latitude' => '18.265510',
                'longitude' => '-66.700452'
            ],
            [
                'name' => 'Vega Baja',
                'area' => 'Vega Baja',
                'latitude' => '18.444391',
                'longitude' => '-66.387670'
            ],
            [
                'name' => 'Yauco',
                'area' => 'Yauco',
                'latitude' => '18.034964',
                'longitude' => '-66.849898'
            ]
        ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        foreach ($this->localityData as $item) {
            $locObject = new Locality();
            $locObject->setName($item['name']);
            $locObject->setLatitude($item['latitude']);
            $locObject->setLongitude($item['longitude']);
            
            if (!empty($item['area'])) {
                $area = $this->getReference('area.' . str_replace(' ', '', $item['area']));
                $locObject->setArea($area);
            }

            $this->addReference('locality.' . str_replace(' ', '', $item['name']), $locObject);

            $this->manager->persist($locObject);
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
        return 9;
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
