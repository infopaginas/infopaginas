<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170410150309 extends AbstractMigration implements ContainerAwareInterface
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
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $data = [
            'es' => 'Infopaginas Media',
            'en' => 'Infopaginas Media',
            'code' => Category::CATEGORY_ARTICLE_CODE,
        ];

        $category = $this->em->getRepository(Category::class)->findOneBy([
            'code' => Category::CATEGORY_ARTICLE_CODE,
        ]);

        if (!$category) {
            $this->addCategory($data);
        }

        $this->addNotificationTemplate();
        $this->updateArticlesAuthorName();
        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }

    protected function updateArticlesAuthorName()
    {
        $articles = $this->em->getRepository(Article::class)->findBy(
            [
                'isExternal' => false,
            ]
        );

        $seoSettings = $this->container->getParameter('seo_custom_settings');

        $defaultAuthorName = $seoSettings['default_article_author'];

        foreach ($articles as $article) {
            $user = $article->getCreatedUser();

            if ($user) {
                $article->setAuthorName($user->getFullName());
            } else {
                $article->setAuthorName($defaultAuthorName);
            }
        }
    }

    protected function addNotificationTemplate()
    {
        if (!$this->checkNewConfigValue(ConfigInterface::ARTICLE_API_ERROR_EMAIL_TEMPLATE)) {
            $value = $this->container->get('twig')
                ->render('OxaConfigBundle:Fixtures:mail_article_api_response_invalid.html.twig');

            $configMail = new Config();
            $configMail->setKey(ConfigInterface::ARTICLE_API_ERROR_EMAIL_TEMPLATE);
            $configMail->setTitle('Article API error template');
            $configMail->setValue($value);
            $configMail->setFormat('html');
            $configMail->setDescription('Notify if article API response returns error');

            $this->em->persist($configMail);
        }

        $this->em->flush();
    }

    /**
     * @param array $data
     *
     * @return Category
     */
    protected function addCategory($data)
    {
        $category = new Category();

        $category->setName($data['en']);
        $category->setCode($data['code']);
        $category->setSearchTextEn($data['en']);
        $category->setSearchTextEs($data['es']);
        $category = $this->addTranslation($category, $data, 'es');
        $category = $this->addTranslation($category, $data, 'en');

        $this->em->persist($category);

        return $category;
    }

    /**
     * @param Category $category
     * @param array    $item
     * @param string   $locale
     *
     * @return Category
     */
    protected function addTranslation(Category $category, $item, $locale)
    {
        foreach (Category::getTranslatableFields() as $field) {
            $translation = $category->getTranslationItem($field, $locale);

            if ($translation) {
                $translation->setContent($item[$locale]);
            } else {
                $translation = new CategoryTranslation();

                $translation->setField($field);
                $translation->setLocale($locale);
                $translation->setContent($item[$locale]);
                $translation->setObject($category);
                $this->em->persist($translation);
            }
        }

        return $category;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    protected function checkNewConfigValue($key)
    {
        $config = $this->em->getRepository(Config::class)->findOneBy(
            [
                'key' => $key,
            ]
        );

        return (bool)$config;
    }
}
