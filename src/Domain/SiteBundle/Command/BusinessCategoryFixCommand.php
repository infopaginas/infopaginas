<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessCategoryFixCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:business-category:fix');
        $this->setDescription('Fix business category');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $businessProfiles = $this->em->getRepository(BusinessProfile::class)
            ->getBusinessProfilesWithoutCategoriesIterator();

        foreach ($businessProfiles as $row) {
            /* @var BusinessProfile $businessProfile */
            $businessProfile = $row[0];

            $this->handleDefaultCategory($businessProfile);

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
    }

    /**
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    protected function handleDefaultCategory($businessProfile)
    {
        if ($businessProfile->getCategories()->isEmpty()) {
            //add undefined categories
            $category = $this->getDefaultCategory();
            $businessProfile->addCategory($category);
        }

        return $businessProfile;
    }

    /**
     * @return Category|null
     */
    protected function getDefaultCategory()
    {
        $slug = Category::CATEGORY_UNDEFINED_SLUG;
        $entity = $this->em->getRepository(Category::class)->getCategoryBySlug($slug);

        return $entity;
    }
}
