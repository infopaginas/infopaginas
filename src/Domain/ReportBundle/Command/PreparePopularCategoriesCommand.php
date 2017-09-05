<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\CategoryReportManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PreparePopularCategoriesCommand
 * @package Domain\ReportBundle\Command
 */
class PreparePopularCategoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('domain:popular-category:update')
            ->setDescription('Update popular category list')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::MONGO_POPULAR_CATEGORIES, $logger::STATUS_START, 'execute:start');

        $output->writeln('Start aggregation...');

        $output->writeln('Process popular categories');

        $this->getCategoryReportManager()->updatePopularCategories();

        $logger->addInfo(
            $logger::MONGO_POPULAR_CATEGORIES,
            $logger::STATUS_IN_PROGRESS,
            'execute:Process popular category'
        );

        $output->writeln('done');
        $logger->addInfo($logger::MONGO_POPULAR_CATEGORIES, $logger::STATUS_END, 'execute:stop');
    }

    /**
     * @return CategoryReportManager
     */
    protected function getCategoryReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.category_report_manager');
    }
}
