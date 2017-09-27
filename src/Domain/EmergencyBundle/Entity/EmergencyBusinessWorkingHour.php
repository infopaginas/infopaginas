<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmergencyBusinessWorkingHour
 *
 * @ORM\Table(name="emergency_business_working_hour")
 * @ORM\Entity(repositoryClass="Domain\EmergencyBundle\Repository\EmergencyBusinessWorkingHourRepository")
 */
class EmergencyBusinessWorkingHour
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
     * @var EmergencyBusiness
     * @ORM\ManyToOne(targetEntity="Domain\EmergencyBundle\Entity\EmergencyBusiness",
     *     cascade={"persist"},
     *     inversedBy="collectionWorkingHours",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $business;

    /**
     * @ORM\Column(name="days", type="json_array", nullable=true)
     * @Assert\Count(min = 1)
     */
    protected $days;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->openAllTime = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getJsonData();
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
     * @param \DateTime $timeStart
     *
     * @return EmergencyBusinessWorkingHour
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
     * @return EmergencyBusinessWorkingHour
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
     * @return EmergencyBusinessWorkingHour
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
     * @param EmergencyBusiness|null $business
     *
     * @return EmergencyBusinessWorkingHour
     */
    public function setBusiness(EmergencyBusiness $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return EmergencyBusiness
     */
    public function getBusiness()
    {
        return $this->business;
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
     * @return EmergencyBusinessWorkingHour
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }
}
