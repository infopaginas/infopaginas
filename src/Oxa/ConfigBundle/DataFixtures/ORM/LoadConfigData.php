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
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $item) {
            $config = new Config();
            $config->setKey($item['key']);
            $config->setTitle($item['title']);
            $config->setValue($item['value']);
            $config->setFormat($item['format']);
            $config->setDescription($item['description']);

            if (!empty($item['hidden'])) {
                $config->setIsActive(false);
            }

            $manager->persist($config);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
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
                'key' => ConfigInterface::DEFAULT_EMAIL_ADDRESS,
                'title' => 'Default Email Address',
                'value' => 'info@infopaginas.com',
                'format' => 'text',
                'description' => 'Used as "from" value in emails',
            ],[
                'key' => ConfigInterface::MAIL_REGISTRATION_TEMPLATE,
                'title' => 'Registration mail template',
                'value' => $this->container->get('twig')->render(
                    'OxaConfigBundle:Fixtures:mail_registration_template.html.twig'
                ),
                'format' => 'html',
                'description' => 'Registration mail template',
            ],[
                'key' => ConfigInterface::MAIL_NEW_MERCHANT_TEMPLATE,
                'title' => 'New merchant mail template',
                'value' => $this->container->get('twig')->render(
                    'OxaConfigBundle:Fixtures:mail_new_merchant_template.html.twig'
                ),
                'format' => 'html',
                'description' => 'New merchant mail template',
            ],[
                'key' => ConfigInterface::MAIL_RESET_PASSWORD_TEMPLATE,
                'title' => 'Reset password mail template',
                'value' => $this->container->get('twig')->render(
                    'OxaConfigBundle:Fixtures:mail_reset_password_template.html.twig'
                ),
                'format' => 'html',
                'description' => 'Reset password mail template',
            ], [
                'key' => ConfigInterface::MAIL_TEMPLATE_TO_USER,
                'title' => 'User mail template',
                'value' => $this->container->get('twig')->render(
                    'OxaConfigBundle:Fixtures:mail_template_to_user.html.twig'
                ),
                'format' => 'html',
                'description' => 'User mail template',
            ], [
                'key' => ConfigInterface::MAIL_CHANGE_WAS_REJECTED,
                'title' => 'User mail template',
                'value' => $this->container->get('twig')->render(
                    'OxaConfigBundle:Fixtures:mail_change_was_rejected.html.twig'
                ),
                'format' => 'html',
                'description' => 'Change was rejected message',
            ], [
                'key' => ConfigInterface::MAPBOX_API_KEY,
                'title' => 'MapBox api key',
                'value' => 'AIzaSyACRiuSCjh3c3jgxC53StYJCvag6Ig8ZIw',
                'format' => 'text',
                'description' => 'Used for access to MapBox',
            ], [
                'key' => ConfigInterface::DEFAULT_MAP_COORDINATE_LATITUDE,
                'title' => 'Default map latitude parameter',
                'value' => '18.446344',
                'format' => 'text',
                'description' => 'Used to show default map position',
            ], [
                'key' => ConfigInterface::DEFAULT_MAP_COORDINATE_LONGITUDE,
                'title' => 'Default map longitude parameter',
                'value' => '-66.07558',
                'format' => 'text',
                'description' => 'Used to show default map position',
            ], [
                'key' => ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE,
                'title' => 'Default results page size',
                'value' => 20,
                'format' => 'text',
                'description' => 'Defines how many results will be shown on (any) results page',
            ], [
                'key' => ConfigInterface::DEFAULT_SEARCH_CITY,
                'title' => 'Default search city name',
                'value' => 'San Juan',
                'format' => 'text',
                'description' => 'Defines how many results will be shown on (any) results page',
            ], [
                'key' => ConfigInterface::YOUTUBE_ACCESS_TOKEN,
                'title' => 'Youtube access token',
                'value' => json_encode([]),
                'format' => 'json',
                'description' => 'Token to access youtube account',
                'hidden' => true,
            ], [
                'key' => ConfigInterface::YOUTUBE_ERROR_EMAIL_TEMPLATE,
                'title' => 'Youtube token error template',
                'value' => $this->container->get('twig')->render(
                    'OxaConfigBundle:Fixtures:mail_youtube_token_invalid.html.twig'
                ),
                'format' => 'html',
                'description' => 'Notify if youtube token is invalid',
            ], [
                'key' => ConfigInterface::ARTICLE_API_ERROR_EMAIL_TEMPLATE,
                'title' => 'Article API error template',
                'value' => $this->container->get('twig')->render(
                    'OxaConfigBundle:Fixtures:mail_article_api_response_invalid.html.twig'
                ),
                'format' => 'html',
                'description' => 'Notify if article API response returns error',
            ], [
                'key' => ConfigInterface::SEARCH_ADS_ALLOWED,
                'title' => 'Allow ads for search results',
                'value' => 1,
                'format' => 'text',
                'description' => 'Set to 1 to enable ads for search results (0 - disable)',
            ], [
                'key' => ConfigInterface::SEARCH_ADS_MAX_PAGE,
                'title' => 'Pages where ads will be shown',
                'value' => 1,
                'format' => 'text',
                'description' => 'Max page number where ads will be shown (set to 0 to show ads at all pages)',
            ], [
                'key' => ConfigInterface::SEARCH_ADS_PER_PAGE,
                'title' => 'Ads per page for search results',
                'value' => 6,
                'format' => 'text',
                'description' => 'Max ads quantity that can be shown at one search page (note it is max quantity)',
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
