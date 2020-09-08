<?php

namespace Domain\BusinessBundle\Util\Traits;

use Domain\BusinessBundle\Model\StatusInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Exception\InvalidArgumentException;
use Oxa\VideoBundle\Entity\VideoMedia;

/**
 * Class StatusTrait
 * @package Domain\BusinessBundle\Util\Traits
 */
trait StatusTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status;

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        if (!array_key_exists($status, self::getStatuses())) {
            throw new InvalidArgumentException('Unknown status code');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            StatusInterface::STATUS_ACTIVE    => 'Active',
            StatusInterface::STATUS_EXPIRED   => 'Expired',
            StatusInterface::STATUS_CANCELED  => 'Canceled',
            StatusInterface::STATUS_PENDING   => 'Pending',
        ];
    }

    public static function getActualStatuses()
    {
        return [
            StatusInterface::STATUS_ACTIVE,
            StatusInterface::STATUS_PENDING,
        ];
    }

    public static function getVideoStatuses(): array
    {
        return [
            VideoMedia::VIDEO_STATUS_ACTIVE  => 'Active',
            VideoMedia::VIDEO_STATUS_PENDING => 'Pending',
            VideoMedia::VIDEO_STATUS_ERROR   => 'Error',
        ];
    }

    /**
     * @return array
     */
    public function getStatusValue()
    {
        $statuses = self::getStatuses();

        return $statuses[$this->getStatus()];
    }
}
