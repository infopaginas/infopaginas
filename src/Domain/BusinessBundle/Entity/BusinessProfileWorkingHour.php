<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessProfileWorkingHour
 *
 * @ORM\Table(name="business_profile_working_hour")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileWorkingHourRepository")
 */
class BusinessProfileWorkingHour
{
    const DEFAULT_TASK_TIME_FORMAT = 'g:i A';
    const DEFAULT_DATE = '1970-01-01';

    const FIELD_PREFIX_COMMENT = 'comment';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_start", type="time", nullable=true)
     * @Assert\Time()
     */
    private $timeStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_end", type="time", nullable=true)
     * @Assert\Time()
     */
    private $timeEnd;

    /**
     * @var bool
     *
     * @ORM\Column(name="open_all_time", type="boolean")
     */
    private $openAllTime;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="collectionWorkingHours",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @ORM\Column(name="day", type="string", nullable=true, length=15)
     */
    protected $day;

    /**
     * @ORM\Column(name="days", type="json_array", nullable=true)
     * @Assert\Count(min = 1)
     */
    protected $days;

    /**
     * @var string - comment eng
     *
     * @ORM\Column(name="comment_en", type="string", length=80, nullable=true)
     * @Assert\Length(max="80")
     */
    private $commentEn;

    /**
     * @var string - comment esp
     *
     * @ORM\Column(name="comment_es", type="string", length=80, nullable=true)
     * @Assert\Length(max="80")
     */
    private $commentEs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->openAllTime = false;
        $this->day = DayOfWeekModel::CODE_MONDAY;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $data = [
            'day'         => $this->getDay(),
            'timeStart'   => $this->getTimeStart(),
            'timeEnd'     => $this->getTimeEnd(),
            'openAllTime' => $this->getOpenAllTime(),
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        $data = [
            'days'        => $this->getDays(),
            'timeStart'   => $this->getTimeStart(),
            'timeEnd'     => $this->getTimeEnd(),
            'openAllTime' => $this->getOpenAllTime(),
        ];

        foreach (LocaleHelper::getLocaleList() as $locale => $name) {
            $property = self::FIELD_PREFIX_COMMENT . LocaleHelper::getLangPostfix($locale);

            if (property_exists($this, $property)) {
                $data[$property] = $this->{'get' . ucfirst($property)}();
            }
        }

        return json_encode($data);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param string $day
     *
     * @return BusinessProfileWorkingHour
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param \DateTime $timeStart
     *
     * @return BusinessProfileWorkingHour
     */
    public function setTimeStart($timeStart)
    {
        if (!($timeStart instanceof \DateTime) and strtotime($timeStart)) {
            $timeStart = new \DateTime($timeStart);
        } else {
            $timeStart = new \DateTime(self::DEFAULT_DATE);
        }

        $this->timeStart = $timeStart;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * @param \DateTime $timeEnd
     *
     * @return BusinessProfileWorkingHour
     */
    public function setTimeEnd($timeEnd)
    {
        if (!($timeEnd instanceof \DateTime) and strtotime($timeEnd)) {
            $timeEnd = new \DateTime($timeEnd);
        } else {
            $timeEnd = new \DateTime(self::DEFAULT_DATE);
        }

        $this->timeEnd = $timeEnd;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * @param boolean $openAllTime
     *
     * @return BusinessProfileWorkingHour
     */
    public function setOpenAllTime($openAllTime)
    {
        $this->openAllTime = $openAllTime;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getOpenAllTime()
    {
        return $this->openAllTime;
    }

    /**
     * Set businessProfile
     *
     * @param BusinessProfile $businessProfile
     *
     * @return BusinessProfileWorkingHour
     */
    public function setBusinessProfile(BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param mixed $days
     *
     * @return BusinessProfileWorkingHour
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * @param string $commentEn
     *
     * @return BusinessProfileWorkingHour
     */
    public function setCommentEn($commentEn)
    {
        $this->commentEn = $commentEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommentEn()
    {
        return $this->commentEn;
    }

    /**
     * @param string $commentEs
     *
     * @return BusinessProfileWorkingHour
     */
    public function setCommentEs($commentEs)
    {
        $this->commentEs = $commentEs;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommentEs()
    {
        return $this->commentEs;
    }
}
