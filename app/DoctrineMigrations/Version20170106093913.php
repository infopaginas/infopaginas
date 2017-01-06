<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Model\CategoryUpdateModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170106093913 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

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
        //add/update categories

        $categoryList = CategoryUpdateModel::getCategories();

        foreach ($categoryList as $item1) {
            $category1 = $this->findCategory($item1['en'], Category::CATEGORY_LEVEL_1);

            if ($category1) {
                $category1 = $this->updateCategory($category1, $item1);
            } else {
                $category1 = $this->createCategory($item1);
            }

            if ($item1['children']) {
                foreach ($item1['children'] as $item2) {
                    $category2 = $this->findCategory($item2['en'], Category::CATEGORY_LEVEL_2, $category1);

                    if ($category2) {
                        $category2 = $this->updateCategory($category2, $item2);
                    } else {
                        $category2 = $this->createCategory($item2, $category1);
                    }

                    if ($item2['children']) {
                        foreach ($item2['children'] as $item3) {
                            $category3 = $this->findCategory($item3['es'], Category::CATEGORY_LEVEL_3, $category2);

                            if ($category3) {
                                $category3 = $this->updateCategory($category3, $item3);
                            } else {
                                $category3 = $this->createCategory($item3, $category2);
                            }
                        }
                    }
                }
            }

            $this->em->flush();
        }
    }

    protected function createCategory($data, $parent = null)
    {
        $object = new Category();

        if ($parent) {
            $object->setParent($parent);
        }

        //workaround to generate es slug
        $object->setLocale('es');
        $object->setName($data['es']);

        $this->em->persist($object);
        $this->em->flush();

        $this->em->refresh($object);

        $object->setLocale('en');

        $object = $this->updateCategory($object, $data);

        return $object;
    }

    protected function updateCategory(Category $object, $data)
    {
        $object->setName($data['en']);

        if (!empty($data['code'])) {
            $object->setCode($data['code']);
        }

        $object->setSearchTextEn($data['en']);
        $object->setSearchTextEs($data['es']);
        $object = $this->addTranslation($object, $data, 'es');
        $object = $this->addTranslation($object, $data, 'en');

        return $object;
    }

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

    protected function findCategory($name, $level, $parent = null)
    {
        $category = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(
            [
                'name' => $name,
                'lvl'  => $level,
                'parent' => $parent,
            ]
        );

        return $category;
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
