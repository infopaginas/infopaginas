<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.07.16
 * Time: 12:05
 */

namespace Domain\BusinessBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Gedmo\Translatable\TranslatableListener;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Form\FormInterface;

/**
 * Class BusinessProfilesChangesetExtension
 * @package Domain\BusinessBundle\Twig\Extension
 */
class BusinessProfilesChangesetExtension extends \Twig_Extension
{
    /** @var FormInterface */
    private $form;

    /** @var EntityManager */
    private $entityManager;

    /** @var TranslatableListener */
    private $translatableListener;

    /**
     * @param FormInterface $form
     */
    public function setFormObject(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @param TranslatableListener $translatableListener
     */
    public function setTranslatableListener(TranslatableListener $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return ['get_business_profiles_changeset_array' => new \Twig_Function_Method($this, 'deserializeChangeSet')];
    }

    /**
     * @param string $changeSet
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    public function deserializeChangeSet(string $changeSet)
    {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->deserialize($changeSet, 'array', 'json');
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'business_profiles_changeset_extension';
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @return TranslatableListener
     */
    private function getTranslatableListener() : TranslatableListener
    {
        return $this->translatableListener;
    }
}
