<?php

namespace Oxa\DfpBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;

/**
 * DoubleClickCompany
 *
 * @ORM\Table(name="double_click_company")
 * @ORM\Entity(repositoryClass="Oxa\DfpBundle\Repository\DoubleClickCompanyRepository")
 */
class DoubleClickCompany
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="doubleclick_company_id", type="text")
     */
    private $doubleClickCompanyId;

    /**
     * @var BusinessProfile
     *
     * @ORM\OneToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="doubleClickCompany",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $businessProfile;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Oxa\DfpBundle\Entity\DoubleClickOrder",
     *     mappedBy="doubleClickCompany",
     *     cascade={"persist"}
     * )
     */
    private $doubleClickOrders;

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
     * Constructor
     */
    public function __construct()
    {
        $this->doubleClickOrders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set doubleClickCompanyId
     *
     * @param string $doubleClickCompanyId
     *
     * @return DoubleClickCompany
     */
    public function setDoubleClickCompanyId($doubleClickCompanyId)
    {
        $this->doubleClickCompanyId = $doubleClickCompanyId;

        return $this;
    }

    /**
     * Get doubleClickCompanyId
     *
     * @return string
     */
    public function getDoubleClickCompanyId()
    {
        return $this->doubleClickCompanyId;
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return DoubleClickCompany
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

    /**
     * Add doubleClickOrder
     *
     * @param \Oxa\DfpBundle\Entity\DoubleClickOrder $doubleClickOrder
     *
     * @return DoubleClickCompany
     */
    public function addDoubleClickOrder(\Oxa\DfpBundle\Entity\DoubleClickOrder $doubleClickOrder)
    {
        $this->doubleClickOrders[] = $doubleClickOrder;

        return $this;
    }

    /**
     * Remove doubleClickOrder
     *
     * @param \Oxa\DfpBundle\Entity\DoubleClickOrder $doubleClickOrder
     */
    public function removeDoubleClickOrder(\Oxa\DfpBundle\Entity\DoubleClickOrder $doubleClickOrder)
    {
        $this->doubleClickOrders->removeElement($doubleClickOrder);
    }

    /**
     * Get doubleClickOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoubleClickOrders()
    {
        return $this->doubleClickOrders;
    }
}
