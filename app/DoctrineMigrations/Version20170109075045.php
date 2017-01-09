<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\Category;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170109075045 extends AbstractMigration implements ContainerAwareInterface
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
        $data = [
            'es' => 'Unclassified',
            'en' => 'Unclassified',
            'code' => Category::CATEGORY_UNDEFINED_SLUG,
        ];

        $undefinedSlug = Category::CATEGORY_UNDEFINED_SLUG;

        $undefined = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(['slug' => $undefinedSlug]);

        if ($undefined) {
            $undefined = $this->updateCategory($undefined, $data);
            $this->em->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function updateCategory(Category $object, $data)
    {
        $object->setName($data['en']);
        $object->setCode($data['code']);
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
}
