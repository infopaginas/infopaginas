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

        return self::getProfilesDifferencesArray($updatedProfileDataArray, $currentProfileDataArray);
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
            if (!is_array($value->getData()) && !is_object($value->getData())) {
                $data[$value->getName()] = [
                    'label' => $value->getConfig()->getOption('label'),
                    'value' => $value->getData(),
                ];
            } elseif (is_object($value->getData())) {
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
                    'oldValue' => $currentProfileDataArray[$property]['value'],
                    'newValue' => $data['value'],
                    'action' => 'Field change',
                    'field' => $data['label'],
                ];
            }
        }

        return $differences;
    }
}
