<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Entity\Translation\PageTranslation;
use Domain\PageBundle\Model\PageInterface;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20190513084031 extends AbstractMigration implements ContainerAwareInterface
{
    private $em;

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function up(Schema $schema): void
    {
        $this->updateCatalogPageTitle();

        $searchPage = [
            PageInterface::CODE_SEARCH => [
                'name'        => 'Search',
                'titleEn'     => '[category] in [locality]',
                'titleEs'     => '[category] en [locality]',
                'seoTitleEn'  => '[category] in [locality]',
                'seoTitleEs'  => '[category] en [locality]',
                'seoDescEn'   => 'Find best [category] in [locality]',
                'seoDescEs'   => 'Encuentra el mejor [category] en [locality]',
                'code'        => PageInterface::CODE_SEARCH,
                'body'        => '',
                'isPublished' => true,
                'slug'        => '/businesses',
            ],
        ];

        foreach ($searchPage as $key => $item) {
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

    public function down(Schema $schema): void
    {

    }

    protected function getPageByCode($code)
    {
        return $this->em->getRepository(Page::class)->findOneBy([
            'code' => $code,
        ]);
    }

    protected function addTranslation($translation, $fieldName, $value, $object, $locale = LocaleHelper::LOCALE_ES)
    {
        $translation->setField($fieldName);
        $translation->setContent($value);
        $translation->setLocale($locale);
        $translation->setObject($object);

        $this->em->persist($translation);
    }

    protected function updateCatalogPageTitle()
    {
        $page = $this->getPageByCode(PageInterface::CODE_CATALOG_LOCALITY_CATEGORY);

        $page->setTitle('[category] in [locality]');
        $titleTranslation = $page->getTranslationItem('title', LocaleHelper::LOCALE_ES);

        if ($titleTranslation) {
            $titleTranslation->setContent('[category] en [locality]');
        }
    }
}
