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

class DownloadVideoCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected $withDebug;

    protected function configure()
    {
        $this->setName('data:video:download');
        $this->setDescription('Download video files');
        $this->setDefinition(
            new InputDefinition(array(
                new InputOption('withDebug', 'd'),
            ))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        $videoMapping = VideoMappingModel::getVideoMapping();
        $videoAdditionalMapping = VideoMappingModel::getAdditionalVideoMapping();

        $counter = $this->downloadVideo($videoMapping);
        $counter += $this->downloadVideo($videoAdditionalMapping);

        $output->writeln('Downloaded ' . $counter);
    }

    protected function downloadVideo($data)
    {
        $counter = 0;

        foreach ($data as $item) {
            if (!empty($item['uid']) and !empty($item['asset'])) {
                $downloadedPath = $this->generateLocalVideoPath($item['uid']);

                $tmpFile = tempnam('/tmp', 'glo');
                $filename = $this->getOriginalFilename();

                $path = $downloadedPath . $filename;

                $urlPath = $item['asset'];

                file_put_contents($tmpFile, $urlPath);

                shell_exec('wget -q -c -P ' . $path . ' -i ' . $tmpFile);

                $counter ++;
            }
        }

        return $counter;
    }

    protected function getOriginalFilename()
    {
        return uniqid();
    }

    protected function generateLocalVideoPath($uid)
    {
        $path = $this->getBaseDownloadDir() . $uid . '/';

        return $path;
    }

    protected function getBaseDownloadDir()
    {
        $container = $this->getContainer();
        return $container->get('kernel')->getRootDir() .
        $container->getParameter('video_back_up_path');
    }
}
