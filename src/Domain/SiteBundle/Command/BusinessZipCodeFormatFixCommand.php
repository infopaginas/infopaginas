<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Zip;
use Domain\BusinessBundle\Util\ZipFormatterUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessZipCodeFormatFixCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:business-zip-codes:fix');
        $this->setDescription('Fix business zip codes');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateBusinessZipCodes();
        $this->updateNeighborhoodZipCodes();
    }

    protected function updateBusinessZipCodes()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getActiveBusinessProfilesIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            $business = current($row);

            $zipCode = ZipFormatterUtil::getFormattedZip($business->getZipCode());

            $business->setZipCode($zipCode);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
    }

    protected function updateNeighborhoodZipCodes()
    {
        $zipCodes = $this->em->getRepository(Zip::class)->getZipCodesIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($zipCodes as $row) {
            $zipCode = current($row);

            $zipCodeValue = ZipFormatterUtil::getFormattedZip($zipCode->getZipCode());

            $zipCode->setZipCode($zipCodeValue);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
    }
}
