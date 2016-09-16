<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Interaction
 *
 * @ORM\Table(name="interaction")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\InteractionRepository")
 */
class Interaction
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

