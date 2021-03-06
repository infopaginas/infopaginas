<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Address\Country;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCountryData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $data = [
            [
                'name'      => 'Puerto Rico',
                'shortName' => strtoupper(Country::PUERTO_RICO_SHORT_NAME),
            ], [
                'name'      => 'Australia',
                'shortName' => 'AU'
            ], [
                'name'      => 'Brunei Darussalam',
                'shortName' => 'BN'
            ], [
                'name'      => 'United States',
                'shortName' => strtoupper(Country::USA_SHORT_NAME),
            ]
        ];

        foreach ($data as $value) {
            $object = new Country();
            $object->setName($value['name']);
            $object->setShortName($value['shortName']);

            $this->manager->persist($object);

            // set reference to find this
            $this->addReference('country.'.$value['shortName'], $object);
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
        return 4;
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
