<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Brand;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Tag;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTestBusinessData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadTags();
        $this->loadArea();
        $this->loadCategory();
        $this->loadBrands();
        $this->loadPaymentMethods();
        $this->loadBusiness();

        $manager->flush();
    }

    protected function loadBusiness()
    {
        $data = [
            'Test business profile 1',
            'Test business profile 2',
            'Test business profile 3',
            'Test business profile 4',
        ];

        foreach ($data as $key => $value) {
            $object = new BusinessProfile();
            $object->setName($value);
            $object->setEmail('test@test.com');
            $object->setWebsite('www.google.com');
            $object->setPhone('+375-29-1862356');

            $object->addArea($this->getReference('area.0'));
            $object->addArea($this->getReference('area.' . rand(1,2)));

            $object->addBrand($this->getReference('brand.0'));
            $object->addBrand($this->getReference('brand.' . rand(1,2)));

            $object->addCategory($this->getReference('category.0'));
            $object->addCategory($this->getReference('category.' . rand(1,2)));

            $object->addPaymentMethod($this->getReference('payment_method.0'));
            $object->addPaymentMethod($this->getReference('payment_method.' . rand(1,2)));

            $object->setSubscription($this->getReference('subscription.' . rand(1,5)));
            $object->setUser($this->getReference('user.admin'));

            $this->manager->persist($object);
        }
    }

    protected function loadTags()
    {
        $data = [
            'Jewelry',
            'Cars',
            'Food'
        ];

        foreach ($data as $key => $value) {
            $object = new Tag();
            $object->setName($value);
            $this->manager->persist($object);

            // set reference to find this
            $this->addReference('tag.'.$key, $object);
        }
    }

    protected function loadBrands()
    {
        $data = [
            'Audi',
            'Aston Martin',
            'BMW',
        ];

        foreach ($data as $key => $value) {
            $object = new Brand();
            $object->setName($value);
            $this->manager->persist($object);

            // set reference to find this
            $this->addReference('brand.'.$key, $object);
        }
    }

    protected function loadPaymentMethods()
    {
        $data = [
            'Visa',
            'Master Cards',
            'PayPal',
        ];

        foreach ($data as $key => $value) {
            $object = new PaymentMethod();
            $object->setName($value);
            $this->manager->persist($object);

            // set reference to find this
            $this->addReference('payment_method.'.$key, $object);
        }
    }

    protected function loadArea()
    {
        $data = [
            'West',
            'North',
            'East',
        ];

        foreach ($data as $key => $value) {
            $object = new Area();
            $object->setName($value);
            $this->manager->persist($object);

            // set reference to find this
            $this->addReference('area.'.$key, $object);
        }
    }

    protected function loadCategory()
    {
        $data = [
            'Education',
            'Service',
            'Shop',
        ];

        foreach ($data as $key => $value) {
            $object = new Category();
            $object->setName($value);
            $this->manager->persist($object);

            // set reference to find this
            $this->addReference('category.'.$key, $object);
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
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
