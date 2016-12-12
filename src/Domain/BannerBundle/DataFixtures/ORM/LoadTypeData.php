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
                'code'      => TypeInterface::CODE_HOME_VERTICAL,
                'name'      => 'homepage-320x50-728x90',
                'placement' => 'Homepage page',
                'comment'   => 'By default are used for Google AdSence or Infopaginas advertising, (should be set up for both sizes)',
            ],
            [
                'code'      => TypeInterface::CODE_STATIC_BOTTOM,
                'name'      => 'static-auto-bottom-320x50-728x90',
                'placement' => 'Bottom banner of static pages, auto size',
                'comment'   => 'By default are used for Google AdSence or Infopaginas advertising (should be set up for both sizes)',
            ],
            [
                'code'      => TypeInterface::CODE_PORTAL_RIGHT,
                'name'      => 'common-side-right-300x250',
                'placement' => 'Static pages, and business profile view',
                'comment'   => 'By default are used for Google AdSence or Infopaginas advertising',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                'name'      => 'search-auto-bottom-468x60-320x50',
                'placement' => 'Search, compare and catalog pages, auto size',
                'comment'   => 'By default are used for Google AdSence or Infopaginas advertising (should be set up for both sizes)',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_PAGE_TOP,
                'name'      => 'search-auto-up-468x60-320x50',
                'placement' => 'Search, compare and catalog pages',
                'comment'   => 'By default are used for Google AdSence or Infopaginas advertising (should be set up for both sizes)',
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
