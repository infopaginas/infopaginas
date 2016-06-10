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
use Domain\BusinessBundle\Entity\Translation\AreaTranslation;
use Domain\BusinessBundle\Entity\Translation\BrandTranslation;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Domain\BusinessBundle\Entity\Translation\TagTranslation;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
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

        foreach ($data as $value) {
            $object = new BusinessProfile();
            $object->setName($value);
            $object->setEmail('test@test.com');
            $object->setWebsite('www.google.com');
            $object->setPhone('+375-29-1862356');
            $object->setSlogan('Just do it');
            $object->setProduct('Good product');
            $object->setDescription('Some description');

            $object->setStreetAddress('address');
            $object->setCity('address');
            $object->setCustomAddress('address');
            $object->setLongitude(5);
            $object->setLatitude(5);

            $object->addArea($this->getReference('area.0'));
            $object->addArea($this->getReference('area.' . rand(1, 2)));

            $object->addBrand($this->getReference('brand.0'));
            $object->addBrand($this->getReference('brand.' . rand(1, 2)));

            $object->addCategory($this->getReference('category.' . rand(1, 4)));
            $object->addCategory($this->getReference('category.' . rand(5, 8)));
            $object->addCategory($this->getReference('category.' . rand(9, 12)));

            $object->addPaymentMethod($this->getReference('payment_method.0'));
            $object->addPaymentMethod($this->getReference('payment_method.' . rand(1, 2)));

            $object->setCountry($this->getReference('country.PR'));
            $object->setSubscription($this->getReference('subscription.' . rand(1, 5)));
            $object->setUser($this->getReference('user.admin'));

            $this->addTranslation(new BusinessProfileTranslation(), 'name', sprintf('Spain %s', $value), $object);
            $this->addTranslation(new BusinessProfileTranslation(), 'slogan', 'Spain Slogan', $object);
            $this->addTranslation(new BusinessProfileTranslation(), 'product', 'Spain Product', $object);
            $this->addTranslation(new BusinessProfileTranslation(), 'description', 'Spain Description', $object);

            $this->manager->persist($object);
        }
    }

    /**
     * @param AbstractPersonalTranslation $translation
     * @param string $fieldName
     * @param string $value
     * @param TranslatableInterface $object
     * @param string $locale
     */
    protected function addTranslation($translation, $fieldName, $value, $object, $locale = 'es')
    {
        $translation->setField($fieldName);
        $translation->setContent($value);
        $translation->setLocale($locale);
        $translation->setObject($object);
        $this->manager->persist($translation);
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

            $translation = new TagTranslation();
            $translation->setContent(sprintf('Spain %s', $value));
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($object);

            $this->manager->persist($translation);
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

            $translation = new BrandTranslation();
            $translation->setContent(sprintf('Spain %s', $value));
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($object);

            $this->manager->persist($translation);
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

            $translation = new PaymentMethodTranslation();
            $translation->setContent(sprintf('Spain %s', $value));
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($object);

            $this->manager->persist($translation);
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

            $translation = new AreaTranslation();
            $translation->setContent(sprintf('Spain %s', $value));
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($object);

            $this->manager->persist($translation);
            $this->manager->persist($object);

            // set reference to find this
            $this->addReference('area.'.$key, $object);
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
