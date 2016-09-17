<?php

namespace Oxa\DfpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Gedmo\Timestampable\Traits\Timestampable;

/**
 * DoubleClickSynchLog
 *
 * @ORM\Table(name="double_click_synch_log")
 * @ORM\Entity(repositoryClass="Oxa\DfpBundle\Repository\DoubleClickSynchLogRepository")
 */
class DoubleClickSynchLog
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var BusinessProfile
     *
     * @ORM\OneToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="doubleClickSynchLog",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $businessProfile;

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
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return DoubleClickSynchLog
     */
    public function setBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }
}
