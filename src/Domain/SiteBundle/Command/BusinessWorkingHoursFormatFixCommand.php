<?php

namespace Domain\SiteBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessWorkingHoursFormatFixCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    /* @var array $skipIds */
    protected $skipIds = [];

    protected function configure()
    {
        $this->setName('data:business-hours:fix');
        $this->setDescription('Fix business working hours');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateBusinessWorkingHours();
    }

    protected function updateBusinessWorkingHours()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getAllBusinessProfilesIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];
            $this->skipIds = [];

            $workingDays = $business->getCollectionWorkingHours();

            foreach ($workingDays as $workingHours) {
                /* @var BusinessProfileWorkingHour $workingHours */
                $workingHours->setDays($this->getRealDays($workingHours->getDay()));
            }

            foreach ($workingDays as $workingHours) {
                /* @var BusinessProfileWorkingHour $workingHours */
                $this->mergeWorkingDays($workingDays, $workingHours);
            }

            $workingHours = DayOfWeekModel::getBusinessProfileWorkingHoursJson($business);

            if ($workingHours != $business->getWorkingHoursJson()) {
                $business->setWorkingHoursJson($workingHours);
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i ++;
        }

        $this->em->flush();
    }

    /**
     * @param ArrayCollection               $workingDays
     * @param BusinessProfileWorkingHour    $workingHours
     *
     * @return bool
     */
    protected function mergeWorkingDays($workingDays, $workingHours)
    {
        foreach ($workingDays as $item) {
            /* @var BusinessProfileWorkingHour $item */

            if (($item->getTimeStart() == $workingHours->getTimeStart() and
                $item->getTimeEnd() == $workingHours->getTimeEnd() and $item->getId() != $workingHours->getId() and
                !in_array($workingHours->getId(), $this->skipIds)) or
                ($item->getOpenAllTime() and $workingHours->getOpenAllTime())
            ) {
                $days = $workingHours->getDays();
                $itemDay = $item->getDays();

                $days = array_unique(array_merge($days, $itemDay));

                $workingHours->setDays($days);

                $this->skipIds[] = $item->getId();

                $workingDays->removeElement($item);
            }
        }
    }

    protected function getRealDays($day)
    {
        switch ($day) {
            case DayOfWeekModel::CODE_WEEKDAY:
                $dayList = DayOfWeekModel::getWeekday();
                break;
            case DayOfWeekModel::CODE_WEEKEND:
                $dayList = DayOfWeekModel::getWeekend();
                break;
            default:
                $dayList = [
                    $day,
                ];

                break;
        }

        return $dayList;
    }

}
