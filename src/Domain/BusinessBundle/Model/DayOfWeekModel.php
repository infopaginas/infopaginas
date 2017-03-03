<?php

namespace Domain\BusinessBundle\Model;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;

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
        foreach ($workingHours as $workingHour) {
            if (!$workingHour->getOpenAllTime() and $workingHour->getTimeStart() >= $workingHour->getTimeEnd()) {
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
        if (
            (
                $workingHour->getOpenAllTime() or $checkWorkingHour->getOpenAllTime()
            ) or (
                $workingHour->getTimeStart() >= $checkWorkingHour->getTimeStart() and
                $workingHour->getTimeStart() < $checkWorkingHour->getTimeEnd()
            ) or (
                $checkWorkingHour->getTimeStart() >= $workingHour->getTimeStart() and
                $checkWorkingHour->getTimeStart() < $workingHour->getTimeEnd()
            )
        ) {
            $check = false;
        } else {
            $check = true;
        }

        return $check;
    }


    public static function getBusinessProfileOpenNowData(BusinessProfile $businessProfile)
    {
        $workingHours = $businessProfile->getCollectionWorkingHours();

        $time = date(BusinessProfileWorkingHour::DEFAULT_TASK_TIME_FORMAT);

        $now = new \DateTime(BusinessProfileWorkingHour::DEFAULT_DATE);
        $now->modify($time);

        $dayOfWeek = strtoupper(date('D'));

        $workingHourData = [];

        if (!$workingHours->isEmpty()) {
            $data = [
                'status' => true,
                'open'   => false,
                'hours'  => false,
            ];

            foreach ($workingHours as $workingHour) {
                /* @var $workingHour BusinessProfileWorkingHour */
                $workingHourDay = $workingHour->getDay();

                if (
                    (
                        (
                            $workingHourDay == self::CODE_WEEKDAY and in_array($dayOfWeek, self::getWeekday())
                        ) or (
                            $workingHourDay == self::CODE_WEEKEND and in_array($dayOfWeek, self::getWeekend())
                        ) or (
                            in_array($dayOfWeek, self::getDaysOfWeek()) and $workingHourDay == $dayOfWeek
                        )
                    ) and (
                        (
                            $now < $workingHour->getTimeEnd() and $now >= $workingHour->getTimeStart()
                        ) or $workingHour->getOpenAllTime()
                    )
                ) {
                    $workingHourData[$dayOfWeek][] = $workingHour;
                }
            }

            // particular days always has priority
            if (count($workingHourData) > 1) {
                unset($workingHourData[self::CODE_WEEKEND], $workingHourData[self::CODE_WEEKDAY]);
            }

            if (!empty($workingHourData[$dayOfWeek])) {
                $data = [
                    'status' => true,
                    'open'   => true,
                    'hours'  => current($workingHourData[$dayOfWeek]),
                ];
            }
        } else {
            $data = [
                'status' => false,
                'open'   => false,
                'hours'  => false,
            ];
        }

        return $data;
    }

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

    public static function sortDailyWorkingHours($dailyHours)
    {
        foreach ($dailyHours as $key => $hours) {
            // working hours can't overlap that's why sort by time start
            usort($dailyHours[$key], function($a, $b) {
                return $a->getTimeStart()->getTimestamp() - $b->getTimeStart()->getTimestamp();
            });
        }

        return $dailyHours;
    }

    public static function orderDailyWorkingDayByDay($dailyHours) {
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
}
