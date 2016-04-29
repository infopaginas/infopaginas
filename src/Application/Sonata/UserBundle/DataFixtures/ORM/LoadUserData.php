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
        $User = new User();
        $User->setEmail('admin@admin.by');
        $User->setUsername('admin');
        $User->setPlainPassword('admin');
        $User->setSuperAdmin(true);
        $User->setEnabled(true);

        $manager->persist($User);
        $manager->flush();
    }
}