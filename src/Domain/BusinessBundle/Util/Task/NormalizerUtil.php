<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 31.08.16
 * Time: 21:36
 */

namespace Domain\BusinessBundle\Util\Task;

use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class NormalizerUtil
 * @package Domain\BusinessBundle\Util\Task
 */
class NormalizerUtil
{
    /**
     * @param string $action
     * @param TranslatorInterface $translator
     * @return string
     */
    public static function normalizeTaskChangeActionLabel(string $action, TranslatorInterface $translator)
    {
        $actionLabels = [
            ChangeSetCalculator::PROPERTY_CHANGE                => 'Field Change',
            ChangeSetCalculator::PROPERTY_ADD                   => 'Field Change',
            ChangeSetCalculator::PROPERTY_REMOVE                => 'Field Change',
            ChangeSetCalculator::IMAGE_ADD                      => 'Image Add',
            ChangeSetCalculator::IMAGE_REMOVE                   => 'Image Remove',
            ChangeSetCalculator::VIDEO_ADD                      => 'Video Add',
            ChangeSetCalculator::VIDEO_REMOVE                   => 'Video Remove',
            ChangeSetCalculator::VIDEO_UPDATE                   => 'Video Change',
            ChangeSetCalculator::VIDEO_PROPERTY_UPDATE          => 'Video Property Change',
            ChangeSetCalculator::LOGO_ADD                       => 'Logo Add',
            ChangeSetCalculator::LOGO_REMOVE                    => 'Logo Remove',
            ChangeSetCalculator::LOGO_UPDATE                    => 'Logo Update',
            ChangeSetCalculator::BACKGROUND_ADD                 => 'Background Add',
            ChangeSetCalculator::BACKGROUND_REMOVE              => 'Background Remove',
            ChangeSetCalculator::BACKGROUND_UPDATE              => 'Background Update',
            ChangeSetCalculator::PROPERTY_IMAGE_PROPERTY_UPDATE => 'Image Property Change',

            ChangeSetCalculator::CHANGE_COMMON_PROPERTY              => 'Property changed',
            ChangeSetCalculator::CHANGE_TRANSLATION                  => 'Translation changed',
            ChangeSetCalculator::CHANGE_RELATION_MANY_TO_ONE         => 'Relation changed',
            ChangeSetCalculator::CHANGE_RELATION_ONE_TO_MANY         => 'Relation changed',
            ChangeSetCalculator::CHANGE_RELATION_MANY_TO_MANY        => 'Relation changed',
            ChangeSetCalculator::CHANGE_MEDIA_RELATION_MANY_TO_ONE   => 'Media Relation changed',
            ChangeSetCalculator::CHANGE_MEDIA_RELATION_ONE_TO_MANY   => 'Media Relation changed',
        ];

        return $translator->trans($actionLabels[$action]);
    }

    /**
     * @param string $field
     * @param FormInterface $form
     * @return string
     */
    public static function normalizeTaskFieldNameLabel(string $field, FormInterface $form)
    {
        $labels = [];

        foreach ($form->all() as $value) {
            $labels[$value->getName()] = $value->getConfig()->getOption('label');
        }

        return isset($labels[$field]) ? $labels[$field] : $field;
    }
}
