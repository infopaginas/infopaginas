<?php

namespace Domain\SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;

class LinkImageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('data:images:link');
        $this->setDescription('Link images to businesses');
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
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        $this->localePrimary = 'en';

        $this->totalTimer = 0;

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
            $itemCounter = 1;
            $this->totalTimer = microtime(true);
        }

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {
            // see http://www.doctrine-project.org/2009/08/07/doctrine2-batch-processing.html

            if ($this->withDebug) {
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

                    $businessProfile = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
                        ->findOneBy(['uid' => $itemId]);

                    if ($businessProfile) {
                        if ($this->withDebug) {
                            $itemCounter ++;
                            $output->writeln('Starts request item with id ' . $itemId);
                            $curlTimer = microtime(true);
                        }

                        $itemPrimary = $this->getCurlData($baseUrl . '/' . $itemId, $this->localePrimary);

                        if ($this->withDebug) {
                            $dbTimer = microtime(true);
                        }

                        $this->addBusinessProfileByApiData(
                            $itemPrimary,
                            $businessProfile
                        );

                        if ($this->withDebug) {
                            $curlInterval = $dbTimer - $curlTimer;
                            $dbInterval = microtime(true) - $dbTimer;

                            $pageCurlTime += $curlInterval;
                            $pageDbTime += $dbInterval;

                            $output->writeln('Curl Timer: ' . $curlInterval . '; DB Timer: ' . $dbInterval);
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip as not existed item with id ' . $itemId);
                        }
                    }
                }

                $this->em->flush();
                $this->em->clear();
            }
        }

        if ($this->withDebug) {
            $output->writeln('Total time: '.(microtime(true) - $this->totalTimer));
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

    private function addBusinessProfileByApiData($itemPrimary, $entity)
    {
        $business = $itemPrimary->business;

        $profile = $business->profile;

        $managerGallery = $this->getContainer()->get('domain_business.manager.business_gallery');
        $container = $this->getContainer();

        $pathWeb = $container->get('kernel')->getRootDir() .
            $container->getParameter('image_back_up_path') .
            SiteHelper::generateBusinessSubfolder($business->id);

        if ($profile->images) {
            foreach ($profile->images as $image) {
                if ($image->label == 'logo') {
                    $isLogo = true;
                } else {
                    $isLogo = false;
                }

                $path = $pathWeb . substr($image->image->url, strrpos($image->image->url, '/'));

                /*
                 * Migration page 2834
                 * [Imagine\Exception\RuntimeException]
                 * An image could not be created from the given input
                 */

                if (file_exists($path)) {
                    $managerGallery->createNewEntryFromLocalFile($entity, $path, $isLogo);
                } else {
                    $path = 'http://assets3.drxlive.com' . $image->image->url;

                    $managerGallery->createNewEntryFromRemoteFile($entity, $path, $isLogo);
                }
            }
        }
    }
}
