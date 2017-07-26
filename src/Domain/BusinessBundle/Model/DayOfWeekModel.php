<?php

namespace Domain\BusinessBundle\Model;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\ReportBundle\Util\DatesUtil;
use Symfony\Component\Validator\Constraints\DateTime;

class DayOfWeekModel
{
    const CODE_MONDAY    = 'MON';
    const CODE_TUESDAY   = 'TUE';
    const CODE_WEDNESDAY = 'WED';
    const CODE_THURSDAY  = 'THU';
    const CODE_FRIDAY    = 'FRI';
    const CODE_SATURDAY  = 'SAT';
    const CODE_SUNDAY    = 'SUN';
    const CODE_WEEKDAY   = 'WD';
    const CODE_WEEKEND   = 'WE';

    // see https://developers.google.com/search/docs/data-types/local-businesses
    const SCHEMA_ORG_OPEN_ALL_DAY_OPEN_TIME  = '00:00';
    const SCHEMA_ORG_OPEN_ALL_DAY_CLOSE_TIME = '23:59';

    const SCHEMA_ORG_CLOSE_ALL_DAY_OPEN_TIME  = '00:00';
    const SCHEMA_ORG_CLOSE_ALL_DAY_CLOSE_TIME = '00:00';

    const SCHEMA_ORG_OPEN_TIME_FORMAT = 'H:i';
    const FORM_DEFAULT_FORMAT = 'h:i a';

    /**
     * @return array
     */
    public static function getDayOfWeekMapping()
    {
        return [
            self::CODE_WEEKDAY   => 'Weekday',
            self::CODE_WEEKEND   => 'Weekend',
            self::CODE_MONDAY    => 'Monday',
            self::CODE_TUESDAY   => 'Tuesday',
            self::CODE_WEDNESDAY => 'Wednesday',
            self::CODE_THURSDAY  => 'Thursday',
            self::CODE_FRIDAY    => 'Friday',
            self::CODE_SATURDAY  => 'Saturday',
            self::CODE_SUNDAY    => 'Sunday',
        ];
    }

    /**
     * see http://schema.org/DayOfWeek
     *
     * @return array
     */
    public static function getDayOfWeekSchemaOrgMapping()
    {
        return [
            self::CODE_MONDAY    => 'http://schema.org/Monday',
            self::CODE_TUESDAY   => 'http://schema.org/Tuesday',
            self::CODE_WEDNESDAY => 'http://schema.org/Wednesday',
            self::CODE_THURSDAY  => 'http://schema.org/Thursday',
            self::CODE_FRIDAY    => 'http://schema.org/Friday',
            self::CODE_SATURDAY  => 'http://schema.org/Saturday',
            self::CODE_SUNDAY    => 'http://schema.org/Sunday',
        ];
    }

    /**
     * @return array
     */
    public static function getDaysOfWeek()
    {
        return [
            self::CODE_MONDAY,
            self::CODE_TUESDAY,
            self::CODE_WEDNESDAY,
            self::CODE_THURSDAY,
            self::CODE_FRIDAY,
            self::CODE_SATURDAY,
            self::CODE_SUNDAY,
        ];
    }

    /**
     * @return array
     */
    public static function getDaysOfWeekStartWithSunday()
    {
        return [
            self::CODE_SUNDAY,
            self::CODE_MONDAY,
            self::CODE_TUESDAY,
            self::CODE_WEDNESDAY,
            self::CODE_THURSDAY,
            self::CODE_FRIDAY,
            self::CODE_SATURDAY,
        ];
    }

    /**
     * @return array
     */
    public static function getAllDaysOfWeek()
    {
        return [
            self::CODE_WEEKDAY,
            self::CODE_WEEKEND,
            self::CODE_MONDAY,
            self::CODE_TUESDAY,
            self::CODE_WEDNESDAY,
            self::CODE_THURSDAY,
            self::CODE_FRIDAY,
            self::CODE_SATURDAY,
            self::CODE_SUNDAY,
        ];
    }

    /**
     * @return array
     */
    public static function getWeekday()
    {
        return [
            self::CODE_MONDAY,
            self::CODE_TUESDAY,
            self::CODE_WEDNESDAY,
            self::CODE_THURSDAY,
            self::CODE_FRIDAY,
        ];
    }

    /**
     * @return array
     */
    public static function getWeekend()
    {
        return [
            self::CODE_SATURDAY,
            self::CODE_SUNDAY,
        ];
    }

    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     *
     * @return bool
     */
    public static function validateWorkingHoursTime($workingHours)
    {
        $defaultDate = DatesUtil::getToday();

        foreach ($workingHours as $workingHour) {
            if (!$workingHour->getOpenAllTime() and
                ($workingHour->getTimeStart() >= $workingHour->getTimeEnd() and
                $workingHour->getTimeEnd() != $defaultDate or
                $workingHour->getTimeStart() == $workingHour->getTimeEnd())
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     *
     * @return bool
     */
    public static function validateWorkingHoursOverlap($workingHours)
    {
        $check = true;

        $dailyHours = self::getWorkingHoursWeekList($workingHours);

        foreach ($dailyHours as $dailyHoursSet) {
            $check = self::validateDayWorkingHours($dailyHoursSet);

            if (!$check) {
                break;
            }
        }

        return $check;
    }

    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     *
     * @return bool
     */
    public static function validateWorkingHoursTimeBlank($workingHours)
    {
        $check = true;

        foreach ($workingHours as $workingHour) {
            if (!$workingHour->getOpenAllTime() and (!$workingHour->getTimeEnd() or !$workingHour->getTimeStart())) {
                $check = false;
                break;
            }
        }

        return $check;
    }

    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     *
     * @return bool
     */
    public static function validateDayWorkingHours($workingHours)
    {
        $data = $workingHours;

        foreach ($workingHours as $key => $workingHour) {
            unset($data[$key]);

            if ($data) {
                $check = self::validateDayWorkingHoursAgainstOtherDay($data, $workingHour);

                if (!$check) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     * @param BusinessProfileWorkingHour   $checkWorkingHour
     *
     * @return bool
     */
    public static function validateDayWorkingHoursAgainstOtherDay($workingHours, $checkWorkingHour)
    {
        foreach ($workingHours as $workingHour) {
            if (!self::checkWorkingHourOverlap($workingHour, $checkWorkingHour)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param BusinessProfileWorkingHour $workingHour
     * @param BusinessProfileWorkingHour $checkWorkingHour
     *
     * @return bool
     */
    public static function checkWorkingHourOverlap($workingHour, $checkWorkingHour)
    {
        $defaultDate = DatesUtil::getToday();

        $timeEnd      = $workingHour->getTimeEnd();
        $checkTimeEnd = $checkWorkingHour->getTimeEnd();

        if ($timeEnd == $defaultDate) {
            $timeEnd = clone $timeEnd;
            $timeEnd->modify('+1 day');
        }

        if ($checkTimeEnd == $defaultDate) {
            $checkTimeEnd = clone $checkTimeEnd;
            $checkTimeEnd->modify('+1 day');
        }

        if (($workingHour->getOpenAllTime() or $checkWorkingHour->getOpenAllTime()
            ) or (
                $workingHour->getTimeStart() >= $checkWorkingHour->getTimeStart() and
                $workingHour->getTimeStart() < $checkTimeEnd
            ) or (
                $checkWorkingHour->getTimeStart() >= $workingHour->getTimeStart() and
                $checkWorkingHour->getTimeStart() < $timeEnd
            )
        ) {
            $check = false;
        } else {
            $check = true;
        }

        return $check;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return array
     */
    public static function getBusinessProfileOpenNowData($businessProfile)
    {
        $workingHours = $businessProfile->getWorkingHoursJsonAsObject();

        $data = [
            'status' => false,
            'open'   => false,
            'hours'  => false,
        ];

        if ($workingHours) {
            $time = date(BusinessProfileWorkingHour::DEFAULT_TASK_TIME_FORMAT);

            $now = new \DateTime(BusinessProfileWorkingHour::DEFAULT_DATE);
            $now->modify($time);

            $dayOfWeek = strtoupper(date('D'));
            $daysKey = '';

            foreach ($workingHours as $dayItems => $items) {
                $days = explode(',', $dayItems);

                if (in_array($dayOfWeek, $days)) {
                    $daysKey = $dayItems;
                }
            }

            if ($daysKey and !empty($workingHours->{$daysKey})) {
                $data = [
                    'status' => true,
                    'open'   => false,
                    'hours'  => false,
                ];

                $defaultDate = self::getDefaultDateTime();

                foreach ($workingHours->{$daysKey} as $workingHour) {
                    $startDate = clone $defaultDate;
                    $startDate->modify($workingHour->timeStart);

                    $endDate   = clone $defaultDate;
                    $endDate->modify($workingHour->timeEnd);

                    if ($endDate == $defaultDate) {
                        $timeEnd = clone $endDate;
                        $timeEnd->modify('+1 day');
                    } else {
                        $timeEnd = $endDate;
                    }

                    if (($now < $timeEnd and $now >= $startDate) or $workingHour->openAllTime) {
                        $workingHour->timeStart = $startDate;
                        $workingHour->timeEnd   = $endDate;

                        $data = [
                            'status' => true,
                            'open'   => true,
                            'hours'  => $workingHour,
                        ];

                        break;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return array
     */
    public static function getBusinessProfileWorkingHoursList(BusinessProfile $businessProfile)
    {
        $workingHours = $businessProfile->getCollectionWorkingHours();

        $dailyHours = [];

        if (!$workingHours->isEmpty()) {
            $dailyHours = self::getWorkingHoursWeekList($workingHours);

            // merge weekday and weekend to real days
            $dailyHours = self::mergeCustomDayToReal($dailyHours, self::CODE_WEEKDAY);
            $dailyHours = self::mergeCustomDayToReal($dailyHours, self::CODE_WEEKEND);
            $dailyHours = self::sortDailyWorkingHours($dailyHours);
            $dailyHours = self::orderDailyWorkingDayByDay($dailyHours);
            $dailyHours = self::mergeSimilarWorkingDays($dailyHours);
        }

        return $dailyHours;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return \stdClass
     */
    public static function getBusinessProfileWorkingHoursListView($businessProfile)
    {
        $dailyHours  = [];

        $workingHours = $businessProfile->getWorkingHoursJsonAsObject();

        if ($workingHours) {
            $defaultDate = self::getDefaultDateTime();

            foreach ($workingHours as $key => $items) {
                foreach ($items as $hourKey => $workingHour) {
                    $startDate = clone $defaultDate;
                    $startDate->modify($workingHour->timeStart);
                    $workingHour->timeStart = $startDate;

                    $endDate   = clone $defaultDate;
                    $endDate->modify($workingHour->timeEnd);
                    $workingHour->timeEnd = $endDate;

                    $dailyHours[$key][$hourKey] = $workingHour;
                }
            }
        }

        return $dailyHours;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return string
     */
    public static function getBusinessProfileWorkingHoursJson($businessProfile)
    {
        $data = [];

        $dailyHours = self::getBusinessProfileWorkingHoursList($businessProfile);

        foreach ($dailyHours as $dayItems => $hours) {
            $workingHoursData = [];

            foreach ($hours as $workingHours) {
                $workingHoursData[] = self::convertWorkingHoursToArray($workingHours);
            }

            $data[$dayItems] = $workingHoursData;
        }

        return json_encode($data);
    }

    /**
     * @param BusinessProfileWorkingHour $workingHours
     *
     * @return string
     */
    public static function convertWorkingHoursToArray($workingHours)
    {
        if ($workingHours->getTimeStart()) {
            $timeStart = $workingHours->getTimeStart();
        } else {
            $timeStart = DatesUtil::getToday();
        }

        if ($workingHours->getTimeEnd()) {
            $timeEnd = $workingHours->getTimeEnd();
        } else {
            $timeEnd = DatesUtil::getToday();
        }

        return [
            'timeStart'   => $timeStart->format(self::SCHEMA_ORG_OPEN_TIME_FORMAT),
            'timeEnd'     => $timeEnd->format(self::SCHEMA_ORG_OPEN_TIME_FORMAT),
            'openAllTime' => $workingHours->getOpenAllTime(),
        ];
    }

    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     *
     * @return array
     */
    public static function getWorkingHoursWeekList($workingHours)
    {
        $dailyHours = [];

        foreach (self::getAllDaysOfWeek() as $day) {
            foreach ($workingHours as $workingHour) {
                if ($workingHour->getDay() == $day) {
                    $dailyHours[$day][] = $workingHour;
                }
            }
        }

        return $dailyHours;
    }

    /**
     * @param array $dailyHours
     * @param string $dayCode
     *
     * @return array
     */
    public static function mergeCustomDayToReal($dailyHours, $dayCode)
    {
        if (!empty($dailyHours[$dayCode])) {
            foreach ($dailyHours[$dayCode] as $weekday) {
                switch ($dayCode) {
                    case self::CODE_WEEKDAY:
                        $dayList = self::getWeekday();
                        break;
                    case self::CODE_WEEKEND:
                        $dayList = self::getWeekend();
                        break;
                    default:
                        $dayList = [];
                        break;
                }

                foreach ($dayList as $day) {
                    // real day has priority over weekday and weekend
                    if (empty($dailyHours[$day])) {
                        $dailyHours[$day][] = $weekday;
                    }
                }
            }
        }

        unset($dailyHours[$dayCode]);

        return $dailyHours;
    }

    /**
     * @param array $dailyHours
     *
     * @return array
     */
    public static function sortDailyWorkingHours($dailyHours)
    {
        foreach ($dailyHours as $key => $hours) {
            // working hours can't overlap that's why sort by time start
            usort($dailyHours[$key], function ($a, $b) {
                return $a->getTimeStart()->getTimestamp() - $b->getTimeStart()->getTimestamp();
            });
        }

        return $dailyHours;
    }

    /**
     * @param array $dailyHours
     *
     * @return array
     */
    public static function orderDailyWorkingDayByDay($dailyHours)
    {
        $ordered = [];
        $dayOrder = self::getDaysOfWeek();

        foreach ($dayOrder as $key) {
            if (array_key_exists($key, $dailyHours)) {
                $ordered[$key] = $dailyHours[$key];
            } else {
                $ordered[$key] = [];
            }
        }
        return $ordered;
    }

    /**
     * @param array $dailyHours
     *
     * @return array
     */
    public static function mergeSimilarWorkingDays($dailyHours)
    {
        $data = [];
        $mergedDays = [];

        foreach ($dailyHours as $day => $workingHours) {
            if (!in_array($day, $mergedDays)) {
                $daysMerging = self::checkMergeDayAgainstOtherDay($workingHours, $dailyHours, $day);

                $data[implode($daysMerging, ',')] = $workingHours;

                $mergedDays = array_merge($mergedDays, $daysMerging);
            }
        }

        return $data;
    }


    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     * @param array  $dailyHours
     * @param string $day
     *
     * @return array
     */
    public static function checkMergeDayAgainstOtherDay($workingHours, $dailyHours, $day)
    {
        $days[] = $day;

        foreach ($dailyHours as $key => $checkWorkingHours) {
            if ($key != $day and self::checkWorkingHourMerge($workingHours, $checkWorkingHours)) {
                $days[] = $key;
            }
        }

        return $days;
    }

    /**
     * @param BusinessProfileWorkingHour[] $workingHours
     * @param BusinessProfileWorkingHour[] $checkWorkingHours
     *
     * @return bool
     */
    public static function checkWorkingHourMerge($workingHours, $checkWorkingHours)
    {
        $check = true;

        if (count($checkWorkingHours) == count($workingHours)) {
            foreach ($workingHours as $key => $workingHour) {
                // items have already been ordered by time
                if (!((!empty($checkWorkingHours[$key]) and
                    (
                        $checkWorkingHours[$key]->getTimeStart() == $workingHour->getTimeStart() and
                        $checkWorkingHours[$key]->getTimeEnd() == $workingHour->getTimeEnd())
                    ) or (
                        $checkWorkingHours[$key]->getOpenAllTime() and $workingHour->getOpenAllTime()
                    ))
                ) {
                    return false;
                }
            }
        } else {
            $check = false;
        }

        return $check;
    }

    /**
     * @param \DateTime|null $time
     *
     * @return string
     */
    public static function getFormFormattedTime($time)
    {
        if (!$time) {
            $time = self::getDefaultDateTime();
        }

        return $time->format(self::FORM_DEFAULT_FORMAT);
    }

    /**
     * @return \DateTime
     */
    public static function getDefaultDateTime()
    {
        return new \DateTime(BusinessProfileWorkingHour::DEFAULT_DATE);
    }
}
