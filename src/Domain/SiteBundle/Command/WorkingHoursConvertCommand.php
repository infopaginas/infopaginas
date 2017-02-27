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

    const CSV_DELIMITER = ',';

    const BUSINESS_ID = 0;

    const WORKING_HOURS = 1;

    protected function configure()
    {
        $this->setName('data:working-hours:convert');
        $this->setDescription('working hours conversion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $this->em->createQueryBuilder()
            ->select('bp')
            ->from('DomainBusinessBundle:BusinessProfile', 'bp')
            ->where('bp.workingHours IS NOT NULL')
            ->andWhere('bp.workingHours != \'\'')
        ;

        $query = $this->em->createQuery($qb->getDQL());

        $businesses = $query->iterate();

        foreach ($businesses as $row) {
            $business = $row[0];

            $data = $this->convertWorkingHours($business->getId(), $business->getWorkingHours());

//            dump($data);
//            die();
            if ($data) {
                $this->createWorkingHours(current($data), $business);
            }

            $this->em->flush();
            $this->em->clear();
        }
    }

    protected function createWorkingHours($data, BusinessProfile $business)
    {
        $oldWorkingHours = $business->getCollectionWorkingHours();

        foreach ($oldWorkingHours as $oldWorkingHour) {
            $business->removeCollectionWorkingHour($oldWorkingHour);
            $this->em->remove($oldWorkingHour);
        }

        foreach ($data['days'] as $day) {
            $workingHour = new BusinessProfileWorkingHour();

            $workingHour->setDay($day);
            $workingHour->setTimeStart($data['open']);

            // todo
            $workingHour->setTimeEnd($data['close']);
            $workingHour->setOpenAllTime(false);

            $workingHour->setBusinessProfile($business);

            $this->em->persist($workingHour);

            $business->addCollectionWorkingHour($workingHour);
        }
    }

    protected function executeTemp(InputInterface $input, OutputInterface $output)
    {
        $original = $this->getContainer()->get('kernel')->getRootDir() . '/../web/working-hours-raw.csv';

//        $businesses = $this->getContainer()->get('kernel')->getRootDir() . '/../web/businesses-undefined-new.csv';
//        $businesses = $this->getContainer()->get('kernel')->getRootDir() . '/../web/category-undefined-3.csv';

//        $outputPath = $this->getContainer()->get('kernel')->getRootDir() . '/../web/result3.txt';

        $success = [];
        $error = [];

        $handle = fopen($original, 'r');

        if ($handle !== false) {
            $i = 0;

            while (($data = fgetcsv($handle, 1000, self::CSV_DELIMITER)) !== false) {
                $bid = $data[self::BUSINESS_ID];
                $text = $data[self::WORKING_HOURS];

                $data = $this->convertWorkingHours($bid, $text);

                if ($data) {
                    $success[] = $data;
                } else {
                    $error[$bid] = [
                        'id' => $bid,
                        'text' => $text,
                    ];
                }

                $i++;

//                if ($i > 100) {
//                    break;
//                }
            }

            fclose($handle);

            dump(count($success));
            dump(count($error));

//            dump($error);
        }
    }

    protected function convertWorkingHours($bid, $text)
    {
        preg_match('/\d/', $text, $raw, PREG_OFFSET_CAPTURE);

        $data = [];

        if (!empty($raw[0][1])) {
            $rawDays = mb_strtolower(substr($text, 0, $raw[0][1]));
            $rawHours = mb_strtolower(substr($text, $raw[0][1]));

            $dayOfWeek = DayOfWeekModel::getDayOfWeekMapping();

            $data = $this->checkRawData($dayOfWeek, $rawDays, $rawHours, $bid);

            if (!$data) {
                $dayOfWeek = $this->getDayOfWeekMappingAbrEn();

                $data = $this->checkRawData($dayOfWeek, $rawDays, $rawHours, $bid);

                if (!$data) {
                    $dayOfWeek = $this->getDayOfWeekMappingEs();

                    $data = $this->checkRawData($dayOfWeek, $rawDays, $rawHours, $bid);

                    if (!$data) {
                        $dayOfWeek = $this->getDayOfWeekMappingAbrEs();

                        $data = $this->checkRawData($dayOfWeek, $rawDays, $rawHours, $bid);
                    }
                }
            }
        }

        return $data;
    }

    protected function checkRawData($dayOfWeek, $rawDays, $rawHours, $bid)
    {
        $data = [];

        foreach ($dayOfWeek as $key => $day) {
            $openDays = [];

            if (strpos($rawDays, mb_strtolower($day)) !== false) {
                $openDays[] = $key;

                $hours = $this->explodeByArray($this->getHoursDelimiters(), $rawHours);

                if (!empty($hours[0]) and !empty($hours[1])) {
                    $startTime = strtotime($hours[0]);
                    $endTime = strtotime($hours[1]);

                    if ($startTime and $endTime) {
                        $startDateTime = new \DateTime();
                        $startDateTime->setTimestamp($startTime);

                        $endDateTime = new \DateTime();
                        $endDateTime->setTimestamp($endTime);

                        $data[$bid] =  [
                            'id' => $bid,
                            'open' => $startDateTime,
                            'close' => $endDateTime,
                            'days' => $openDays,

                        ];
                    }
                }
            }
        }

        return $data;
    }

    public function getDayOfWeekMappingAbrEn()
    {
        return [
            DayOfWeekModel::CODE_MONDAY    => 'Mon',
            DayOfWeekModel::CODE_TUESDAY   => 'Tue',
            DayOfWeekModel::CODE_WEDNESDAY => 'Wed',
            DayOfWeekModel::CODE_THURSDAY  => 'Thu',
            DayOfWeekModel::CODE_FRIDAY    => 'Fri',
            DayOfWeekModel::CODE_SATURDAY  => 'Sat',
            DayOfWeekModel::CODE_SUNDAY    => 'Sun',
        ];
    }

    public function getDayOfWeekMappingAbrEs()
    {
        return [
            DayOfWeekModel::CODE_MONDAY    => 'Lun',
            DayOfWeekModel::CODE_TUESDAY   => 'Mar',
            DayOfWeekModel::CODE_WEDNESDAY => 'Mié',
            DayOfWeekModel::CODE_THURSDAY  => 'Jue',
            DayOfWeekModel::CODE_FRIDAY    => 'Vie',
            DayOfWeekModel::CODE_SATURDAY  => 'Sáb',
            DayOfWeekModel::CODE_SUNDAY    => 'Dom',
        ];
    }

    public function getDayOfWeekMappingEs()
    {
        return [
            DayOfWeekModel::CODE_WEEKDAY   => 'Dias de Semana',
            DayOfWeekModel::CODE_WEEKEND   => 'Fin de Semana',
            DayOfWeekModel::CODE_MONDAY    => 'Lunes',
            DayOfWeekModel::CODE_TUESDAY   => 'Martes',
            DayOfWeekModel::CODE_WEDNESDAY => 'Miércoles',
            DayOfWeekModel::CODE_THURSDAY  => 'Jueves',
            DayOfWeekModel::CODE_FRIDAY    => 'Viernes',
            DayOfWeekModel::CODE_SATURDAY  => 'Sábado',
            DayOfWeekModel::CODE_SUNDAY    => 'Domingo',
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

    protected function explodeByArray($delimiters, $input) {
        $delimiter = $delimiters[0];
        $raw = str_replace($delimiters, $delimiter, $input); //Extra step to create a uniform value

        return explode($delimiter, $raw);
    }
}
