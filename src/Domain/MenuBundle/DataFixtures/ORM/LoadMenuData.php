<?php
namespace Domain\MenuBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Address\Country;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Brand;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\Tag;
use Domain\BusinessBundle\Entity\Translation\SubscriptionTranslation;
use Domain\BusinessBundle\Model\SubscriptionInterface;
use Domain\MenuBundle\Entity\Menu;
use Domain\MenuBundle\Model\MenuInterface;
use Domain\MenuBundle\Model\MenuModel;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMenuData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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

        $data = MenuModel::getMenuCategoriesNames();

        foreach ($data as $menuCode => $value) {
            $object = new Menu();
            $object->setCode($menuCode);
            $object->setCategory($this->getReference(sprintf('category.%s', $menuCode)));

            $this->manager->persist($object);
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
