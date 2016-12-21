<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Tag;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Domain\BusinessBundle\Entity\Translation\TagTranslation;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\MenuBundle\Model\MenuModel;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DownloadCommand extends ContainerAwareCommand
{
    const SYSTEM_CATEGORY_SEPARATOR = ' / ';
    const CATEGORY_NAME_MAX_LENGTH = 250;

    public $configureTime = 0;
    public $downloadTime  = 0;
    public $moveTime      = 0;

    protected function configure()
    {
        $this->setName('data:migration:images');
        $this->setDescription('Migrate all site images data');
        $this->setDefinition(
            new InputDefinition(array(
                new InputOption('withDebug', 'd'),
                new InputOption('pageCountLimit', 'pl', InputOption::VALUE_OPTIONAL),
                new InputOption('pageStart', 'ps', InputOption::VALUE_OPTIONAL),
            ))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->localePrimary = 'en';
        $this->localeSecond = 'es';
        
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

        $baseUrl = 'http://infopaginas.drxlive.com/api/businesses';

        if ($this->withDebug) {
            $pageCurlTime = 0;
            $pageDbTime = 0;
            $timeStart = microtime(true);
            $itemCounter = 1;
        }

        $this->createDownloadDirectory();

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {
            if ($this->withDebug) {
                if ($page != $pageStart) {
                    $output->writeln('');
                }
                $output->writeln(
                    'Start request page number ' . $page .
                    '; Curl Timer: ' . ($pageCurlTime/$itemCounter) .
                    '; DB Timer: ' . ($pageDbTime/$itemCounter)
                );
            }

            $data = $this->getCurlData($baseUrl . '?page=' . $page, $this->localePrimary);

            if ($data) {
                foreach ($data as $item) {
                    $itemId = $item->_id;

                    if ($this->withDebug) {
                        $itemCounter ++;
                        $output->writeln('Starts request item with id ' . $itemId);
                        $curlTimer = microtime(true);
                    }

                    $itemPrimary = $this->getCurlData($baseUrl . '/' . $itemId, $this->localePrimary);

                    if ($this->withDebug) {
                        $dbTimer = microtime(true);
                    }

                    $this->addBusinessProfileByApiData($itemPrimary);

                    if ($this->withDebug) {
                        $curlInterval = $dbTimer - $curlTimer;
                        $dbInterval = microtime(true) - $dbTimer;

                        $pageCurlTime += $curlInterval;
                        $pageDbTime += $dbInterval;

                        $output->writeln('Curl Timer: ' . $curlInterval . '; DB Timer: ' . $dbInterval);
                        $output->writeln('Finish request item with id ' . $itemId);
                    }
                }
            }
        }

        if ($this->withDebug) {
            $output->writeln('');
            $output->writeln('Total time: ' . (microtime(true) - $timeStart));

//$this->output->writeln('Conf: ' . $this->configureTime . ';    Load: ' . $this->downloadTime . ';    Move: ' . $this->moveTime);

            $output->writeln('Finish requests');
        }
    }

    private function getCurlData($url, $locale)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Token token=coh6fQgxVkK989OTnVoP3w",
            "Accept-Language: " . $locale
        ));

        $htmlContent = curl_exec($ch);

        if ($htmlContent) {
            $curlData = json_decode($htmlContent);

            if (!$curlData or !empty($curlData->error)) {
                //todo - set timeout and retry

                $this->output->writeln('Error occured: ' . json_encode($curlData));

                // wait 10 secs
                sleep(10);

                //todo - add counter for retry
                return $this->getCurlData($url, $locale);
            } else {
                return $curlData;
            }
        } else {
            return null;
        }
    }

    private function addBusinessProfileByApiData($itemPrimary)
    {
        $business = $itemPrimary->business;
        $profile = $business->profile;

        $timer = microtime(true);
        // process assigned items
        if ($profile->images) {
            $downloadedPath = $this->generateLocalImagePath($business->id);

            $tmpFile = tempnam('/tmp', 'glo');
            $content = '';
            foreach ($profile->images as $image) {
                $filename = $this->getOriginalFilename($image->image->url);

                $path = $downloadedPath . $filename;
                if (!file_exists($path)) {
                    $urlPath = 'http://assets3.drxlive.com' . $image->image->url;
                    $content .= $urlPath . "\n";
//  Save next string for possible reliable download
//                    $this->downloadImageFromUrl($urlPath, $downloadedPath, $filename);
                }
            }
            if ($content) {
                file_put_contents($tmpFile, $content);

                shell_exec('wget -q -c -P ' . $downloadedPath . ' -i ' . $tmpFile);

            }
        }
        $this->output->writeln('Loaded images for: ' . (microtime(true) - $timer));
    }

    protected function getBaseDownloadDir()
    {
        $container = $this->getContainer();
        return $container->get('kernel')->getRootDir() .
                $container->getParameter('image_back_up_path');
    }

    protected function generateLocalImagePath($businessId)
    {
        $path = $this->getBaseDownloadDir();
        $subdirs = explode(DIRECTORY_SEPARATOR, SiteHelper::generateBusinessSubfolder($businessId));
        foreach ($subdirs as $dir) {
            $path .= $dir . DIRECTORY_SEPARATOR;
            if (!file_exists($path)) {
                mkdir($path);
            }
        }

        return $path;
    }

    protected function createDownloadDirectory()
    {
        $basePath = $this->getBaseDownloadDir();
        if (!file_exists($basePath)) {
            mkdir($basePath);
        }
        return true;
    }
    
    protected function downloadImageFromUrl($url, $downloadedPath, $filename)
    {
        $configTimer = $downloadTimer = $moveTimer = 0;
        $headers = SiteHelper::checkUrlExistence($url);

        if ($headers && in_array($headers['content_type'], SiteHelper::$imageContentTypes) && exif_imagetype($url)) {
            touch($downloadedPath . $filename);
            if (!file_exists($downloadedPath . $filename)) {
                throw new \Exception(self::CANT_CREATE_TEMP_FILE_ERROR_MESSAGE);
            }

            file_put_contents($downloadedPath . $filename, file_get_contents($url));
            return true;
        } else {
            return false;
        }

    }

    protected function createDirectory($path)
    {
        $container = $this->getContainer();
        $path = $container->get('kernel')->getRootDir() .
                $container->getParameter('image_back_up_path');

    }

    protected function getOriginalFilename($url)
    {
        return substr($url, strrpos($url, '/'));
    }

}
