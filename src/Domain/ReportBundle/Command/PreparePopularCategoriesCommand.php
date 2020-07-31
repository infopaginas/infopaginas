<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\SemaphoreStore;

/**
 * Class PreparePopularCategoriesCommand
 * @package Domain\ReportBundle\Command
 */
class PreparePopularCategoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName(CronLogger::MONGO_POPULAR_CATEGORIES)
            ->setDescription('Update popular category list')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $store   = new SemaphoreStore();
        $factory = new Factory($store);
        $lock    = $factory->createLock(CronLogger::MONGO_POPULAR_CATEGORIES);

        if ($lock->acquire()) {
            $logger = $this->getContainer()->get('domain_site.cron.logger');
            $logger->addInfo(CronLogger::MONGO_POPULAR_CATEGORIES, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

            $output->writeln('Start aggregation...');

            $output->writeln('Process popular categories');

            $this->getCategoryOverviewReportManager()->updatePopularCategories();

            $logger->addInfo(
                CronLogger::MONGO_POPULAR_CATEGORIES,
                CronLogger::STATUS_IN_PROGRESS,
                'execute:Process popular category'
            );

            $output->writeln('done');
            $logger->addInfo(CronLogger::MONGO_POPULAR_CATEGORIES, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);

            $lock->release();
        } else {
            return $output->writeln('Command is locked by another process');
        }

        return 0;
    }

    /**
     * @return CategoryOverviewReportManager
     */
    protected function getCategoryOverviewReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.category_overview_report_manager');
    }
}
