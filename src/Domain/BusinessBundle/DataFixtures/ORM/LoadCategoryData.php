<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Model\CategoryModel;
use Domain\BusinessBundle\Util\SlugUtil;
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
     * @var array
     */
    protected $mapping;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->mapping = CategoryModel::getCategoryMapping();

        //create undefined category
        $this->createCategory(CategoryModel::getUndefinedCategory());

        $data = CategoryModel::getCategories();

        foreach ($data as $category1) {
            $object1 = $this->createCategory($category1);

            if (!empty($category1['children'])) {
                foreach ($category1['children'] as $category2) {
                    $object2 = $this->createCategory($category2);
                    $object2->setParent($object1);

                    if (!empty($category2['children'])) {
                        foreach ($category2['children'] as $category3) {
                            $object3 = $this->createCategory($category3);
                            $object3->setParent($object2);
                        }
                    }
                }
            }

            $this->manager->flush();
            $this->manager->clear();
        }
    }

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

        if (!empty($category['code']) and !empty($this->mapping[$category['code']])) {
            $object->setSlugEn($this->mapping[$category['code']]);
            $object->setSlugEs($this->mapping[$category['code']]);
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
