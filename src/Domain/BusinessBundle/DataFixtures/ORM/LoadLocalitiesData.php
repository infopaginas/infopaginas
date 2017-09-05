<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Neighborhood;
use Domain\BusinessBundle\Entity\Zip;
use Domain\BusinessBundle\Entity\Translation\LocalityTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLocalitiesData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ObjectManager
     */
    protected $manager;

    private $localityData = [
            [
                'name_es' => 'Adjuntas',
                'name_en' => 'Adjuntas',
                'area' => 'Central',
                'neighborhood' =>
                [
                    'Adjuntas' => ['00601', '00631'],
                ],
            ],
            [
                'name_es' => 'Aguada',
                'name_en' => 'Aguada',
                'area' => 'West',
                'neighborhood' =>
                [
                    'Aguada' => '00602',
                ],
            ],
            [
                'name_es' => 'Aguadilla',
                'name_en' => 'Aguadilla',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Aguadilla' => ['00603', '00605'],
                        'Ramey Station' => ['00604'],
                        'San Antonio' => ['00690'],
                    ],
            ],
            [
                'name_es' => 'Aguas Buenas',
                'name_en' => 'Aguas Buenas',
                'area' => 'Central',
                'neighborhood' =>
                [
                    'Aguas Buenas' => '00703',
                ],
            ],
            [
                'name_es' => 'Aibonito',
                'name_en' => 'Aibonito',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Aibonito' => '00705',
                        'La Plata' => '00786',
                    ],
            ],
            [
                'name_es' => 'Añasco',
                'name_en' => 'Anasco',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Añasco' => '00610',
                    ],
            ],
            [
                'name_es' => 'Arecibo',
                'name_en' => 'Arecibo',
                'area' => 'North',
                'neighborhood' =>
                    [
                        'Arecibo' => ['00612', '00613', '00614'],
                        'Bajadero' => '00616',
                        'Garrochales' => '00652',
                        'Sabana Hoyos' => '00688',
                    ],
            ],
            [
                'name_es' => 'Arroyo',
                'name_en' => 'Arroyo',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Arroyo' => '00714',
                    ],
            ],
            [
                'name_es' => 'Barceloneta',
                'name_en' => 'Barceloneta',
                'area' => 'North',
                'neighborhood' =>
                    [
                        'Barceloneta' => '00617',
                    ],
            ],
            [
                'name_es' => 'Barranquitas',
                'name_en' => 'Barranquitas',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Barranquitas' => '00794',
                    ],
            ],
            [
                'name_es' => 'Bayamón',
                'name_en' => 'Bayamon',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Bayamón' => ['00958', '00960', '00956', '00957', '00959', '00961'],
                    ],
            ],
            [
                'name_es' => 'Cabo Rojo',
                'name_en' => 'Cabo Rojo',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Boquerón' => '00622',
                        'Cabo Rojo' => '00623',
                    ],
            ],
            [
                'name_es' => 'Caguas',
                'name_en' => 'Caguas',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Caguas' => ['00725', '00726', '00727'],
                    ],
            ],
            [
                'name_es' => 'Canóvanas',
                'name_en' => 'Canovanas',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Canóvanas' => ['00729', '00745'],
                    ],
            ],
            [
                'name_es' => 'Carolina',
                'name_en' => 'Carolina',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Carolina' => [
                            '00979',
                            '00981',
                            '00982',
                            '00983',
                            '00984',
                            '00985',
                            '00986',
                            '00987',
                            '00988'
                        ],
                    ],
            ],
            [
                'name_es' => 'Cataño',
                'name_en' => 'Catano',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Cataño' => ['00962', '00963'],
                    ],
            ],
            [
                'name_es' => 'Cayey',
                'name_en' => 'Cayey',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Cayey' => ['00736', '00737'],
                    ],
            ],
            [
                'name_es' => 'Ceiba',
                'name_en' => 'Ceiba',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Ceiba' => ['00735', '00742'],
                    ],
            ],
            [
                'name_es' => 'Ciales',
                'name_en' => 'Ciales',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Ciales' => '00638',
                    ],
            ],
            [
                'name_es' => 'Cidra',
                'name_en' => 'Cidra',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Cidra' => '00739',
                    ],
            ],
            [
                'name_es' => 'Coamo',
                'name_en' => 'Coamo',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Coamo' => '00769',
                    ],
            ],
            [
                'name_es' => 'Comerío',
                'name_en' => 'Comerio',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Comerío' => '00782',
                    ],
            ],
            [
                'name_es' => 'Corozal',
                'name_en' => 'Corozal',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Corozal' => '00783',
                    ],
            ],
            [
                'name_es' => 'Culebra',
                'name_en' => 'Culebra',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Culebra' => '00775',
                    ],
            ],
            [
                'name_es' => 'Dorado',
                'name_en' => 'Dorado',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Dorado' => '00646',
                    ],
            ],
            [
                'name_es' => 'Fajardo',
                'name_en' => 'Fajardo',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Fajardo' => '00738',
                        'Puerto Real' => '00740',
                    ],
            ],
            [
                'name_es' => 'Florida',
                'name_en' => 'Florida',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Florida' => '00650',
                    ],
            ],
            [
                'name_es' => 'Guánica',
                'name_en' => 'Guanica',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Ensenada' => '00647',
                        'Guánica' => '00653',
                    ],
            ],
            [
                'name_es' => 'Guayama',
                'name_en' => 'Guayama',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Aguirre' => '00704',
                        'Guayama' => ['00784', '00785'],
                    ],
            ],
            [
                'name_es' => 'Guayanilla',
                'name_en' => 'Guayanilla',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Guayanilla' => '00656',
                    ],
            ],
            [
                'name_es' => 'Guaynabo',
                'name_en' => 'Guaynabo',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Guaynabo' => ['00965', '00966', '00968', '00969', '00970', '00971'],
                    ],
            ],
            [
                'name_es' => 'Gurabo',
                'name_en' => 'Gurabo',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Gurabo' => '00778',
                    ],
            ],
            [
                'name_es' => 'Hatillo',
                'name_en' => 'Hatillo',
                'area' => 'North',
                'neighborhood' =>
                    [
                        'Hatillo' => '00659',
                    ],
            ],
            [
                'name_es' => 'Hormigueros',
                'name_en' => 'Hormigueros',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Hormigueros' => '00660',
                    ],
            ],
            [
                'name_es' => 'Humacao',
                'name_en' => 'Humacao',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Humacao' => ['00791', '00792'],
                        'Punta Santiago' => '00741',
                    ],
            ],
            [
                'name_es' => 'Isabela',
                'name_en' => 'Isabela',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Isabela' => '00662',
                    ],
            ],
            [
                'name_es' => 'Jayuya',
                'name_en' => 'Jayuya',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Jayuya' => '00664',
                    ],
            ],
            [
                'name_es' => 'Juana Díaz',
                'name_en' => 'Juana Diaz',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Juana Díaz' => '00795',
                    ],
            ],
            [
                'name_es' => 'Juncos',
                'name_en' => 'Juncos',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Juncos' => '00777',
                    ],
            ],
            [
                'name_es' => 'Lajas',
                'name_en' => 'Lajas',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Lajas' => '00667',
                    ],
            ],
            [
                'name_es' => 'Lares',
                'name_en' => 'Lares',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Lares' => '00669',
                        'Castañer' => '00631',
                    ],
            ],
            [
                'name_es' => 'Las Marías',
                'name_en' => 'Las Marias',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Las Marías' => '00670',
                    ],
            ],
            [
                'name_es' => 'Las Piedras',
                'name_en' => 'Las Piedras',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Las Piedras' => '00771',
                    ],
            ],
            [
                'name_es' => 'Loíza',
                'name_en' => 'Loiza',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Loíza' => '00772',
                    ],
            ],
            [
                'name_es' => 'Luquillo',
                'name_en' => 'Luquillo',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Luquillo' => '00773',
                    ],
            ],
            [
                'name_es' => 'Manatí',
                'name_en' => 'Manati',
                'area' => 'North',
                'neighborhood' =>
                    [
                        'Manatí' => '00674',
                    ],
            ],
            [
                'name_es' => 'Maricao',
                'name_en' => 'Maricao',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Maricao' => '00606',
                    ],
            ],
            [
                'name_es' => 'Maunabo',
                'name_en' => 'Maunabo',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Maunabo' => '00707',
                    ],
            ],
            [
                'name_es' => 'Mayagüez',
                'name_en' => 'Mayaguez',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Mayagüez' => ['00680', '00681', '00682'],
                    ],
            ],
            [
                'name_es' => 'Moca',
                'name_en' => 'Moca',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Moca' => '00676',
                    ],
            ],
            [
                'name_es' => 'Morovis',
                'name_en' => 'Morovis',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Morovis' => '00687',
                    ],
            ],
            [
                'name_es' => 'Naguabo',
                'name_en' => 'Naguabo',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Naguabo' => '00718',
                        'Rio Blanco' => '00744',
                    ],
            ],
            [
                'name_es' => 'Naranjito',
                'name_en' => 'Naranjito',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Naranjito' => '00719',
                    ],
            ],
            [
                'name_es' => 'Orocovis',
                'name_en' => 'Orocovis',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Orocovis' => '00720',
                    ],
            ],
            [
                'name_es' => 'Patillas',
                'name_en' => 'Patillas',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Patillas' => '00723',
                    ],
            ],
            [
                'name_es' => 'Peñuelas',
                'name_en' => 'Penuelas',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Peñuelas' => '00624',
                    ],
            ],
            [
                'name_es' => 'Ponce',
                'name_en' => 'Ponce',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Atocha' => '00733',
                        'Coto Laurel' => '00780',
                        'Mercedita' => '00715',
                        'Pámpanos' => '00732',
                        'Playa' => '00734',
                        'Ponce' => ['00716', '00717', '00728', '00730', '00731'],
                    ],
            ],
            [
                'name_es' => 'Quebradillas',
                'name_en' => 'Quebradillas',
                'area' => 'North',
                'neighborhood' =>
                    [
                        'Quebradillas' => '00678',
                    ],
            ],
            [
                'name_es' => 'Rincón',
                'name_en' => 'Rincon',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Rincón' => '00677',
                    ],
            ],
            [
                'name_es' => 'Río Grande',
                'name_en' => 'Rio Grande',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Palmer' => '00721',
                        'Río Grande' => ['00721', '00745'],
                    ],
            ],
            [
                'name_es' => 'Sabana Grande',
                'name_en' => 'Sabana Grande',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Sabana Grande' => '00637',
                    ],
            ],
            [
                'name_es' => 'Salinas',
                'name_en' => 'Salinas',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Salinas' => '00751',
                    ],
            ],
            [
                'name_es' => 'San Germán',
                'name_en' => 'San German',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'Rosario' => '00636',
                        'San Germán' => '00683',
                    ],
            ],
            [
                'name_es' => 'San Juan',
                'name_en' => 'San Juan',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        '65 de Infantería' => ['00923', '00924', '00929'],
                        'Barrio Obrero' => ['00915', '00916'],
                        'Caparra Heights' => '00920',
                        'Fernández Juncos' => ['00909', '00910'],
                        'Fort Buchanan' => '00934',
                        'General Post Office' => '00936',
                        'Hato Rey' => ['00917', '00919'],
                        'Loíza Street Station' => ['00911', '00912', '00913', '00914'],
                        'Minillas Station' => '00940',
                        'Old San Juan' => ['00901', '00902'],
                        'Puerta de Tierra' => '00906',
                        'Río Piedras' => ['00925', '00926', '00927', '00928'],
                        'San José' => '00930',
                        'San Juan' => '00921',
                        'Santurce' => ['00907', '00908'],
                        'UPR Station' => '00931',
                        'Veterans Plaza' => '00933',
                    ],
            ],
            [
                'name_es' => 'San Lorenzo',
                'name_en' => 'San Lorenzo',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'San Lorenzo' => '00754',
                    ],
            ],
            [
                'name_es' => 'San Sebastián',
                'name_en' => 'San Sebastian',
                'area' => 'West',
                'neighborhood' =>
                    [
                        'San Sebastián' => '00685',
                    ],
            ],
            [
                'name_es' => 'Santa Isabel',
                'name_en' => 'Santa Isabel',
                'area' => '',
                'neighborhood' =>
                    [
                        'Santa Isabel' => '00757',
                    ],
            ],
            [
                'name_es' => 'Toa Alta',
                'name_en' => 'Toa Alta',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Toa Alta' => ['00953', '00954'],
                    ],
            ],
            [
                'name_es' => 'Toa Baja',
                'name_en' => 'Toa Baja',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Levittown' => '00950',
                        'Sabana Seca' => '00952',
                        'Toa Baja' => ['00949', '00951'],
                    ],
            ],
            [
                'name_es' => 'Trujillo Alto',
                'name_en' => 'Trujillo Alto',
                'area' => 'Metro',
                'neighborhood' =>
                    [
                        'Saint Just' => '00978',
                        'Trujillo Alto' => ['00976', '00977'],
                    ],
            ],
            [
                'name_es' => 'Utuado',
                'name_en' => 'Utuado',
                'area' => 'Central',
                'neighborhood' =>
                    [
                        'Angeles' => '00611',
                        'Utuado' => '00641',
                    ],
            ],
            [
                'name_es' => 'Vega Alta',
                'name_en' => 'Vega Alta',
                'area' => 'North',
                'neighborhood' =>
                    [
                        'Vega Alta' => '00692',
                    ],
            ],
            [
                'name_es' => 'Vega Baja',
                'name_en' => 'Vega Baja',
                'area' => 'North',
                'neighborhood' =>
                    [
                        'Vega Baja' => ['00693', '00694'],
                    ],
            ],
            [
                'name_es' => 'Vieques',
                'name_en' => 'Vieques',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Vieques' => '00765',
                    ],
            ],
            [
                'name_es' => 'Villalba',
                'name_en' => 'Villalba',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Villalba' => '00766',
                    ],
            ],
            [
                'name_es' => 'Yabucoa',
                'name_en' => 'Yabucoa',
                'area' => 'East',
                'neighborhood' =>
                    [
                        'Yabucoa' => '00767',
                    ],
            ],
            [
                'name_es' => 'Yauco',
                'name_en' => 'Yauco',
                'area' => 'South',
                'neighborhood' =>
                    [
                        'Yauco' => '00698',
                    ],
            ],
        ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        foreach ($this->localityData as $item) {
            $locObject = new Locality();
            $locObject->setName($item['name_en']);

            if (!empty($item['area'])) {
                $area = $this->getReference('area.' . str_replace(' ', '', $item['area']));
                $locObject->setArea($area);
            }

            $translation = new LocalityTranslation();
            $translation->setContent($item['name_es']);
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($locObject);

            $this->manager->persist($translation);

            $this->addReference('locality.' . str_replace(' ', '', $item['name_en']), $locObject);

            $this->manager->persist($locObject);

            // todo - add Neighborhood

            if (!empty($item['neighborhood'])) {
                foreach ($item['neighborhood'] as $name => $zips) {
                    $neighborhood = new Neighborhood();
                    $neighborhood->setName($name);
                    $neighborhood->setLocality($locObject);

                    if (!is_array($zips)) {
                        $zips = [$zips];
                    }

                    if ($zips) {
                        foreach ($zips as $zipCode) {
                            $zip = new Zip();
                            $zip->setZipCode($zipCode);
                            $zip->setNeighborhood($neighborhood);

                            $neighborhood->addZip($zip);
                        }
                    }

                    $this->manager->persist($neighborhood);
                }
            }
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 9;
    }

    /**
     * @param ContainerInterface|null $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }
}
