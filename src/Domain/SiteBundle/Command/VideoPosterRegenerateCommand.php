<?php

namespace Domain\SiteBundle\Command;

use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class VideoPosterRegenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('data:video-poster:regenerate');
        $this->setDescription('Regenerates video poster');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('updateAll'),
            ])
        );
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $videoManager = $this->getContainer()->get('oxa.manager.video');

        if ($input->getOption('updateAll')) {
            $updateAll = true;
            $removeOldPoster = true;
        } else {
            $updateAll = false;
            $removeOldPoster = false;
        }

        $videos = $em->getRepository(VideoMedia::class)->getActiveVideoIterator($updateAll);

        foreach ($videos as $row) {
            /* @var $videoMedia VideoMedia */
            $videoMedia = $row[0];
            $output->writeln('Processing video: '. $videoMedia->getId());

            $status = $videoManager->regenerateVideoPoster($videoMedia, $removeOldPoster);

            if (!$status) {
                $output->writeln('Error: fail to create poster: '. $videoMedia->getId());
            } else {
                $output->writeln('Done');
            }

            $em->flush();
            $em->clear();
        }

        $em->flush();
        $em->clear();
    }
}
