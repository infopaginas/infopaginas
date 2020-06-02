<?php

namespace Oxa\DfpBundle\Command;

use Oxa\DfpBundle\Manager\DfpManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Google\AdsApi\AdManager\v201911\DateRangeType;

class DoubleClickDataSynchCommand extends ContainerAwareCommand
{
    private const REPORT_SYNCHRONIZATION_PERIOD_TODAY      = 'today';
    private const REPORT_SYNCHRONIZATION_PERIOD_YESTERDAY  = 'yesterday';

    protected function configure()
    {
        $this
            ->setName('ipgn:dfp:synch')
            ->setDescription('Synchronize dfp data')
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputOption(
                            'period',
                            'p',
                            InputOption::VALUE_OPTIONAL,
                            'Synchronization period, available options:
                            ' . self::REPORT_SYNCHRONIZATION_PERIOD_TODAY . ' - default,
                            ' . self::REPORT_SYNCHRONIZATION_PERIOD_YESTERDAY
                        ),
                    ]
                )
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $period = $this->getSynchronizationPeriod($input);

        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::DOUBLE_CLICK_SYNC, $logger::STATUS_START, 'execute:start, period: ' . $period);

        $output->writeln('Synchronize doubleClick orders..');
        $this->getDFPManager()->synchronizeOrderReport($period);
        $output->writeln('.. done!');

        $logger->addInfo($logger::DOUBLE_CLICK_SYNC, $logger::STATUS_END, 'execute:start, period: ' . $period);
    }

    /**
     * @param InputInterface  $input
     *
     * @return string
     */
    protected function getSynchronizationPeriod(InputInterface $input)
    {
        if ($input->getOption('period')) {
            switch ($input->getOption('period')) {
                case self::REPORT_SYNCHRONIZATION_PERIOD_TODAY:
                    $period = DateRangeType::TODAY;
                    break;
                case self::REPORT_SYNCHRONIZATION_PERIOD_YESTERDAY:
                    $period = DateRangeType::YESTERDAY;
                    break;
                default:
                    $period = DateRangeType::TODAY;
                    break;
            }
        } else {
            $period = DateRangeType::TODAY;
        }

        return $period;
    }

    /**
     * @return DfpManager
     */
    protected function getDFPManager() : DfpManager
    {
        return $this->getContainer()->get('oxa_dfp.manager');
    }
}
