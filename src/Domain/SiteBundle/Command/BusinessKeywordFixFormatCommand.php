<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileKeyword;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessKeywordFixFormatCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:business-keyword:fix');
        $this->setDescription('Fix business keywords format');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateBusinessKeywords();
    }

    protected function updateBusinessKeywords()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getAllBusinessProfilesIterator();

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];
            $newKeywords = [];

            $keywords = $business->getKeywords();

            foreach ($keywords as $keyword) {
                /* @var BusinessProfileKeyword $keyword */
                if ($keyword->getValueEn()) {
                    $newKeywords[] = $keyword->getValueEn();
                }

                if ($keyword->getValueEs()) {
                    $newKeywords[] = $keyword->getValueEs();
                }
            }

            if ($newKeywords) {
                $business->setKeywordText(implode(BusinessProfile::KEYWORD_DELIMITER, $newKeywords));
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
