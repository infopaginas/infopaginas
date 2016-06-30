<?php
namespace Oxa\Sonata\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // set reference to find this
        /** @var $adminGroup Group*/
        $adminGroup = $this->getReference('group.'.Group::CODE_ADMINISTRATOR);

        $user = new User();
        $user->setEmail('admin@admin.by');
        $user->setUsername('admin');
        $user->setFirstname('Main');
        $user->setLastname('Admin');
        $user->setPlainPassword('admin');
        $user->setRole($adminGroup);
        $user->setSuperAdmin(true);
        $user->setEnabled(true);

        // set reference to find this
        $this->addReference('user.'.$user->getUsername(), $user);

        $manager->persist($user);
        $manager->flush();
    }

    private function getData()
    {
        return [
            [
//                'email' => ConfigInterface::DEFAULT_TITLE,
//                'username' => 'Default title',
//                'value' => 'Infopaginas',
//                'format' => 'text',
//                'description' => 'Default site title',
            ], [

            ]
            ];
    }
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
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
