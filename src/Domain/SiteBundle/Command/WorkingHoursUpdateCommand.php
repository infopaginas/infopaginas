<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class WorkingHoursUpdateCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:working-hours:update');
        $this->setDescription('Update working hours');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::WORKING_HOURS_UPDATE, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateWorkingHours();

        $this->em->flush();
        $logger->addInfo(CronLogger::WORKING_HOURS_UPDATE, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);
    }

    protected function updateWorkingHours()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getActiveBusinessProfilesIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];
            $business->setWorkingHoursJson(DayOfWeekModel::getBusinessProfileWorkingHoursJson($business));

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i ++;
        }

        $this->em->flush();
    }
}
