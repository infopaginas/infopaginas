<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Manager\BusinessGalleryManager;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;

class MigrationImageFixCommand extends ContainerAwareCommand
{
    const DEFAULT_LOCALE  = 'en';
    const IMAGE_TYPE_LOGO = 'logo';

    const API_BASE_URL    = 'http://infopaginas.drxlive.com/api/businesses';
    const API_MEDIA_URL   = 'http://assets3.drxlive.com';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var BusinessGalleryManager $galleryManager
     */
    protected $galleryManager;

    /**
     * @var bool $withDebug
     */
    protected $withDebug;

    protected function configure()
    {
        $this->setName('data:migration:image-fix');
        $this->setDescription('Add images to businesses');
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

        $this->galleryManager = $this->getContainer()->get('domain_business.manager.business_gallery');

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

                    if ($businessProfile and $businessProfile->getImages()->isEmpty()
                        and !$businessProfile->getLogo()
                    ) {
                        $itemPrimary = $this->getCurlData($this->getBusinessByPageUid($itemId), self::DEFAULT_LOCALE);

                        $this->updateBusinessImages($itemPrimary, $businessProfile);

                        if ($this->withDebug) {
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip item with id ' . $itemId);
                        }
                    }
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
     * @param mixed             $itemPrimary
     * @param BusinessProfile   $businessProfile
     */
    private function updateBusinessImages($itemPrimary, $businessProfile)
    {
        $business = $itemPrimary->business;
        $profile  = $business->profile;

        if ($profile->images) {
            $localPathWeb = $this->getBusinessLocalImageStoragePath($business->id);

            foreach ($profile->images as $image) {
                if ($image->label == self::IMAGE_TYPE_LOGO) {
                    $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO;
                } else {
                    $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES;
                }

                $localPath = $localPathWeb . substr($image->image->url, strrpos($image->image->url, '/'));

                if (file_exists($localPath)) {
                    $this->galleryManager->createNewEntryFromLocalFile($businessProfile, $context, $localPath);
                } else {
                    $remotePath = $this->getBusinessRemoteImageStoragePath($image->image->url);

                    $this->galleryManager->createNewEntryFromRemoteFile($businessProfile, $context, $remotePath);
                }
            }
        }
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
    private function getBusinessByPageUid($uid)
    {
        return self::API_BASE_URL . '/' . $uid;
    }

    /**
     * @param string $uid
     *
     * @return string
     */
    private function getBusinessLocalImageStoragePath($uid)
    {
        $path = $this->getLocalImageStoragePath() . SiteHelper::generateBusinessSubfolder($uid);

        return $path;
    }

    /**
     * @return string
     */
    private function getLocalImageStoragePath()
    {
        $container = $this->getContainer();

        $pathWeb = $container->get('kernel')->getRootDir() . $container->getParameter('image_back_up_path');

        return $pathWeb;
    }

    /**
     * @param string $imageUrl
     *
     * @return string
     */
    private function getBusinessRemoteImageStoragePath($imageUrl)
    {
        return self::API_MEDIA_URL . $imageUrl;
    }
}
