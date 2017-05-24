<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Model\VideoMappingModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LinkVideoCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    /* @var OutputInterface $output */
    protected $output;

    protected $withDebug;
    protected $youtube;

    protected $allowedFileType = 'file';
    protected $allowedFileExtensions = [
        'mp4',
        'webm',
        'ogv',
    ];

    protected function configure()
    {
        $this->setName('data:video:link');
        $this->setDescription('Download video files');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('withDebug', 'd'),
                new InputOption('youtube', 'y'),
            ])
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        if ($input->getOption('youtube')) {
            $this->youtube = true;
        } else {
            $this->youtube = false;
        }

        $videoMapping = VideoMappingModel::getVideoMapping();

        $this->linkVideo($videoMapping);
    }

    protected function linkVideo($videoMapping)
    {
        $videoManager = $this->getContainer()->get('oxa.manager.video');

        $wrongFormats = [];
        $wrongFormatBusinesses = [];
        $itemCounter = 0;
        $wrongFormatCounter = 0;
        $businessNotFound = 0;
        $businessAlreadyHasVideo = 0;
        $noAssets = 0;

        $batchSize = 20;
        $i = 0;

        foreach ($videoMapping as $item) {
            $businessProfile = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
                ->findOneBy(['uid' => $item['uid']]);

            if (!empty($item['asset']) or ($this->youtube and $item['youtubeUrl'])) {
                if ($businessProfile) {
                    if ($businessProfile->getVideo()) {
                        if ($this->withDebug) {
                            $this->output->writeln('Skip as item already has video ' . $item['uid']);
                        }

                        $businessAlreadyHasVideo ++;
                    } else {
                        if ($this->withDebug) {
                            $this->output->writeln('Start request item number ' . $item['uid']);
                        }


                        try {
                            $iterator = new \RecursiveDirectoryIterator($this->generateLocalVideoPath($item['uid']));
                        } catch (\Exception $e) {
                            if ($this->withDebug) {
                                $this->output->writeln($e->getMessage());
                            }
                            continue;
                        }

                        foreach (new \RecursiveIteratorIterator($iterator) as $file) {

                            if ($file->getType() == $this->allowedFileType and
                                in_array($file->getExtension(), $this->allowedFileExtensions)
                            ) {
                                $uploadedFile = new UploadedFile(
                                    $file->getRealPath(),
                                    $file->getFileName(),
                                    mime_content_type($file->getRealPath()),
                                    $file->getSize(),
                                    null,
                                    true
                                );

                                try {
                                    $media = $videoManager->uploadLocalFile($uploadedFile);

                                    $media->setYoutubeSupport(false);
                                    $media->setYoutubeAction(null);
                                } catch (\Exception $e) {
                                    if ($this->withDebug) {
                                        $this->output->writeln(
                                            'Skip as not supported format ' . $item['uid'] . ' - ' . $e->getMessage()
                                        );
                                    }

                                    $wrongFormats[$e->getMessage()] = $e->getMessage();
                                    $wrongFormatBusinesses[$item['uid']] = $item['uid'];
                                    $wrongFormatCounter ++;

                                    continue;
                                }

                                if ($media) {
                                    $businessProfile->setVideo($media);
                                }

                                if ($this->withDebug) {
                                    $this->output->writeln(
                                        'Finish request item with id ' . $item['uid'] . '; Item count: ' . $itemCounter
                                    );
                                }

                                if (($i % $batchSize) === 0) {
                                    $this->em->flush();
                                    $this->em->clear();
                                }
                                $i ++;

                                $itemCounter ++;
                            }
                        }
                    }
                } else {
                    if ($this->withDebug) {
                        $this->output->writeln('Skip as not found item ' . $item['uid']);
                    }

                    $businessNotFound ++;
                }
            } else {
                if ($this->withDebug) {
                    $this->output->writeln('Skip as no asset ' . $item['uid']);
                }

                $noAssets ++;
            }
        }

        $this->em->flush();

        $this->output->writeln('Added videos: ' . $itemCounter);
        $this->output->writeln('Wrong formats video: ' . $wrongFormatCounter);
        $this->output->writeln('Businesses not found: ' . $businessNotFound);
        $this->output->writeln('Assets not found: ' . $noAssets);
        $this->output->writeln('Businesses already have video: ' . $businessAlreadyHasVideo);

        $this->output->writeln('Wrong format messages:');

        foreach ($wrongFormats as $error) {
            $this->output->writeln($error);
        }

        $this->output->writeln('Wrong format businesses: ');

        foreach ($wrongFormatBusinesses as $business) {
            $this->output->writeln($business);
        }
    }

    protected function generateLocalVideoPath($uid)
    {
        $path = $this->getBaseDownloadDir() . $uid . '/';

        return $path;
    }

    protected function getBaseDownloadDir()
    {
        $container = $this->getContainer();

        $path = $container->get('kernel')->getRootDir();

        if ($this->youtube) {
            $path .= $this->getContainer()->getParameter('youtube_back_up_path');
        } else {
            $path .= $this->getContainer()->getParameter('video_back_up_path');
        }

        return $path;
    }
}
