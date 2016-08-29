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
                'code' => TypeInterface::CODE_SERP_BANNER,
                'name' => 'homepage-main-728x90',
                'placement' => 'Homepage big top banner',
                'comment' => 'Shown if Business corresponds to search category and location.',
            ], [
                'code' => TypeInterface::CODE_PORTAL_LEFT,
                'name' => 'homepage-bottom-left-300x250',
                'placement' => 'Home page banner at the bottom. left one',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_PORTAL_RIGHT,
                'name' => 'homepage-bottom-right-300x250',
                'placement' => 'Home page banner at the bottom. right one',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ],[
                'code' => TypeInterface::CODE_PORTAL_LEFT_MOBILE,
                'name' => 'homepage-bottom-left-mobile-300x250',
                'placement' => 'Home page banner at the bottom. left one. Mobile',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_PORTAL_RIGHT_MOBILE,
                'name' => 'homepage-bottom-right-mobile-300x250',
                'placement' => 'Home page banner at the bottom. left one. Mobile',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_PORTAL_LEADERBOARD,
                'name' => 'search-page-main-728x90',
                'placement' => 'Search results page big banner',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_PORTAL,
                'name' => 'search-page-secondary-300x250',
                'placement' => 'Search results page. Secondary banner',
                'comment' => 'By default are used for Google AdSence or Infopaginas advertising',
            ], [
                'code' => TypeInterface::CODE_SERP_BOXED,
                'name' => 'business-profile-secondary-300x250',
                'placement' => 'Business profile page',
                'comment' => 'Shown if Business corresponds to search category and location.',
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
