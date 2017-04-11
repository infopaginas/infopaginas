<?php

namespace Domain\ArticleBundle\Command;

use Domain\ArticleBundle\Model\Manager\ArticleApiManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

class UpdateExternalArticlesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('domain:article:update')
            ->setDescription('Create/update articles from infopaginas media API')
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputOption('updateAll'),
                        new InputOption('numberOfItemToUpdate', null, InputOption::VALUE_OPTIONAL),
                    ]
                )
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $articleApiManager = $this->getContainer()->get('domain_article.manager.api');

        if ($input->getOption('numberOfItemToUpdate')) {
            $pageStart = $input->getOption('numberOfItemToUpdate');
        } else {
            $pageStart = ArticleApiManager::DEFAULT_NUMBER_OF_ITEM_TO_UPDATE;
        }

        if ($input->getOption('updateAll')) {
            $updateAll = true;
        } else {
            $updateAll = false;
        }

        $output->writeln('Processing...');
        $articleApiManager->updateExternalArticles($pageStart, $updateAll);
        $output->writeln('Done');
    }
}
