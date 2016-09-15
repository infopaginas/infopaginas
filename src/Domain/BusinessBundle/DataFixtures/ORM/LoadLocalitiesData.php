<?php
namespace Domain\BusinessBundle\DataFixture\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Locality;
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
            ],
            [
                'name_es' => 'Aguada',
                'name_en' => 'Aguada',
                'area' => 'West',
            ],
            [
                'name_es' => 'Aguadilla',
                'name_en' => 'Aguadilla',
                'area' => 'West',
            ],
            [
                'name_es' => 'Aguas Buenas',
                'name_en' => 'Aguas Buenas',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Aibonito',
                'name_en' => 'Aibonito',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Añasco',
                'name_en' => 'Anasco',
                'area' => 'West',
            ],
            [
                'name_es' => 'Arecibo',
                'name_en' => 'Arecibo',
                'area' => 'North',
            ],
            [
                'name_es' => 'Arroyo',
                'name_en' => 'Arroyo',
                'area' => 'South',
            ],
            [
                'name_es' => 'Barceloneta',
                'name_en' => 'Barceloneta',
                'area' => 'North',
            ],
            [
                'name_es' => 'Barranquitas',
                'name_en' => 'Barranquitas',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Bayamón',
                'name_en' => 'Bayamon',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Cabo Rojo',
                'name_en' => 'Cabo Rojo',
                'area' => 'West',
            ],
            [
                'name_es' => 'Caguas',
                'name_en' => 'Caguas',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Canóvanas',
                'name_en' => 'Canovanas',
                'area' => 'East',
            ],
            [
                'name_es' => 'Carolina',
                'name_en' => 'Carolina',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Cataño',
                'name_en' => 'Catano',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Cayey',
                'name_en' => 'Cayey',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Ceiba',
                'name_en' => 'Ceiba',
                'area' => 'East',
            ],
            [
                'name_es' => 'Ciales',
                'name_en' => 'Ciales',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Cidra',
                'name_en' => 'Cidra',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Coamo',
                'name_en' => 'Coamo',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Comerío',
                'name_en' => 'Comerio',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Corozal',
                'name_en' => 'Corozal',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Culebra',
                'name_en' => 'Culebra',
                'area' => 'East',
            ],
            [
                'name_es' => 'Dorado',
                'name_en' => 'Dorado',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Fajardo',
                'name_en' => 'Fajardo',
                'area' => 'East',
            ],
            [
                'name_es' => 'Florida',
                'name_en' => 'Florida',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Guánica',
                'name_en' => 'Guanica',
                'area' => 'West',
            ],
            [
                'name_es' => 'Guayama',
                'name_en' => 'Guayama',
                'area' => 'South',
            ],
            [
                'name_es' => 'Guayanilla',
                'name_en' => 'Guayanilla',
                'area' => 'South',
            ],
            [
                'name_es' => 'Guaynabo',
                'name_en' => 'Guaynabo',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Gurabo',
                'name_en' => 'Gurabo',
                'area' => 'East',
            ],
            [
                'name_es' => 'Hatillo',
                'name_en' => 'Hatillo',
                'area' => 'North',
            ],
            [
                'name_es' => 'Hormigueros',
                'name_en' => 'Hormigueros',
                'area' => 'West',
            ],
            [
                'name_es' => 'Humacao',
                'name_en' => 'Humacao',
                'area' => 'East',
            ],
            [
                'name_es' => 'Isabela',
                'name_en' => 'Isabela',
                'area' => 'West',
            ],
            [
                'name_es' => 'Jayuya',
                'name_en' => 'Jayuya',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Juana Díaz',
                'name_en' => 'Juana Diaz',
                'area' => 'South',
            ],
            [
                'name_es' => 'Juncos',
                'name_en' => 'Juncos',
                'area' => 'East',
            ],
            [
                'name_es' => 'Lajas',
                'name_en' => 'Lajas',
                'area' => 'West',
            ],
            [
                'name_es' => 'Las Marías',
                'name_en' => 'Las Marias',
                'area' => 'West',
            ],
            [
                'name_es' => 'Las Piedras',
                'name_en' => 'Las Piedras',
                'area' => 'East',
            ],
            [
                'name_es' => 'Loíza',
                'name_en' => 'Loiza',
                'area' => 'East',
            ],
            [
                'name_es' => 'Luquillo',
                'name_en' => 'Luquillo',
                'area' => 'East',
            ],
            [
                'name_es' => 'Manatí',
                'name_en' => 'Manati',
                'area' => 'North',
            ],
            [
                'name_es' => 'Maricao',
                'name_en' => 'Maricao',
                'area' => 'East',
            ],
            [
                'name_es' => 'Maunabo',
                'name_en' => 'Maunabo',
                'area' => 'West',
            ],
            [
                'name_es' => 'Mayagüez',
                'name_en' => 'Mayaguez',
                'area' => 'West',
            ],
            [
                'name_es' => 'Moca',
                'name_en' => 'Moca',
                'area' => 'West',
            ],
            [
                'name_es' => 'Morovis',
                'name_en' => 'Morovis',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Naguabo',
                'name_en' => 'Naguabo',
                'area' => 'East',
            ],
            [
                'name_es' => 'Naranjito',
                'name_en' => 'Naranjito',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Orocovis',
                'name_en' => 'Orocovis',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Patillas',
                'name_en' => 'Patillas',
                'area' => 'South',
            ],
            [
                'name_es' => 'Peñuelas',
                'name_en' => 'Penuelas',
                'area' => 'South',
            ],
            [
                'name_es' => 'Ponce',
                'name_en' => 'Ponce',
                'area' => 'South',
            ],
            [
                'name_es' => 'Quebradillas',
                'name_en' => 'Quebradillas',
                'area' => 'North',
            ],
            [
                'name_es' => 'Rincón',
                'name_en' => 'Rincon',
                'area' => 'West',
            ],
            [
                'name_es' => 'Río Grande',
                'name_en' => 'Rio Grande',
                'area' => 'East',
            ],
            [
                'name_es' => 'Sabana Grande',
                'name_en' => 'Sabana Grande',
                'area' => 'West',
            ],
            [
                'name_es' => 'Salinas',
                'name_en' => 'Salinas',
                'area' => 'South',
            ],
            [
                'name_es' => 'San Germán',
                'name_en' => 'San German',
                'area' => 'West',
            ],
            [
                'name_es' => 'San Juan',
                'name_en' => 'San Juan',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'San Lorenzo',
                'name_en' => 'San Lorenzo',
                'area' => 'East',
            ],
            [
                'name_es' => 'San Sebastián',
                'name_en' => 'San Sebastian',
                'area' => 'West',
            ],
            [
                'name_es' => 'Santa Isabel',
                'name_en' => 'Santa Isabel',
                'area' => '',
            ],
            [
                'name_es' => 'Toa Alta',
                'name_en' => 'Toa Alta',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Toa Baja',
                'name_en' => 'Toa Baja',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Trujillo Alto',
                'name_en' => 'Trujillo Alto',
                'area' => 'Metro',
            ],
            [
                'name_es' => 'Utuado',
                'name_en' => 'Utuado',
                'area' => 'Central',
            ],
            [
                'name_es' => 'Vega Alta',
                'name_en' => 'Vega Alta',
                'area' => 'North',
            ],
            [
                'name_es' => 'Vega Baja',
                'name_en' => 'Vega Baja',
                'area' => 'North',
            ],
            [
                'name_es' => 'Vieques',
                'name_en' => 'Vieques',
                'area' => 'East',
            ],
            [
                'name_es' => 'Villalba',
                'name_en' => 'Villalba',
                'area' => 'South',
            ],
            [
                'name_es' => 'Yabucoa',
                'name_en' => 'Yabucoa',
                'area' => 'East',
            ],
            [
                'name_es' => 'Yauco',
                'name_en' => 'Yauco',
                'area' => 'South',
            ],
        ];

    /**
     * {@inheritDoc}
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
