<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

trait ValidateIsUsedCollectionTrait
{
    private $isUsed = false;

    public function validateIsUsedCollection($deleteDiff, $errorElement, $repository)
    {
        foreach ($deleteDiff as $item) {
            $isUsed = $repository->findBy(['value' => $item->getId()]);

            if ($isUsed && !$this->isUsed) {
                $this->isUsed = true;

                $errorElement->with('position')
                    ->addViolation($this->getTranslator()->trans('business_custom_field_item.exist'))
                    ->end();

                break;
            }
        }
    }
}
