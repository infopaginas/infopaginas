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
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::WORKING_HOURS_CONVERT, $logger::STATUS_START, 'execute:start');
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $businessesWithTextWorkingHours = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getBusinessesWithTextWorkingHoursIterator();

        $successItemCounter = 0;
        $errorItemCounter   = 0;

        foreach ($businessesWithTextWorkingHours as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];

            $data = $this->convertWorkingHours($business->getWorkingHours());

            if ($data) {
                $this->createWorkingHours($data, $business);
                $this->em->flush();
                $this->em->clear();

                $successItemCounter++;
            } else {
                $errorItemCounter++;
            }
        }

        $output->writeln('Success:' . $successItemCounter . '; Error: ' . $errorItemCounter);

        $this->em->flush();
        $logger->addInfo($logger::WORKING_HOURS_CONVERT, $logger::STATUS_END, 'execute:stop');
    }

    protected function createWorkingHours($data, BusinessProfile $business)
    {
        $oldWorkingHours = $business->getCollectionWorkingHours();

        foreach ($oldWorkingHours as $oldWorkingHour) {
            $business->removeCollectionWorkingHour($oldWorkingHour);
            $this->em->remove($oldWorkingHour);
        }

        foreach ($data as $item) {
            if (in_array(DayOfWeekModel::CODE_SATURDAY, $item['days']) and
                in_array(DayOfWeekModel::CODE_SUNDAY, $item['days'])
            ) {
                $item['days'][DayOfWeekModel::CODE_WEEKEND] = DayOfWeekModel::CODE_WEEKEND;

                unset(
                    $item['days'][DayOfWeekModel::CODE_SATURDAY],
                    $item['days'][DayOfWeekModel::CODE_SUNDAY]
                );
            }

            if (in_array(DayOfWeekModel::CODE_MONDAY, $item['days']) and
                in_array(DayOfWeekModel::CODE_TUESDAY, $item['days']) and
                in_array(DayOfWeekModel::CODE_WEDNESDAY, $item['days']) and
                in_array(DayOfWeekModel::CODE_THURSDAY, $item['days']) and
                in_array(DayOfWeekModel::CODE_FRIDAY, $item['days'])
            ) {
                $item['days'][DayOfWeekModel::CODE_WEEKDAY] = DayOfWeekModel::CODE_WEEKDAY;

                unset(
                    $item['days'][DayOfWeekModel::CODE_MONDAY],
                    $item['days'][DayOfWeekModel::CODE_TUESDAY],
                    $item['days'][DayOfWeekModel::CODE_WEDNESDAY],
                    $item['days'][DayOfWeekModel::CODE_THURSDAY],
                    $item['days'][DayOfWeekModel::CODE_FRIDAY]
                );
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

        if (!empty($item['allTime'])) {
            $workingHour->setOpenAllTime(true);
        } else {
            $workingHour->setOpenAllTime(false);
        }

        $workingHour->setBusinessProfile($business);

        $this->em->persist($workingHour);

        $business->addCollectionWorkingHour($workingHour);
    }

    protected function convertWorkingHours($text)
    {
        $data = [];

        // remove empty string
        $text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\r\n", $text);

        // days and hours at same line
        if (!$data) {
            $dataItem = $this->explodeByArray($this->getLineDelimiters(), $text);

            foreach ($dataItem as $textItem) {
                $result = $this->parseWorkingHours($textItem);

                if ($result) {
                    $data[] = $result;
                }
            }
        }

        // days and hours at different lines
        if (!$data) {
            $dataItem = $this->explodeByArray($this->getLineDelimiters(), $text);

            foreach ($dataItem as $key => $textItem) {
                if (!empty($dataItem[$key + 1]) and ($key % 2) == 0) {
                    $rawDays = mb_strtolower($textItem);
                    $rawHours = mb_strtolower($dataItem[$key + 1]);

                    $dayOfWeek = $this->getDayOfWeekMapping();

                    $result = $this->checkRawData($dayOfWeek, $rawDays, $rawHours);

                    if ($result) {
                        $data[] = $result;
                    } else {
                        preg_match('/pm/', mb_strtolower($textItem), $raw, PREG_OFFSET_CAPTURE);

                        if (!empty($raw[0][1])) {
                            $rawHours = mb_strtolower($textItem);
                            $rawDays = mb_strtolower($dataItem[$key + 1]);

                            $result = $this->checkRawData($dayOfWeek, $rawDays, $rawHours);

                            if ($result) {
                                $data[] = $result;
                            }
                        }
                    }
                }
            }

            if (!$data) {
                foreach ($this->getAllDayWorkingMapping() as $item) {
                    if (strpos($text, $item) !== false) {
                        $startDateTime = new \DateTime(BusinessProfileWorkingHour::DEFAULT_DATE);
                        $endDateTime   = clone $startDateTime;

                        $data[] =  [
                            'open'  => $startDateTime,
                            'close' => $endDateTime,
                            'days'  => [
                                DayOfWeekModel::CODE_WEEKDAY,
                                DayOfWeekModel::CODE_WEEKEND,
                            ],
                            'allTime' => true,
                        ];
                    }
                }
            }
        }

        return $data;
    }

    protected function checkRawData($dayOfWeek, $rawDays, $rawHours)
    {
        $data = [];
        $openDays = $this->parseDayText($dayOfWeek, $rawDays);

        if ($openDays) {
            $hours = $this->explodeByArray($this->getHoursDelimiters(), $rawHours);

            if (empty($hours[1])) {
                preg_match('/am/', mb_strtolower($rawHours), $timeRaw, PREG_OFFSET_CAPTURE);

                if (!empty($timeRaw[0][1])) {
                    $hours[0] = mb_strtolower(substr($rawHours, 0, $timeRaw[0][1] + 2));
                    $hours[1] = mb_strtolower(substr($rawHours, $timeRaw[0][1] + 2));
                }
            }

            if (!empty($hours[0]) and !empty($hours[1])) {
                $startTime = strtotime($hours[0]);
                $endTime = strtotime($hours[1]);

                if (!$endTime) {
                    preg_match('/pm/', mb_strtolower($hours[1]), $endTimeRaw, PREG_OFFSET_CAPTURE);

                    if (!empty($endTimeRaw[0][1])) {
                        $endTimeHours = mb_strtolower(substr($hours[1], 0, $endTimeRaw[0][1] + 2));
                        $endTimeText  = mb_strtolower(substr($hours[1], $endTimeRaw[0][1] + 2));

                        preg_match('/\d/', $endTimeText, $check, PREG_OFFSET_CAPTURE);

                        if (empty($raw[0][1])) {
                            $endTime = strtotime($endTimeHours);
                        }
                    }
                }

                if ($startTime and $endTime) {
                    $startDateTime = new \DateTime();
                    $startDateTime->setTimestamp($startTime);

                    $endDateTime = new \DateTime();
                    $endDateTime->setTimestamp($endTime);

                    $data =  [
                        'open'  => $startDateTime,
                        'close' => $endDateTime,
                        'days'  => $openDays,
                    ];
                }
            } elseif (strpos($rawHours, '24') !== false) {
                $startDateTime = new \DateTime(BusinessProfileWorkingHour::DEFAULT_DATE);
                $endDateTime   = clone $startDateTime;

                $data =  [
                    'open' => $startDateTime,
                    'close' => $endDateTime,
                    'days' => $openDays,
                    'allTime' => true,
                ];
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
            'Mondays'   => DayOfWeekModel::CODE_MONDAY,
            'Lun'       => DayOfWeekModel::CODE_MONDAY,
            'Lunes'     => DayOfWeekModel::CODE_MONDAY,

            // Tuesday
            'Tue'       => DayOfWeekModel::CODE_TUESDAY,
            'Tuesday'   => DayOfWeekModel::CODE_TUESDAY,
            'Tuesdays'  => DayOfWeekModel::CODE_TUESDAY,
            'Mar'       => DayOfWeekModel::CODE_TUESDAY,
            'Martes'    => DayOfWeekModel::CODE_TUESDAY,

            // Wednesday
            'Wed'       => DayOfWeekModel::CODE_WEDNESDAY,
            'Wednesday' => DayOfWeekModel::CODE_WEDNESDAY,
            'Wednesdays' => DayOfWeekModel::CODE_WEDNESDAY,
            'Mié'       => DayOfWeekModel::CODE_WEDNESDAY,
            'Mie'       => DayOfWeekModel::CODE_WEDNESDAY,
            'Miércoles' => DayOfWeekModel::CODE_WEDNESDAY,
            'Miercoles' => DayOfWeekModel::CODE_WEDNESDAY,

            // Thursday
            'Thu'       => DayOfWeekModel::CODE_THURSDAY,
            'Thursday'  => DayOfWeekModel::CODE_THURSDAY,
            'Thursdays' => DayOfWeekModel::CODE_THURSDAY,
            'Jue'       => DayOfWeekModel::CODE_THURSDAY,
            'Jueves'    => DayOfWeekModel::CODE_THURSDAY,

            // Friday
            'Fri'       => DayOfWeekModel::CODE_FRIDAY,
            'Friday'    => DayOfWeekModel::CODE_FRIDAY,
            'Fridays'   => DayOfWeekModel::CODE_FRIDAY,
            'Vie'       => DayOfWeekModel::CODE_FRIDAY,
            'Viernes'   => DayOfWeekModel::CODE_FRIDAY,

            // Saturday
            'Sat'       => DayOfWeekModel::CODE_SATURDAY,
            'Saturday'  => DayOfWeekModel::CODE_SATURDAY,
            'Saturdays' => DayOfWeekModel::CODE_SATURDAY,
            'Sáb'       => DayOfWeekModel::CODE_SATURDAY,
            'Sab'       => DayOfWeekModel::CODE_SATURDAY,
            'Sábado'    => DayOfWeekModel::CODE_SATURDAY,
            'Sabado'    => DayOfWeekModel::CODE_SATURDAY,

            // Sunday
            'Sun'       => DayOfWeekModel::CODE_SUNDAY,
            'Sunday'    => DayOfWeekModel::CODE_SUNDAY,
            'Sundays'   => DayOfWeekModel::CODE_SUNDAY,
            'Dom'       => DayOfWeekModel::CODE_SUNDAY,
            'Domingo'   => DayOfWeekModel::CODE_SUNDAY,
            'Doming'    => DayOfWeekModel::CODE_SUNDAY,

            'Todos los días' => [
                DayOfWeekModel::CODE_WEEKDAY,
                DayOfWeekModel::CODE_WEEKEND,
            ],
            'seven days a week' => [
                DayOfWeekModel::CODE_WEEKDAY,
                DayOfWeekModel::CODE_WEEKEND,
            ],
            'Todos los dias' => [
                DayOfWeekModel::CODE_WEEKDAY,
                DayOfWeekModel::CODE_WEEKEND,
            ],
            'everyday'       => [
                DayOfWeekModel::CODE_WEEKDAY,
                DayOfWeekModel::CODE_WEEKEND,
            ],
            'every day'      => [
                DayOfWeekModel::CODE_WEEKDAY,
                DayOfWeekModel::CODE_WEEKEND,
            ],
        ];
    }

    protected function getHoursDelimiters()
    {
        return [
            'to',
            '-',
            'through',
            ' a ',
            ' A ',
            '/',
            'at',
            ' y ',
            'until',
        ];
    }

    protected function getLineDelimiters()
    {
        return [
            "\r",
            "\r\n"
        ];
    }

    protected function getAllDayWorkingMapping()
    {
        return [
            '24/7',
            '24 hours 7 days',
            '24 horas los 7 días',
            '24 horas los 7 dias',
            '24 Hours a day, 7 days a week',
            '7 days 24 hours',
            '24 hours - 7 days a week',
            '24 Horas 7 Días a la Semana',
            '24 Horas 7 Dias a la Semana',
            '24 hours seven days a week',
            '24 horas/7 días',
            '24 horas/7 dias',
            '24 hr 7 days',
            '24 hours every day',
            '24 horas toda la semana',
            '24 horas 7 dás de la semana',
            '24 horas 7 das de la semana',
            '7 días/ 24 horas',
            '7 dias/ 24 horas',
            '7 días a la semana/24 horas',
            '7 dias a la semana/24 horas',
            '24 horas, 7  dias',
        ];
    }

    public function getDaySeparatorPeriod()
    {
        return [
            'to',
            'and',
            'through',
            'a',
            '-',
        ];
    }

    protected function explodeByArray($delimiters, $input)
    {
        $delimiter = $delimiters[0];
        $raw = str_replace($delimiters, $delimiter, $input);

        return explode($delimiter, $raw);
    }

    protected function parseWorkingHours($text)
    {
        $result = [];

        $dayOfWeek = $this->getDayOfWeekMapping();

        preg_match('/\d/', $text, $raw, PREG_OFFSET_CAPTURE);

        if (!empty($raw[0][1])) {
            $rawDays = mb_strtolower(substr($text, 0, $raw[0][1]));
            $rawHours = mb_strtolower(substr($text, $raw[0][1]));

            $result = $this->checkRawData($dayOfWeek, $rawDays, $rawHours);
        } else {
            preg_match('/pm/', mb_strtolower($text), $raw, PREG_OFFSET_CAPTURE);

            if (!empty($raw[0][1])) {
                $rawHours = mb_strtolower(substr($text, 0, $raw[0][1] + 2));
                $rawDays  = mb_strtolower(substr($text, $raw[0][1] + 2));

                $result = $this->checkRawData($dayOfWeek, $rawDays, $rawHours);
            }
        }

        return $result;
    }

    protected function parseDayText($dayOfWeek, $rawDays)
    {
        $openDays = [];
        $dayPositions = [];

        foreach ($dayOfWeek as $key => $day) {
            $dayPosition = strpos($rawDays, mb_strtolower($key));

            if ($dayPosition !== false) {
                $dayPositions[$dayPosition] = [
                    'start' => $dayPosition,
                    'end'   => $dayPosition + mb_strlen($key),
                    'day'   => $day,
                ];
            }
        }

        ksort($dayPositions);

        $previousDay = [];

        foreach ($dayPositions as $dayPosition) {
            $currentDay = $dayPosition['day'];

            if (is_array($currentDay)) {
                foreach ($currentDay as $dayItem) {
                    $openDays[$dayItem] = $dayItem;
                }

                $previousDay = [];
            } else {
                $openDays[$currentDay] = $currentDay;

                if ($previousDay) {
                    $separator = mb_substr($rawDays, $previousDay['end'], $dayPosition['start'] - $previousDay['end']);
                    $separator = str_replace('.', '', $separator);
                    $separator = trim($separator);

                    if (in_array($separator, $this->getDaySeparatorPeriod())) {
                        $period = $this->getDayPeriod($previousDay['day'], $currentDay);

                        foreach ($period as $day) {
                            $openDays[$day] = $day;
                        }
                    } else {
                        $previousDay = $dayPosition;
                    }
                } else {
                    $previousDay = $dayPosition;
                }
            }
        }

        return $openDays;
    }

    protected function getDayPeriod($dayStart, $dayEnd)
    {
        $allowAddDay = false;
        $periodRecorded = false;
        $dayPeriod = [];

        if ($dayStart == DayOfWeekModel::CODE_SUNDAY) {
            $dayOfWeek = DayOfWeekModel::getDaysOfWeekStartWithSunday();
        } else {
            $dayOfWeek = DayOfWeekModel::getDaysOfWeek();
        }

        foreach ($dayOfWeek as $day) {
            if ($day == $dayStart) {
                $dayPeriod[] = $day;
                $allowAddDay = true;
            } elseif ($day == $dayEnd and $allowAddDay) {
                $dayPeriod[] = $day;
                $periodRecorded = true;
                break;
            } elseif ($allowAddDay) {
                $dayPeriod[] = $day;
            }
        }

        if (!$periodRecorded) {
            $dayPeriod = [];
        }

        return $dayPeriod;
    }
}
