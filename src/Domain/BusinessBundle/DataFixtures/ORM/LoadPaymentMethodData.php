<?php

namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPaymentMethodData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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

        $data = PaymentMethod::getPaymentMethodData();

        foreach ($data as $item) {
            $object = new PaymentMethod();
            $object->setName($item['nameEn']);
            $object->setType($item['type']);

            $translation = new PaymentMethodTranslation();
            $translation->setContent($item['nameEs']);
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($object);

            $this->manager->persist($object);
            $this->manager->persist($translation);
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
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }
}
