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

    public function getCustomFieldsOrderedBySectionPosition($request, $businessProfile)
    {
        $orderedSections = $this->getEntityManager()->getRepository(Section::class)->findBy([], ['position' => 'ASC']);

        $sections = [];

        foreach ($orderedSections as $section) {
            $sections[$section->getTitle()] = [];
        }

        foreach ($businessProfile->getCheckboxCollection() as $key => $value) {
            $sections[$value->getCheckboxes()->getSection()->getTitle()][] = [
                'title' => $value->getCheckboxes()->getTitle(),
                'value' => $value->getIsAvailable(),
            ];
        }

        foreach ($businessProfile->getRadioButtonCollection() as $key => $value) {
            $radioButtonValue = $this->getEntityManager()->getRepository(BusinessCustomFieldRadioButtonItem::class)
                ->find($value->getValue());

            $sections[$value->getRadioButtons()->getSection()->getTitle()][] = [
                'title' => $value->getRadioButtons()->getTitle(),
                'value' => $radioButtonValue->getTitle(),
            ];
        }

        foreach ($businessProfile->getListCollection() as $key => $value) {
            $listValue = $this->getEntityManager()->getRepository(BusinessCustomFieldListItem::class)->find($value->getValue());

            $sections[$value->getLists()->getSection()->getTitle()][] = [
                'title' => $value->getLists()->getTitle(),
                'value' => $listValue->getTitle(),
            ];
        }

        foreach ($businessProfile->getTextAreaCollection() as $key => $value) {
            $textAreaValue = ($request->getLocale() === RedirectController::LOCALE_EN)
                ? $value->getTextAreaValueEn()
                : $value->getTextAreaValueEs();

            $sections[$value->getTextAreas()->getSection()->getTitle()][] = [
                'title' => $value->getTextAreas()->getTitle(),
                'value' => $textAreaValue,
            ];
        }

        return $sections;
    }
}
