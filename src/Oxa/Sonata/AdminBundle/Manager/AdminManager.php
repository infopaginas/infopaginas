<?php
declare(strict_types=1);

namespace Oxa\Sonata\AdminBundle\Manager;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckbox;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldList;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButton;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldTextArea;
use Domain\BusinessBundle\Entity\LandingPageShortCut;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\EmergencyBundle\Entity\EmergencyAbstractBusiness;
use Domain\ReportBundle\Model\UserActionModel;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\Sonata\AdminBundle\Model\PostponeRemoveInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * Used to customise admin
 *
 * Class AdminManager
 * @package Oxa\Sonata\AdminBundle\Manager
 */
class AdminManager extends DefaultManager
{
    /**
     * Delete record completely
     *
     * @param $entity
     * @param AdminInterface $admin
     * @throws InvalidArgumentException
     */
    public function deletePhysicalEntity($entity, AdminInterface $admin)
    {
        $existDependentFields = $this->checkExistDependentEntity($entity);

        if ($existDependentFields) {
            throw new \Exception($this->getContainer()->get('translator')->trans(
                'flash_delete_error_rel',
                array(
                    '%fields%' => implode(', ', $existDependentFields),
                ),
                'SonataAdminBundle'
            ));
        }

        $this->removeEntity($entity, $admin);
        $this->getEntityManager()->flush();
    }

    /**
     * Clone and persist object
     *
     * @param CopyableEntityInterface $entity
     * @return CopyableEntityInterface
     * @throws \Throwable
     * @throws \TypeError
     */
    protected function cloneEntityObject(CopyableEntityInterface $entity)
    {
        $propertyAccessor = $this->getContainer()->get('property_accessor');
        $copyMark = $this->getContainer()->get('translator')->trans('copy_', [], 'SonataAdminBundle');

        $clone = clone $entity;
        $value = $propertyAccessor->getValue($clone, $entity->getMarkCopyPropertyName());
        $propertyAccessor->setValue($clone, $entity->getMarkCopyPropertyName(), sprintf('%s%s', $copyMark, $value));

        $this->getEntityManager()->persist($clone);

        return $clone;
    }

    /**
     * Clone object with all relations
     *
     * @param CopyableEntityInterface $entity
     * @return CopyableEntityInterface
     * @throws \Throwable
     * @throws \TypeError
     */
    public function cloneEntity(CopyableEntityInterface $entity)
    {
        $this->cloneEntityObject($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Restore object with all relations
     *
     * @param PostponeRemoveInterface $entity
     * @param AdminInterface $admin
     */
    public function restoreEntity(PostponeRemoveInterface $entity, $admin)
    {
        $entity->setIsDeleted(false);
        $this->getEntityManager()->flush();

        $admin->handleActionLog(UserActionModel::TYPE_ACTION_RESTORE, $entity);
    }

    /**
     * Restore objects
     *
     * @param array             $entityArray
     * @param AdminInterface    $admin
     */
    public function restoreEntities(array $entityArray, $admin)
    {
        foreach ($entityArray as $entity) {
            /** @var $entity PostponeRemoveInterface */
            if ($entity->getIsDeleted()) {
                $entity->setIsDeleted(false);

                $admin->handleActionLog(UserActionModel::TYPE_ACTION_RESTORE, $entity);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Check if entity has relation with other entities
     *
     * @param $entity
     * @return array
     */
    public function checkExistDependentEntity($entity)
    {
        $metadata = $this->getEntityManager()->getClassMetadata(get_class($entity));
        $existDependentField = [];
        foreach ($metadata->getAssociationMappings() as $associationMapping) {
            if ($associationMapping['type'] == ClassMetadataInfo::ONE_TO_MANY ||
                $associationMapping['type'] == ClassMetadataInfo::ONE_TO_ONE
            ) {
                // ignore for translations
                if ($associationMapping['fieldName'] == 'translations') {
                    // disable sonata event listener
                    // to prevent removing record translations
                    // while we softdelete record
                    $this->getEntityManager()
                        ->getEventManager()
                        ->removeEventSubscriber(
                            $this->getContainer()->get('sonata_translation.listener.translatable')
                        );

                    continue;
                }

                //allow delete business profile
                if ($entity instanceof BusinessProfile) {
                    continue;
                }

                //allow delete article
                if ($entity instanceof Article) {
                    continue;
                }

                //allow delete emergency businesses and drafts
                if ($entity instanceof EmergencyAbstractBusiness) {
                    continue;
                }

                //allow delete catalog locality - see LocalityAdmin preRemove
                if ($entity instanceof Locality and
                    ($associationMapping['fieldName'] == Locality::ALLOW_DELETE_ASSOCIATED_FIELD_BUSINESS_PROFILES or
                        $associationMapping['fieldName'] == Locality::ALLOW_DELETE_ASSOCIATED_FIELD_CATALOG_ITEMS
                    )
                ) {
                    continue;
                }

                //allow delete category that associated only with Catalog items
                if ($entity instanceof Category and
                    $associationMapping['fieldName'] == Category::ALLOW_DELETE_ASSOCIATED_FIELD_CATALOG_ITEMS
                ) {
                    continue;
                }

                //allow delete LandingPageShortCut
                if ($entity instanceof LandingPageShortCut) {
                    continue;
                }

                //allow delete BusinessCustomFieldList
                if ($entity instanceof BusinessCustomFieldList) {
                    continue;
                }

                //allow delete BusinessCustomFieldRadioButton
                if ($entity instanceof BusinessCustomFieldRadioButton) {
                    continue;
                }

                //allow delete BusinessCustomFieldCheckbox
                if ($entity instanceof BusinessCustomFieldCheckbox) {
                    continue;
                }

                //allow delete BusinessCustomFieldTextArea
                if ($entity instanceof BusinessCustomFieldTextArea) {
                    continue;
                }

                $methodGet = 'get' . ucfirst($associationMapping['fieldName']);
                $childs = $entity->$methodGet();
                if (count($childs)) {
                    $existDependentField[] = $this->getContainer()->get('translator')->trans(
                        ucfirst($associationMapping['fieldName']),
                        []
                    );
                }
            }
        }

        // prevent deletion of external article
        if ($entity instanceof Article and $entity->getIsExternal()) {
            $existDependentField[] = 'External Article';
        }

        // prevent deletion of hardcoded categories
        if ($entity instanceof Category and ($entity->getSlugEn() or $entity->getSlugEs())) {
            $existDependentField[] = 'Protected Category';
        }

        // prevent deletion of hardcoded subscription plans
        if ($entity instanceof SubscriptionPlan) {
            $existDependentField[] = 'Protected Item';
        }

        // prevent deletion of hardcoded paymentMethods
        if ($entity instanceof PaymentMethod and $entity->getType()) {
            $existDependentField[] = 'Protected Item';
        }

        return $existDependentField;
    }

    /**
     * Check if such entity class really exists
     *
     * @param $entityClass
     * @throws InvalidArgumentException
     */
    protected function checkIfEntityClassIsValid(string $entityClass)
    {
        $mappedEntities = $this->getEntityManager()
            ->getConfiguration()
            ->getMetadataDriverImpl()
            ->getAllClassNames();

        if (!in_array($entityClass, $mappedEntities)) {
            throw new InvalidArgumentException(sprintf('Entity "%s" does not exist', $entityClass));
        }
    }

    /**
     * Delete records softly
     *
     * @param array             $entityArray
     * @param AdminInterface    $admin
     * @throws \Exception
     */
    public function removeEntities(array $entityArray, $admin)
    {
        foreach ($entityArray as $entity) {
            $existDependentFields = $this->checkExistDependentEntity($entity);

            if ($existDependentFields) {
                throw new \Exception($this->getContainer()->get('translator')->trans(
                    'batch_delete_error_rel',
                    array(
                        'record_id' => $entity->getId(),
                        '%fields%' => implode(', ', $existDependentFields),
                    ),
                    'SonataAdminBundle'
                ));
            }

            $this->removeEntity($entity, $admin);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Clone objects with all relations
     *
     * @param array $entityArray
     */
    public function cloneEntities(array $entityArray = [])
    {
        foreach ($entityArray as $entity) {
            if ($entity instanceof CopyableEntityInterface) {
                $this->cloneEntityObject($entity);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Used in twig extension
     *
     * @param $entityClass
     * @param array $entityIdList
     * @return array
     */
    public function getObjectList($entityClass, array $entityIdList)
    {
        $qb = $this->getEntityManager()
            ->getRepository($entityClass)
            ->createQueryBuilder('o');

        return $qb
            ->where(
                $qb->expr()->in('o.id', $entityIdList)
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $entity object
     *
     * @return string
     */
    public function getDeleteSuccessFlashMessage($entity)
    {
        if ($entity instanceof PostponeRemoveInterface) {
            $message = 'flash_postpone_delete_success';
        } else {
            $message = 'flash_delete_success';
        }

        return $message;
    }

    /**
     * @param $entityArray array
     *
     * @return string
     */
    public function getBatchDeleteSuccessFlashMessage($entityArray = [])
    {
        $message = 'flash_batch_delete_success';

        if ($entityArray) {
            $entity = current($entityArray);

            if ($entity and $entity instanceof PostponeRemoveInterface) {
                $message = 'flash_postpone_batch_delete_success';
            }
        }

        return $message;
    }

    /**
     * @param $entity object
     * @param AdminInterface $admin
     */
    private function removeEntity($entity, $admin)
    {
        if ($entity instanceof PostponeRemoveInterface) {
            $entity->setIsDeleted(true);
            $admin->handleActionLog(
                UserActionModel::TYPE_ACTION_POSTPONE_DELETE,
                $entity
            );

            if ($entity instanceof BusinessProfile) {
                $entity->setIsActive(false);
            } elseif ($entity instanceof Article) {
                $entity->setIsPublished(false);
            }
        } else {
            $this->getEntityManager()->remove($entity);
            $admin->handleActionLog(
                UserActionModel::TYPE_ACTION_PHYSICAL_DELETE,
                $entity
            );
        }
    }
}
