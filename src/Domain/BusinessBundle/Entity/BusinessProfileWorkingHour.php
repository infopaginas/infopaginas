<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Column(name="time_start", type="time")
     */
    private $timeStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_end", type="time")
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
     * @Assert\NotBlank()
     * @Assert\Length(max=15)
     */
    protected $day;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isOpenAllTime = false;
    }

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
        if (!$timeStart) {
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
        if (!$timeEnd) {
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
}
