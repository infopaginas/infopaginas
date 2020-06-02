<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\EmergencyBundle\Entity\EmergencyService;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Entity\Translation\PageTranslation;
use Domain\PageBundle\Model\PageInterface;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170926112607 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em        = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $areas = $this->getEmergencyAreas();

        foreach ($areas as $area) {
            $this->createEmergencyArea($area);
        }

        $categories = $this->getEmergencyCategories();

        foreach ($categories as $category) {
            $this->createEmergencyCategory($category);
        }

        $services = $this->getEmergencyServices();

        foreach ($services as $service) {
            $this->createEmergencyService($service);
        }

        $emergencyPages = $this->getEmergencyPages();

        foreach ($emergencyPages as $page) {
            $this->createEmergencyPage($page);
        }

        $this->createConfigItem();

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }

    protected function createConfigItem()
    {
        $config = $this->em->getRepository(Config::class)->findOneBy([
            'key' => ConfigInterface::EMERGENCY_SITUATION_ON,
        ]);

        if (!$config) {
            $config = new Config();
            $config->setKey(ConfigInterface::EMERGENCY_SITUATION_ON);
            $config->setTitle('Emergency situation enabled');
            $config->setValue('1');
            $config->setFormat('text');
            $config->setDescription('Enable emergency site features');
            $config->setIsActive(true);

            $this->em->persist($config);
        }
    }

    /**
     * @param array $item
     */
    protected function createEmergencyPage($item)
    {
        $page = new Page();

        $page->setName($item['name']);
        $page->setCode($item['code']);
        $page->setBody($item['body']);
        $page->setIsPublished($item['isPublished']);
        $page->setSlug($item['slug']);

        $page->setDescription($item['descrEn']);
        $page->setTitle($item['titleEn']);
        $page->setSeoTitle($item['seoTitleEn']);
        $page->setSeoDescription($item['seoDescEn']);

        $this->addTranslation(new PageTranslation(), 'description', $item['descrEs'], $page);
        $this->addTranslation(new PageTranslation(), 'title', $item['titleEs'], $page);
        $this->addTranslation(new PageTranslation(), 'seoTitle', $item['seoTitleEs'], $page);
        $this->addTranslation(new PageTranslation(), 'seoDescription', $item['seoDescEs'], $page);

        $this->em->persist($page);
    }

    /**
     * @param AbstractPersonalTranslation $translation
     * @param string $fieldName
     * @param string $value
     * @param $object
     * @param string $locale
     */
    protected function addTranslation($translation, $fieldName, $value, $object, $locale = LocaleHelper::LOCALE_ES)
    {
        $translation->setField($fieldName);
        $translation->setContent($value);
        $translation->setLocale($locale);
        $translation->setObject($object);

        $this->em->persist($translation);
    }

    /**
     * @param array $item
     */
    protected function createEmergencyService($item)
    {
        $area = new EmergencyService();

        $area->setName($item['name']);
        $area->setPosition($item['position']);

        $this->em->persist($area);
    }

    /**
     * @param array $item
     */
    protected function createEmergencyCategory($item)
    {
        $area = new EmergencyCategory();

        $area->setName($item['name']);
        $area->setPosition($item['position']);

        $this->em->persist($area);
    }

    /**
     * @param array $item
     */
    protected function createEmergencyArea($item)
    {
        $area = new EmergencyArea();

        $area->setName($item['name']);
        $area->setPosition($item['position']);

        $this->em->persist($area);
    }

    /**
     * @return array
     */
    protected function getEmergencyCategories()
    {
        return [
            [
                'name'      => 'Restaurantes',
                'position'  => 1,
            ],
            [
                'name'      => 'Doctores Generalistas',
                'position'  => 2,
            ],
            [
                'name'      => 'Centro Diálisis',
                'position'  => 3,
            ],
            [
                'name'      => 'Farmacia',
                'position'  => 4,
            ],
            [
                'name'      => 'Veterinarios',
                'position'  => 5,
            ],
            [
                'name'      => 'Ferreterías',
                'position'  => 6,
            ],
            [
                'name'      => 'Hielo',
                'position'  => 7,
            ],
            [
                'name'      => 'Gas Propano',
                'position'  => 8,
            ],
            [
                'name'      => 'Cisternas',
                'position'  => 9,
            ],
            [
                'name'      => 'Generatores',
                'position'  => 10,
            ],
            [
                'name'      => 'Limpiezas',
                'position'  => 11,
            ],
            [
                'name'      => 'Handyman',
                'position'  => 12,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getEmergencyServices()
    {
        return [
            [
                'name'      => 'WiFi Disponible',
                'position'  => 1,
            ],
            [
                'name'      => 'Takeout Disponible',
                'position'  => 2,
            ],
            [
                'name'      => 'Takeout Solamente',
                'position'  => 3,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getEmergencyAreas()
    {
        return [
            [
                'name'      => 'Metro',
                'position'  => 1,
            ],
            [
                'name'      => 'Centro',
                'position'  => 2,
            ],
            [
                'name'      => 'Este',
                'position'  => 3,
            ],
            [
                'name'      => 'Norte',
                'position'  => 4,
            ],
            [
                'name'      => 'Oeste',
                'position'  => 5,
            ],
            [
                'name'      => 'Sur',
                'position'  => 6,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getEmergencyPages()
    {
        return [
            PageInterface::CODE_EMERGENCY => [
                'name'        => 'Emergency Page',
                'titleEn'     => 'Business opened after Mary',
                'titleEs'     => 'Negocios abiertos luego de Maria',
                'descrEn'     => '',
                'descrEs'     => '',
                'seoTitleEn'  => 'Emergency Services and Open Businesses in Puerto Rico',
                'seoTitleEs'  => 'Servicios de Emergencia y Negocios Abiertos en Puerto Rico',
                'seoDescEn'   => 'Hurricane Maria Disaster Relief: Find emergency services, resources, assistance, and' .
                    ' open businesses in San Juan and Puerto Rico.',
                'seoDescEs'   => 'Huracán María Desastre: Encuentre servicios de emergencia, recursos, asistencia y' .
                    ' negocios abiertos en San Juan y Puerto Rico.',
                'code'        => PageInterface::CODE_EMERGENCY,
                'body'        => '',
                'isPublished' => true,
                'slug'        => 'emergency',
            ],
            PageInterface::CODE_EMERGENCY_AREA_CATEGORY => [
                'name'        => 'Emergency Category in Area Page',
                'titleEn'     => '[area]',
                'titleEs'     => '[area]',
                'descrEn'     => '[category]',
                'descrEs'     => '[category]',
                'seoTitleEn'  => 'Open [category] in [area] to help after Hurricane Maria',
                'seoTitleEs'  => 'Abrir [category] en [area] para ayudar después del huracán María',
                'seoDescEn'   => 'Find [category] in [area] that are open and ready to serve after Hurricane Maria',
                'seoDescEs'   => 'Encuentre [category] en [area] que estén abiertas y listas para servir después' .
                    ' del huracán María',
                'code'        => PageInterface::CODE_EMERGENCY_AREA_CATEGORY,
                'body'        => '',
                'isPublished' => true,
                'slug'        => 'emergency/area/category',
            ],
        ];
    }
}
