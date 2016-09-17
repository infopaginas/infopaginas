<?php

namespace Oxa\DfpBundle\Command;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\DfpBundle\Manager\DoubleClickCompaniesManager;
use Oxa\DfpBundle\Manager\DoubleClickLineItemsManager;
use Oxa\DfpBundle\Manager\DoubleClickOrdersManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 11.09.16
 * Time: 15:05
 */
class DoubleClickDataSynchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "app/console")
            ->setName('ipgn:dfp:synch')
            // the short description shown while running "php app/console list"
            ->setDescription('Synchronize dfp data (better performance).')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to fetch doubleclick data & save to local db. Run each 2h");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Synch doubleclick companies..');
        $this->getDoubleClickCompaniesManager()->synchronizeBusinessProfilesDoubleClickCompanies();
        $output->writeln('.. done!');

        $output->writeln('Synch doubleclick orders..');
        $this->getDoubleClickOrdersManager()->synchronizeDoubleClickOrders();
        $output->writeln('.. done!');

        $output->writeln('Synch doubleclick line items..');
        $this->getDoubleClickLineItemsManager()->synchronizeDoubleClickLineItems();
        $output->writeln('.. done!');
    }

    protected function getDoubleClickLineItemsManager() : DoubleClickLineItemsManager
    {
        return $this->getContainer()->get('oxa_dfp.manager.doubleclick_line_items');
    }

    /**
     * @return DoubleClickOrdersManager
     */
    protected function getDoubleClickOrdersManager() : DoubleClickOrdersManager
    {
        return $this->getContainer()->get('oxa_dfp.manager.doubleclick_orders');
    }

    /**
     * @return DoubleClickCompaniesManager
     */
    protected function getDoubleClickCompaniesManager() : DoubleClickCompaniesManager
    {
        return $this->getContainer()->get('oxa_dfp.manager.doubleclick_companies');
    }
}