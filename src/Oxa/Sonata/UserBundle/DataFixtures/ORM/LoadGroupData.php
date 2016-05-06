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

class LoadGroupData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        $trans = $this->container->get('translator');
        $domain = 'OxaUserGroupDataFixtures';
        $locale = 'en';
        
        $groupLabelParts = [
            Group::CODE_ADMINISTRATOR    => 'administrator',
            Group::CODE_CONTENT_MANAGER  => 'content_manager',
            Group::CODE_SALES_MANAGER    => 'sales_manager',
            Group::CODE_MERCHANT         => 'merchant',
            Group::CODE_CONSUMER         => 'consumer',
        ];

        foreach ($groupLabelParts as $code => $labelPart)
        {
            // values has to be like 'group_administrator_name' (which is stored in translation file)
            $name = $trans->trans(sprintf('group_%s_name', $labelPart), [], $domain, $locale);
            $description = $trans->trans(sprintf('group_%s_description', $labelPart), [], $domain, $locale);

            $group = new Group($name);
            $group->setDescription($description);
            $group->setCode($code);
            $group->addRole(Group::$groupRoles[$code]);

            $manager->persist($group);

            // set reference to find this
            $this->addReference('group.'.$code, $group);
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
        return 0;
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
