<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class VideoConverterCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    const VIDEO_CONVERT_LOCK = 'VIDEO_CONVERT.lock';

    protected function configure()
    {
        $this->setName('data:video:convert');
        $this->setDescription('Converting video files');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lockHandler = new LockHandler(self::VIDEO_CONVERT_LOCK);

        if (!$lockHandler->lock()) {
            return $output->writeln('Command is locked by another process');
        }

        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::VIDEO_CONVERT, $logger::STATUS_START, 'execute:start');
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $videoMapping = $this->em->getRepository(VideoMedia::class)->getConvertVideos(VideoMedia::VIDEO_STATUS_PENDING);
        $videoManager = $this->getContainer()->get('oxa.manager.video');

        $batchSize = 20;
        $i = 0;

        foreach ($videoMapping as $row) {
            /* @var $videoMedia VideoMedia */
            $videoMedia = $row[0];
            $output->writeln('Video id '. $videoMedia->getId() . ' is converting now');
            $videoMedia = $videoManager->convertVideoMedia($videoMedia);
            $this->em->persist($videoMedia);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $output->writeln('Flushed');
                $this->em->clear();
            }

            $i ++;
        }

        $logger->addInfo($logger::VIDEO_CONVERT, $logger::STATUS_END, 'execute:stop');

        $this->em->flush();
        $this->em->clear();

        $lockHandler->release();
    }
}