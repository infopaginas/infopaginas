<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Entity\Translation\PageTranslation;
use Domain\PageBundle\Model\PageInterface;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170901135649 extends AbstractMigration implements ContainerAwareInterface
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
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->updateOldPages();
        $this->addNewPages();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function updateOldPages()
    {
        $pages = $this->getOldPageData();

        foreach ($pages as $key => $item) {
            $page = $this->getPageByCode($key);

            if ($page) {
                $page->setName($item['name']);

                if ($key == PageInterface::CODE_LANDING) {
                    $page->setSeoTitle($item['seoTitleEn']);
                    $page->setSeoDescription($item['seoDescEn']);

                    $seoTitleTranslation = $page->getTranslationItem('seoTitle', LocaleHelper::LOCALE_ES);

                    if (!$seoTitleTranslation) {
                        $this->addTranslation(new PageTranslation(), 'seoTitle', $item['seoTitleEs'], $page);
                    } else {
                        $seoTitleTranslation->setContent($item['seoTitleEs']);
                    }

                    $seoDescTranslation = $page->getTranslationItem('seoDescription', LocaleHelper::LOCALE_ES);

                    if (!$seoDescTranslation) {
                        $this->addTranslation(new PageTranslation(), 'seoDescription', $item['seoDescEs'], $page);
                    } else {
                        $seoDescTranslation->setContent($item['seoDescEs']);
                    }

                    $page->setTitle($item['titleEn']);
                    $page->setDescription($item['descEn']);

                    $titleTranslation = $page->getTranslationItem('title', LocaleHelper::LOCALE_ES);

                    if (!$titleTranslation) {
                        $this->addTranslation(new PageTranslation(), 'title', $item['titleEs'], $page);
                    } else {
                        $titleTranslation->setContent($item['titleEs']);
                    }

                    $descTranslation = $page->getTranslationItem('description', LocaleHelper::LOCALE_ES);

                    if (!$descTranslation) {
                        $this->addTranslation(new PageTranslation(), 'description', $item['descEs'], $page);
                    } else {
                        $descTranslation->setContent($item['descEs']);
                    }
                } else {
                    $seoTitleEn = $this->convertSeoTitle($page->getSeoTitle());

                    $page->setSeoTitle($seoTitleEn);
                    $seoTitleTranslation = $page->getTranslationItem('seoTitle', LocaleHelper::LOCALE_ES);

                    if ($seoTitleTranslation) {
                        $seoTitleEs = $this->convertSeoTitle($page->getTranslation('seoTitle', LocaleHelper::LOCALE_ES));
                        $seoTitleTranslation->setContent($seoTitleEs);
                    }
                }

                if (!empty($item['redirectUrl'])) {
                    $redirectUrl = filter_var($item['redirectUrl'], FILTER_VALIDATE_URL);

                    if ($redirectUrl) {
                        $page->setRedirectUrl($redirectUrl);
                    }
                }
            }
        }

        $this->em->flush();
    }

    protected function addNewPages()
    {
        $pages = $this->getNewPageData();

        foreach ($pages as $key => $item) {
            $page = $this->getPageByCode($key);

            if (!$page) {
                $page = new Page();

                $page->setName($item['name']);
                $page->setCode($item['code']);
                $page->setBody($item['body']);
                $page->setIsPublished($item['isPublished']);
                $page->setSlug($item['slug']);

                $page->setTitle($item['titleEn']);
                $page->setSeoTitle($item['seoTitleEn']);
                $page->setSeoDescription($item['seoDescEn']);
                $this->addTranslation(new PageTranslation(), 'title', $item['titleEs'], $page);
                $this->addTranslation(new PageTranslation(), 'seoTitle', $item['seoTitleEs'], $page);
                $this->addTranslation(new PageTranslation(), 'seoDescription', $item['seoDescEs'], $page);

                $this->em->persist($page);
            }
        }

        $this->em->flush();
    }

    /**
     * @param int $code
     *
     * @return Page|null
     */
    protected function getPageByCode($code)
    {
        return $this->em->getRepository(Page::class)->findOneBy([
            'code' => $code,
        ]);
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
     * @return array
     */
    protected function getNewPageData()
    {
        return [
            PageInterface::CODE_ARTICLE_LIST => [
                'name'          => 'Article list',
                'titleEn'       => 'Articles',
                'titleEs'       => 'Artículos',
                'seoTitleEn'    => 'Articles',
                'seoTitleEs'    => 'Artículos',
                'seoDescEn'     => 'Stay up to date with what’s happening in Puerto Rico and learn valuable digital' .
                    ' marketing skills. Browse stories and articles about Puerto Rico local events and businesses.',
                'seoDescEs'     => 'Manténgase al día con lo que está sucediendo en Puerto Rico y aprenda valiosas' .
                    ' habilidades de marketing digital. Ver historias y artículos sobre eventos y negocios locales' .
                    ' en Puerto Rico.',
                'code'          => PageInterface::CODE_ARTICLE_LIST,
                'body'          => '',
                'isPublished'   => true,
                'slug'          => 'articles',
            ],
            PageInterface::CODE_ARTICLE_CATEGORY_LIST => [
                'name'          => 'Article List by Category',
                'titleEn'       => 'Articles by Category',
                'titleEs'       => 'Artículos por Categoría',
                'seoTitleEn'    => 'Articles - [category]',
                'seoTitleEs'    => 'Artículos - [category]',
                'seoDescEn'     => 'Articles - [category]',
                'seoDescEs'     => 'Artículos - [category]',
                'code'          => PageInterface::CODE_ARTICLE_CATEGORY_LIST,
                'body'          => '',
                'isPublished'   => true,
                'slug'          => 'articles',
            ],
            PageInterface::CODE_CATALOG => [
                'name'          => 'Catalog',
                'titleEn'       => 'Catalog',
                'titleEs'       => 'Catálogo',
                'seoTitleEn'    => 'Catalog of Business Listings in Puerto Rico',
                'seoTitleEs'    => 'Catálogo de listados de negocios en Puerto Rico',
                'seoDescEn'     => 'Browse business listings in San Juan, Ponce, Caguas, Mayagüez, Arecibo, and other' .
                    ' cities in Puerto Rico.',
                'seoDescEs'     => 'Busque listados de negocios en San Juan, Ponce, Caguas, Mayagüez, Arecibo y otras' .
                    ' ciudades en Puerto Rico.',
                'code'          => PageInterface::CODE_CATALOG,
                'body'          => '',
                'isPublished'   => true,
                'slug'          => 'c',
            ],
            PageInterface::CODE_CATALOG_LOCALITY => [
                'name'          => 'Catalog - locality',
                'titleEn'       => 'Catalog - locality',
                'titleEs'       => 'Catálogo - localidad',
                'seoTitleEn'    => 'Local business listings in [locality]',
                'seoTitleEs'    => 'Listados de empresas locales en [locality]',
                'seoDescEn'     => 'Find directions and information for local businesses including' .
                    ' [popular_category_1], [popular_category_2], and [popular_category_3] in [locality]',
                'seoDescEs'     => 'Encuentre direcciones e información para negocios locales incluyendo' .
                    ' [popular_category_1], [popular_category_2] y [popular_category_3] en [locality]',
                'code'          => PageInterface::CODE_CATALOG_LOCALITY,
                'body'          => '',
                'isPublished'   => true,
                'slug'          => 'c/locality',
            ],
            PageInterface::CODE_CATALOG_LOCALITY_CATEGORY => [
                'name'          => 'Catalog - locality - category',
                'titleEn'       => 'Catalog - locality - category',
                'titleEs'       => 'Catálogo - localidad - categoría',
                'seoTitleEn'    => '[category] in [locality]',
                'seoTitleEs'    => '[category] en [locality]',
                'seoDescEn'     => 'Browse [category] business listings in [locality]. Get directions, contact' .
                    ' information, hours of operation, and other information.',
                'seoDescEs'     => 'Busque [category] en [locality]. Encuentre direcciones, información de contacto,' .
                    ' horas de operación y más.',
                'code'          => PageInterface::CODE_CATALOG_LOCALITY_CATEGORY,
                'body'          => '',
                'isPublished'   => true,
                'slug'          => 'c/locality/category',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getOldPageData()
    {
        return [
            PageInterface::CODE_LANDING => [
                'name' => 'Home Page',
                'titleEn'    => 'Find what you are looking for instantly!',
                'titleEs'    => 'Encuentra lo que buscas al instante!',
                'descEn'     => '',
                'descEs'     => '',
                'seoTitleEn' => '',
                'seoTitleEs' => '',
                'seoDescEn'  => 'Find the phone numbers and addresses of restaurants, doctors, pharmacies and more' .
                    ' in Puerto Rico\'s largest directory.',
                'seoDescEs'  => 'Encuentra los teléfonos y direcciones de restaurantes, doctores, farmacias y más' .
                    ' en el directorio más grande de Puerto Rico.',
            ],
            PageInterface::CODE_CONTACT_US => [
                'name' => 'Contact Us',
            ],
            PageInterface::CODE_PRIVACY_STATEMENT => [
                'name' => 'Privacy Policy',
            ],
            PageInterface::CODE_TERMS_OF_USE => [
                'name' => 'Terms of Service',
            ],
            PageInterface::CODE_ADVERTISE => [
                'name' => 'Advertise with Us',
                'redirectUrl' => 'https://infopaginasmedia.com/',
            ],
        ];
    }

    /**
     * @param string
     *
     * @return string
     */
    protected function convertSeoTitle($title)
    {
        $parts = explode('|', $title);

        return trim(current($parts));
    }
}
