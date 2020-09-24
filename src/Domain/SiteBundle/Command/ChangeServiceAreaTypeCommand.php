<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class ChangeServiceAreaTypeCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('change-service-area-type');
        $this->setDescription('Change service area type of free profiles to locality');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $bpIterator = $this->em
            ->getRepository(BusinessProfile::class)
            ->getBusinessProfilesWithSubscriptionAndServiceAreaTypeIterator(
                SubscriptionPlanInterface::CODE_FREE,
                '=',
                BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE
            );

        $batchSize = 100;
        $i = 0;

        foreach ($bpIterator as $row) {
            /** @var BusinessProfile $bp */
            $bp = $row[0];

            if ($catalogLocality = $bp->getCatalogLocality()) {
                $bp->setServiceAreasType(BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE);
                $bp->setLocalities([$catalogLocality]);
                $bp->addArea($catalogLocality->getArea());
                $bp->setNeighborhoods($catalogLocality->getNeighborhoods());
                $this->em->persist($bp);
            }

            if (!($i % $batchSize)) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
    }
}
