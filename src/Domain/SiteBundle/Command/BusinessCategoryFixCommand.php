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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $businessProfiles = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getActiveBusinessProfilesIterator();

        foreach ($businessProfiles as $row) {
            /* @var BusinessProfile $businessProfile */
            $businessProfile = $row[0];

            $this->handleBusinessCategories($businessProfile);

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
    }

    protected function handleBusinessCategories(BusinessProfile $businessProfile)
    {
        $newCategories     = [];
        $parentCategoryIds = [];
        $currentCategories = [];

        $oldCategories = $businessProfile->getCategories();

        foreach ($oldCategories as $category) {
            $currentCategories[$category->getLvl()][$category->getId()] = $category;
        }

        if (!empty($currentCategories[Category::CATEGORY_LEVEL_3])) {
            foreach ($currentCategories[Category::CATEGORY_LEVEL_3] as $key => $category3) {
                $parent = $category3->getParent();

                if ($parent) {
                    if (empty($currentCategories[Category::CATEGORY_LEVEL_2][$parent->getId()])) {
                        $currentCategories[Category::CATEGORY_LEVEL_2][$parent->getId()] = $parent;
                    }
                } else {
                    unset($currentCategories[Category::CATEGORY_LEVEL_3][$key]);
                    $businessProfile->removeCategory($category3);
                }
            }
        }

        if (!empty($currentCategories[Category::CATEGORY_LEVEL_2])) {
            foreach ($currentCategories[Category::CATEGORY_LEVEL_2] as $key => $category2) {
                $parent = $category2->getParent();

                if ($parent) {
                    if (empty($currentCategories[Category::CATEGORY_LEVEL_1][$parent->getId()])) {
                        $currentCategories[Category::CATEGORY_LEVEL_1][$parent->getId()] = $parent;
                    }
                } else {
                    unset($currentCategories[Category::CATEGORY_LEVEL_2][$key]);
                    $businessProfile->removeCategory($category2);
                }
            }
        }

        if (!empty($currentCategories[Category::CATEGORY_LEVEL_1])) {
            $first = true;
            foreach ($currentCategories[Category::CATEGORY_LEVEL_1] as $key => $category) {
                if ($first) {
                    $first = false;
                    $category1 = $category;
                } else {
                    unset($currentCategories[Category::CATEGORY_LEVEL_1][$key]);
                    $businessProfile->removeCategory($category);
                }
            }
        } else {
            $category1 = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(
                [
                    'slug' => 'unclassified',
                ]
            );
        }

        $parentCategoryIds[] = $category1->getId();
        $newCategories[$category1->getId()] = $category1;

        if (!empty($currentCategories[Category::CATEGORY_LEVEL_2])) {
            foreach ($currentCategories[Category::CATEGORY_LEVEL_2] as $category2) {
                if (in_array($category2->getParent()->getId(), $parentCategoryIds)) {
                    $newCategories[$category2->getId()] = $category2;
                    $parentCategoryIds[] = $category2->getId();
                } elseif ($businessProfile->getCategories()->contains($category2)) {
                    $businessProfile->removeCategory($category2);
                }
            }
        }

        if (!empty($currentCategories[Category::CATEGORY_LEVEL_3])) {
            foreach ($currentCategories[Category::CATEGORY_LEVEL_3] as $category3) {
                if (in_array($category3->getParent()->getId(), $parentCategoryIds)) {
                    $newCategories[$category3->getId()] = $category3;
                    $parentCategoryIds[] = $category3->getId();
                } elseif ($businessProfile->getCategories()->contains($category3)) {
                    $businessProfile->removeCategory($category3);
                }
            }
        }

        foreach ($newCategories as $category) {
            if (!$businessProfile->getCategories()->contains($category)) {
                $businessProfile->addCategory($category);
            }
        }
    }
}
