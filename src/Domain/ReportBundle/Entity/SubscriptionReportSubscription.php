<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SubscriptionReport
 *
 * @ORM\Table(name="subscription_report_subscription")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\SubscriptionReportSubscriptionRepository")
 */
class SubscriptionReportSubscription implements DefaultEntityInterface
{
    use DefaultEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\SubscriptionPlan")
     * @ORM\JoinColumn(name="subscription_plan_id", referencedColumnName="id")
     */
    protected $subscriptionPlan;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Domain\ReportBundle\Entity\SubscriptionReport",
     *     inversedBy="subscriptionReportSubscriptions"
     * )
     * @ORM\JoinColumn(name="subscription_report_id", referencedColumnName="id")
     */
    protected $subscriptionReport;

    /**
     * @var \DateTime
     * @ORM\Column(name="quantity", type="integer", options={"default" : 0})
     */
    protected $quantity = 0;

    /**
     * @return mixed
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getSubscriptionPlan(), $this->getQuantity());
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
     * Set subscriptionPlan
     *
     * @param \Domain\BusinessBundle\Entity\SubscriptionPlan $subscriptionPlan
     *
     * @return SubscriptionReport
     */
    public function setSubscriptionPlan(\Domain\BusinessBundle\Entity\SubscriptionPlan $subscriptionPlan = null)
    {
        $this->subscriptionPlan = $subscriptionPlan;

        return $this;
    }

    /**
     * Get subscriptionPlan
     *
     * @return \Domain\BusinessBundle\Entity\SubscriptionPlan
     */
    public function getSubscriptionPlan()
    {
        return $this->subscriptionPlan;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return SubscriptionReport
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set subscriptionReport
     *
     * @param \Domain\ReportBundle\Entity\SubscriptionReport $subscriptionReport
     *
     * @return SubscriptionReportSubscription
     */
    public function setSubscriptionReport(\Domain\ReportBundle\Entity\SubscriptionReport $subscriptionReport = null)
    {
        $this->subscriptionReport = $subscriptionReport;

        return $this;
    }

    /**
     * Get subscriptionReport
     *
     * @return \Domain\ReportBundle\Entity\SubscriptionReport
     */
    public function getSubscriptionReport()
    {
        return $this->subscriptionReport;
    }
}
