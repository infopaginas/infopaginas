<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessSeoDescriptionFixCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:business-seo-description:fix');
        $this->setDescription('Fix business seo description');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $businesses = $this->em->getRepository(BusinessProfile::class)->getActiveBusinessProfilesIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            $business = $row[0];

            $this->handleSeoBlockUpdate($business);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i ++;
        }

        $this->em->flush();
    }

    /**
     * @param BusinessProfile $entity
     *
     * @return BusinessProfile
     */
    private function handleSeoBlockUpdate($entity)
    {
        $seoTitleEn = BusinessProfileUtil::seoTitleBuilder(
            $entity,
            $this->getContainer(),
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoTitleEs = BusinessProfileUtil::seoTitleBuilder(
            $entity,
            $this->getContainer(),
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $seoDescriptionEn = BusinessProfileUtil::seoDescriptionBuilder(
            $entity,
            $this->getContainer(),
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoDescriptionEs = BusinessProfileUtil::seoDescriptionBuilder(
            $entity,
            $this->getContainer(),
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $this->handleTranslations(
            $entity,
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE,
            [
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_EN => $seoTitleEn,
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_ES => $seoTitleEs,
            ]
        );

        $seoDescKeyEn = BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_EN;
        $seoDescKeyEs = BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_ES;

        $this->handleTranslations(
            $entity,
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION,
            [
                $seoDescKeyEn => $seoDescriptionEn,
                $seoDescKeyEs => $seoDescriptionEs,
            ]
        );

        return $entity;
    }

    /**
     * @param BusinessProfile $entity
     * @param string          $property
     * @param string|null     $data
     *
     * @return BusinessProfile
     */
    private function handleTranslations($entity, $property, $data)
    {
        $propertyEn = $property . BusinessProfile::TRANSLATION_LANG_EN;
        $propertyEs = $property . BusinessProfile::TRANSLATION_LANG_ES;

        $dataEn = false;
        $dataEs = false;

        if (!empty($data[$propertyEn])) {
            $dataEn = trim($data[$propertyEn]);
        }

        if (!empty($data[$propertyEs])) {
            $dataEs = trim($data[$propertyEs]);
        }

        if (property_exists($entity, $property)) {
            if ($dataEs) {
                if ($entity->{'get' . $property}() and $dataEn) {
                    $entity->{'set' . $property}($dataEn);
                } else {
                    $entity->{'set' . $property}($dataEs);
                }

                if (property_exists($entity, $propertyEs)) {
                    $entity->{'set' . $propertyEs}($dataEs);
                }

                $this->addBusinessTranslation($entity, $property, $dataEs, BusinessProfile::TRANSLATION_LANG_ES);
            } elseif ($dataEn) {
                if (!$entity->{'get' . $property}()) {
                    $entity->{'set' . $property}($dataEn);
                }
            }

            if ($dataEn) {
                $this->addBusinessTranslation($entity, $property, $dataEn, BusinessProfile::TRANSLATION_LANG_EN);

                if (property_exists($entity, $propertyEn)) {
                    $entity->{'set' . $propertyEn}($dataEn);
                }
            }
        }

        return $entity;
    }

    /**
     * @param BusinessProfile $entity
     * @param string          $property
     * @param string|null     $data
     * @param string          $locale
     *
     * @return BusinessProfile
     */
    private function addBusinessTranslation($entity, $property, $data, $locale)
    {
        $translation = $entity->getTranslationItem(
            $property,
            mb_strtolower($locale)
        );

        if ($translation) {
            $translation->setContent($data);
        } else {
            $translation = new BusinessProfileTranslation(
                mb_strtolower($locale),
                $property,
                $data
            );

            $this->em->persist($translation);
        }

        $entity->addTranslation($translation);

        return $entity;
    }
}
