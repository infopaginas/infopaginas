<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessProfilePhone
 *
 * @ORM\Table(name="business_profile_phones")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfilePhoneRepository")
 */
class BusinessProfilePhone
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
     * @var string - Contact phone number
     *
     * @ORM\Column(name="phone", type="string", length=10)
     */
    private $phone;

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
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return BusinessProfilePhone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }
}
