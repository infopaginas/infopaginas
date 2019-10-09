<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\ClickbaitTitle;
use Domain\BusinessBundle\VO\Url;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UrlClickBaitUpdateCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    protected $items = 0;

    protected $total = 0;

    protected function configure()
    {
        // this command is needed only to migrate urls see https://jira.oxagile.com/browse/INFT-3132
        // and should be removed after it
        $this->setName('data:click-bait-url:update');
        $this->setDescription('Update click-bait url');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $output->writeln('Started...');

        $this->updateUrls();

        $output->writeln(sprintf('Updated: %s', $this->items));

        $output->writeln(sprintf('Total: %s', $this->total));
        $output->writeln('Done');
    }

    protected function updateUrls()
    {
        $items = $this->em->getRepository(ClickbaitTitle::class)->findAll();

        foreach ($items as $item) {
            foreach ($this->getUrlFields() as $oldField => $newField) {
                $this->updateUrlField($item, $oldField, $newField);
            }

            $this->total++;
        }

        $this->em->flush();
    }

    private function updateUrlField($business, $oldField, $newField)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $oldValue = $accessor->getValue($business, $oldField);

        if ($oldValue) {
            $url = $this->createUrl($oldValue);
            $accessor->setValue($business, $newField, $url);

            $this->items++;
        }
    }

    /**
     * @param string $value
     *
     * @return Url
     */
    private function createUrl($value)
    {
        $url = new Url();

        $url->setUrl($value);

        return $url;
    }

    private function getUrlFields()
    {
        return [
            'url' => 'urlItem',
        ];
    }
}
