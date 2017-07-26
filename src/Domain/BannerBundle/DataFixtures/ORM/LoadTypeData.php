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
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getData() as $item) {
            $config = new Type();
            $config->setCode($item['code']);
            $config->setName($item['name']);
            $config->setPlacement($item['placement']);
            $config->setComment($item['comment']);
            $manager->persist($config);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public static function getData()
    {
        return [
            [
                'code'      => TypeInterface::CODE_LANDING_PAGE_RIGHT,
                'name'      => 'Landing Page 300x250',
                'placement' => 'Landing Page',
                'comment'   => 'Ad block in the right column of Landing Page',
            ],
            [
                'code'      => TypeInterface::CODE_BUSINESS_PAGE_RIGHT,
                'name'      => 'Business Profile Page 300x250',
                'placement' => 'Business Profile Pages',
                'comment'   => 'Ad block in the right column of Business Profile Pages',
            ],
            [
                'code'      => TypeInterface::CODE_ARTICLE_PAGE_RIGHT,
                'name'      => 'Articles 300x250',
                'placement' => 'Article List and Article Page',
                'comment'   => 'Ad block in the right column of Article List and Article Page',
            ],
            [
                'code'      => TypeInterface::CODE_VIDEO_PAGE_RIGHT,
                'name'      => 'Videos 300x250',
                'placement' => 'Video List',
                'comment'   => 'Ad block in the right column of Video List',
            ],
            [
                'code'      => TypeInterface::CODE_PORTAL_RIGHT,
                'name'      => 'Static 300x250',
                'placement' => 'Static pages (advertise, contact us, terms, privacy)',
                'comment'   => 'Ad block in the right column of Static pages',
            ],
            [
                'code'      => TypeInterface::CODE_HOME_VERTICAL,
                'name'      => 'Landing Page 728x90 and 320x50',
                'placement' => 'Landing Page',
                'comment'   => 'Vertical ad block under search (should be set up for both sizes)',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_PAGE_TOP,
                'name'      => 'Search Results Top 728x90 and 320x50',
                'placement' => 'Search Results Page and Catalog',
                'comment'   => 'Vertical ad block on top of search results (should be set up for both sizes)',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                'name'      => 'Search Results Bottom 728x90 and 320x50',
                'placement' => 'Search Results Page and Catalog',
                'comment'   => 'Vertical ad block at the bottom of search results (should be set up for both sizes)',
            ],
            [
                'code'      => TypeInterface::CODE_COMPARE_PAGE_TOP,
                'name'      => 'Compare Page Top 320x50',
                'placement' => 'Compare page',
                'comment'   => 'Vertical ad block on top of compare results',
            ],
            [
                'code'      => TypeInterface::CODE_COMPARE_PAGE_BOTTOM,
                'name'      => 'Compare Page Bottom 320x50',
                'placement' => 'Compare page',
                'comment'   => 'Vertical ad block at the bottom of compare results',
            ],
            [
                'code'      => TypeInterface::CODE_BUSINESS_PAGE_BOTTOM,
                'name'      => 'Business Profile Page 728x90 and 320x50',
                'placement' => 'Business Profile Page',
                'comment'   => 'Ad block at the bottom of Business Profile Pages',
            ],
            [
                'code'      => TypeInterface::CODE_ARTICLE_PAGE_BOTTOM,
                'name'      => 'Articles 728x90 and 320x50',
                'placement' => 'Article List and Article Page',
                'comment'   => 'Ad block at the bottom of Article List and Article Page',
            ],
            [
                'code'      => TypeInterface::CODE_VIDEO_PAGE_BOTTOM,
                'name'      => 'Videos 728x90 and 320x50',
                'placement' => 'Video List',
                'comment'   => 'Ad block at the bottom of Video List',
            ],
            [
                'code'      => TypeInterface::CODE_STATIC_BOTTOM,
                'name'      => 'Static 728x90 and 320x50',
                'placement' => 'Static pages (advertise, contact us, terms, privacy)',
                'comment'   => 'Ad block at the bottom of static pages',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_FLOAT_BOTTOM,
                'name'      => 'Floating Banner at Search Page 320x50',
                'placement' => 'Search Results Page and Catalog',
                'comment'   => 'Floating ad block at the bottom of search and catalog pages',
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
