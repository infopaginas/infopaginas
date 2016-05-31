<?php

namespace Domain\BannerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BannerBundle\Entity\Type;
use Domain\BannerBundle\Model\BannerInterface;
use Domain\BannerBundle\Model\TypeInterface;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTypeData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $item) {
            $config = new Type();
            $config->setCode($item['code']);
            $config->setName($item['name']);
            $config->setPlacement($item['placement']);
            $config->setComment($item['comment']);
            $manager->persist($config);
        }

        $manager->flush();
    }

    private function getData()
    {
        return [
            [
                'code' => TypeInterface::CODE_HOME,
                'name' => 'home-120x420',
                'placement' => 'Home page',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_PORTAL,
                'name' => 'portal-300x250',
                'placement' => 'Home page',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_PORTAL_LEADERBOARD,
                'name' => 'portal-leaderboard',
                'placement' => 'Home page',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_SERP_BANNER,
                'name' => 'serp-banner',
                'placement' => 'Search results',
                'comment' => 'Shown if Business corresponds to search category and location.',
            ], [
                'code' => TypeInterface::CODE_SERP_BOXED,
                'name' => 'serp-boxad',
                'placement' => 'Search results',
                'comment' => 'Shown if Business corresponds to search category and location.',
            ], [
                'code' => TypeInterface::CODE_SERP_FEATUREAD,
                'name' => 'serp-featuread',
                'placement' => 'Search results',
                'comment' => 'Gives priority in results by searches by category and town.',
            ], [
                'code' => TypeInterface::CODE_SERP_MOBILE_TOP,
                'name' => 'serp-mobile-top',
                'placement' => '',
                'comment' => 'Mobile version of serp-banner. Used by search on mobile devices.',
            ],
        ];
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 7;
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
