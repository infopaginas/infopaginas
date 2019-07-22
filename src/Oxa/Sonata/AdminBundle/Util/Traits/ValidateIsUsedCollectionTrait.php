<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

trait ValidateIsUsedCollectionTrait
{
    private $isUsed = false;
    private $maxBusinessNamesShow = 10;

    public function validateIsUsedCollection($deleteDiff, $errorElement, $repository)
    {
        foreach ($deleteDiff as $item) {
            $isUsed = $repository->findBy(['value' => $item->getId()]);

            if ($isUsed && !$this->isUsed) {
                $this->isUsed = true;
                $i = 0;

                foreach ($isUsed as $value) {
                    $businessProfiles[] = $value->getBusinessProfile()->getName();
                    $i++;

                    if ($i >= $this->maxBusinessNamesShow) {
                        break;
                    }
                }

                $businessProfiles = implode($businessProfiles, ', ');

                if (count($isUsed) > $this->maxBusinessNamesShow) {
                    $businessProfiles = $businessProfiles . '...';
                }

                $errorElement->with('position')
                    ->addViolation($this->getTranslator()->trans(
                        'business_custom_field_item.exist'),
                        ['%businessProfiles%' => $businessProfiles]
                    )
                    ->end();

                break;
            }
        }
    }
}
