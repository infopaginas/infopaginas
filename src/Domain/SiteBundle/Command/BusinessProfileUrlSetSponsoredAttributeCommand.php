<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessProfileUrlSetSponsoredAttributeCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:business-url:set-sponsored');
        $this->setDescription('Set sponsored rel attributes for paid profiles');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateBusinessUrls();
    }

    protected function updateBusinessUrls()
    {
        $businesses = $this->em
            ->getRepository(BusinessProfile::class)
            ->getBusinessProfilesWithSubscriptionIterator(SubscriptionPlanInterface::CODE_PRIORITY, '>=');

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];

            foreach (BusinessProfile::gerUrlTypeFields() as $urlTypeField) {
                if ($business->{'get' . ucfirst($urlTypeField)}()) {
                    $urlTypeFieldClone = clone $business->{'get' . ucfirst($urlTypeField)}();
                    $urlTypeFieldClone->setRelSponsored(true);
                    $business->{'set' . ucfirst($urlTypeField)}($urlTypeFieldClone);
                }
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
    }
}
