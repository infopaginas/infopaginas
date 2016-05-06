<?php
namespace Domain\DefaultBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;

class LoadUserData  extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // set reference to find this
        /** @var $adminGroup Group*/
        $adminGroup = $this->getReference('group.'.Group::CODE_ADMINISTRATOR);

        $user = new User();
        $user->setEmail('admin2@admin.by');
        $user->setUsername('admin2');
        $user->setPlainPassword('admin2');
        $user->setRole($adminGroup);
        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}
