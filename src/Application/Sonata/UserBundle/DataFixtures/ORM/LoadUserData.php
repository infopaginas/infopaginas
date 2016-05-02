<?php
namespace Application\Sonata\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Application\Sonata\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@admin.by');
        $user->setUsername('admin');
        $user->setPlainPassword('admin');
        $user->setSuperAdmin(true);
        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();
    }
}
