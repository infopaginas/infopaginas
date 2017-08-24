<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Util\PhoneFormatterUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessPhoneFormatFixCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:business-phones:fix');
        $this->setDescription('Fix business phones');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateBusinessPhones();
    }

    protected function updateBusinessPhones()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getAllBusinessProfilesIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var $business BusinessProfile */
            $business = $row[0];
            $isFirst  = true;

            foreach ($business->getPhones() as $phone) {
                $phoneNumber = PhoneFormatterUtil::getFormattedPhone($phone->getPhone());

                if ($phoneNumber) {
                    $phone->setPhone($phoneNumber);

                    if ($isFirst) {
                        $phone->setType(BusinessProfilePhone::PHONE_TYPE_MAIN);
                        $isFirst = false;
                    } else {
                        $phone->setType(BusinessProfilePhone::PHONE_TYPE_SECONDARY);
                    }
                } else {
                    $this->em->remove($phone);
                }
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i ++;
        }

        $this->em->flush();
    }
}
