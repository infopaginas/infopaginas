<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

class MigrationLogDataCommand extends ContainerAwareCommand
{
    const DIRECTORY = '/../web/uploads/legacyLogs/';

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /* @var EntityManager $em */
    protected $em;

    /* @var OutputInterface $output */
    protected $output;

    /* @var bool $withDebug */
    protected $withDebug;

    /* @var string $allowedFileType */
    protected $allowedFileType = 'file';

    /* @var array $allowedFileExtensions */
    protected $allowedFileExtensions = [
        'csv',
    ];

    protected function configure()
    {
        $this->setName('data:log:migrate-old');
        $this->setDescription('Migrate old log data');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('withDebug', 'd'),
            ])
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->mongoDbManager = $this->getContainer()->get('mongodb.manager');
        $this->output = $output;

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        $this->migrateLogData();
    }

    protected function migrateLogData()
    {
        try {
            $iterator = new \RecursiveDirectoryIterator($this->getBaseDownloadDir());
        } catch (\Exception $e) {
            if ($this->withDebug) {
                $this->output->writeln($e->getMessage());
            }

            return;
        }

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            $extension = $file->getExtension();

            if ($file->getType() == $this->allowedFileType and in_array($extension, $this->allowedFileExtensions)) {
                $path = $file->getRealPath();
                $dir  = $this->getBaseDir();

                if ($this->withDebug) {
                    $this->output->writeln('Process file: ' . $path);
                }

                shell_exec(
                    'cd ' . $dir . ' && php app/console data:log:migrate-old-file --path="' . $path . '" -e=prod'
                );

                if ($this->withDebug) {
                    $this->output->writeln('Done');
                }
            }
        }
    }

    /**
     * @return string
     */
    protected function getBaseDownloadDir()
    {
        return $this->getContainer()->get('kernel')->getRootDir() . self::DIRECTORY;
    }

    /**
     * @return string
     */
    protected function getBaseDir()
    {
        return $this->getContainer()->get('kernel')->getRootDir() . '/../';
    }
}
