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
            'Panadería La Catalana'                     => '78 Calle El Tren, Cataño, 00962, Puerto Rico',
            'RST Puerto Rico'                           => '215 Cll Julian Pesante, San Juan, 00912, Puerto Rico',
            'Chinchorreando En Hato Rey'                => 'Calle A Urbanización Santa Catalina, Guaynabo, 00966, Puerto Rico',
            'CASA DE LAS ARMADURAS INC.'                => 'Ave. Andalucía no. 407,, Puerto Nuevo, 00920, Puerto Rico',
            'Piola Pizzeria Artesanal & Rum Bar'        => 'Ave. Andalucía no. 407,, Puerto Nuevo, 00920, Puerto Rico',
            'Pizzería Artesanal Don Bigote'             => 'Interstate PR2, San Juan, 00920, Puerto Rico',
            'Pizzas Artesanales en Jamburguer Jil'      => 'Pajaro Carpintero, San Juan, 00920, Puerto Rico',
            'Pizza Ranch II'                            => 'Ave. Lomas Verdes Bo. Minillas Bayamón, Puerto Rico 00961',
            'El Jardín Burger'                          => '482 PR-845, San Juan, 00926, Puerto Rico',
            'Burger House'                              => 'Sec. La Marina Carr. #19 Km. 0.8 Guaynabo, Puerto Rico 00966',
            'Laboratorio Clínico Michelsan'             => 'Calle H, Guaynabo, 00966, Puerto Rico',
            'Environmental Quality Laboratories, Inc.'  => 'Av. Diego Puerto Nuevo, San Juan, 00927, Puerto Rico',
            'Héctor Electric & Plumbing Services'       => 'Calle Gallera, San Juan, 00923, Puerto Rico',
            'Laboratorio Clinico Rodríguez - Arecibo'   => 'Cll Eloy Hernandez, Carolina, 00983, Puerto Rico',
            'Bradford Master Plumbing Services'         => 'Sec. La Marina Carr. #19 Km. 0.8 Guaynabo, Puerto Rico 00966',
            'Aguacentro Plumbing'                       => 'Calle Monseñor Torres, San Juan, 00925, Puerto Rico',
            'Junito Fuji'                               => 'Calle Chardon Esquina Oliver San Juan, Puerto Rico',
            'Destapes Caribe'                           => 'Cll Zurna, San Juan, 00924, Puerto Rico',
            'Huertas Plumbing - 7 Días 24 Hrs'          => 'F5 Cll Ebano, Guaynabo, 00968, Puerto Rico',
        ];

        $addressManager = $this->container->get('domain_business.manager.address_manager');

        foreach ($data as $name => $address) {
            $googleResponse = $addressManager->validateAddress($address);

            if ($googleResponse['error']) {
                throw new \Exception(sprintf('Invalid business address: %s . Fixture', $address));
            }

            $object = new BusinessProfile();
            $object->setName($name);
            $object->setEmail('test@test.com');
            $object->setWebsite('www.google.com');
            $object->setPhone('+375-29-1862356');
            $object->setSlogan('Just do it');
            $object->setProduct('Good product');
            $object->setDescription('Some description');

            $object->setFullAddress($address);
            $addressManager->setGoogleAddress($googleResponse['result'], $object);

            $object->addArea($this->getReference('area.0'));
            $object->addArea($this->getReference('area.' . rand(1, 2)));

            $object->addBrand($this->getReference('brand.0'));
            $object->addBrand($this->getReference('brand.' . rand(1, 2)));

            $object->setLatitude($this->getRandomLat());
            $object->setLongitude($this->getRandomLng());

            $object->addCategory($this->getReference('category.' . rand(1, 4)));
            $object->addCategory($this->getReference('category.' . rand(5, 8)));
            $object->addCategory($this->getReference('category.' . rand(9, 12)));

            $object->addPaymentMethod($this->getReference('payment_method.0'));
            $object->addPaymentMethod($this->getReference('payment_method.' . rand(1, 2)));

            $object->setCountry($this->getReference('country.PR'));
            $object->setUser($this->getReference('user.admin'));

            $this->addTranslation(new BusinessProfileTranslation(), 'name', sprintf('Spain %s', $name), $object);
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

    protected function getRandomLat()
    {
        return rand(17984133, 18465539) / 1000000;
    }

    protected function getRandomLng()
    {
        return -(rand(65652384, 67154070) / 1000000);
    }
}
