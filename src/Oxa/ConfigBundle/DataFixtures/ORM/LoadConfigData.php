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
                'description' => 'Default site title',
            ], [
                'key' => ConfigInterface::DEFAULT_META_DESCRIPTION,
                'title' => 'Default Meta-description',
                'value' => 'Default Meta-description',
                'format' => 'text',
                'description' => 'Meta-description by default',
            ], [
                'key' => ConfigInterface::DEFAULT_META_KEYWORDS,
                'title' => 'Default Meta-keywords',
                'value' => 'Default Meta-keywords',
                'format' => 'text',
                'description' => 'Meta-keywords by default',
            ], [
                'key' => ConfigInterface::FOOTER_CONTENT,
                'title' => 'Footer content',
                'value' => $this->container->get('templating')->render(
                    'OxaConfigBundle:Fixtures:footer_content.html.twig'
                ),
                'format' => 'html',
                'description' => 'Footer content',
            ], [
                'key' => ConfigInterface::MAIL_TEMPLATE_TO_USER,
                'title' => 'User mail template',
                'value' => $this->container->get('templating')->render(
                    'OxaConfigBundle:Fixtures:mail_template_to_user.html.twig'
                ),
                'format' => 'html',
                'description' => 'User mail template',
            ], [
                'key' => ConfigInterface::SOCIAL_FACEBOOK_PROFILE,
                'title' => 'Social Facebook profile',
                'value' => 'https://www.facebook.com/',
                'format' => 'text',
                'description' => 'Facebook profile link',
            ], [
                'key' => ConfigInterface::SOCIAL_TWITTER_PROFILE,
                'title' => 'Social Twitter profile',
                'value' => 'https://www.twitter.com/',
                'format' => 'text',
                'description' => 'Twitter profile link',
            ], [
                'key' => ConfigInterface::SOCIAL_GOOGLE_PROFILE,
                'title' => 'Social Google Plus profile',
                'value' => 'https://plus.google.com/',
                'format' => 'text',
                'description' => 'Google Plus profile link',
            ], [
                'key' => ConfigInterface::SOCIAL_LINKEDIN_PROFILE,
                'title' => 'Social LinkendIn profile',
                'value' => 'https://www.linkedin.com/',
                'format' => 'text',
                'description' => 'LinkendIn profile link',
            ], [
                'key' => ConfigInterface::SOCIAL_INSTAGRAM_PROFILE,
                'title' => 'Social Instagram profile',
                'value' => 'https://www.instagram.com/',
                'format' => 'text',
                'description' => 'Instagram profile link',
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
