<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Model\CategoryModel;
use Domain\BusinessBundle\Util\SlugUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;

class MigrationHeadingFixCommand extends ContainerAwareCommand
{
    const DEFAULT_LOCALE  = 'en';

    const API_BASE_URL    = 'http://infopaginas.drxlive.com/api/businesses';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var bool $withDebug
     */
    protected $withDebug;

    /**
     * @var array $categoryEnMergeMapping
     */
    protected $categoryEnMergeMapping;

    /**
     * @var array $categoryEsMergeMapping
     */
    protected $categoryEsMergeMapping;

    protected function configure()
    {
        $this->setName('data:migration:category-headings');
        $this->setDescription('Update category/headings');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('withDebug', 'd'),
                new InputOption('pageCountLimit', 'pl', InputOption::VALUE_OPTIONAL),
                new InputOption('pageStart', 'ps', InputOption::VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        $this->categoryEnMergeMapping = CategoryModel::getCategoryEnMergeMapping();
        $this->categoryEsMergeMapping = CategoryModel::getCategoryEsMergeMapping();

        if ($input->getOption('pageStart')) {
            $pageStart = $input->getOption('pageStart');
        } else {
            $pageStart = 1;
        }

        if ($input->getOption('pageCountLimit')) {
            $pageCountLimit = $input->getOption('pageCountLimit');
        } else {
            $pageCountLimit = 1;
        }

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {
            if ($this->withDebug) {
                $output->writeln('Start request page number ' . $page);
            }

            $data = $this->getCurlData($this->getBusinessesByPageUrl($page), self::DEFAULT_LOCALE);

            if ($data) {
                foreach ($data as $item) {
                    $itemId = $item->_id;

                    /* @var BusinessProfile $businessProfile */
                    $businessProfile = $this->em->getRepository(BusinessProfile::class)->findOneBy(
                        [
                            'uid' => $itemId,
                        ]
                    );

                    if ($businessProfile) {
                        $data = $this->getCurlData($this->getBusinessByUid($itemId), self::DEFAULT_LOCALE);

                        $businessProfile = $this->removeOldCategories($businessProfile);

                        $this->updateBusinessCategories($data, $businessProfile);

                        $this->handleDefaultCategory($businessProfile);

                        if ($this->withDebug) {
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip item with id ' . $itemId);
                        }
                    }

                    $this->em->flush();
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        if ($this->withDebug) {
            $output->writeln('Finish requests');
        }
    }

    /**
     * @param string $url
     * @param string $locale
     *
     * @return mixed
     */
    private function getCurlData($url, $locale)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Token token=coh6fQgxVkK989OTnVoP3w",
            "Accept-Language: " . $locale,
        ]);

        $htmlContent = curl_exec($ch);

        if ($htmlContent) {
            $curlData = json_decode($htmlContent);

            if (!$curlData or !empty($curlData->error)) {
                $this->output->writeln('Error occured: ' . json_encode($curlData));

                // wait 10 secs
                sleep(10);

                return $this->getCurlData($url, $locale);
            } else {
                return $curlData;
            }
        } else {
            return null;
        }
    }

    /**
     * @param mixed             $item
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    private function updateBusinessCategories($item, $businessProfile)
    {
        $headings = $item->business->profile->headings;

        if ($headings) {
            //add categories
            foreach ($headings as $value) {
                $category = $this->getCategory($value);

                if ($category and !$businessProfile->getCategories()->contains($category)) {
                    $businessProfile->addCategory($category);
                }
            }
        }

        return $businessProfile;
    }

    /**
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    private function removeOldCategories($businessProfile)
    {
        $categories = $businessProfile->getCategories();

        foreach ($categories as $category) {
            $businessProfile->removeCategory($category);
        }

        return $businessProfile;
    }

    /**
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    private function handleDefaultCategory($businessProfile)
    {
        if ($businessProfile->getCategories()->isEmpty()) {
            //add undefined categories
            $category = $this->getDefaultCategory();
            $businessProfile->addCategory($category);
        }

        return $businessProfile;
    }

    private function getCategory($name)
    {
        $slug = SlugUtil::convertSlug($name);

        $entity = $this->em->getRepository(Category::class)->getCategoryByOldSlugs($slug);

        if (!$entity) {
            $newSlug = '';

            if (!empty($this->categoryEnMergeMapping[$slug])) {
                $newSlug = $this->categoryEnMergeMapping[$slug];
            } elseif (!empty($this->categoryEsMergeMapping[$slug])) {
                $newSlug = $this->categoryEsMergeMapping[$slug];
            }

            if ($newSlug) {
                $entity = $this->em->getRepository(Category::class)->getCategoryByOldSlugs($newSlug);
            }
        }

        return $entity;
    }

    private function getDefaultCategory()
    {
        $slug = Category::CATEGORY_UNDEFINED_SLUG;
        $entity = $this->em->getRepository('DomainBusinessBundle:Category')->getCategoryBySlug($slug);

        return $entity;
    }

    /**
     * @param int $pageNumber
     *
     * @return string
     */
    private function getBusinessesByPageUrl($pageNumber)
    {
        return self::API_BASE_URL . '?page=' . $pageNumber;
    }

    /**
     * @param string $uid
     *
     * @return string
     */
    private function getBusinessByUid($uid)
    {
        return self::API_BASE_URL . '/' . $uid;
    }
}
