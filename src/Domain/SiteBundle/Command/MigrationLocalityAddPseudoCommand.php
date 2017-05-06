<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\LocalityPseudo;
use Domain\BusinessBundle\Util\SlugUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;

class MigrationLocalityAddPseudoCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    protected function configure()
    {
        $this->setName('data:migration:locality-add-pseudo');
        $this->setDescription('Update Locality');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        $output->writeln('Processing...');

        $added = $this->processLocalityPseudo();

        $output->writeln($added . ' Pseudo Localities were added');
    }

    protected function processLocalityPseudo()
    {
        $added = 0;
        $deleteLocalityList = LocalityConvertCommand::getDeleteLocalities();

        foreach ($deleteLocalityList as $pseudoName => $localityName) {
            $locality = $this->getLocality($localityName);

            if ($locality) {
                $localityPseudo = $this->getLocalityPseudo($pseudoName);

                if ($localityPseudo and !$locality->getPseudos()->contains($localityPseudo)) {
                    $locality->addPseudo($localityPseudo);

                    $added++;
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        return $added;
    }

    /**
     * @param string $localityPseudoName
     *
     * @return LocalityPseudo
     */
    protected function getLocalityPseudo($localityPseudoName)
    {
        $localityPseudoName = trim($localityPseudoName);
        $slug = SlugUtil::convertSlug($localityPseudoName);

        $localityPseudo = $this->em->getRepository(LocalityPseudo::class)->getLocalityPseudoBySlug($slug);

        if (!$localityPseudo) {
            $localityPseudo = $this->createLocalityPseudo($localityPseudoName);
        }

        return $localityPseudo;
    }

    /**
     * @param string $localityPseudoName
     *
     * @return LocalityPseudo
     */
    protected function createLocalityPseudo($localityPseudoName)
    {
        $localityPseudo = new LocalityPseudo();
        $localityPseudo->setName($localityPseudoName);

        $this->em->persist($localityPseudo);
        $this->em->flush();

        return $localityPseudo;
    }

    /**
     * @param string $localityName
     *
     * @return Locality|null
     */
    protected function getLocality($localityName)
    {
        $slug = SlugUtil::convertSlug(trim($localityName));

        $locality = $this->em->getRepository(Locality::class)->getLocalityBySlug($slug);

        return $locality;
    }
}
