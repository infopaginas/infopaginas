<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;

class CreateElasticCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var EntityManager $em
     */
    protected $em;

    protected $withDebug;

    protected function configure()
    {
        // todo remove
        $this->setName('data:elastic:create');
        $this->setDescription('Add data form db to elastic');
        $this->setDefinition(
            new InputDefinition(array(
                new InputOption('withDebug', 'd'),
                new InputOption('itemsCountLimit', 'il', InputOption::VALUE_OPTIONAL),
                new InputOption('itemIdStart', 'si', InputOption::VALUE_OPTIONAL),
            ))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        $this->totalTimer = 0;

        if ($input->getOption('itemIdStart')) {
            $itemIdStart = $input->getOption('itemIdStart');
        } else {
            $itemIdStart = 1;
        }

        if ($input->getOption('itemsCountLimit')) {
            $itemsCountLimit = $input->getOption('itemsCountLimit');
        } else {
            $itemsCountLimit = false;
        }

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        if ($this->withDebug) {
            $this->itemCounter = 0;
            $this->totalTimer = microtime(true);
        }

        $this->handleElasticCreate($itemIdStart, $itemsCountLimit);

        if ($this->withDebug) {
            $output->writeln('Total time: ' . (microtime(true) - $this->totalTimer));
            $output->writeln('Finish requests. Elements added: ' . $this->itemCounter);
        }
    }

    protected function handleElasticCreate($itemIdStart, $itemsCountLimit)
    {
        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $businessProfileManager->createElasticSearchIndex();

        $businesses = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getActiveBusinessProfilesIteratorElastic($itemIdStart);

        $i = 0;
        $count = 0;
        $batch = 100;
        $data = [];

        foreach ($businesses as $businessRow) {
            /* @var $business BusinessProfile */
            $business = current($businessRow);

            $item = $businessProfileManager->buildBusinessProfileElasticData($business);

            if ($item) {
                $data[] = $item;
            } else {
                $businessProfileManager->removeBusinessFromElastic($business->getId());
            }

            $i ++;
            $count ++;

            if ($i >= $batch or ($itemsCountLimit and $count >= $itemsCountLimit)) {
                $businessProfileManager->addBusinessesToElasticIndex($data);
                $data = [];
                $i = 0;
            }

            $this->em->detach($businessRow[0]);

            if ($count >= $itemsCountLimit) {
                break;
            }
        }

        if ($this->withDebug) {
            $this->itemCounter = $count;
        }
    }
}
