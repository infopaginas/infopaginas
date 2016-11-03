<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Entity\Translation\SubscriptionPlanTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSubscriptionPlanData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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

        $data = SubscriptionPlan::getCodeNames();

        foreach ($data as $code => $value) {
            $object = new SubscriptionPlan();
            $object->setName($value);
            $object->setCode($code);
            $object->setRank($code);

            $translation = new SubscriptionPlanTranslation();
            $translation->setContent(sprintf('Spain %s', $value));
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($object);

            $this->manager->persist($object);
            $this->manager->persist($translation);

            // set reference to find this
            $this->addReference('subscription_plan.'.$code, $object);
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
