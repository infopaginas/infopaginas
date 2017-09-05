<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Model\CategoryModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCategoryData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        //create system categories
        $systemCategories = CategoryModel::getSystemCategories();

        foreach ($systemCategories as $item) {
            $this->createCategory($item);
        }

        $data = CategoryModel::getCategories();

        foreach ($data as $category) {
            $this->createCategory($category);

            $this->manager->flush();
            $this->manager->clear();
        }
    }

    /**
     * @param array $category
     *
     * @return Category
     */
    private function createCategory($category)
    {
        $object = new Category();

        if (!empty($category['en'])) {
            $categoryEn = $category['en'];
        } else {
            $categoryEn = $category['es'];
        }

        $object->setLocale('es');
        $object->setName($category['es']);

        if (!empty($category['slugEn'])) {
            $object->setSlugEn($category['slugEn']);
        }

        if (!empty($category['slugEs'])) {
            $object->setSlugEs($category['slugEs']);
        }

        if (!empty($category['code'])) {
            $object->setCode($category['code']);
        }

        $object->setSearchTextEn($categoryEn);
        $object->setSearchTextEs($category['es']);

        $this->manager->persist($object);

        $translation = new CategoryTranslation();

        $translation->setField('name');
        $translation->setContent($categoryEn);
        $translation->setLocale('en');
        $translation->setObject($object);

        $this->manager->persist($translation);

        return $object;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 4;
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
