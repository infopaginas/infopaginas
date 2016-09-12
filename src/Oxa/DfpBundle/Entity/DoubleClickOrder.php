<?php

namespace Oxa\DfpBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * DoubleClickOrder
 *
 * @ORM\Table(name="double_click_order")
 * @ORM\Entity(repositoryClass="Oxa\DfpBundle\Repository\DoubleClickOrderRepository")
 */
class DoubleClickOrder
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
     * @ORM\Column(name="doubleclick_order_id", type="text")
     */
    private $doubleClickOrderId;

    /**
     * @var DoubleClickCompany
     *
     * @ORM\ManyToOne(targetEntity="Oxa\DfpBundle\Entity\DoubleClickCompany",
     *     inversedBy="doubleClickOrders",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="doubleclick_company_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $doubleClickCompany;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Oxa\DfpBundle\Entity\DoubleClickLineItem",
     *     mappedBy="doubleClickOrder",
     *     cascade={"persist"}
     * )
     */
    private $doubleClickLineItems;

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
        $this->doubleClickLineItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set doubleClickOrderId
     *
     * @param string $doubleClickOrderId
     *
     * @return DoubleClickOrder
     */
    public function setDoubleClickOrderId($doubleClickOrderId)
    {
        $this->doubleClickOrderId = $doubleClickOrderId;

        return $this;
    }

    /**
     * Get doubleClickOrderId
     *
     * @return string
     */
    public function getDoubleClickOrderId()
    {
        return $this->doubleClickOrderId;
    }

    /**
     * Set doubleClickCompany
     *
     * @param \Oxa\DfpBundle\Entity\DoubleClickCompany $doubleClickCompany
     *
     * @return DoubleClickOrder
     */
    public function setDoubleClickCompany(\Oxa\DfpBundle\Entity\DoubleClickCompany $doubleClickCompany = null)
    {
        $this->doubleClickCompany = $doubleClickCompany;

        return $this;
    }

    /**
     * Get doubleClickCompany
     *
     * @return \Oxa\DfpBundle\Entity\DoubleClickCompany
     */
    public function getDoubleClickCompany()
    {
        return $this->doubleClickCompany;
    }

    /**
     * Add doubleClickLineItem
     *
     * @param \Oxa\DfpBundle\Entity\DoubleClickLineItem $doubleClickLineItem
     *
     * @return DoubleClickOrder
     */
    public function addDoubleClickLineItem(\Oxa\DfpBundle\Entity\DoubleClickLineItem $doubleClickLineItem)
    {
        $this->doubleClickLineItems[] = $doubleClickLineItem;

        return $this;
    }

    /**
     * Remove doubleClickLineItem
     *
     * @param \Oxa\DfpBundle\Entity\DoubleClickLineItem $doubleClickLineItem
     */
    public function removeDoubleClickLineItem(\Oxa\DfpBundle\Entity\DoubleClickLineItem $doubleClickLineItem)
    {
        $this->doubleClickLineItems->removeElement($doubleClickLineItem);
    }

    /**
     * Get doubleClickLineItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoubleClickLineItems()
    {
        return $this->doubleClickLineItems;
    }
}
