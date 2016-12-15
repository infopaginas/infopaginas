<?php

namespace Domain\MenuBundle\Model;

use Gedmo\Exception\InvalidArgumentException;

class MenuModel implements MenuInterface
{
    /**
     * @var integer
     */
    protected $code;

    /**
     * @return array
     */
    public static function getCodes()
    {
        return [
            self::CODE_ELECTRICIANS,
            self::CODE_SOLICITORS,
            self::CODE_BUILDERS,
            self::CODE_PHARMACIES,
            self::CODE_ELECTRICIANS,
            self::CODE_PLUMBING,
            self::CODE_MECHANICS,
            self::CODE_DENTISTS,
            self::CODE_RESTAURANTS,
            self::CODE_FLORISTS,
            self::CODE_BEAUTY_SALONS,
            self::CODE_HAIRDRESSERS,
            self::CODE_DOCTORS,
            self::CODE_UNDEFINED,
        ];
    }

    /**
     * Used to load default fixtures
     *
     * @return array
     */
    public static function getMenuCategoriesNames()
    {
        return [];
    }

    /**
     * Used to load default fixtures
     *
     * @return array
     */
    public static function getOtherCategoriesNames()
    {
        return [
            self::CODE_SOLICITORS => [
                'en' => 'Solicitors',
                'es' => 'Solicitors',
            ],
            self::CODE_BUILDERS => [
                'en' => 'Buildings',
                'es' => 'Edificios',
            ],
            self::CODE_PHARMACIES => [
                'en' => 'Pharmacies',
                'es' => 'Farmacias',
            ],
            self::CODE_ELECTRICIANS => [
                'en' => 'Electricians',
                'es' => 'Electricians',
            ],
            self::CODE_MECHANICS => [
                'en' => 'Mechanics',
                'es' => 'Mechanics',
            ],
            self::CODE_DENTISTS => [
                'en' => 'Dentists',
                'es' => 'Dentists',
            ],
            self::CODE_RESTAURANTS => [
                'en' => 'Restaurants',
                'es' => 'Restaurantes',
            ],
            self::CODE_FLORISTS => [
                'en' => 'Florists',
                'es' => 'Floristerías',
            ],
            self::CODE_BEAUTY_SALONS => [
                'en' => 'Beauty Salons',
                'es' => 'Salones De Belleza',
            ],
            self::CODE_HAIRDRESSERS => [
                'en' => 'Hairdressers',
                'es' => 'Hairdressers',
            ],
            self::CODE_DOCTORS => [
                'en' => 'Doctors',
                'es' => 'Médicos',
            ],
            self::CODE_AGRICULTURE => [
                'en' => 'Agriculture',
                'es' => 'Agricultura',
            ],
            self::CODE_ARTS_AND_ENTERTAINMENT => [
                'en' => 'Arts and Entertainment',
                'es' => 'Artes y Entretenimiento',
            ],
            self::CODE_AUTO => [
                'en' => 'Auto',
                'es' => 'Automóviles',
            ],
            self::CODE_BEAUTY_AND_WELLNESS => [
                'en' => 'Beauty and Wellness',
                'es' => 'Belleza y Salud',
            ],
            self::CODE_BUSINESS_SERVICES => [
                'en' => 'Business Services',
                'es' => 'Servicios a Negocios',
            ],
            self::CODE_CLOTHING => [
                'en' => 'Clothing',
                'es' => 'Vestimentas',
            ],
            self::CODE_COMMUNICATION => [
                'en' => 'Communication',
                'es' => 'Comunicaciones',
            ],
            self::CODE_COMMUNITY => [
                'en' => 'Community',
                'es' => 'Comunidades',
            ],
            self::CODE_CONSTRUCTION => [
                'en' => 'Construction',
                'es' => 'Construcción y Arreglos',
            ],
            self::CODE_EDUCATION => [
                'en' => 'Education',
                'es' => 'Educación',
            ],
            self::CODE_FINANCE => [
                'en' => 'Finance',
                'es' => 'Finanzas',
            ],
            self::CODE_HOUSE_AND_HOME => [
                'en' => 'House and Home',
                'es' => 'Casa y Hogar',
            ],
            self::CODE_INSURANCE => [
                'en' => 'Insurance',
                'es' => 'Seguros',
            ],
            self::CODE_JOBS => [
                'en' => 'Jobs',
                'es' => 'Empleos',
            ],
            self::CODE_LAWN_AND_GARDEN => [
                'en' => 'Lawn and Garden',
                'es' => 'Jardinería y Patio',
            ],
            self::CODE_LEGAL => [
                'en' => 'Legal',
                'es' => 'Legal',
            ],
            self::CODE_MANUFACTURING => [
                'en' => 'Manufacturing',
                'es' => 'Manufactura',
            ],
            self::CODE_MEDICAL => [
                'en' => 'Medicine',
                'es' => 'Medicina',
            ],
            self::CODE_NIGHTLIFE => [
                'en' => 'Nightlife',
                'es' => 'Vida Nocturna',
            ],
            self::CODE_PETS_AND_ANIMALS => [
                'en' => 'Pets and Animals',
                'es' => 'Animales y Mascotas',
            ],
            self::CODE_PHOTO_AND_VIDEO => [
                'en' => 'Photo and Video',
                'es' => 'Foto y Video',
            ],
            self::CODE_PRINTING => [
                'en' => 'Printing',
                'es' => 'Impresión',
            ],
            self::CODE_REAL_ESTATE => [
                'en' => 'Real Estate',
                'es' => 'Bienes Raíces',
            ],
            self::CODE_RECREATION => [
                'en' => 'Recreation',
                'es' => 'Recreación',
            ],
            self::CODE_SERVICES => [
                'en' => 'Services',
                'es' => 'Servicios',
            ],
            self::CODE_SHOPPING => [
                'en' => 'Shopping',
                'es' => 'Shopping',
            ],
            self::CODE_STORAGE => [
                'en' => 'Storage',
                'es' => 'Bodegas',
            ],
            self::CODE_TECHNICAL => [
                'en' => 'Technical',
                'es' => 'Técnicos',
            ],
            self::CODE_TRANSPORTATION => [
                'en' => 'Transportation',
                'es' => 'Transporte',
            ],
            self::CODE_TRAVEL => [
                'en' => 'Travel',
                'es' => 'Viajes',
            ],
            self::CODE_UTILITIES => [
                'en' => 'Utilities',
                'es' => 'Arreglos Generales',
            ],
            self::CODE_WEDDING_AND_PARTY => [
                'en' => 'Wedding and Party',
                'es' => 'Bodas y Fiestas',
            ],
            self::CODE_FLOWERS => [
                'en' => 'Flowers',
                'es' => 'Flores',
            ],
            self::CODE_PHOTOGRAPHIC => [
                'en' => 'Photograph',
                'es' => 'Fotografía',
            ],
            self::CODE_FUNERALS => [
                'en' => 'Funerals',
                'es' => 'Funerarias',
            ],
            self::CODE_LAWYERS => [
                'en' => 'Lawyers',
                'es' => 'Abogados',
            ],
            self::CODE_GARDENING => [
                'en' => 'Gardening',
                'es' => 'Jardinería',
            ],
            self::CODE_BAKERY => [
                'en' => 'Bakery',
                'es' => 'Panaderías',
            ],
            self::CODE_BAKERY => [
                'en' => 'Bakery',
                'es' => 'Panaderías',
            ],
            self::CODE_BAKERIES => [
                'en' => 'Bakeries',
                'es' => 'Reposterías',
            ],
            self::CODE_BICYCLES => [
                'en' => 'Bicycles',
                'es' => 'Bicicletas',
            ],
            self::CODE_CHURCHES => [
                'en' => 'Churches',
                'es' => 'Iglesias',
            ],
            self::CODE_CONSULTANTS => [
                'en' => 'Consultants',
                'es' => 'Consultores',
            ],
            self::CODE_BOOKSTORES => [
                'en' => 'Bookstores',
                'es' => 'Librerías',
            ],
            self::CODE_ART => [
                'en' => 'Art',
                'es' => 'Arte',
            ],
            self::CODE_ASSOCIATIONS => [
                'en' => 'Associations',
                'es' => 'Asociaciones',
            ],
            self::CODE_ASTROLOGY => [
                'en' => 'Astrology',
                'es' => 'Astrología',
            ],
            self::CODE_BANKS => [
                'en' => 'Banks',
                'es' => 'Bancos',
            ],
            self::CODE_BOATS => [
                'en' => 'Boats',
                'es' => 'Botes',
            ],
            self::CODE_TRUCKS => [
                'en' => 'Trucks',
                'es' => 'Camiones',
            ],
            self::CODE_COMPUTERS => [
                'en' => 'Computers',
                'es' => 'Computadoras',
            ],
            self::CODE_COPYING_MACHINES => [
                'en' => 'Copying Machines',
                'es' => 'Copiadoras',
            ],
            self::CODE_CANVAS_CURTAINS => [
                'en' => 'Canvas Curtains',
                'es' => 'Cortinas',
            ],
            self::CODE_ELEVATORS => [
                'en' => 'Elevators',
                'es' => 'Elevadores',
            ],
            self::CODE_EXTERMINATOR => [
                'en' => 'Exterminators',
                'es' => 'Exterminadores',
            ],
            self::CODE_TOOLS => [
                'en' => 'Tools',
                'es' => 'Herramientas',
            ],
            self::CODE_ICE => [
                'en' => 'Ice',
                'es' => 'Hielo',
            ],
            self::CODE_HOSPITALS => [
                'en' => 'Hospitals',
                'es' => 'Hospitales',
            ],
            self::CODE_HOTELS => [
                'en' => 'Hotels',
                'es' => 'Hoteles',
            ],
            self::CODE_INTERNET => [
                'en' => 'Internet',
                'es' => 'Internet',
            ],
            self::CODE_LABORATORIES => [
                'en' => 'Laboratories',
                'es' => 'Laboratorios',
            ],
            self::CODE_LAUNDRIES => [
                'en' => 'Laundries',
                'es' => 'Lavanderías',
            ],
            self::CODE_CLEANING => [
                'en' => 'Cleaning',
                'es' => 'Limpieza',
            ],
            self::CODE_MACHINERY => [
                'en' => 'Machinery',
                'es' => 'Maquinaria',
            ],
            self::CODE_TYPEWRITERS => [
                'en' => 'Typewriters',
                'es' => 'Maquinillas',
            ],
            self::CODE_MARINAS => [
                'en' => 'Marinas',
                'es' => 'Marinas',
            ],
            self::CODE_FURNITURE => [
                'en' => 'Furniture',
                'es' => 'Mueblerías',
            ],
            self::CODE_HOME => [
                'en' => 'Home',
                'es' => 'Hogar',
            ],
            self::CODE_ENGINES => [
                'en' => 'Engines',
                'es' => 'Motores',
            ],
            self::CODE_PLUMBING => [
                'en' => 'Plumbing',
                'es' => 'Plomería',
            ],
            self::CODE_DOORS => [
                'en' => 'Doors',
                'es' => 'Puertas',
            ],
            self::CODE_SIGNS => [
                'en' => 'Signs',
                'es' => 'Rótulos',
            ],
            self::CODE_SECURITY => [
                'en' => 'Security',
                'es' => 'Seguridad',
            ],
            self::CODE_UPHOLSTERERS => [
                'en' => 'Upholsterers',
                'es' => 'Tapicerías',
            ],
            self::CODE_CARDS => [
                'en' => 'Cards',
                'es' => 'Tarjetas',
            ],
            self::CODE_WINDOWS => [
                'en' => 'Windows',
                'es' => 'Ventanas',
            ],
            self::CODE_VETERINARIANS => [
                'en' => 'Veterinarians',
                'es' => 'Veterinarios',
            ],
            self::CODE_VIDEO => [
                'en' => 'Video',
                'es' => 'Video',
            ],
            self::CODE_Fences => [
                'en' => 'Fences',
                'es' => 'Verjas Y Portones',
            ],
            self::CODE_TRAILERS => [
                'en' => 'Trailers',
                'es' => 'Trailers',
            ],
            self::CODE_WATER => [
                'en' => 'Water',
                'es' => 'Agua',
            ],
            self::CODE_AIR_CONDITIONER => [
                'en' => 'Air Conditioning',
                'es' => 'Aire Acondicionado',
            ],
            self::CODE_JEWELERS => [
                'en' => 'Jewelers',
                'es' => 'Joyerías',
            ],
            self::CODE_THERAPY => [
                'en' => 'Therapy',
                'es' => 'Terapia',
            ],
            self::CODE_DIVERS => [
                'en' => 'Divers',
                'es' => 'Buzos',
            ],
            self::CODE_SWIMMING_POOLS => [
                'en' => 'Swimming Pools',
                'es' => 'Piscinas',
            ],
            self::CODE_PHYSICIANS_AND_SURGEONS => [
                'en' => 'Physicians And Surgeons',
                'es' => 'Medicos Especialistas',
            ],
            self::CODE_HANDBAGS => [
                'en' => 'Handbags',
                'es' => 'Carteras',
            ],
            self::CODE_UNIFORMS => [
                'en' => 'Uniforms',
                'es' => 'Uniformes',
            ],
            self::CODE_TELEVISION => [
                'en' => 'Television',
                'es' => 'Televisión',
            ],
            self::CODE_TELECOMMUNICATION => [
                'en' => 'Telecommunication',
                'es' => 'Telecomunicaciones',
            ],
            self::CODE_SPRINKLERS => [
                'en' => 'Sprinklers',
                'es' => 'Rociadores',
            ],
            self::CODE_SERVICE_STATIONS => [
                'en' => 'Service Stations',
                'es' => 'Estaciones De Servicio',
            ],
            self::CODE_SCHOOLS => [
                'en' => 'Schools',
                'es' => 'Escuelas',
            ],
            self::CODE_REFRIGERATING_EQUIPMENT => [
                'en' => 'Refrigerating Equipment',
                'es' => 'Refrigeración',
            ],
            self::CODE_RADIO_COMMUNICATION => [
                'en' => 'Radio Communication',
                'es' => 'Radiocomunicación',
            ],
            self::CODE_PUMPS => [
                'en' => 'Pumps',
                'es' => 'Bombas',
            ],
            self::CODE_PSYCHOLOGIST => [
                'en' => 'Psychologist',
                'es' => 'Sicólogos Por Especialidad',
            ],
            self::CODE_OPTICIANS => [
                'en' => 'Opticians',
                'es' => 'Opticas',
            ],
            self::CODE_MUSIC => [
                'en' => 'Music',
                'es' => 'Música',
            ],
            self::CODE_HEAVY_EQUIPMENT => [
                'en' => 'Heavy Equipment',
                'es' => 'Equipo Pesado',
            ],
            self::CODE_GROCERY_STORE => [
                'en' => 'Grocery Store',
                'es' => 'Colmados',
            ],
            self::CODE_GAS => [
                'en' => 'Gas',
                'es' => 'Gases',
            ],
            self::CODE_FISH_SEAFOOD => [
                'en' => 'Fish & Seafood',
                'es' => 'Pescados Y Mariscos',
            ],
            self::CODE_VIDEO_GAME => [
                'en' => 'Video - Games',
                'es' => 'Video Juegos',
            ],
            self::CODE_SPORTS => [
                'en' => 'Sports',
                'es' => 'Deportes',
            ],
            self::CODE_ADVERTISING => [
                'en' => 'Advertising',
                'es' => 'Publicidad',
            ],
            self::CODE_VIDEO_RECORDING => [
                'en' => 'Video Recording',
                'es' => 'Video Grabaciones',
            ],
            self::CODE_BABY => [
                'en' => 'Baby',
                'es' => 'Bebés',
            ],
            self::CODE_ALARM_SYSTEMS => [
                'en' => 'Alarm Systems',
                'es' => 'Alarmas',
            ],
            self::CODE_APARTMENTS => [
                'en' => 'Apartments',
                'es' => 'Apartamentos',
            ],
            self::CODE_APPLIANCES => [
                'en' => 'Appliances',
                'es' => 'Enseres',
            ],
            self::CODE_ENGINEERS => [
                'en' => 'Engineers',
                'es' => 'Ingenieros',
            ],
            self::CODE_FOOD => [
                'en' => 'Food',
                'es' => 'Alimentos',
            ],
            self::CODE_FREIGHT => [
                'en' => 'Freight',
                'es' => 'Carga',
            ],
            self::CODE_MOTORCYCLES => [
                'en' => 'Motorcycles',
                'es' => 'Motocicletas',
            ],

            self::CODE_UNDEFINED => [
                'en' => 'Undefined',
                'es' => 'Undefined',
            ],
        ];
    }

    /**
     * Used to load default fixtures
     *
     * @return array
     */
    public static function getAllCategoriesNames()
    {
        return array_merge(static::getMenuCategoriesNames(), static::getOtherCategoriesNames());
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return mixed
     */
    public function setCode($code)
    {
        if (!in_array($code, self::getCodes())) {
            throw new InvalidArgumentException('Unknown menu code');
        }

        $this->code = $code;
    }
}
