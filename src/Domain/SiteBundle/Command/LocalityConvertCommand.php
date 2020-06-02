<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class LocalityConvertCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:locality-mapping:convert');
        $this->setDescription('Localities conversion');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $deleteList = self::getDeleteLocalities();

        foreach ($deleteList as $deleteLocality => $newLocality) {
            $parent   = $this->getLocalityItemByName($newLocality);
            $locality = $this->getLocalityItemByName($deleteLocality);

            if ($locality && $parent) {
                $businesses = $this->getBusinessesByLocalityIdIterator($locality->getId());

                foreach ($businesses as $row) {
                    /* @var $business BusinessProfile */
                    $business = $row[0];

                    if ($business->getCatalogLocality() and
                        $business->getCatalogLocality()->getId() == $locality->getId()
                    ) {
                        $business->setCatalogLocality($parent);
                    }

                    if ($business->getLocalities()->contains($locality)) {
                        if (!$business->getLocalities()->contains($parent)) {
                            $business->addLocality($parent);
                        }

                        $business->removeLocality($locality);
                    }
                }

                $this->em->remove($locality);
                $this->em->flush();

                $this->em->clear();
            }
        }

        $this->em->flush();
    }

    /**
     * @param string $name
     *
     * @return Locality|null
     */
    protected function getLocalityItemByName($name)
    {
        $locality = $this->em->getRepository(Locality::class)->findOneBy(['name' => $name]);

        return $locality;
    }

    protected function getBusinessesByLocalityIdIterator($id)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('b.id')
            ->distinct()
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->leftJoin('b.localities', 'l')
            ->leftJoin('b.catalogLocality', 'cl')
            ->where('cl.id = :id')
            ->orWhere('l.id = :id')
            ->setParameter('id', $id)
        ;

        $businessesIds = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($businessesIds as $row) {
            $data[] = $row['id'];
        }

        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->where('b.id IN (:ids)')
            ->setParameter('ids', $data)
        ;

        $query = $this->em->createQuery($qb->getDQL());
        $query->setParameter('ids', $data);

        return $query->iterate();
    }

    /**
     * @return array
     */
    public static function getDeleteLocalities()
    {
        $localities = [
            'Fenton'            => 'San Juan',
            'Puerto Rico'       => 'San Juan',
            'San Juan,PR'       => 'San Juan',
            'San Juan, P. R.'   => 'San Juan',
            'Urb. Villa Nevarez, San Juan' => 'San Juan',
            'Viejo San Juan'    => 'San Juan',
            'Viejo San Juan '   => 'San Juan',
            'Cupey'             => 'San Juan',
            'Hato Rey'          => 'San Juan',
            'Isla Verde'        => 'San Juan',
            'Miramar'           => 'San Juan',
            'Reston'            => 'San Juan',
            'Rio Piedras'       => 'San Juan',
            'Santurce'          => 'San Juan',
            'Levitown'          => 'San Juan',
            'Levittown'         => 'San Juan',
            'Hato Rey, San Juan' => 'San Juan',
            'Añasco P.R.'       => 'Anasco',
            'Bayamon P.R.'      => 'Bayamon',
            'Bayamón, PR'       => 'Bayamon',
            'Bayamon  P.R.'     => 'Bayamon',
            'Bayamóm'           => 'Bayamon',
            'Hato Tejas'        => 'Bayamon',
            'Canóvana'          => 'Canovanas',
            'Canóvanas, PR'     => 'Canovanas',
            'Canóvana '         => 'Canovanas',
            'Corozal, PR'       => 'Corozal',
            'Guayama, PR'       => 'Guayama',
            'guaynabo'          => 'Guaynabo',
            'Guaynabo PR'       => 'Guaynabo',
            'Guaynabo, P.R.'    => 'Guaynabo',
            'Guaynabo, P.R. '   => 'Guaynabo',
            'Trujillo Alto (1semana/mes en Guaynabo)' => 'Guaynabo',
            'Hatillo PR'        => 'Hatillo',
            'Hormiguero'        => 'Hormigueros',
            'Hormiqueros'       => 'Hormigueros',
            'Peñuela'           => 'Penuelas',
            'San Germán P.R.'   => 'San German',
            'San Germán, PR'    => 'San German',
            'San Germán P.R. '  => 'San German',
            'Vega Baja, PR'     => 'Vega Baja',
            'Toa Baja PR'       => 'Toa Baja',
            'villalba'          => 'Villalba',
            'Coto Laurel'       => 'Ponce',
            'Coto Laurel PR'    => 'Ponce',
            'Boquerón'          => 'Cabo Rojo',
            'Ramey'             => 'Aguadilla',
            'Rio Blanco'        => 'Naguabo',
            'San Antonio'       => 'Caguas',
            'San Antonio , Texas' => 'Caguas',
            'St. Just Station'  => 'Trujillo Alto',
            'guayanilla'        => 'Guayanilla',
            'Mayaguez PR'       => 'Mayaguez',
            'Guyanabo'          => 'Guaynabo',
            'PR'                => 'San Juan',
            'VEGA BAJA 00693'   => 'Vega Baja',
        ];

        return $localities;
    }
}
