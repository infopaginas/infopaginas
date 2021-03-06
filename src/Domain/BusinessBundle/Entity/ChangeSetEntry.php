<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChangeSetEntry
 *
 * @ORM\Table(name="change_set_entry")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\ChangeSetEntryRepository")
 */
class ChangeSetEntry
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
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\ChangeSet",
     *     inversedBy="entries"
     * )
     * @ORM\JoinColumn(name="changeset_id", referencedColumnName="id", nullable=false)
     */
    private $changeSet;

    /**
     * @ORM\Column(name="field_name", type="string", length=100)
     */
    private $fieldName;

    /**
     * @ORM\Column(name="class_name", type="string", nullable=true, length=100)
     */
    private $className;

    /**
     * @ORM\Column(name="old_value", type="text", nullable=true)
     */
    private $oldValue;

    /**
     * @ORM\Column(name="new_value", type="text", nullable=true)
     */
    private $newValue;

    /**
     * @ORM\Column(name="action", type="string", length=100)
     */
    private $action;

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
     * @return ChangeSet
     */
    public function getChangeSet()
    {
        return $this->changeSet;
    }

    /**
     * @param ChangeSet $changeSet
     *
     * @return ChangeSetEntry
     */
    public function setChangeSet($changeSet)
    {
        $this->changeSet = $changeSet;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     * @return ChangeSetEntry
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @param string|null $oldValue
     *
     * @return ChangeSetEntry
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * @param string|null $newValue
     *
     * @return ChangeSetEntry
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return ChangeSetEntry
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string|null $className
     *
     * @return ChangeSetEntry
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }
}
