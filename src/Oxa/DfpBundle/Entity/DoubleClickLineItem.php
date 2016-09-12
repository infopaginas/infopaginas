<?php

namespace Oxa\DfpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoubleClickLineItem
 *
 * @ORM\Table(name="double_click_line_item")
 * @ORM\Entity(repositoryClass="Oxa\DfpBundle\Repository\DoubleClickLineItemRepository")
 */
class DoubleClickLineItem
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
     * @ORM\Column(name="doubleclick_line_item_id", type="text")
     */
    private $doubleClickLineItemId;

    /**
     * @var DoubleClickOrder
     *
     * @ORM\ManyToOne(targetEntity="Oxa\DfpBundle\Entity\DoubleClickOrder",
     *     inversedBy="doubleClickLineItems",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="doubleclick_order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $doubleClickOrder;

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
     * Set doubleClickLineItemId
     *
     * @param string $doubleClickLineItemId
     *
     * @return DoubleClickLineItem
     */
    public function setDoubleClickLineItemId($doubleClickLineItemId)
    {
        $this->doubleClickLineItemId = $doubleClickLineItemId;

        return $this;
    }

    /**
     * Get doubleClickLineItemId
     *
     * @return string
     */
    public function getDoubleClickLineItemId()
    {
        return $this->doubleClickLineItemId;
    }

    /**
     * Set doubleClickOrder
     *
     * @param \Oxa\DfpBundle\Entity\DoubleClickOrder $doubleClickOrder
     *
     * @return DoubleClickLineItem
     */
    public function setDoubleClickOrder(\Oxa\DfpBundle\Entity\DoubleClickOrder $doubleClickOrder = null)
    {
        $this->doubleClickOrder = $doubleClickOrder;

        return $this;
    }

    /**
     * Get doubleClickOrder
     *
     * @return \Oxa\DfpBundle\Entity\DoubleClickOrder
     */
    public function getDoubleClickOrder()
    {
        return $this->doubleClickOrder;
    }
}
