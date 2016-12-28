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
            ChangeSetCalculator::PROPERTY_CHANGE        => 'Field Change',
            ChangeSetCalculator::PROPERTY_ADD           => 'Field Change',
            ChangeSetCalculator::PROPERTY_REMOVE        => 'Field Change',
            ChangeSetCalculator::PROPERTY_IMAGE_UPDATE  => 'Field Change',
            ChangeSetCalculator::PROPERTY_IMAGE_ADD     => 'Field Change',
            ChangeSetCalculator::PROPERTY_IMAGE_REMOVE  => 'Field Change',
            ChangeSetCalculator::IMAGE_ADD              => 'Image Add',
            ChangeSetCalculator::IMAGE_REMOVE           => 'Image Remove',
            ChangeSetCalculator::IMAGE_UPDATE           => 'Image Change',
            ChangeSetCalculator::VIDEO_ADD              => 'Video Add',
            ChangeSetCalculator::VIDEO_REMOVE           => 'Video Remove',
            ChangeSetCalculator::VIDEO_UPDATE           => 'Video Change',
            ChangeSetCalculator::PROPERTY_IMAGE_PROPERTY_UPDATE => 'Image Propetry Change',
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
