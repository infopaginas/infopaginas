<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\SiteBundle\Logger\CronLogger;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class RefreshYoutubeVideoDataCommand extends ContainerAwareCommand
{
    private const YOUTUBE_VIDEO_DATA_REFRESH_LOCK = 'YOUTUBE_VIDEO_DATA_REFRESH.lock';
    private const FIELDS_TO_UPDATE = 'id,status';

    /* @var EntityManager $em */
    protected $em;

    /* @var OutputInterface $output */
    protected $output;

    protected function configure()
    {
        $this->setName('data:youtube-video:refresh');
        $this->setDescription('Refresh youtube video data');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::YOUTUBE_VIDEO_DATA_REFRESH, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $lockHandler = new LockHandler(self::YOUTUBE_VIDEO_DATA_REFRESH_LOCK);
        
        $this->refreshData();

        $lockHandler->release();
        
        $logger->addInfo(CronLogger::YOUTUBE_VIDEO_DATA_REFRESH, CronLogger::STATUS_START, CronLogger::MESSAGE_STOP);
    }
    
    private function refreshData(): void
    {
        $error = null;

        $youtubeVideoManager = $this->getContainer()->get('oxa.manager.video.youtube');
        $check = $youtubeVideoManager->handleUserTokenAuth();

        if ($check['error'] === false && $check['status']) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $ids = $this->em->getRepository(VideoMedia::class)->getAllYoutubeIds();
            $data = $youtubeVideoManager->getVideosInfo($ids, self::FIELDS_TO_UPDATE);

            if (!$data['error']) {
                $this->updateVideos($data);
            } else {
                $error = $data['error'];
            }
        } else {
            $error = $check['error'];
        }

        if ($error) {
            $admins = $this->em->getRepository(User::class)->findByRole('ROLE_ADMINISTRATOR');

            $this->getContainer()->get('domain_site.mailer')->sendYoutubeTokenErrorEmailMessage($error, $admins);
        }
    }

    private function updateVideos(array $data): void
    {
        $videoMediaIterator = $this->em->getRepository(VideoMedia::class)->getActiveVideosWithValidYoutubeIdIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($videoMediaIterator as $row) {
            /* @var $videoMedia VideoMedia */
            $videoMedia = $row[0];

            $videoMedia->setUploadStatus($data['data'][$videoMedia->getYoutubeId()] ?? VideoMedia::INVALID_YOUTUBE_ID_STATUS);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
    }
}
