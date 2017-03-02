<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Model\CategoryUniqueModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class UniqueCategoryConvertCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected $categoryList;

    protected $deleteCategoriesList = [];

    protected $updatedCategoriesCount  = 0;
    protected $mergedCategoriesCount   = 0;
    protected $skippedCategoriesCount  = 0;
    protected $deletedCategoriesCount  = 0;

    protected function configure()
    {
        $this->setName('data:category-unique-mapping:convert');
        $this->setDescription('Unique category conversion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->categoryList = CategoryUniqueModel::getCategories();

        foreach ($this->categoryList as $oldId1 => $categoryItem1) {
            $category1 = $this->getCategoryById($categoryItem1['newId']);
            $category1 = $this->updateCategory($category1, $categoryItem1);

            if ($category1) {
                foreach ($categoryItem1['children'] as $oldId2 => $categoryItem2) {
                    $categoryItem2['parentNewId'] = $category1->getId();

                    $category2 = $this->getCategoryById($categoryItem2['newId']);
                    $category2 = $this->updateCategory($category2, $categoryItem2);

                    if ($category2) {
                        foreach ($categoryItem2['children'] as $oldId3 => $categoryItem3) {
                            $categoryItem3['parentNewId'] = $category2->getId();

                            $category3 = $this->getCategoryById($categoryItem3['newId']);
                            $category3 = $this->updateCategory($category3, $categoryItem3);
                        }
                    }
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        $this->deleteCategories();

        $this->em->flush();

        $output->writeln($this->updatedCategoriesCount . ' categories were updated');
        $output->writeln($this->mergedCategoriesCount . ' categories were merged');
        $output->writeln($this->skippedCategoriesCount . ' categories were skipped');
        $output->writeln($this->deletedCategoriesCount . ' categories were deleted');
    }

    protected function getCategoryById($id)
    {
        $category = $this->em->getRepository('DomainBusinessBundle:Category')->find($id);

        return $category;
    }

    protected function updateCategory($category, $data)
    {
        if ($category) {
            if (!empty($data['mergeOldId'])) {
                $category = $this->mergeCategory($category, $data);
            } else {
                if (!empty($data['parentNewId'])) {
                    $parentCategory = $this->getCategoryById($data['parentNewId']);

                    if ($parentCategory) {
                        $category->setParent($parentCategory);
                    }
                }

                $category = $this->updateCategoryText($category, $data);
                $this->updatedCategoriesCount++;
            }
        } else {
            $this->skippedCategoriesCount++;
        }

        return $category;
    }

    protected function mergeCategory(Category $category, $data)
    {
        $categoryForMerge = $this->getCategoryForMerge($data['mergeOldId']);

        if ($categoryForMerge) {
            $businesses = $category->getBusinessProfiles();

            foreach ($businesses as $business) {
                /* @var BusinessProfile $business */
                if ($categoryForMerge->getLvl() == Category::CATEGORY_LEVEL_3) {
                    $business->removeCategory($category);
                } else {
                    $childrenCategories = $category->getChildren();

                    foreach ($childrenCategories as $childCategory) {
                        $business->removeCategory($childCategory);
                    }
                }

                if (!$business->getCategories()->contains($categoryForMerge)) {
                    $business->addCategory($categoryForMerge);
                }
            }

            $this->deleteCategoriesList[] = $category->getId();
            $this->mergedCategoriesCount++;
        } else {
            $this->skippedCategoriesCount++;
        }

        return $category;
    }

    protected function getCategoryForMerge($oldCategoryId)
    {
        $categoryId = null;
        $categoryForMerge = null;

        if (!empty($this->categoryList[$oldCategoryId])) {
            $categoryId = $this->categoryList[$oldCategoryId]['newId'];
        } else {
            foreach ($this->categoryList as $category1) {
                if (!empty($category1['children'][$oldCategoryId])) {
                    $categoryId = $category1['children'][$oldCategoryId]['newId'];
                    break;
                } else {
                    foreach ($category1['children'] as $category2) {
                        if (!empty($category2['children'][$oldCategoryId])) {
                            $categoryId = $category2['children'][$oldCategoryId]['newId'];
                            break;
                        }
                    }

                    if ($categoryId) {
                        break;
                    }
                }
            }
        }

        if ($categoryId) {
            $categoryForMerge = $this->getCategoryById($categoryId);
        }

        return $categoryForMerge;
    }

    protected function updateCategoryText(Category $category, $data)
    {
        //workaround to set esp slug
        $category->setSlug(null);
        $category->setName($data['newEspName']);

        $this->em->flush();

        $category->setName($data['newEngName']);
        $category->setSearchTextEn($data['newEngName']);
        $category->setSearchTextEs($data['newEspName']);
        $category = $this->addCategoryTranslation($category, $data['newEspName'], 'es');
        $category = $this->addCategoryTranslation($category, $data['newEngName'], 'en');

        return $category;
    }

    protected function addCategoryTranslation(Category $category, $content, $locale)
    {
        foreach (Category::getTranslatableFields() as $field) {
            $translation = $category->getTranslationItem($field, $locale);

            if ($translation) {
                $translation->setContent($content);
            } else {
                $translation = new CategoryTranslation();

                $translation->setField($field);
                $translation->setLocale($locale);
                $translation->setContent($content);
                $translation->setObject($category);
                $this->em->persist($translation);
            }
        }

        return $category;
    }

    protected function deleteCategories()
    {
        foreach ($this->deleteCategoriesList as $deleteCategoryId) {
            $deleteCategory = $this->getCategoryById($deleteCategoryId);

            if ($deleteCategory) {
                $this->em->remove($deleteCategory);
                $this->deletedCategoriesCount++;
            }
        }

        $deleteCategories = $this->em->getRepository('DomainBusinessBundle:Category')->findBy(
            [
                'name' => 'DELETE',
            ]
        );

        foreach ($deleteCategories as $deleteCategory) {
            $this->em->remove($deleteCategory);
            $this->deletedCategoriesCount++;
        }
    }
}
