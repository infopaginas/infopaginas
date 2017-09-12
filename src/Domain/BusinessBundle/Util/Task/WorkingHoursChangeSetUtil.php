<?php

namespace Domain\BusinessBundle\Util\Task;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class WorkingHoursChangeSetUtil
{
    /**
     * Prepare business profile working hours collection
     *
     * @access public
     * @param ChangeSetEntry $change
     * @param BusinessProfile $businessProfile
     * @param EntityManagerInterface $entityManager
     * @return ArrayCollection
     */
    public static function getWorkingHoursCollectionFromChangeSet(
        ChangeSetEntry $change,
        BusinessProfile $businessProfile,
        EntityManagerInterface $entityManager
    ) : ArrayCollection {
        $collection = new ArrayCollection();

        $workingHours = json_decode($change->getNewValue());
        $dataOld      = json_decode($change->getOldValue());

        if ($dataOld) {
            foreach ($dataOld as $key => $itemOld) {
                if (!empty($workingHours[$key])) {
                    $workingHours[$key]->id = $itemOld->id;
                }
            }
        }

        if ($workingHours) {
            foreach ($workingHours as $item) {
                $data = json_decode($item->value);

                if (!$item->id) {
                    $workingHour = new BusinessProfileWorkingHour();
                    $workingHour->setBusinessProfile($businessProfile);
                    $entityManager->persist($workingHour);
                } else {
                    $workingHour = $entityManager->getRepository(BusinessProfileWorkingHour::class)->find($item->id);
                }

                $workingHour->setDays($data->days);
                $workingHour->setOpenAllTime($data->openAllTime);

                $accessor = PropertyAccess::createPropertyAccessor();

                foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                    $property = BusinessProfileWorkingHour::FIELD_PREFIX_COMMENT . LocaleHelper::getLangPostfix($locale);

                    if (property_exists($workingHour, $property) and !empty($data->$property)) {
                        $accessor->setValue($workingHour, $property, $data->$property);
                    }
                }

                if (!empty($data->timeStart->date)) {
                    $workingHour->setTimeStart($data->timeStart->date);
                } else {
                    $workingHour->setTimeStart(new \DateTime(BusinessProfileWorkingHour::DEFAULT_DATE));
                }

                if (!empty($data->timeEnd->date)) {
                    $workingHour->setTimeEnd($data->timeEnd->date);
                } else {
                    $workingHour->setTimeEnd(new \DateTime(BusinessProfileWorkingHour::DEFAULT_DATE));
                }

                $collection->add($workingHour);
            }
        }

        return $collection;
    }
}
