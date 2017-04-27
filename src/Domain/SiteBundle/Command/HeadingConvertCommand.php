<?php

namespace Domain\SiteBundle\Command;

use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Model\CategoryModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class HeadingConvertCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected $updatedCategoriesCount  = 0;
    protected $createdCategoriesCount  = 0;

    protected function configure()
    {
        $this->setName('data:heading-mapping:convert');
        $this->setDescription('Heading conversion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        // remove old categories
        $this->clearCategories();

        $this->em->flush();
        $this->em->clear();

        // add new categories
        $data = CategoryModel::getCategories();

        //create system categories
        $systemCategories = CategoryModel::getSystemCategories();

        $this->handleCategories($systemCategories);
        $this->handleCategories($data);

        $this->em->flush();

        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');
        $businessProfileManager->handleElasticSearchIndexRefresh();

        $output->writeln($this->updatedCategoriesCount . ' categories were updated');
        $output->writeln($this->createdCategoriesCount . ' categories were created');
    }

    /**
     * @param array $data
     */
    protected function handleCategories($data)
    {
        foreach ($data as $item) {
            $category = $this->getCategoryByCustomSlug([
                $item['slugEn'],
                $item['slugEs']
            ]);

            if ($category) {
                $this->updateCategory($category, $item);
                $this->updatedCategoriesCount++;
            } else {
                $this->createCategory($item);
                $this->createdCategoriesCount++;
            }

            $this->em->flush();
            $this->em->clear();
        }
    }

    /**
     * @param array $data
     *
     * @return Category
     */
    protected function createCategory($data)
    {
        $category = new Category();

        $category = $this->updateCategory($category, $data);
        $this->em->persist($category);

        return $category;
    }

    /**
     * @param array $slugs
     *
     * @return Category|null
     */
    protected function getCategoryByCustomSlug($slugs)
    {
        $category = $this->em->getRepository(Category::class)->getCategoryByOldSlugs($slugs);

        return $category;
    }

    /**
     * @param Category  $category
     * @param array     $data
     *
     * @return Category
     */
    protected function updateCategory($category, $data)
    {
        //workaround to set esp slug
        $category->setSlug(null);
        $category->setName($data['es']);

        $this->em->flush();

        if (!empty($data['slugEn'])) {
            $category->setSlugEn($data['slugEn']);
        }

        if (!empty($data['slugEs'])) {
            $category->setSlugEs($data['slugEs']);
        }

        if (!empty($data['code'])) {
            $category->setCode($data['code']);
        }

        $category->setName($data['en']);
        $category->setSearchTextEn($data['en']);
        $category->setSearchTextEs($data['es']);
        $category = $this->addCategoryTranslation($category, $data['es'], 'es');
        $category = $this->addCategoryTranslation($category, $data['en'], 'en');

        return $category;
    }

    /**
     * @param Category $category
     * @param string   $content
     * @param string   $locale
     *
     * @return Category
     */
    protected function addCategoryTranslation($category, $content, $locale)
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

    protected function clearCategories()
    {
        $this->em->createQueryBuilder()
            ->delete(Article::class, 'a')
            ->getQuery()
            ->execute()
        ;

        $this->em->createQueryBuilder()
            ->delete(Category::class, 'c')
            ->where('c.slug NOT IN (:systemSlugs)')
            ->andWhere('c.slugEn NOT IN (:systemSlugs) OR c.slugEn IS NULL')
            ->andWhere('c.slugEs NOT IN (:systemSlugs) OR c.slugEs IS NULL')
            ->setParameter('systemSlugs', Category::getSystemCategorySlugs())
            ->getQuery()
            ->execute()
        ;
    }
}
