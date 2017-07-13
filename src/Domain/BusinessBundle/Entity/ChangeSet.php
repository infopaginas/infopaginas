<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * ChangeSet
 *
 * @ORM\Table(name="change_set")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\ChangeSetRepository")
 */
class ChangeSet
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\ChangeSetEntry",
     *      mappedBy="changeSet",
     *      cascade={"persist", "remove"}
     * )
     */
    private $entries;

    /**
     * ChangeSet constructor.
     */
    public function __construct()
    {
        $this->entries = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param ChangeSetEntry $entry
     *
     * @return ChangeSet
     */
    public function addEntry($entry)
    {
        $this->entries[] = $entry;

        return $this;
    }

    /**
     * @param ChangeSetEntry $changeSetEntry
     */
    public function removeEntry(ChangeSetEntry $changeSetEntry)
    {
        $this->entries->removeElement($changeSetEntry);
    }
}
