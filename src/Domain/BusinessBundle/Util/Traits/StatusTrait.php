<?php

namespace Domain\BusinessBundle\Util\Traits;

use Domain\BusinessBundle\Model\StatusInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Exception\InvalidArgumentException;

/**
 * Class StatusTrait
 * @package Domain\BusinessBundle\Util\Traits
 */
trait StatusTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
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
