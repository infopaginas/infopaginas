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
     * @ORM\Column(name="old_value", type="text")
     */
    private $oldValue;

    /**
     * @ORM\Column(name="new_value", type="text")
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
     * @return mixed
     */
    public function getChangeSet()
    {
        return $this->changeSet;
    }

    /**
     * @param mixed $changeSet
     * @return ChangeSetEntry
     */
    public function setChangeSet($changeSet)
    {
        $this->changeSet = $changeSet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param mixed $fieldName
     * @return ChangeSetEntry
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @param mixed $oldValue
     * @return ChangeSetEntry
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * @param mixed $newValue
     * @return ChangeSetEntry
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     * @return ChangeSetEntry
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     * @return ChangeSetEntry
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }
}
