<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldListItem;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonItem;
use Domain\BusinessBundle\Entity\CustomFields\Section;
use Domain\SiteBundle\Controller\RedirectController;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class SectionManager extends Manager
{
    public function getSectionsByPositionOrder()
    {
        return $this->getEntityManager()->getRepository(Section::class)->findBy([], ['position' => 'ASC']);
    }

    public function getCustomFieldsOrderedBySectionPosition($locale, $businessProfile)
    {
        $orderedSections = $this->getEntityManager()->getRepository(Section::class)->findBy([], ['position' => 'ASC']);

        $sections = [];

        foreach ($orderedSections as $section) {
            $sections[$section->getTitle()] = [];
        }

        foreach ($businessProfile->getCheckboxCollection() as $key => $value) {
            $sections[$value->getCheckboxes()->getSection()->getTitle()][] = [
                'title' => !$value->getCheckboxes()->getHideTitle() ? $value->getCheckboxes()->getTitle() : null,
                'value' => $value->getIsAvailable(),
            ];
        }

        foreach ($businessProfile->getRadioButtonCollection() as $key => $value) {
            $radioButtonValue = $this->getEntityManager()->getRepository(BusinessCustomFieldRadioButtonItem::class)
                ->find($value->getValue());

            $sections[$value->getRadioButtons()->getSection()->getTitle()][] = [
                'title' => !$value->getRadioButtons()->getHideTitle() ? $value->getRadioButtons()->getTitle() : null,
                'value' => $radioButtonValue->getTitle(),
            ];
        }

        foreach ($businessProfile->getListCollection() as $key => $value) {
            $listValue = $this->getEntityManager()->getRepository(BusinessCustomFieldListItem::class)
                ->find($value->getValue());

            $sections[$value->getLists()->getSection()->getTitle()][] = [
                'title' => !$value->getLists()->getHideTitle() ? $value->getLists()->getTitle() : null,
                'value' => $listValue->getTitle(),
            ];
        }

        foreach ($businessProfile->getTextAreaCollection() as $key => $value) {
            $textAreaValue = ($locale === RedirectController::LOCALE_EN)
                ? $value->getTextAreaValueEn()
                : $value->getTextAreaValueEs();

            $sections[$value->getTextAreas()->getSection()->getTitle()][] = [
                'title' => !$value->getTextAreas()->getHideTitle() ? $value->getTextAreas()->getTitle() : null,
                'value' => $textAreaValue,
            ];
        }

        return $sections;
    }
}
