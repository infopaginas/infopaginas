<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\QueryBuilder;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class PostponeRemoveCommand extends ContainerAwareCommand
{
    const POSTPONE_REMOVE_LOCK = 'POSTPONE_REMOVE.lock';

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
        $this->setName('data:postpone:remove');
        $this->setDescription('Delete scheduled for remove entities');
    }

    /**
     * @param $input InputInterface
     * @param $output OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::POSTPONE_REMOVE, $logger::STATUS_START, 'execute:start');

        $lockHandler = new LockHandler(self::POSTPONE_REMOVE_LOCK);

        if (!$lockHandler->lock()) {
            $logger->addInfo($logger::POSTPONE_REMOVE, $logger::STATUS_END, 'execute:stop');

            return $output->writeln('Command is locked by another process');
        }

        $this->handlePostponeRemove();

        $lockHandler->release();
        $logger->addInfo($logger::POSTPONE_REMOVE, $logger::STATUS_END, 'execute:stop');
    }

    protected function handlePostponeRemove()
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        // remove businesses
        $this->removeScheduledEntitiesByClass(BusinessProfile::class);

        // remove articles
        $this->removeScheduledEntitiesByClass(Article::class);

        // remove images
        $this->removeScheduledEntitiesByClass(Media::class);
    }

    /**
     * @param $class string
     */
    protected function removeScheduledEntitiesByClass($class)
    {
        $entities = $this->getEntityIterator($class);

        $i = 0;
        $batchSize = 20;

        foreach ($entities as $row) {
            $entity = current($row);

            $this->em->remove($entity);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
    }

    /**
     * @param $class string
     *
     * @return IterableResult
     */
    protected function getEntityIterator($class)
    {
        $qb = $this->getDeletedItemsQueryBuilder($class);

        $query = $this->em->createQuery($qb->getDQL());

        return $query->iterate();
    }

    /**
     * @param $class string
     *
     * @return QueryBuilder
     */
    protected function getDeletedItemsQueryBuilder($class)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder
            ->select('e')
            ->from($class, 'e')
            ->andWhere('e.isDeleted = TRUE')
        ;

        return $queryBuilder;
    }
}
