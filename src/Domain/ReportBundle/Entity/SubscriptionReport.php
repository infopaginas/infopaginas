<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\ReportInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Domain\ReportBundle\Entity\SubscriptionReportSubscription;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SubscriptionReport
 *
 * @ORM\Table(name="subscription_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\SubscriptionReportRepository")
 */
class SubscriptionReport implements DefaultEntityInterface, ReportInterface
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
     * @var \DateTime
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * @var SubscriptionReportSubscription[] $subscriptionReportSubscriptions
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ReportBundle\Entity\SubscriptionReportSubscription",
     *     mappedBy="subscriptionReport",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $subscriptionReportSubscriptions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subscriptionReportSubscriptions = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_SUBSCRIPTION_REPORT => self::FORMAT_PDF,
            self::CODE_EXCEL_SUBSCRIPTION_REPORT => self::FORMAT_EXCEL,
        ];
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return SubscriptionReport
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getTotal()
    {
        $sum = 0;

        foreach ($this->getSubscriptionReportSubscriptions() as $subscriptionReportSubscription) {
            $sum += $subscriptionReportSubscription->getQuantity();
        }

        return $sum;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add subscriptionReportSubscription
     *
     * @param \Domain\ReportBundle\Entity\SubscriptionReportSubscription $subscriptionReportSubscription
     *
     * @return SubscriptionReport
     */
    public function addSubscriptionReportSubscription(SubscriptionReportSubscription $subscriptionReportSubscription)
    {
        $this->subscriptionReportSubscriptions[] = $subscriptionReportSubscription;

        return $this;
    }

    /**
     * Remove subscriptionReportSubscription
     *
     * @param \Domain\ReportBundle\Entity\SubscriptionReportSubscription $subscriptionReportSubscription
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSubscriptionReportSubscription(SubscriptionReportSubscription $subscriptionReportSubscription)
    {
        return $this->subscriptionReportSubscriptions->removeElement($subscriptionReportSubscription);
    }

    /**
     * Get subscriptionReportSubscriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscriptionReportSubscriptions()
    {
        return $this->subscriptionReportSubscriptions;
    }
}
