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
        foreach ($this->getData() as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setUsername($userData['username']);
            $user->setFirstname($userData['first_name']);
            $user->setLastname($userData['last_name']);
            $user->setPlainPassword($userData['plain_password']);
            $user->setRole($userData['role']);
            $user->setSuperAdmin($userData['super_admin']);
            $user->setEnabled($userData['enabled']);
            $user->updateRoleGroup();

            // set reference to find this
            $this->addReference('user.'.$user->getUsername(), $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    private function getData()
    {
        return [
            [
                'email' => 'admin@admin.by',
                'username' => 'admin',
                'first_name' => 'Main',
                'last_name' => 'Admin',
                'plain_password' => 'admin',
                'role' => $this->getReference('group.'.Group::CODE_ADMINISTRATOR),
                'super_admin' => true,
                'enabled' => true,
            ], [
                'email' => 'administrator@admin.by',
                'username' => 'administrator',
                'first_name' => 'Main',
                'last_name' => 'Administrator',
                'plain_password' => 'administrator',
                'role' => $this->getReference('group.'.Group::CODE_ADMINISTRATOR),
                'super_admin' => false,
                'enabled' => true,
            ], [
                'email' => 'manager@admin.by',
                'username' => 'manager',
                'first_name' => 'Main',
                'last_name' => 'Manager',
                'plain_password' => 'manager',
                'role' => $this->getReference('group.'.Group::CODE_CONTENT_MANAGER),
                'super_admin' => false,
                'enabled' => true,
            ], [
                'email' => 'sales@admin.by',
                'username' => 'sales',
                'first_name' => 'Main',
                'last_name' => 'Sales',
                'plain_password' => 'sales',
                'role' => $this->getReference('group.'.Group::CODE_SALES_MANAGER),
                'super_admin' => false,
                'enabled' => true,
            ], [
                'email' => 'merchant@admin.by',
                'username' => 'merchant',
                'first_name' => 'Main',
                'last_name' => 'Merchant',
                'plain_password' => 'merchant',
                'role' => $this->getReference('group.'.Group::CODE_MERCHANT),
                'super_admin' => false,
                'enabled' => true,
            ], [
                'email' => 'consumer@admin.by',
                'username' => 'consumer',
                'first_name' => 'Main',
                'last_name' => 'Consumer',
                'plain_password' => 'consumer',
                'role' => $this->getReference('group.'.Group::CODE_CONSUMER),
                'super_admin' => false,
                'enabled' => true,
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
