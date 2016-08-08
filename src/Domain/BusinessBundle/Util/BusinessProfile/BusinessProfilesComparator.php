<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.07.16
 * Time: 11:15
 */

namespace Domain\BusinessBundle\Util\BusinessProfile;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Oxa\WistiaBundle\Entity\WistiaMedia;
use Symfony\Component\Form\FormInterface;

/**
 * Class BusinessProfilesComparator
 * @package Domain\BusinessBundle\Util\BusinessProfile
 */
class BusinessProfilesComparator
{
    /**
     * @param FormInterface $updatedBusinessProfileForm
     * @param FormInterface $currentBusinessProfileForm
     * @return array
     */
    public static function compare(
        FormInterface $updatedBusinessProfileForm,
        FormInterface $currentBusinessProfileForm
    ) : array {

        $updatedProfileDataArray = self::mapFormDataAsAnArray($updatedBusinessProfileForm);
        $currentProfileDataArray = self::mapFormDataAsAnArray($currentBusinessProfileForm);

        $fieldDifferences = self::getProfilesDifferencesArray($updatedProfileDataArray, $currentProfileDataArray);
        $imageDifferences = self::getProfileImageDifferencesArray(
            $updatedBusinessProfileForm,
            $currentBusinessProfileForm
        );

        $videoDifferences = self::getProfileVideoDifferencesArray(
            $updatedBusinessProfileForm,
            $currentBusinessProfileForm
        );

        $differences = array_merge($fieldDifferences, $imageDifferences);
        $differences = array_merge($differences, $videoDifferences);

        return $differences;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private static function mapFormDataAsAnArray(FormInterface $form) : array
    {
        $data = [];

        /** @var FormInterface $value */
        foreach ($form->all() as $value) {
            if ($value->getConfig()->getOption('read_only') == true) {
                continue;
            }

            if (!is_array($value->getData()) && !is_object($value->getData()) && $value->getName() !== 'video') {
                $data[$value->getName()] = [
                    'label' => $value->getConfig()->getOption('label'),
                    'value' => $value->getData(),
                ];
            } elseif (is_object($value->getData()) && !in_array($value->getName(), ['images', 'video'])) {
                $isObjectInstanceOfCollection = ($value->getData() instanceof ArrayCollection)
                    || ($value->getData() instanceof PersistentCollection);

                if ($isObjectInstanceOfCollection) {
                    $data[$value->getName()]['label'] = $value->getConfig()->getOption('label');

                    $collection = [];

                    foreach ($value->getData() as $obj) {
                        $collection[] = (string)$obj;
                    }

                    $data[$value->getName()]['value'] = implode(', ', $collection);
                }
            }
        }

        return $data;
    }

    /**
     * @param FormInterface $updatedBusinessProfileForm
     * @param FormInterface $currentBusinessProfileForm
     * @return array
     */
    private static function getProfileImageDifferencesArray(
        FormInterface $updatedBusinessProfileForm,
        FormInterface $currentBusinessProfileForm
    ) : array {
        $updatedBusinessProfileImagesArray = self::getProfileImagesArray($updatedBusinessProfileForm);
        $currentBusinessProfileImagesArray = self::getProfileImagesArray($currentBusinessProfileForm);

        $addedImages = array_diff_assoc($updatedBusinessProfileImagesArray, $currentBusinessProfileImagesArray);
        $removedImages = array_diff_assoc($currentBusinessProfileImagesArray, $updatedBusinessProfileImagesArray);

        $differences = [];

        foreach ($addedImages as $mediaId => $mediaName) {
            $differences['image_' . $mediaId] = [
                'oldValue' => '-',
                'newValue' => $mediaName,
                'action' => 'Image Added',
                'field' => 'Images',
            ];
        }

        foreach ($removedImages as $mediaId => $mediaName) {
            $differences['image_' . $mediaId] = [
                'oldValue' => $mediaName,
                'newValue' => '-',
                'action' => 'Image Removed',
                'field' => 'Images',
            ];
        }

        return $differences;
    }

    private static function getProfileVideoDifferencesArray(
        FormInterface $updatedBusinessProfileForm,
        FormInterface $currentBusinessProfileForm
    ) {
        $differences = [];

        if (!$updatedBusinessProfileForm->has('video')) {
            return $differences;
        }

        /** @var WistiaMedia $video */
        $video = $updatedBusinessProfileForm->get('video')->getData();

        if ($currentBusinessProfileForm->has('video')) {
            /** @var WistiaMedia $oldVideo */
            $oldVideo = $currentBusinessProfileForm->get('video')->getData();
        } else {
            $oldVideo = null;
        }

        if ($video !== null) {
            if ($oldVideo === null) {
                //if videofile added
                $differences['video'] = [
                    'oldValue' => '-',
                    'newValue' => $video->getName(),
                    'action' => 'Video added',
                    'field' => 'Video',
                ];
            } elseif ($oldVideo->getName() !== $video->getName()) {
                $differences['video'] = [
                    'oldValue' => $oldVideo->getName(),
                    'newValue' => $video->getName(),
                    'action' => 'Video changed',
                    'field' => 'Video',
                ];
            }
        }

        if ($oldVideo !== null && $video === null) {
            $differences['video'] = [
                'oldValue' => $oldVideo->getName(),
                'newValue' => '-',
                'action' => 'Video removed',
                'field' => 'Video',
            ];
        }

        return $differences;
    }

    /**
     * @param FormInterface $businessProfileForm
     * @return array
     */
    private static function getProfileImagesArray(FormInterface $businessProfileForm) : array
    {
        $images = [];

        //some subscription plans doesn't support images
        if (!$businessProfileForm->has('images')) {
            return $images;
        }

        $businessProfileGallery = $businessProfileForm->get('images')->getData();

        /** @var BusinessGallery $image */
        foreach ($businessProfileGallery as $image) {
            $images[$image->getMedia()->getId()] = $image->getMedia()->getName();
        }

        return $images;
    }

    /**
     * @param array $updatedProfileDataArray
     * @param array $currentProfileDataArray
     * @return array
     */
    private static function getProfilesDifferencesArray(
        array $updatedProfileDataArray,
        array $currentProfileDataArray
    ) : array {
        $differences = [];

        foreach ($updatedProfileDataArray as $property => $data) {
            if ((string)$currentProfileDataArray[$property]['value'] !== (string)$data['value']) {
                $differences[$property] = [
                    'oldValue' => (string)$currentProfileDataArray[$property]['value'],
                    'newValue' => (string)$data['value'],
                    'action' => 'Field change',
                    'field' => $data['label'],
                ];
            }
        }

        return $differences;
    }
}
