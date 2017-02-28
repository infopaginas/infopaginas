<?php

namespace Domain\SiteBundle\Command;

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

class WorkingHoursConvertCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:working-hours:convert');
        $this->setDescription('working hours conversion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $businessesWithTextWorkingHours = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getBusinessesWithTextWorkingHoursIterator();

        foreach ($businessesWithTextWorkingHours as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];

            $data = $this->convertWorkingHours($business->getWorkingHours());

            if ($data) {
                $this->createWorkingHours($data, $business);

                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
    }

    protected function createWorkingHours($data, BusinessProfile $business)
    {
        $oldWorkingHours = $business->getCollectionWorkingHours();

        foreach ($oldWorkingHours as $oldWorkingHour) {
            $business->removeCollectionWorkingHour($oldWorkingHour);
            $this->em->remove($oldWorkingHour);
        }

        foreach ($data as $item) {
            if (count($item['days'] == 2)) {
                $day = '';

                if (in_array(DayOfWeekModel::CODE_MONDAY, $item['days']) and
                    in_array(DayOfWeekModel::CODE_FRIDAY, $item['days'])
                ) {
                    $day = DayOfWeekModel::CODE_WEEKDAY;
                } elseif (in_array(DayOfWeekModel::CODE_SATURDAY, $item['days']) and
                    in_array(DayOfWeekModel::CODE_SUNDAY, $item['days'])
                ) {
                    $day = DayOfWeekModel::CODE_WEEKEND;
                }

                if ($day) {
                    $this->addWorkingHours($day, $item, $business);
                    continue;
                }
            }

            foreach ($item['days'] as $day) {
                $this->addWorkingHours($day, $item, $business);
            }
        }
    }

    protected function addWorkingHours($day, $item, BusinessProfile $business)
    {
        $workingHour = new BusinessProfileWorkingHour();

        $workingHour->setDay($day);
        $workingHour->setTimeStart($item['open']);
        $workingHour->setTimeEnd($item['close']);
        $workingHour->setOpenAllTime(false);

        $workingHour->setBusinessProfile($business);

        $this->em->persist($workingHour);

        $business->addCollectionWorkingHour($workingHour);
    }

    protected function convertWorkingHours($text)
    {
        $data = [];

        $result = $this->parseWorkingHours($text);

        if ($result) {
            $data[] = $result;
        }

        if (!$data) {
            $dataItem = $this->explodeByArray($this->getLineDelimiters(), $text);

            foreach ($dataItem as $textItem) {
                $result = $this->parseWorkingHours($textItem);

                if ($result) {
                    $data[] = $result;
                }
            }
        }

        if (!$data) {
            $dataItem = $this->explodeByArray($this->getLineDelimiters(), $text);

            foreach ($dataItem as $key => $textItem) {
                if (!empty($dataItem[$key + 1])) {
                    $rawDays = mb_strtolower($textItem);
                    $rawHours = mb_strtolower($dataItem[$key + 1]);

                    $dayOfWeek = $this->getDayOfWeekMapping();

                    $result = $this->checkRawData($dayOfWeek, $rawDays, $rawHours);

                    if ($result) {
                        $data[] = $result;
                    }
                }
            }
        }

        return $data;
    }

    protected function checkRawData($dayOfWeek, $rawDays, $rawHours)
    {
        $data = [];
        $openDays = [];

        foreach ($dayOfWeek as $key => $day) {
            if (strpos($rawDays, mb_strtolower($key)) !== false) {
                $openDays[$day] = $day;
            }
        }

        if ($openDays) {
            $hours = $this->explodeByArray($this->getHoursDelimiters(), $rawHours);

            if (!empty($hours[0]) and !empty($hours[1])) {
                $startTime = strtotime($hours[0]);
                $endTime = strtotime($hours[1]);

                if ($startTime and $endTime) {
                    $startDateTime = new \DateTime();
                    $startDateTime->setTimestamp($startTime);

                    $endDateTime = new \DateTime();
                    $endDateTime->setTimestamp($endTime);

                    $data =  [
                        'open' => $startDateTime,
                        'close' => $endDateTime,
                        'days' => $openDays,
                    ];
                }
            }
        }

        return $data;
    }

    public function getDayOfWeekMapping()
    {
        return [
            // Monday
            'Mon'       => DayOfWeekModel::CODE_MONDAY,
            'Monday'    => DayOfWeekModel::CODE_MONDAY,
            'Lun'       => DayOfWeekModel::CODE_MONDAY,
            'Lunes'     => DayOfWeekModel::CODE_MONDAY,

            // Tuesday
            'Tue'       => DayOfWeekModel::CODE_TUESDAY,
            'Tuesday'   => DayOfWeekModel::CODE_TUESDAY,
            'Mar'       => DayOfWeekModel::CODE_TUESDAY,
            'Martes'    => DayOfWeekModel::CODE_TUESDAY,

            // Wednesday
            'Wed'       => DayOfWeekModel::CODE_WEDNESDAY,
            'Wednesday' => DayOfWeekModel::CODE_WEDNESDAY,
            'Mié'       => DayOfWeekModel::CODE_WEDNESDAY,
            'Mie'       => DayOfWeekModel::CODE_WEDNESDAY,
            'Miércoles' => DayOfWeekModel::CODE_WEDNESDAY,
            'Miercoles' => DayOfWeekModel::CODE_WEDNESDAY,

            // Thursday
            'Thu'       => DayOfWeekModel::CODE_THURSDAY,
            'Thursday'  => DayOfWeekModel::CODE_THURSDAY,
            'Jue'       => DayOfWeekModel::CODE_THURSDAY,
            'Jueves'    => DayOfWeekModel::CODE_THURSDAY,

            // Friday
            'Fri'       => DayOfWeekModel::CODE_FRIDAY,
            'Friday'    => DayOfWeekModel::CODE_FRIDAY,
            'Vie'       => DayOfWeekModel::CODE_FRIDAY,
            'Viernes'   => DayOfWeekModel::CODE_FRIDAY,

            // Saturday
            'Sat'       => DayOfWeekModel::CODE_SATURDAY,
            'Saturday'  => DayOfWeekModel::CODE_SATURDAY,
            'Sáb'       => DayOfWeekModel::CODE_SATURDAY,
            'Sab'       => DayOfWeekModel::CODE_SATURDAY,
            'Sábado'    => DayOfWeekModel::CODE_SATURDAY,
            'Sabado'    => DayOfWeekModel::CODE_SATURDAY,

            // Sunday
            'Sun'       => DayOfWeekModel::CODE_SUNDAY,
            'Sunday'    => DayOfWeekModel::CODE_SUNDAY,
            'Dom'       => DayOfWeekModel::CODE_SUNDAY,
            'Domingo'   => DayOfWeekModel::CODE_SUNDAY,
        ];
    }

    protected function getHoursDelimiters()
    {
        return [
            'to',
            '-',
            'through',
            ' a ',
        ];
    }

    protected function getLineDelimiters()
    {
        return [
            "\r",
            "\r\n"
        ];
    }

    protected function explodeByArray($delimiters, $input) {
        $delimiter = $delimiters[0];
        $raw = str_replace($delimiters, $delimiter, $input);

        return explode($delimiter, $raw);
    }

    protected function parseWorkingHours($text)
    {
        $result = [];

        preg_match('/\d/', $text, $raw, PREG_OFFSET_CAPTURE);

        if (!empty($raw[0][1])) {
            $rawDays = mb_strtolower(substr($text, 0, $raw[0][1]));
            $rawHours = mb_strtolower(substr($text, $raw[0][1]));

            $dayOfWeek = $this->getDayOfWeekMapping();

            $result = $this->checkRawData($dayOfWeek, $rawDays, $rawHours);
        }

        return $result;
    }
}
