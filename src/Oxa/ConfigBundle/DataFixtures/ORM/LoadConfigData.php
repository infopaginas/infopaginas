<?php

namespace Oxa\ConfigBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadConfigData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
            $config = new Config();
            $config->setKey($item['key']);
            $config->setTitle($item['title']);
            $config->setValue($item['value']);
            $config->setFormat($item['format']);
            $config->setIsActive($item['is_active']);
            $config->setDescription($item['description']);
            $manager->persist($config);
        }

        $manager->flush();
    }

    private function getData()
    {
        return [
            [
                'key' => ConfigInterface::DEFAULT_TITLE,
                'title' => 'Default title',
                'value' => 'Infopaginas',
                'format' => 'text',
                'is_active' => true,
                'description' => 'Default site title',
            ], [
                'key' => ConfigInterface::DEFAULT_META_DESCRIPTION,
                'title' => 'Default Meta-description',
                'value' => 'Default Meta-description',
                'format' => 'text',
                'is_active' => true,
                'description' => 'Meta-description by default',
            ], [
                'key' => ConfigInterface::DEFAULT_META_KEYWORDS,
                'title' => 'Default Meta-keywords',
                'value' => 'Default Meta-keywords',
                'format' => 'text',
                'is_active' => true,
                'description' => 'Meta-keywords by default',
            ], [
                'key' => ConfigInterface::FOOTER_CONTENT,
                'title' => 'Footer content',
                'value' => $this->container->get('templating')->render(
                    'OxaConfigBundle:Fixtures:footer_content.html.twig'
                ),
                'format' => 'html',
                'is_active' => true,
                'description' => 'Footer content',
            ], [
                'key' => ConfigInterface::MAIL_TEMPLATE_TO_USER,
                'title' => 'User mail template',
                'value' => $this->container->get('templating')->render(
                    'OxaConfigBundle:Fixtures:mail_template_to_user.html.twig'
                ),
                'format' => 'html',
                'is_active' => true,
                'description' => 'User mail template',
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
        return 6;
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
