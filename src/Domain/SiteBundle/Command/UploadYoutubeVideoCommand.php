<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\SiteBundle\Mailer\Mailer;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Manager\VideoMediaManager;
use Oxa\VideoBundle\Manager\YoutubeManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadYoutubeVideoCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    /* @var OutputInterface $output */
    protected $output;

    /* @var YoutubeManager $youtubeVideoManager */
    private $youtubeVideoManager;

    /* @var Mailer $mailer */
    private $mailer;

    /* @var bool $withDebug */
    protected $withDebug;

    protected function configure()
    {
        $this->setName('data:youtube-video:upload');
        $this->setDescription('Upload youtube video files');
        $this->setDefinition(
            new InputDefinition(array(
                new InputOption('withDebug', 'd'),
            ))
        );
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::YOUTUBE_UPLOAD, $logger::STATUS_START, 'execute:start');

        $this->youtubeVideoManager = $this->getContainer()->get('oxa.manager.video.youtube');
        $this->mailer              = $this->getContainer()->get('domain_site.mailer');
        $this->em                  = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->output = $output;

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        $check = $this->youtubeVideoManager->handleUserTokenAuth();

        if ($check['error'] === false and $check['status']) {
            $resultUpload = $this->uploadVideo();
            $currentError = $resultUpload['error'];

            if ($resultUpload['error'] === false) {
                $resultUpdate = $this->updateVideo();
                $currentError = $resultUpdate['error'];

                if ($resultUpdate['error'] === false) {
                    $resultRemove = $this->removeVideo();
                    $currentError = $resultRemove['error'];
                }
            }
        } else {
            $currentError = $check['error'];
        }

        if ($currentError !== false) {
            $admins = $this->em->getRepository(User::class)->findByRole('ROLE_ADMINISTRATOR');

            $this->mailer->sendYoutubeTokenErrorEmailMessage($currentError, $admins);
        }
        $logger->addInfo($logger::YOUTUBE_UPLOAD, $logger::STATUS_END, 'execute:stop');
    }

    /**
     * @return array
     */
    protected function uploadVideo()
    {
        $error  = false;
        $status = true;

        $videoMapping = $this->em->getRepository(VideoMedia::class)
            ->getVideoForYoutubeActionIterator(VideoMedia::YOUTUBE_ACTION_ADD);

        $batchSize = 20;
        $i = 0;

        foreach ($videoMapping as $row) {
            /* @var $videoMedia VideoMedia */
            $videoMedia = $row[0];

            if ($videoMedia and !$videoMedia->getBusinessProfiles()->isEmpty()) {
                $data = $this->youtubeVideoManager->uploadMedia($videoMedia);

                if ($data['error'] == YoutubeManager::ERROR_ASSET_NOT_EXIST) {
                    $videoMedia->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ERROR);
                    $videoMedia->setYoutubeSupport(false);
                } elseif ($data['error'] !== false) {
                    $error = $data['error'];
                    $status = false;
                    break;
                } elseif ($data['youtubeId']) {
                    $videoMedia->setYoutubeId($data['youtubeId']);
                    $videoMedia->setYoutubeAction(null);
                } else {
                    $videoMedia->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ERROR);
                    $videoMedia->setYoutubeSupport(false);
                }

                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $i ++;
            } else {
                $videoMedia->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ERROR);
                $videoMedia->setYoutubeSupport(false);
            }
        }

        $this->em->flush();

        return [
            'status' => $status,
            'error'  => $error,
        ];
    }

    /**
     * @return array
     */
    protected function updateVideo()
    {
        $error  = false;
        $status = true;

        $videoMapping = $this->em->getRepository(VideoMedia::class)
            ->getVideoForYoutubeActionIterator(VideoMedia::YOUTUBE_ACTION_UPDATE);

        $batchSize = 20;
        $i = 0;

        foreach ($videoMapping as $row) {
            /* @var $videoMedia VideoMedia */
            $videoMedia = $row[0];

            if ($videoMedia and !$videoMedia->getBusinessProfiles()->isEmpty()) {
                $response = $this->youtubeVideoManager->updateMedia($videoMedia);

                $youtubeError = $response['error'];

                if ($youtubeError == YoutubeManager::ERROR_NOT_FOUND) {
                    $videoMedia->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ERROR);
                    $videoMedia->setYoutubeSupport(false);
                } elseif ($youtubeError !== false) {
                    $error = $youtubeError;
                    $status = false;
                    break;
                } else {
                    $videoMedia->setYoutubeAction(null);
                }

                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $i ++;
            }
        }

        $this->em->flush();

        return [
            'status' => $status,
            'error'  => $error,
        ];
    }

    /**
     * @return array
     */
    protected function removeVideo()
    {
        $error  = false;
        $status = true;

        $videoMapping = $this->em->getRepository(VideoMedia::class)
            ->getVideoForYoutubeActionIterator(VideoMedia::YOUTUBE_ACTION_REMOVE);

        $batchSize = 20;
        $i = 0;

        foreach ($videoMapping as $row) {
            /* @var $videoMedia VideoMedia */
            $videoMedia = $row[0];

            if ($videoMedia->getBusinessProfiles()->isEmpty()) {
                $response = $this->youtubeVideoManager->removeMedia($videoMedia);

                $youtubeError = $response['error'];

                if ($youtubeError == YoutubeManager::ERROR_NOT_FOUND) {
                    $this->em->remove($videoMedia);
                } elseif ($youtubeError !== false) {
                    $error  = $youtubeError;
                    $status = false;
                    break;
                } else {
                    $this->em->remove($videoMedia);
                }

                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $i ++;
            } else {
                $videoMedia->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_UPDATE);
            }
        }

        $this->em->flush();

        return [
            'status' => $status,
            'error'  => $error,
        ];
    }
}
