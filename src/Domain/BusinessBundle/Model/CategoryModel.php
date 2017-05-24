<?php

namespace Domain\BusinessBundle\Model;

use Domain\BusinessBundle\Entity\Category;

class CategoryModel
{
    // todo
    const UNDEFINED_CATEGORY = 'Unclassified';

    public static function getSystemCategories()
    {
        return [
            [
                'en' => 'Unclassified',
                'es' => 'Unclassified',
                'slugEn' => Category::CATEGORY_UNDEFINED_SLUG,
                'slugEs' => Category::CATEGORY_UNDEFINED_SLUG,
                'code'   => Category::CATEGORY_UNDEFINED_CODE,
            ],
            [
                'es' => 'Infopaginas Media',
                'en' => 'Infopaginas Media',
                'slugEn' => Category::CATEGORY_ARTICLE_SLUG,
                'slugEs' => Category::CATEGORY_ARTICLE_SLUG,
                'code' => Category::CATEGORY_ARTICLE_CODE,
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getCategories()
    {
        $categories = array (
            1 =>
                array (
                    'en' => '4Life Products',
                    'es' => 'Productos 4Life',
                    'slugEn' => '4life-products',
                    'slugEs' => 'productos-4life',
                ),
            2 =>
                array (
                    'en' => 'Acai Bar',
                    'es' => 'Acai Bar',
                    'slugEn' => 'acai-bar',
                    'slugEs' => 'acai-bar',
                ),
            3 =>
                array (
                    'en' => 'Acai Bowl',
                    'es' => 'Acai Bowl',
                    'slugEn' => 'acai-bowl',
                    'slugEs' => 'acai-bowl',
                ),
            4 =>
                array (
                    'en' => 'Acarus',
                    'es' => 'Acaros',
                    'slugEn' => 'acarus',
                    'slugEs' => 'acaros',
                ),
            5 =>
                array (
                    'en' => 'Accesories and Equipment',
                    'es' => 'Efectos y Equipos',
                    'slugEn' => 'accesories-and-equipment',
                    'slugEs' => 'efectos-y-equipos',
                ),
            6 =>
                array (
                    'en' => 'Accesories for Cars',
                    'es' => 'Accesorios Para Autos',
                    'slugEn' => 'accesories-for-cars',
                    'slugEs' => 'accesorios-para-autos',
                ),
            7 =>
                array (
                    'en' => 'Access Control-Security',
                    'es' => 'Control de Acceso-Seguridad',
                    'slugEn' => 'access-control-security',
                    'slugEs' => 'control-de-acceso-seguridad',
                ),
            8 =>
                array (
                    'en' => 'Accountants',
                    'es' => 'Contadores',
                    'slugEn' => 'accountants',
                    'slugEs' => 'contadores',
                ),
            9 =>
                array (
                    'en' => 'Accountants - Certified Public',
                    'es' => 'Contadores Publicos Autorizados',
                    'slugEn' => 'accountants-certified-public',
                    'slugEs' => 'contadores-publicos-autorizados',
                ),
            10 =>
                array (
                    'en' => 'Accountants - Services',
                    'es' => 'Contadores Servicios',
                    'slugEn' => 'accountants-services',
                    'slugEs' => 'contadores-servicios',
                ),
            11 =>
                array (
                    'en' => 'Accounting - Consultation',
                    'es' => 'Contabilidad - Consultas',
                    'slugEn' => 'accounting-consultation',
                    'slugEs' => 'contabilidad-consultas',
                ),
            12 =>
                array (
                    'en' => 'Accounting - Financial Statements',
                    'es' => 'Contabilidad - Estados Financieros',
                    'slugEn' => 'accounting-financial-statements',
                    'slugEs' => 'contabilidad-estados-financieros',
                ),
            13 =>
                array (
                    'en' => 'Accounting - Service',
                    'es' => 'Contabilidad-Servicio',
                    'slugEn' => 'accounting-service',
                    'slugEs' => 'contabilidad-servicio',
                ),
            14 =>
                array (
                    'en' => 'Accounting - Systems',
                    'es' => 'Contabilidad - Sistemas',
                    'slugEn' => 'accounting-systems',
                    'slugEs' => 'contabilidad-sistemas',
                ),
            15 =>
                array (
                    'en' => 'Acid',
                    'es' => 'Acidos',
                    'slugEn' => 'acid',
                    'slugEs' => 'acidos',
                ),
            16 =>
                array (
                    'en' => 'Acrylic',
                    'es' => 'Acrilico',
                    'slugEn' => 'acrylic',
                    'slugEs' => 'acrilico',
                ),
            17 =>
                array (
                    'en' => 'Acrylic Nails',
                    'es' => 'Uñas Acrílicas',
                    'slugEn' => 'acrylic-nails',
                    'slugEs' => 'unas-acrilicas',
                ),
            18 =>
                array (
                    'en' => 'Acupuncture',
                    'es' => 'Acupuntura',
                    'slugEn' => 'acupuncture',
                    'slugEs' => 'acupuntura',
                ),
            19 =>
                array (
                    'en' => 'Acura Parts',
                    'es' => 'Piezas Acura',
                    'slugEn' => 'acura-parts',
                    'slugEs' => 'piezas-acura',
                ),
            20 =>
                array (
                    'en' => 'Acustic',
                    'es' => 'Acustica-Contratistas',
                    'slugEn' => 'acustic',
                    'slugEs' => 'acustica-contratistas',
                ),
            21 =>
                array (
                    'en' => 'Acustic - Materials',
                    'es' => 'Acusticos - Materiales',
                    'slugEn' => 'acustic-materials',
                    'slugEs' => 'acusticos-materiales',
                ),
            22 =>
                array (
                    'en' => 'Addiction',
                    'es' => 'Adiccion',
                    'slugEn' => 'addiction',
                    'slugEs' => 'adiccion',
                ),
            23 =>
                array (
                    'en' => 'Adhesives - Sticky',
                    'es' => 'Adhesivos',
                    'slugEn' => 'adhesives-sticky',
                    'slugEs' => 'adhesivos',
                ),
            24 =>
                array (
                    'en' => 'Administration',
                    'es' => 'Administración',
                    'slugEn' => 'administration',
                    'slugEs' => 'administracion',
                ),
            25 =>
                array (
                    'en' => 'Administration - Property',
                    'es' => 'Administracion - Propiedades',
                    'slugEn' => 'administration-property',
                    'slugEs' => 'administracion-propiedades',
                ),
            26 =>
                array (
                    'en' => 'Administration Office',
                    'es' => 'Oficina de Administracion',
                    'slugEn' => 'administration-office',
                    'slugEs' => 'oficina-de-administracion',
                ),
            27 =>
                array (
                    'en' => 'Advanced Laparoscopic Surgery',
                    'es' => 'Cirugía de Laparoscopía Avanzada',
                    'slugEn' => 'advanced-laparoscopic-surgery',
                    'slugEs' => 'cirugia-de-laparoscopia-avanzada',
                ),
            28 =>
                array (
                    'en' => 'Advertising',
                    'es' => 'Publicidad-Agencias',
                    'slugEn' => 'advertising',
                    'slugEs' => 'publicidad-agencias',
                ),
            29 =>
                array (
                    'en' => 'Advertising - Air',
                    'es' => 'Publicidad - Aérea',
                    'slugEn' => 'advertising-air',
                    'slugEs' => 'publicidad-aerea',
                ),
            30 =>
                array (
                    'en' => 'Advertising - Digital',
                    'es' => 'Publicidad Digital',
                    'slugEn' => 'advertising-digital',
                    'slugEs' => 'publicidad-digital',
                ),
            31 =>
                array (
                    'en' => 'Advertising - Direct Mail',
                    'es' => 'Publicidad - Directa',
                    'slugEn' => 'advertising-direct-mail',
                    'slugEs' => 'publicidad-directa',
                ),
            32 =>
                array (
                    'en' => 'Advertising - Directories & Guides',
                    'es' => 'Publicidad - Directorios Y Guias',
                    'slugEn' => 'advertising-directories-guides',
                    'slugEs' => 'publicidad-directorios-y-guias',
                ),
            33 =>
                array (
                    'en' => 'Advertising - Media Representatives',
                    'es' => 'Publicidad-Representantes De Medios',
                    'slugEn' => 'advertising-media-representatives',
                    'slugEs' => 'publicidad-representantes-de-medios',
                ),
            34 =>
                array (
                    'en' => 'Advertising - On Hold',
                    'es' => 'Publicidad-On Hold',
                    'slugEn' => 'advertising-on-hold',
                    'slugEs' => 'publicidad-on-hold',
                ),
            35 =>
                array (
                    'en' => 'Advertising - Outdoor',
                    'es' => 'Publicidad-Exterior',
                    'slugEn' => 'advertising-outdoor',
                    'slugEs' => 'publicidad-exterior',
                ),
            36 =>
                array (
                    'en' => 'Advertising - Talent Agents',
                    'es' => 'Publicidad - Agentes Artistas',
                    'slugEn' => 'advertising-talent-agents',
                    'slugEs' => 'publicidad-agentes-artistas',
                ),
            37 =>
                array (
                    'en' => 'Advertising - Television',
                    'es' => 'Publicidad - Producción Televisión',
                    'slugEn' => 'advertising-television',
                    'slugEs' => 'publicidad-produccion-television',
                ),
            38 =>
                array (
                    'en' => 'Advertising-Buses and Trains',
                    'es' => 'Publicidad-Guaguas Y Trenes',
                    'slugEn' => 'advertising-buses-and-trains',
                    'slugEs' => 'publicidad-guaguas-y-trenes',
                ),
            39 =>
                array (
                    'en' => 'Advertising-Interior',
                    'es' => 'Publicidad-Interior',
                    'slugEn' => 'advertising-interior',
                    'slugEs' => 'publicidad-interior',
                ),
            40 =>
                array (
                    'en' => 'Advertising-Press',
                    'es' => 'Publicidad-Prensa',
                    'slugEn' => 'advertising-press',
                    'slugEs' => 'publicidad-prensa',
                ),
            41 =>
                array (
                    'en' => 'Aesthetics - Fashion',
                    'es' => 'Estetica - Moda',
                    'slugEn' => 'aesthetics-fashion',
                    'slugEs' => 'estetica-moda',
                ),
            42 =>
                array (
                    'en' => 'Aged People - Services',
                    'es' => 'Envejecientes - Servicios',
                    'slugEn' => 'aged-people-services',
                    'slugEs' => 'envejecientes-servicios',
                ),
            43 =>
                array (
                    'en' => 'Aged People homes - Temporary',
                    'es' => 'Hogares Envejecientes - Temporeros',
                    'slugEn' => 'aged-people-homes-temporary',
                    'slugEs' => 'hogares-envejecientes-temporeros',
                ),
            44 =>
                array (
                    'en' => 'Agricultural Center',
                    'es' => 'Agrocentro',
                    'slugEn' => 'agricultural-center',
                    'slugEs' => 'agrocentro',
                ),
            45 =>
                array (
                    'en' => 'Agriculture - Administration',
                    'es' => 'Agricultura - Administración',
                    'slugEn' => 'agriculture-administration',
                    'slugEs' => 'agricultura-administracion',
                ),
            46 =>
                array (
                    'en' => 'Agriculture - Products',
                    'es' => 'Agrícolas - Productos',
                    'slugEn' => 'agriculture-products',
                    'slugEs' => 'agricolas-productos',
                ),
            47 =>
                array (
                    'en' => 'Agritourism',
                    'es' => 'Turismo-Oficinas Oficiales',
                    'slugEn' => 'agritourism',
                    'slugEs' => 'turismo-oficinas-oficiales',
                ),
            48 =>
                array (
                    'en' => 'Agronomists (Agricultural Expert)',
                    'es' => 'Agrónomos',
                    'slugEn' => 'agronomists-agricultural-expert',
                    'slugEs' => 'agronomos',
                ),
            49 =>
                array (
                    'en' => 'Aids - Orientation - Prevention - Treatment',
                    'es' => 'Sida - Orientación - Prevención - Tratamiento',
                    'slugEn' => 'aids-orientation-prevention-treatment',
                    'slugEs' => 'sida-orientacion-prevencion-tratamiento',
                ),
            50 =>
                array (
                    'en' => 'Air - Purifiers',
                    'es' => 'Aire-Purificadores',
                    'slugEn' => 'air-purifiers',
                    'slugEs' => 'aire-purificadores',
                ),
            51 =>
                array (
                    'en' => 'Air Ambulance - Service',
                    'es' => 'Ambulancia-Aérea',
                    'slugEn' => 'air-ambulance-service',
                    'slugEs' => 'ambulancia-aerea',
                ),
            52 =>
                array (
                    'en' => 'Air Cargo',
                    'es' => 'Carga Aérea',
                    'slugEn' => 'air-cargo',
                    'slugEs' => 'carga-aerea',
                ),
            53 =>
                array (
                    'en' => 'Air Conditioner',
                    'es' => 'Aire Acondicionado',
                    'slugEn' => 'air-conditioner',
                    'slugEs' => 'aire-acondicionado',
                ),
            54 =>
                array (
                    'en' => 'Air Conditioner - Balance',
                    'es' => 'Aire Acondicionado - Balanceo',
                    'slugEn' => 'air-conditioner-balance',
                    'slugEs' => 'aire-acondicionado-balanceo',
                ),
            55 =>
                array (
                    'en' => 'Air Conditioner Maintenance',
                    'es' => 'Aire Acondicionado Mantenimiento',
                    'slugEn' => 'air-conditioner-maintenance',
                    'slugEs' => 'aire-acondicionado-mantenimiento',
                ),
            56 =>
                array (
                    'en' => 'Air Conditioner Sales',
                    'es' => 'Aire Acondicionado Ventas',
                    'slugEn' => 'air-conditioner-sales',
                    'slugEs' => 'aire-acondicionado-ventas',
                ),
            57 =>
                array (
                    'en' => 'Air Conditioner Service & Maintenance',
                    'es' => 'Aires Acondicionados Servicio y Mantenimiento',
                    'slugEn' => 'air-conditioner-service-maintenance',
                    'slugEs' => 'aires-acondicionados-servicio-y-mantenimiento',
                ),
            58 =>
                array (
                    'en' => 'Air Conditioning - Conduit',
                    'es' => 'Aire Acondicionado-Conductos',
                    'slugEn' => 'air-conditioning-conduit',
                    'slugEs' => 'aire-acondicionado-conductos',
                ),
            59 =>
                array (
                    'en' => 'Air Conditioning - Household',
                    'es' => 'Aire Acondicionado-Doméstico',
                    'slugEn' => 'air-conditioning-household',
                    'slugEs' => 'aire-acondicionado-domestico',
                ),
            60 =>
                array (
                    'en' => 'Air Conditioning - Industrial & Commercial',
                    'es' => 'Aire Acondicionado-Industrial Y Comercial',
                    'slugEn' => 'air-conditioning-industrial-commercial',
                    'slugEs' => 'aire-acondicionado-industrial-y-comercial',
                ),
            61 =>
                array (
                    'en' => 'Air Conditioning - Repair',
                    'es' => 'Aire Acondicionado - Reparación',
                    'slugEn' => 'air-conditioning-repair',
                    'slugEs' => 'aire-acondicionado-reparacion',
                ),
            62 =>
                array (
                    'en' => 'Air Conditioning-Consoles-Maintenance',
                    'es' => 'Aire Acondicionado-Consolas-Mantenimiento',
                    'slugEn' => 'air-conditioning-consoles-maintenance',
                    'slugEs' => 'aire-acondicionado-consolas-mantenimiento',
                ),
            63 =>
                array (
                    'en' => 'Aircraft',
                    'es' => 'Aviones',
                    'slugEn' => 'aircraft',
                    'slugEs' => 'aviones',
                ),
            64 =>
                array (
                    'en' => 'Airline Companies',
                    'es' => 'Líneas Aéreas',
                    'slugEn' => 'airline-companies',
                    'slugEs' => 'lineas-aereas',
                ),
            65 =>
                array (
                    'en' => 'Alarm Systems - Burglar',
                    'es' => 'Alarmas-Robo',
                    'slugEn' => 'alarm-systems-burglar',
                    'slugEs' => 'alarmas-robo',
                ),
            66 =>
                array (
                    'en' => 'Alarm Systems - Central Monitoring',
                    'es' => 'Alarmas-Estación Central',
                    'slugEn' => 'alarm-systems-central-monitoring',
                    'slugEs' => 'alarmas-estacion-central',
                ),
            67 =>
                array (
                    'en' => 'Alarm Systems - Fire',
                    'es' => 'Alarmas-Fuego',
                    'slugEn' => 'alarm-systems-fire',
                    'slugEs' => 'alarmas-fuego',
                ),
            68 =>
                array (
                    'en' => 'Alarm Systems - Wholesale',
                    'es' => 'Alarmas-Al Por Mayor',
                    'slugEn' => 'alarm-systems-wholesale',
                    'slugEs' => 'alarmas-al-por-mayor',
                ),
            69 =>
                array (
                    'en' => 'Alarms',
                    'es' => 'Alarmas',
                    'slugEn' => 'alarms',
                    'slugEs' => 'alarmas',
                ),
            70 =>
                array (
                    'en' => 'Alarms - Computarized',
                    'es' => 'Alarmas-Computadorizadas',
                    'slugEn' => 'alarms-computarized',
                    'slugEs' => 'alarmas-computadorizadas',
                ),
            71 =>
                array (
                    'en' => 'Alcoholism - Centers',
                    'es' => 'Alcoholismo-Información Y Tratamiento',
                    'slugEn' => 'alcoholism-centers',
                    'slugEs' => 'alcoholismo-informacion-y-tratamiento',
                ),
            72 =>
                array (
                    'en' => 'Alternative Medical Therapy',
                    'es' => 'Terapia Médica Alternativa',
                    'slugEn' => 'alternative-medical-therapy',
                    'slugEs' => 'terapia-medica-alternativa',
                ),
            73 =>
                array (
                    'en' => 'Aluminum',
                    'es' => 'Aluminio',
                    'slugEn' => 'aluminum',
                    'slugEs' => 'aluminio',
                ),
            74 =>
                array (
                    'en' => 'Aluminum - Distributors',
                    'es' => 'Aluminio-Distribuidores',
                    'slugEn' => 'aluminum-distributors',
                    'slugEs' => 'aluminio-distribuidores',
                ),
            75 =>
                array (
                    'en' => 'Aluminum Roofs Galvalum',
                    'es' => 'Techos Aluminio Galvalum',
                    'slugEn' => 'aluminum-roofs-galvalum',
                    'slugEs' => 'techos-aluminio-galvalum',
                ),
            76 =>
                array (
                    'en' => 'Alzheimer\'s Prevention',
                    'es' => 'Médicos Especialistas - Prevención Alzheimer',
                    'slugEn' => 'alzheimer-s-prevention',
                    'slugEs' => 'medicos-especialistas-prevencion-alzheimer',
                ),
            77 =>
                array (
                    'en' => 'Ambulance',
                    'es' => 'Ambulancias',
                    'slugEn' => 'ambulance',
                    'slugEs' => 'ambulancias',
                ),
            78 =>
                array (
                    'en' => 'Ambulance - Services, Equipment & Supplies',
                    'es' => 'Ambulancia-Servicios Efectos Y Equipo',
                    'slugEn' => 'ambulance-services-equipment-supplies',
                    'slugEs' => 'ambulancia-servicios-efectos-y-equipo',
                ),
            79 =>
                array (
                    'en' => 'Ambulatory Surgery Center',
                    'es' => 'Centro Cirugia Ambulatoria',
                    'slugEn' => 'ambulatory-surgery-center',
                    'slugEs' => 'centro-cirugia-ambulatoria',
                ),
            80 =>
                array (
                    'en' => 'Amusement Parks',
                    'es' => 'Parques De Diversiones',
                    'slugEn' => 'amusement-parks',
                    'slugEs' => 'parques-de-diversiones',
                ),
            81 =>
                array (
                    'en' => 'Animal Boutique',
                    'es' => 'Boutique de Animales',
                    'slugEn' => 'animal-boutique',
                    'slugEs' => 'boutique-de-animales',
                ),
            82 =>
                array (
                    'en' => 'Animal Care',
                    'es' => 'Cuido de Animales',
                    'slugEn' => 'animal-care',
                    'slugEs' => 'cuido-de-animales',
                ),
            83 =>
                array (
                    'en' => 'Animals',
                    'es' => 'Animales Domésticos',
                    'slugEn' => 'animals',
                    'slugEs' => 'animales-domesticos',
                ),
            84 =>
                array (
                    'en' => 'Animals - Boutiques',
                    'es' => 'Animales - Boutiques',
                    'slugEn' => 'animals-boutiques',
                    'slugEs' => 'animales-boutiques',
                ),
            85 =>
                array (
                    'en' => 'Animals - Fishes',
                    'es' => 'Animales - Peces',
                    'slugEn' => 'animals-fishes',
                    'slugEs' => 'animales-peces',
                ),
            86 =>
                array (
                    'en' => 'Animals - Hospitals',
                    'es' => 'Animales-Hospitales',
                    'slugEn' => 'animals-hospitals',
                    'slugEs' => 'animales-hospitales',
                ),
            87 =>
                array (
                    'en' => 'Animals - Hotel',
                    'es' => 'Animales - Hospedaje',
                    'slugEn' => 'animals-hotel',
                    'slugEs' => 'animales-hospedaje',
                ),
            88 =>
                array (
                    'en' => 'Animals - Rescue',
                    'es' => 'Animales Albergue',
                    'slugEn' => 'animals-rescue',
                    'slugEs' => 'animales-albergue',
                ),
            89 =>
                array (
                    'en' => 'Animals - Training',
                    'es' => 'Animales-Entrenamiento',
                    'slugEn' => 'animals-training',
                    'slugEs' => 'animales-entrenamiento',
                ),
            90 =>
                array (
                    'en' => 'Animals Care - Spa',
                    'es' => 'Animales-Cuidado-Spa',
                    'slugEn' => 'animals-care-spa',
                    'slugEs' => 'animales-cuidado-spa',
                ),
            91 =>
                array (
                    'en' => 'Animals Domestic - Equipment & Supplies',
                    'es' => 'Animales Domésticos-Suplidores Tiendas',
                    'slugEn' => 'animals-domestic-equipment-supplies',
                    'slugEs' => 'animales-domesticos-suplidores-tiendas',
                ),
            92 =>
                array (
                    'en' => 'Animals Domestic - Veterinary Workshops',
                    'es' => 'Animales Domésticos-Talleres de Veterinaria',
                    'slugEn' => 'animals-domestic-veterinary-workshops',
                    'slugEs' => 'animales-domesticos-talleres-de-veterinaria',
                ),
            93 =>
                array (
                    'en' => 'Animals Protection',
                    'es' => 'Animales Protección',
                    'slugEn' => 'animals-protection',
                    'slugEs' => 'animales-proteccion',
                ),
            94 =>
                array (
                    'en' => 'Antennas',
                    'es' => 'Antenas',
                    'slugEn' => 'antennas',
                    'slugEs' => 'antenas',
                ),
            95 =>
                array (
                    'en' => 'Antiquities',
                    'es' => 'Antigüedades',
                    'slugEn' => 'antiquities',
                    'slugEs' => 'antiguedades',
                ),
            96 =>
                array (
                    'en' => 'Apartment - Buildings',
                    'es' => 'Apartamentos - Edificios',
                    'slugEn' => 'apartment-buildings',
                    'slugEs' => 'apartamentos-edificios',
                ),
            97 =>
                array (
                    'en' => 'Apartment for Rent',
                    'es' => 'Alquiler de Apartamentos',
                    'slugEn' => 'apartment-for-rent',
                    'slugEs' => 'alquiler-de-apartamentos',
                ),
            98 =>
                array (
                    'en' => 'Apartments',
                    'es' => 'Apartamentos',
                    'slugEn' => 'apartments',
                    'slugEs' => 'apartamentos',
                ),
            99 =>
                array (
                    'en' => 'Apartments - Furnished',
                    'es' => 'Apartamentos - Amueblados',
                    'slugEn' => 'apartments-furnished',
                    'slugEs' => 'apartamentos-amueblados',
                ),
            100 =>
                array (
                    'en' => 'Apartments - Rental',
                    'es' => 'Apartamentos-Alquiler',
                    'slugEn' => 'apartments-rental',
                    'slugEs' => 'apartamentos-alquiler',
                ),
            101 =>
                array (
                    'en' => 'Apartments - Sale - Rent',
                    'es' => 'Apartamentos - Venta - Alquiler',
                    'slugEn' => 'apartments-sale-rent',
                    'slugEs' => 'apartamentos-venta-alquiler',
                ),
            102 =>
                array (
                    'en' => 'Apartments Sale and Rent',
                    'es' => 'Apartamentos Venta y Alquiler',
                    'slugEn' => 'apartments-sale-and-rent',
                    'slugEs' => 'apartamentos-venta-y-alquiler',
                ),
            103 =>
                array (
                    'en' => 'Apple Computers Repair',
                    'es' => 'Computadoras Apple Reparación',
                    'slugEn' => 'apple-computers-repair',
                    'slugEs' => 'computadoras-apple-reparacion',
                ),
            104 =>
                array (
                    'en' => 'Appliances - Domestic',
                    'es' => 'Enseres - Domésticos',
                    'slugEn' => 'appliances-domestic',
                    'slugEs' => 'enseres-domesticos',
                ),
            105 =>
                array (
                    'en' => 'Appliances - Domestic - Parts & Accessories',
                    'es' => 'Enseres - Accesorios Y Piezas',
                    'slugEn' => 'appliances-domestic-parts-accessories',
                    'slugEs' => 'enseres-accesorios-y-piezas',
                ),
            106 =>
                array (
                    'en' => 'Appliances - Electric Stoves-Repair',
                    'es' => 'Enseres - Estufas Eléctricas-Reparación',
                    'slugEn' => 'appliances-electric-stoves-repair',
                    'slugEs' => 'enseres-estufas-electricas-reparacion',
                ),
            107 =>
                array (
                    'en' => 'Appliances - Electrical',
                    'es' => 'Enseres - Eléctricos',
                    'slugEn' => 'appliances-electrical',
                    'slugEs' => 'enseres-electricos',
                ),
            108 =>
                array (
                    'en' => 'Appliances - Gas',
                    'es' => 'Enseres - Gas',
                    'slugEn' => 'appliances-gas',
                    'slugEs' => 'enseres-gas',
                ),
            109 =>
                array (
                    'en' => 'Appliances - Refrigerators & Freezers',
                    'es' => 'Enseres - Neveras Y Congeladores',
                    'slugEn' => 'appliances-refrigerators-freezers',
                    'slugEs' => 'enseres-neveras-y-congeladores',
                ),
            110 =>
                array (
                    'en' => 'Appliances - Repair',
                    'es' => 'Enseres - Reparación',
                    'slugEn' => 'appliances-repair',
                    'slugEs' => 'enseres-reparacion',
                ),
            111 =>
                array (
                    'en' => 'Appliances - Washing Machines & Dryers',
                    'es' => 'Enseres - Lavadoras Y Secadoras',
                    'slugEn' => 'appliances-washing-machines-dryers',
                    'slugEs' => 'enseres-lavadoras-y-secadoras',
                ),
            112 =>
                array (
                    'en' => 'Applied Psychology',
                    'es' => 'Sicología-Aplicada',
                    'slugEn' => 'applied-psychology',
                    'slugEs' => 'sicologia-aplicada',
                ),
            113 =>
                array (
                    'en' => 'Appraisers',
                    'es' => 'Tasadores',
                    'slugEn' => 'appraisers',
                    'slugEs' => 'tasadores',
                ),
            114 =>
                array (
                    'en' => 'April',
                    'es' => 'Abril',
                    'slugEn' => 'april',
                    'slugEs' => 'abril',
                ),
            115 =>
                array (
                    'en' => 'Aquarium',
                    'es' => 'Acuarios',
                    'slugEn' => 'aquarium',
                    'slugEs' => 'acuarios',
                ),
            116 =>
                array (
                    'en' => 'Aquatic Activities',
                    'es' => 'Actividades Acuáticas',
                    'slugEn' => 'aquatic-activities',
                    'slugEs' => 'actividades-acuaticas',
                ),
            117 =>
                array (
                    'en' => 'Architects',
                    'es' => 'Arquitectos',
                    'slugEn' => 'architects',
                    'slugEs' => 'arquitectos',
                ),
            118 =>
                array (
                    'en' => 'Architecture-Specialty',
                    'es' => 'Arquitectura-Especialidades',
                    'slugEn' => 'architecture-specialty',
                    'slugEs' => 'arquitectura-especialidades',
                ),
            119 =>
                array (
                    'en' => 'Armored Car - Service',
                    'es' => 'Carros Blindados-Servicio',
                    'slugEn' => 'armored-car-service',
                    'slugEs' => 'carros-blindados-servicio',
                ),
            120 =>
                array (
                    'en' => 'Armories',
                    'es' => 'Armerías',
                    'slugEn' => 'armories',
                    'slugEs' => 'armerias',
                ),
            121 =>
                array (
                    'en' => 'Arms - Fire',
                    'es' => 'Armas De Fuego',
                    'slugEn' => 'arms-fire',
                    'slugEs' => 'armas-de-fuego',
                ),
            122 =>
                array (
                    'en' => 'Army & Navy Goods',
                    'es' => 'Militares-Efectos',
                    'slugEn' => 'army-navy-goods',
                    'slugEs' => 'militares-efectos',
                ),
            123 =>
                array (
                    'en' => 'Aromatherapy',
                    'es' => 'Aromaterapia',
                    'slugEn' => 'aromatherapy',
                    'slugEs' => 'aromaterapia',
                ),
            124 =>
                array (
                    'en' => 'Arroz Cinta Azul',
                    'es' => 'Arroz Cinta Azul',
                    'slugEn' => 'arroz-cinta-azul',
                    'slugEs' => 'arroz-cinta-azul',
                ),
            125 =>
                array (
                    'en' => 'Arroz El Mago',
                    'es' => 'Arroz El Mago',
                    'slugEn' => 'arroz-el-mago',
                    'slugEs' => 'arroz-el-mago',
                ),
            126 =>
                array (
                    'en' => 'Arroz Mahatma',
                    'es' => 'Arroz Mahatma',
                    'slugEn' => 'arroz-mahatma',
                    'slugEs' => 'arroz-mahatma',
                ),
            127 =>
                array (
                    'en' => 'Arroz Sello Rojo',
                    'es' => 'Arroz Sello Rojo',
                    'slugEn' => 'arroz-sello-rojo',
                    'slugEs' => 'arroz-sello-rojo',
                ),
            128 =>
                array (
                    'en' => 'Arroz Success',
                    'es' => 'Arroz Success',
                    'slugEn' => 'arroz-success',
                    'slugEs' => 'arroz-success',
                ),
            129 =>
                array (
                    'en' => 'Art',
                    'es' => 'Arte',
                    'slugEn' => 'art',
                    'slugEs' => 'arte',
                ),
            130 =>
                array (
                    'en' => 'Art - Commercial',
                    'es' => 'Arte-Comercial',
                    'slugEn' => 'art-commercial',
                    'slugEs' => 'arte-comercial',
                ),
            131 =>
                array (
                    'en' => 'Art - Galleries',
                    'es' => 'Arte-Galerías',
                    'slugEn' => 'art-galleries',
                    'slugEs' => 'arte-galerias',
                ),
            132 =>
                array (
                    'en' => 'Art - Materials & Supplies',
                    'es' => 'Arte-Efectos Y Equipo',
                    'slugEn' => 'art-materials-supplies',
                    'slugEs' => 'arte-efectos-y-equipo',
                ),
            133 =>
                array (
                    'en' => 'Art - Paintings',
                    'es' => 'Arte - Cuadros',
                    'slugEn' => 'art-paintings',
                    'slugEs' => 'arte-cuadros',
                ),
            134 =>
                array (
                    'en' => 'Art Material',
                    'es' => 'Materiales de Arte',
                    'slugEn' => 'art-material',
                    'slugEs' => 'materiales-de-arte',
                ),
            135 =>
                array (
                    'en' => 'Artisan Pizzas',
                    'es' => 'Pizzas Artesanales',
                    'slugEn' => 'artisan-pizzas',
                    'slugEs' => 'pizzas-artesanales',
                ),
            136 =>
                array (
                    'en' => 'Artists Recruitment',
                    'es' => 'Contratación de Artistas',
                    'slugEn' => 'artists-recruitment',
                    'slugEs' => 'contratacion-de-artistas',
                ),
            137 =>
                array (
                    'en' => 'Art-Metal Crafts',
                    'es' => 'Artesanía',
                    'slugEn' => 'art-metal-crafts',
                    'slugEs' => 'artesania',
                ),
            138 =>
                array (
                    'en' => 'Art-Murals',
                    'es' => 'Arte-Murales',
                    'slugEn' => 'art-murals',
                    'slugEs' => 'arte-murales',
                ),
            139 =>
                array (
                    'en' => 'Art-Painting',
                    'es' => 'Arte-Pinturas',
                    'slugEn' => 'art-painting',
                    'slugEs' => 'arte-pinturas',
                ),
            140 =>
                array (
                    'en' => 'Artworks',
                    'es' => 'Obras de Arte',
                    'slugEn' => 'artworks',
                    'slugEs' => 'obras-de-arte',
                ),
            141 =>
                array (
                    'en' => 'Asbestos - Removal',
                    'es' => 'Asbesto-Remoción',
                    'slugEn' => 'asbestos-removal',
                    'slugEs' => 'asbesto-remocion',
                ),
            142 =>
                array (
                    'en' => 'Asphalt',
                    'es' => 'Asfalto',
                    'slugEn' => 'asphalt',
                    'slugEs' => 'asfalto',
                ),
            143 =>
                array (
                    'en' => 'Assistance - Technology',
                    'es' => 'Asistencia-Tecnológica',
                    'slugEn' => 'assistance-technology',
                    'slugEs' => 'asistencia-tecnologica',
                ),
            144 =>
                array (
                    'en' => 'Associations',
                    'es' => 'Asociaciones',
                    'slugEn' => 'associations',
                    'slugEs' => 'asociaciones',
                ),
            145 =>
                array (
                    'en' => 'Associations - Management',
                    'es' => 'Asociaciones-Administración',
                    'slugEn' => 'associations-management',
                    'slugEs' => 'asociaciones-administracion',
                ),
            146 =>
                array (
                    'en' => 'Astrology',
                    'es' => 'Astrología',
                    'slugEn' => 'astrology',
                    'slugEs' => 'astrologia',
                ),
            147 =>
                array (
                    'en' => 'Astrology - Psychic',
                    'es' => 'Astrologia - Psiquicos',
                    'slugEn' => 'astrology-psychic',
                    'slugEs' => 'astrologia-psiquicos',
                ),
            148 =>
                array (
                    'en' => 'Asylums',
                    'es' => 'Asilos',
                    'slugEn' => 'asylums',
                    'slugEs' => 'asilos',
                ),
            149 =>
                array (
                    'en' => 'Attorneys - By Practice - Administrative',
                    'es' => 'Abogados - Por Práctica - Administrativo',
                    'slugEn' => 'attorneys-by-practice-administrative',
                    'slugEs' => 'abogados-por-practica-administrativo',
                ),
            150 =>
                array (
                    'en' => 'Attorneys - Falls',
                    'es' => 'Abogados - Caidas',
                    'slugEn' => 'attorneys-falls',
                    'slugEs' => 'abogados-caidas',
                ),
            151 =>
                array (
                    'en' => 'Attorneys - Litigation',
                    'es' => 'Abogados - Litigios',
                    'slugEn' => 'attorneys-litigation',
                    'slugEs' => 'abogados-litigios',
                ),
            152 =>
                array (
                    'en' => 'Attorneys by Specialty - Contribution',
                    'es' => 'Abogados por Especialidad - Contribución',
                    'slugEn' => 'attorneys-by-specialty-contribution',
                    'slugEs' => 'abogados-por-especialidad-contribucion',
                ),
            153 =>
                array (
                    'en' => 'Auctioneers',
                    'es' => 'Subastadores',
                    'slugEn' => 'auctioneers',
                    'slugEs' => 'subastadores',
                ),
            154 =>
                array (
                    'en' => 'Audiology',
                    'es' => 'Audiología',
                    'slugEn' => 'audiology',
                    'slugEs' => 'audiologia',
                ),
            155 =>
                array (
                    'en' => 'Audiovisual',
                    'es' => 'Audiovisuales-Producciones',
                    'slugEn' => 'audiovisual',
                    'slugEs' => 'audiovisuales-producciones',
                ),
            156 =>
                array (
                    'en' => 'Audiovisual - Parts And Equipment',
                    'es' => 'Audiovisual-Efectos Y Equipo',
                    'slugEn' => 'audiovisual-parts-and-equipment',
                    'slugEs' => 'audiovisual-efectos-y-equipo',
                ),
            157 =>
                array (
                    'en' => 'Audit',
                    'es' => 'Auditoría',
                    'slugEn' => 'audit',
                    'slugEs' => 'auditoria',
                ),
            158 =>
                array (
                    'en' => 'Auto body parts - Wholesale and retail',
                    'es' => 'Piezas de hojalatería - Al por mayor y al detal',
                    'slugEn' => 'auto-body-parts-wholesale-and-retail',
                    'slugEs' => 'piezas-de-hojalateria-al-por-mayor-y-al-detal',
                ),
            159 =>
                array (
                    'en' => 'Auto Body Repair & Painting for Insurances',
                    'es' => 'Hojalatería y Pintura Estimados Para Seguros',
                    'slugEn' => 'auto-body-repair-painting-for-insurances',
                    'slugEs' => 'hojalateria-y-pintura-estimados-para-seguros',
                ),
            160 =>
                array (
                    'en' => 'Auto Parts',
                    'es' => 'Automoviles Piezas',
                    'slugEn' => 'auto-parts',
                    'slugEs' => 'automoviles-piezas',
                ),
            161 =>
                array (
                    'en' => 'Auto Parts Ford',
                    'es' => 'Auto Piezas Ford',
                    'slugEn' => 'auto-parts-ford',
                    'slugEs' => 'auto-piezas-ford',
                ),
            162 =>
                array (
                    'en' => 'Automated Teller Machines (atm)',
                    'es' => 'Cajeros Automáticos',
                    'slugEn' => 'automated-teller-machines-atm',
                    'slugEs' => 'cajeros-automaticos',
                ),
            163 =>
                array (
                    'en' => 'Automatic Controls',
                    'es' => 'Controles Automáticos-Reguladores',
                    'slugEn' => 'automatic-controls',
                    'slugEs' => 'controles-automaticos-reguladores',
                ),
            164 =>
                array (
                    'en' => 'Automatic Transmission',
                    'es' => 'Transmisiones Automáticas',
                    'slugEn' => 'automatic-transmission',
                    'slugEs' => 'transmisiones-automaticas',
                ),
            165 =>
                array (
                    'en' => 'Automobile',
                    'es' => 'Automóviles',
                    'slugEn' => 'automobile',
                    'slugEs' => 'automoviles',
                ),
            166 =>
                array (
                    'en' => 'Automobile Computer Systems',
                    'es' => 'Automoviles Sistemas Computarizados',
                    'slugEn' => 'automobile-computer-systems',
                    'slugEs' => 'automoviles-sistemas-computarizados',
                ),
            167 =>
                array (
                    'en' => 'Automobile Lights',
                    'es' => 'Automóviles Iluminación',
                    'slugEn' => 'automobile-lights',
                    'slugEs' => 'automoviles-iluminacion',
                ),
            168 =>
                array (
                    'en' => 'Automobiles -  Locksmith',
                    'es' => 'Automóviles - Cerrajería',
                    'slugEn' => 'automobiles-locksmith',
                    'slugEs' => 'automoviles-cerrajeria',
                ),
            169 =>
                array (
                    'en' => 'Automobiles - 4x4',
                    'es' => 'Automóviles - 4x4',
                    'slugEn' => 'automobiles-4x4',
                    'slugEs' => 'automoviles-4x4',
                ),
            170 =>
                array (
                    'en' => 'Automobiles - Air Bags',
                    'es' => 'Automóviles - Bolsas de Aire',
                    'slugEn' => 'automobiles-air-bags',
                    'slugEs' => 'automoviles-bolsas-de-aire',
                ),
            171 =>
                array (
                    'en' => 'Automobiles - Air Conditioner',
                    'es' => 'Automóviles - Aire Acondicionado',
                    'slugEn' => 'automobiles-air-conditioner',
                    'slugEs' => 'automoviles-aire-acondicionado',
                ),
            172 =>
                array (
                    'en' => 'Automobiles - Air Conditioner-Equipment & Supplies',
                    'es' => 'Automóviles - Aires Acondicionados-Efectos Y Equipo',
                    'slugEn' => 'automobiles-air-conditioner-equipment-supplies',
                    'slugEs' => 'automoviles-aires-acondicionados-efectos-y-equipo',
                ),
            173 =>
                array (
                    'en' => 'Automobiles - Alarms',
                    'es' => 'Automóviles - Alarmas',
                    'slugEn' => 'automobiles-alarms',
                    'slugEs' => 'automoviles-alarmas',
                ),
            174 =>
                array (
                    'en' => 'Automobiles - Alignment And Balance',
                    'es' => 'Automóviles - Alineación y Balance',
                    'slugEn' => 'automobiles-alignment-and-balance',
                    'slugEs' => 'automoviles-alineacion-y-balance',
                ),
            175 =>
                array (
                    'en' => 'Automobiles - Alternators',
                    'es' => 'Automóviles - Alternadores',
                    'slugEn' => 'automobiles-alternators',
                    'slugEs' => 'automoviles-alternadores',
                ),
            176 =>
                array (
                    'en' => 'Automobiles - AutoGas (Carburetor)',
                    'es' => 'Automóviles - AutoGas (Carburación)',
                    'slugEn' => 'automobiles-autogas-carburetor',
                    'slugEs' => 'automoviles-autogas-carburacion',
                ),
            177 =>
                array (
                    'en' => 'Automobiles - Batteries',
                    'es' => 'Automóviles - Baterías',
                    'slugEn' => 'automobiles-batteries',
                    'slugEs' => 'automoviles-baterias',
                ),
            178 =>
                array (
                    'en' => 'Automobiles - Body Repair & Painting',
                    'es' => 'Automóviles - Hojalatería Y Pintura',
                    'slugEn' => 'automobiles-body-repair-painting',
                    'slugEs' => 'automoviles-hojalateria-y-pintura',
                ),
            179 =>
                array (
                    'en' => 'Automobiles - Body Repair & Painting - Equipment & Supplies',
                    'es' => 'Automoviles - Hojalateria Y Pintura-Efectos Y Equipo',
                    'slugEn' => 'automobiles-body-repair-painting-equipment-supplies',
                    'slugEs' => 'automoviles-hojalateria-y-pintura-efectos-y-equipo',
                ),
            180 =>
                array (
                    'en' => 'Automobiles - Body Repair & Painting - Quote',
                    'es' => 'Automóviles - Hojalatería Y Pintura - Estimados',
                    'slugEn' => 'automobiles-body-repair-painting-quote',
                    'slugEs' => 'automoviles-hojalateria-y-pintura-estimados',
                ),
            181 =>
                array (
                    'en' => 'Automobiles - Boxes guides',
                    'es' => 'Automóviles - Cajas de guías',
                    'slugEn' => 'automobiles-boxes-guides',
                    'slugEs' => 'automoviles-cajas-de-guias',
                ),
            182 =>
                array (
                    'en' => 'Automobiles - Brakes',
                    'es' => 'Automóviles - Frenos',
                    'slugEn' => 'automobiles-brakes',
                    'slugEs' => 'automoviles-frenos',
                ),
            183 =>
                array (
                    'en' => 'Automobiles - Bumpers',
                    'es' => 'Automóviles - Bumpers (Parachoques)',
                    'slugEn' => 'automobiles-bumpers',
                    'slugEs' => 'automoviles-bumpers-parachoques',
                ),
            184 =>
                array (
                    'en' => 'Automobiles - Carburetors',
                    'es' => 'Automóviles - Carburadores',
                    'slugEn' => 'automobiles-carburetors',
                    'slugEs' => 'automoviles-carburadores',
                ),
            185 =>
                array (
                    'en' => 'Automobiles - Cleaning Battery',
                    'es' => 'Limpieza-Servicio',
                    'slugEn' => 'automobiles-cleaning-battery',
                    'slugEs' => 'limpieza-servicio',
                ),
            186 =>
                array (
                    'en' => 'Automobiles - Combustible',
                    'es' => 'Automóviles - Combustibles',
                    'slugEn' => 'automobiles-combustible',
                    'slugEs' => 'automoviles-combustibles',
                ),
            187 =>
                array (
                    'en' => 'Automobiles - Complete Axles',
                    'es' => 'Automóviles - Ejes Completos',
                    'slugEn' => 'automobiles-complete-axles',
                    'slugEs' => 'automoviles-ejes-completos',
                ),
            188 =>
                array (
                    'en' => 'Automobiles - Computers',
                    'es' => 'Automóviles - Computadoras',
                    'slugEn' => 'automobiles-computers',
                    'slugEs' => 'automoviles-computadoras',
                ),
            189 =>
                array (
                    'en' => 'Automobiles - Computers Repair',
                    'es' => 'Automóviles - Computadoras Reparación',
                    'slugEn' => 'automobiles-computers-repair',
                    'slugEs' => 'automoviles-computadoras-reparacion',
                ),
            190 =>
                array (
                    'en' => 'Automobiles - Dealers - Used',
                    'es' => 'Automóviles - Dealer - Usados',
                    'slugEn' => 'automobiles-dealers-used',
                    'slugEs' => 'automoviles-dealer-usados',
                ),
            191 =>
                array (
                    'en' => 'Automobiles - Detailing',
                    'es' => 'Automóviles - Detailing',
                    'slugEn' => 'automobiles-detailing',
                    'slugEs' => 'automoviles-detailing',
                ),
            192 =>
                array (
                    'en' => 'Automobiles - Diesel Mechanics',
                    'es' => 'Automóviles - Mecánica Diesel',
                    'slugEn' => 'automobiles-diesel-mechanics',
                    'slugEs' => 'automoviles-mecanica-diesel',
                ),
            193 =>
                array (
                    'en' => 'Automobiles - Electromechanics',
                    'es' => 'Automóviles - Electromecánica',
                    'slugEn' => 'automobiles-electromechanics',
                    'slugEs' => 'automoviles-electromecanica',
                ),
            194 =>
                array (
                    'en' => 'Automobiles - European Mechanics',
                    'es' => 'Automóviles - Mecánica Europea',
                    'slugEn' => 'automobiles-european-mechanics',
                    'slugEs' => 'automoviles-mecanica-europea',
                ),
            195 =>
                array (
                    'en' => 'Automobiles - European Parts',
                    'es' => 'Automóviles - Accesorios y Piezas Europeas',
                    'slugEn' => 'automobiles-european-parts',
                    'slugEs' => 'automoviles-accesorios-y-piezas-europeas',
                ),
            196 =>
                array (
                    'en' => 'Automobiles - Financing',
                    'es' => 'Automóviles - Financiamiento',
                    'slugEn' => 'automobiles-financing',
                    'slugEs' => 'automoviles-financiamiento',
                ),
            197 =>
                array (
                    'en' => 'Automobiles - Fuel Injection',
                    'es' => 'Automóviles - Fuel Injection (Inyección De Combustible)',
                    'slugEn' => 'automobiles-fuel-injection',
                    'slugEs' => 'automoviles-fuel-injection-inyeccion-de-combustible',
                ),
            198 =>
                array (
                    'en' => 'Automobiles - Glass',
                    'es' => 'Automóviles - Cristales',
                    'slugEn' => 'automobiles-glass',
                    'slugEs' => 'automoviles-cristales',
                ),
            199 =>
                array (
                    'en' => 'Automobiles - Ignition',
                    'es' => 'Automóviles - Ignición',
                    'slugEn' => 'automobiles-ignition',
                    'slugEs' => 'automoviles-ignicion',
                ),
            200 =>
                array (
                    'en' => 'Automobiles - Inspection Centers',
                    'es' => 'Automóviles - Inspección-Centros',
                    'slugEn' => 'automobiles-inspection-centers',
                    'slugEs' => 'automoviles-inspeccion-centros',
                ),
            201 =>
                array (
                    'en' => 'Automobiles - Japanese Replacement Parts',
                    'es' => 'Automóviles - Piezas de Reemplazo Japonesas',
                    'slugEn' => 'automobiles-japanese-replacement-parts',
                    'slugEs' => 'automoviles-piezas-de-reemplazo-japonesas',
                ),
            202 =>
                array (
                    'en' => 'Automobiles - Keys',
                    'es' => 'Automóviles - Llaves',
                    'slugEn' => 'automobiles-keys',
                    'slugEs' => 'automoviles-llaves',
                ),
            203 =>
                array (
                    'en' => 'Automobiles - Light Mechanics',
                    'es' => 'Automóviles - Mecánica Liviana',
                    'slugEn' => 'automobiles-light-mechanics',
                    'slugEs' => 'automoviles-mecanica-liviana',
                ),
            204 =>
                array (
                    'en' => 'Automobiles - Locks',
                    'es' => 'Automóviles - Cerraduras',
                    'slugEn' => 'automobiles-locks',
                    'slugEs' => 'automoviles-cerraduras',
                ),
            205 =>
                array (
                    'en' => 'Automobiles - Lubrication',
                    'es' => 'Automóviles - Lubricación',
                    'slugEn' => 'automobiles-lubrication',
                    'slugEs' => 'automoviles-lubricacion',
                ),
            206 =>
                array (
                    'en' => 'Automobiles - Lubrication - Equipment & Supplies',
                    'es' => 'Automóviles - Lubricación - Efectos Y Equipo',
                    'slugEn' => 'automobiles-lubrication-equipment-supplies',
                    'slugEs' => 'automoviles-lubricacion-efectos-y-equipo',
                ),
            207 =>
                array (
                    'en' => 'Automobiles - Mechanical',
                    'es' => 'Automóviles - Mecánica',
                    'slugEn' => 'automobiles-mechanical',
                    'slugEs' => 'automoviles-mecanica',
                ),
            208 =>
                array (
                    'en' => 'Automobiles - Mufflers',
                    'es' => 'Automóviles - Mufflers',
                    'slugEn' => 'automobiles-mufflers',
                    'slugEs' => 'automoviles-mufflers',
                ),
            209 =>
                array (
                    'en' => 'Automobiles - Mufflers - Equipment & Supplies',
                    'es' => 'Automóviles - Mufflers - Efectos Y Equipo',
                    'slugEn' => 'automobiles-mufflers-equipment-supplies',
                    'slugEs' => 'automoviles-mufflers-efectos-y-equipo',
                ),
            210 =>
                array (
                    'en' => 'Automobiles - Parts & Accessories',
                    'es' => 'Automoviles - Accesorios Y Piezas',
                    'slugEn' => 'automobiles-parts-accessories',
                    'slugEs' => 'automoviles-accesorios-y-piezas',
                ),
            211 =>
                array (
                    'en' => 'Automobiles - Parts & Accessories - Used',
                    'es' => 'Automóviles - Accesorios Y Piezas-Usadas',
                    'slugEn' => 'automobiles-parts-accessories-used',
                    'slugEs' => 'automoviles-accesorios-y-piezas-usadas',
                ),
            212 =>
                array (
                    'en' => 'Automobiles - Parts & Accessories - Wholesale',
                    'es' => 'Automóviles - Accesorios Y Piezas - Al Por Mayor',
                    'slugEn' => 'automobiles-parts-accessories-wholesale',
                    'slugEs' => 'automoviles-accesorios-y-piezas-al-por-mayor',
                ),
            213 =>
                array (
                    'en' => 'Automobiles - Polishing and Shine',
                    'es' => 'Automóviles - Pulido y Brillo',
                    'slugEn' => 'automobiles-polishing-and-shine',
                    'slugEs' => 'automoviles-pulido-y-brillo',
                ),
            214 =>
                array (
                    'en' => 'Automobiles - Power Steering',
                    'es' => 'Automóviles - \'Power Steering\'',
                    'slugEn' => 'automobiles-power-steering',
                    'slugEs' => 'automoviles-power-steering',
                ),
            215 =>
                array (
                    'en' => 'Automobiles - Race - Equipment & Supplies',
                    'es' => 'Automóviles - Carrera-Efectos y Equipos',
                    'slugEn' => 'automobiles-race-equipment-supplies',
                    'slugEs' => 'automoviles-carrera-efectos-y-equipos',
                ),
            216 =>
                array (
                    'en' => 'Automobiles - Racing-Equipment & Supplies',
                    'es' => 'Automóviles - Carreras-Efectos Y Equipo',
                    'slugEn' => 'automobiles-racing-equipment-supplies',
                    'slugEs' => 'automoviles-carreras-efectos-y-equipo',
                ),
            217 =>
                array (
                    'en' => 'Automobiles - Rack & Pinion',
                    'es' => 'Automóviles - Rack & Pinion',
                    'slugEn' => 'automobiles-rack-pinion',
                    'slugEs' => 'automoviles-rack-pinion',
                ),
            218 =>
                array (
                    'en' => 'Automobiles - Radiators',
                    'es' => 'Automoviles - Radiadores - Reparacion',
                    'slugEn' => 'automobiles-radiators',
                    'slugEs' => 'automoviles-radiadores-reparacion',
                ),
            219 =>
                array (
                    'en' => 'Automobiles - Radiators - Manufacture',
                    'es' => 'Automoviles - Radiadores-Fabricas',
                    'slugEn' => 'automobiles-radiators-manufacture',
                    'slugEs' => 'automoviles-radiadores-fabricas',
                ),
            220 =>
                array (
                    'en' => 'Automobiles - Radios',
                    'es' => 'Automóviles - Radios',
                    'slugEn' => 'automobiles-radios',
                    'slugEs' => 'automoviles-radios',
                ),
            221 =>
                array (
                    'en' => 'Automobiles - Recontruction',
                    'es' => 'Automóviles - Reconstrucción',
                    'slugEn' => 'automobiles-recontruction',
                    'slugEs' => 'automoviles-reconstruccion',
                ),
            222 =>
                array (
                    'en' => 'Automobiles - Rental',
                    'es' => 'Automóviles - Alquiler',
                    'slugEn' => 'automobiles-rental',
                    'slugEs' => 'automoviles-alquiler',
                ),
            223 =>
                array (
                    'en' => 'Automobiles - Repair',
                    'es' => 'Automóviles - Reparaciones',
                    'slugEn' => 'automobiles-repair',
                    'slugEs' => 'automoviles-reparaciones',
                ),
            224 =>
                array (
                    'en' => 'Automobiles - Repair - European Cars',
                    'es' => 'Automóviles - Reparaciones - Autos Europeos',
                    'slugEn' => 'automobiles-repair-european-cars',
                    'slugEs' => 'automoviles-reparaciones-autos-europeos',
                ),
            225 =>
                array (
                    'en' => 'Automobiles - Repair Shops',
                    'es' => 'Automóviles - Garajes',
                    'slugEn' => 'automobiles-repair-shops',
                    'slugEs' => 'automoviles-garajes',
                ),
            226 =>
                array (
                    'en' => 'Automobiles - Rims-Repair',
                    'es' => 'Automóviles - Aros-Reparacion',
                    'slugEn' => 'automobiles-rims-repair',
                    'slugEs' => 'automoviles-aros-reparacion',
                ),
            227 =>
                array (
                    'en' => 'Automobiles - Roadside Assistance',
                    'es' => 'Automóviles - Asistencia en la Carretera',
                    'slugEn' => 'automobiles-roadside-assistance',
                    'slugEs' => 'automoviles-asistencia-en-la-carretera',
                ),
            228 =>
                array (
                    'en' => 'Automobiles - Seat Covers-Tops-Upholstery',
                    'es' => 'Automóviles - Forros-Capotas-Tapicería',
                    'slugEn' => 'automobiles-seat-covers-tops-upholstery',
                    'slugEs' => 'automoviles-forros-capotas-tapiceria',
                ),
            229 =>
                array (
                    'en' => 'Automobiles - Service',
                    'es' => 'Automóviles - Servicio',
                    'slugEn' => 'automobiles-service',
                    'slugEs' => 'automoviles-servicio',
                ),
            230 =>
                array (
                    'en' => 'Automobiles - Services',
                    'es' => 'Automóviles - Servicios',
                    'slugEn' => 'automobiles-services',
                    'slugEs' => 'automoviles-servicios',
                ),
            231 =>
                array (
                    'en' => 'Automobiles - Shock Absorbers',
                    'es' => 'Automóviles - Amortiguadores',
                    'slugEn' => 'automobiles-shock-absorbers',
                    'slugEs' => 'automoviles-amortiguadores',
                ),
            232 =>
                array (
                    'en' => 'Automobiles - Speedway',
                    'es' => 'Automóviles - Carreras-Pistas',
                    'slugEn' => 'automobiles-speedway',
                    'slugEs' => 'automoviles-carreras-pistas',
                ),
            233 =>
                array (
                    'en' => 'Automobiles - Sunroof',
                    'es' => 'Automóviles - Capotas Deslizables-Removibles',
                    'slugEn' => 'automobiles-sunroof',
                    'slugEs' => 'automoviles-capotas-deslizables-removibles',
                ),
            234 =>
                array (
                    'en' => 'Automobiles - Suspension - Repair',
                    'es' => 'Automóviles - Tren Delantero -  Reparacion',
                    'slugEn' => 'automobiles-suspension-repair',
                    'slugEs' => 'automoviles-tren-delantero-reparacion',
                ),
            235 =>
                array (
                    'en' => 'Automobiles - Suspensions',
                    'es' => 'Automóviles - Suspensiones',
                    'slugEn' => 'automobiles-suspensions',
                    'slugEs' => 'automoviles-suspensiones',
                ),
            236 =>
                array (
                    'en' => 'Automobiles - Tint',
                    'es' => 'Automóviles - Tintes',
                    'slugEn' => 'automobiles-tint',
                    'slugEs' => 'automoviles-tintes',
                ),
            237 =>
                array (
                    'en' => 'Automobiles - Tires',
                    'es' => 'Automóviles - Gomas',
                    'slugEn' => 'automobiles-tires',
                    'slugEs' => 'automoviles-gomas',
                ),
            238 =>
                array (
                    'en' => 'Automobiles - Tires - Repair',
                    'es' => 'Automóviles - Gomas-Reparación',
                    'slugEn' => 'automobiles-tires-repair',
                    'slugEs' => 'automoviles-gomas-reparacion',
                ),
            239 =>
                array (
                    'en' => 'Automobiles - Tires-Repair-Equipment & Supplies',
                    'es' => 'Automóviles - Gomas-Reparación-Materiales Y Equipo',
                    'slugEn' => 'automobiles-tires-repair-equipment-supplies',
                    'slugEs' => 'automoviles-gomas-reparacion-materiales-y-equipo',
                ),
            240 =>
                array (
                    'en' => 'Automobiles - Tops',
                    'es' => 'Automóviles - Capotas',
                    'slugEn' => 'automobiles-tops',
                    'slugEs' => 'automoviles-capotas',
                ),
            241 =>
                array (
                    'en' => 'Automobiles - Transmissions',
                    'es' => 'Automóviles - Transmisiones',
                    'slugEn' => 'automobiles-transmissions',
                    'slugEs' => 'automoviles-transmisiones',
                ),
            242 =>
                array (
                    'en' => 'Automobiles - Transmissions - Parts & Accessories',
                    'es' => 'Automóviles - Transmisiones-Accesorios Y Piezas',
                    'slugEn' => 'automobiles-transmissions-parts-accessories',
                    'slugEs' => 'automoviles-transmisiones-accesorios-y-piezas',
                ),
            243 =>
                array (
                    'en' => 'Automobiles - Transmissions-Repair',
                    'es' => 'Automóviles - Transmisiones-Reparación',
                    'slugEn' => 'automobiles-transmissions-repair',
                    'slugEs' => 'automoviles-transmisiones-reparacion',
                ),
            244 =>
                array (
                    'en' => 'Automobiles - Treatments - Maintenance',
                    'es' => 'Automóviles - Tratamientos Anticorrosivo',
                    'slugEn' => 'automobiles-treatments-maintenance',
                    'slugEs' => 'automoviles-tratamientos-anticorrosivo',
                ),
            245 =>
                array (
                    'en' => 'Automobiles - Turbo and Header',
                    'es' => 'Automóviles - Turbo y Header',
                    'slugEn' => 'automobiles-turbo-and-header',
                    'slugEs' => 'automoviles-turbo-y-header',
                ),
            246 =>
                array (
                    'en' => 'Automobiles - Used',
                    'es' => 'Automóviles - Usados',
                    'slugEn' => 'automobiles-used',
                    'slugEs' => 'automoviles-usados',
                ),
            247 =>
                array (
                    'en' => 'Automobiles - Washing',
                    'es' => 'Automóviles - Lavado',
                    'slugEn' => 'automobiles-washing',
                    'slugEs' => 'automoviles-lavado',
                ),
            248 =>
                array (
                    'en' => 'Automobiles - Washing-Equipment & Supplies',
                    'es' => 'Automóviles - Lavado-Efectos Y Equipo',
                    'slugEn' => 'automobiles-washing-equipment-supplies',
                    'slugEs' => 'automoviles-lavado-efectos-y-equipo',
                ),
            249 =>
                array (
                    'en' => 'Automobiles - Wheel Aligning & Balancing Service - Equipment & Supplies',
                    'es' => 'Automóviles - Alineamiento y Balance - Efectos y Equipo',
                    'slugEn' => 'automobiles-wheel-aligning-balancing-service-equipment-supplies',
                    'slugEs' => 'automoviles-alineamiento-y-balance-efectos-y-equipo',
                ),
            250 =>
                array (
                    'en' => 'Automobiles Accesories American Cars',
                    'es' => 'Piezas de Auto Americano',
                    'slugEn' => 'automobiles-accesories-american-cars',
                    'slugEs' => 'piezas-de-auto-americano',
                ),
            251 =>
                array (
                    'en' => 'Automobiles Body Parts',
                    'es' => 'Automóviles Body Parts',
                    'slugEn' => 'automobiles-body-parts',
                    'slugEs' => 'automoviles-body-parts',
                ),
            252 =>
                array (
                    'en' => 'Automobiles Dealers',
                    'es' => 'Automóviles Dealers',
                    'slugEn' => 'automobiles-dealers',
                    'slugEs' => 'automoviles-dealers',
                ),
            253 =>
                array (
                    'en' => 'Automobiles Dealers & Distributors',
                    'es' => 'Automoviles Dealers Y Distribuidores',
                    'slugEn' => 'automobiles-dealers-distributors',
                    'slugEs' => 'automoviles-dealers-y-distribuidores',
                ),
            254 =>
                array (
                    'en' => 'Automobiles Interior Shampoo',
                    'es' => 'Automóviles Champú de Interiores',
                    'slugEn' => 'automobiles-interior-shampoo',
                    'slugEs' => 'automoviles-champu-de-interiores',
                ),
            255 =>
                array (
                    'en' => 'Automotive Center',
                    'es' => 'Centro Automotriz',
                    'slugEn' => 'automotive-center',
                    'slugEs' => 'centro-automotriz',
                ),
            256 =>
                array (
                    'en' => 'Aviation - Classes',
                    'es' => 'Aviacion - Clases',
                    'slugEn' => 'aviation-classes',
                    'slugEs' => 'aviacion-clases',
                ),
            257 =>
                array (
                    'en' => 'Awnings',
                    'es' => 'Toldos Retractables',
                    'slugEn' => 'awnings',
                    'slugEs' => 'toldos-retractables',
                ),
            258 =>
                array (
                    'en' => 'Awnings - Aluminum',
                    'es' => 'Cortinas-Aluminio',
                    'slugEn' => 'awnings-aluminum',
                    'slugEs' => 'cortinas-aluminio',
                ),
            259 =>
                array (
                    'en' => 'Baby - Stores',
                    'es' => 'Bebés-Accesorios',
                    'slugEn' => 'baby-stores',
                    'slugEs' => 'bebes-accesorios',
                ),
            260 =>
                array (
                    'en' => 'Baby Clothes',
                    'es' => 'Ropa de Bebé',
                    'slugEn' => 'baby-clothes',
                    'slugEs' => 'ropa-de-bebe',
                ),
            261 =>
                array (
                    'en' => 'Babysitting Services',
                    'es' => 'Niñeras-Servicios',
                    'slugEn' => 'babysitting-services',
                    'slugEs' => 'nineras-servicios',
                ),
            262 =>
                array (
                    'en' => 'Bachata Lessons',
                    'es' => 'Clases de Baile Bachata',
                    'slugEn' => 'bachata-lessons',
                    'slugEs' => 'clases-de-baile-bachata',
                ),
            263 =>
                array (
                    'en' => 'Bags',
                    'es' => 'Bolsas',
                    'slugEn' => 'bags',
                    'slugEs' => 'bolsas',
                ),
            264 =>
                array (
                    'en' => 'Bags - Plastic',
                    'es' => 'Bolsas-Plásticas',
                    'slugEn' => 'bags-plastic',
                    'slugEs' => 'bolsas-plasticas',
                ),
            265 =>
                array (
                    'en' => 'Bags - Plastic - Paper',
                    'es' => 'Bolsas-Papel',
                    'slugEn' => 'bags-plastic-paper',
                    'slugEs' => 'bolsas-papel',
                ),
            266 =>
                array (
                    'en' => 'Bail',
                    'es' => 'Fianzas',
                    'slugEn' => 'bail',
                    'slugEs' => 'fianzas',
                ),
            267 =>
                array (
                    'en' => 'Bakeries',
                    'es' => 'Reposterías',
                    'slugEn' => 'bakeries',
                    'slugEs' => 'reposterias',
                ),
            268 =>
                array (
                    'en' => 'Bakeries - Equipment & Supplies',
                    'es' => 'Reposterías-Efectos Y Equipo',
                    'slugEn' => 'bakeries-equipment-supplies',
                    'slugEs' => 'reposterias-efectos-y-equipo',
                ),
            269 =>
                array (
                    'en' => 'Bakeries - Supplies',
                    'es' => 'Panaderías-Suplidores',
                    'slugEn' => 'bakeries-supplies',
                    'slugEs' => 'panaderias-suplidores',
                ),
            270 =>
                array (
                    'en' => 'Bakeries - Wholesale',
                    'es' => 'Reposterías - Al Por Mayor',
                    'slugEn' => 'bakeries-wholesale',
                    'slugEs' => 'reposterias-al-por-mayor',
                ),
            271 =>
                array (
                    'en' => 'Bakers - Machinery & Equipment',
                    'es' => 'Panaderías-Maquinaria Y Equipo',
                    'slugEn' => 'bakers-machinery-equipment',
                    'slugEs' => 'panaderias-maquinaria-y-equipo',
                ),
            272 =>
                array (
                    'en' => 'Bakery',
                    'es' => 'Panaderías',
                    'slugEn' => 'bakery',
                    'slugEs' => 'panaderias',
                ),
            273 =>
                array (
                    'en' => 'Bakery - Wholesale',
                    'es' => 'Panaderías - Al Por Mayor',
                    'slugEn' => 'bakery-wholesale',
                    'slugEs' => 'panaderias-al-por-mayor',
                ),
            274 =>
                array (
                    'en' => 'Ballet - Accessories',
                    'es' => 'Ballet-Accesorios',
                    'slugEn' => 'ballet-accessories',
                    'slugEs' => 'ballet-accesorios',
                ),
            275 =>
                array (
                    'en' => 'Ballet Classes',
                    'es' => 'Clases de Ballet',
                    'slugEn' => 'ballet-classes',
                    'slugEs' => 'clases-de-ballet',
                ),
            276 =>
                array (
                    'en' => 'Ballet Clothing',
                    'es' => 'Ropa de Ballet',
                    'slugEn' => 'ballet-clothing',
                    'slugEs' => 'ropa-de-ballet',
                ),
            277 =>
                array (
                    'en' => 'Ballet Shop',
                    'es' => 'Tienda de Ballet',
                    'slugEn' => 'ballet-shop',
                    'slugEs' => 'tienda-de-ballet',
                ),
            278 =>
                array (
                    'en' => 'Balloons',
                    'es' => 'Globos',
                    'slugEn' => 'balloons',
                    'slugEs' => 'globos',
                ),
            279 =>
                array (
                    'en' => 'Balloons - Distributors',
                    'es' => 'Globos Distribuidores',
                    'slugEn' => 'balloons-distributors',
                    'slugEs' => 'globos-distribuidores',
                ),
            280 =>
                array (
                    'en' => 'Ballroom Dancing',
                    'es' => 'Bailes de Salón',
                    'slugEn' => 'ballroom-dancing',
                    'slugEs' => 'bailes-de-salon',
                ),
            281 =>
                array (
                    'en' => 'Banking-accesories-equipment',
                    'es' => 'Bancos-efectos-equipo',
                    'slugEn' => 'banking-accesories-equipment',
                    'slugEs' => 'bancos-efectos-equipo',
                ),
            282 =>
                array (
                    'en' => 'Bankruptcy Personal and Business',
                    'es' => 'Quiebras Personales - Comercial - Negocios',
                    'slugEn' => 'bankruptcy-personal-and-business',
                    'slugEs' => 'quiebras-personales-comercial-negocios',
                ),
            283 =>
                array (
                    'en' => 'Banks',
                    'es' => 'Bancos',
                    'slugEn' => 'banks',
                    'slugEs' => 'bancos',
                ),
            284 =>
                array (
                    'en' => 'Banners',
                    'es' => 'Cruzacalles',
                    'slugEn' => 'banners',
                    'slugEs' => 'cruzacalles',
                ),
            285 =>
                array (
                    'en' => 'Banquet - Services',
                    'es' => 'Banquetes-Servicios',
                    'slugEn' => 'banquet-services',
                    'slugEs' => 'banquetes-servicios',
                ),
            286 =>
                array (
                    'en' => 'Barbecue - Equipment & Supplies',
                    'es' => 'Barbacoa-Efectos Y Equipo',
                    'slugEn' => 'barbecue-equipment-supplies',
                    'slugEs' => 'barbacoa-efectos-y-equipo',
                ),
            287 =>
                array (
                    'en' => 'Barber - Men and Children',
                    'es' => 'Barbería - Caballeros y Niños',
                    'slugEn' => 'barber-men-and-children',
                    'slugEs' => 'barberia-caballeros-y-ninos',
                ),
            288 =>
                array (
                    'en' => 'Barbers',
                    'es' => 'Barberías',
                    'slugEn' => 'barbers',
                    'slugEs' => 'barberias',
                ),
            289 =>
                array (
                    'en' => 'Barbers - Equipment & Supplies',
                    'es' => 'Barberías-Efectos Y Equipo',
                    'slugEn' => 'barbers-equipment-supplies',
                    'slugEs' => 'barberias-efectos-y-equipo',
                ),
            290 =>
                array (
                    'en' => 'Bariatric Surgery',
                    'es' => 'Cirugía Bariátrica',
                    'slugEn' => 'bariatric-surgery',
                    'slugEs' => 'cirugia-bariatrica',
                ),
            291 =>
                array (
                    'en' => 'Barista Lessons',
                    'es' => 'Clases Baristas',
                    'slugEn' => 'barista-lessons',
                    'slugEs' => 'clases-baristas',
                ),
            292 =>
                array (
                    'en' => 'Bars',
                    'es' => 'Bares',
                    'slugEn' => 'bars',
                    'slugEs' => 'bares',
                ),
            293 =>
                array (
                    'en' => 'Baseball',
                    'es' => 'Béisbol',
                    'slugEn' => 'baseball',
                    'slugEs' => 'beisbol',
                ),
            294 =>
                array (
                    'en' => 'Baskets',
                    'es' => 'Canastas',
                    'slugEn' => 'baskets',
                    'slugEs' => 'canastas',
                ),
            295 =>
                array (
                    'en' => 'Bathroom Cabinets',
                    'es' => 'Gabinetes Baños',
                    'slugEn' => 'bathroom-cabinets',
                    'slugEs' => 'gabinetes-banos',
                ),
            296 =>
                array (
                    'en' => 'Bathrooms',
                    'es' => 'Baños-Accesorios',
                    'slugEn' => 'bathrooms',
                    'slugEs' => 'banos-accesorios',
                ),
            297 =>
                array (
                    'en' => 'Batteries',
                    'es' => 'Baterias',
                    'slugEn' => 'batteries',
                    'slugEs' => 'baterias',
                ),
            298 =>
                array (
                    'en' => 'Bazaars',
                    'es' => 'Bazares',
                    'slugEn' => 'bazaars',
                    'slugEs' => 'bazares',
                ),
            299 =>
                array (
                    'en' => 'BBQ',
                    'es' => 'BBQ',
                    'slugEn' => 'bbq',
                    'slugEs' => 'bbq',
                ),
            300 =>
                array (
                    'en' => 'Beach Clubs',
                    'es' => 'Clubes-Playa',
                    'slugEn' => 'beach-clubs',
                    'slugEs' => 'clubes-playa',
                ),
            301 =>
                array (
                    'en' => 'Beads Stores',
                    'es' => 'Beads Tiendas',
                    'slugEn' => 'beads-stores',
                    'slugEs' => 'beads-tiendas',
                ),
            302 =>
                array (
                    'en' => 'Bearings',
                    'es' => 'Cajas De Bolas-Rodillos',
                    'slugEn' => 'bearings',
                    'slugEs' => 'cajas-de-bolas-rodillos',
                ),
            303 =>
                array (
                    'en' => 'Beauty Products',
                    'es' => 'Productos de Belleza',
                    'slugEn' => 'beauty-products',
                    'slugEs' => 'productos-de-belleza',
                ),
            304 =>
                array (
                    'en' => 'Beauty',
                    'es' => 'Beauty',
                    'slugEn' => 'beauty',
                    'slugEs' => 'beauty',
                ),
            305 =>
                array (
                    'en' => 'Beauty Salons',
                    'es' => 'Salones De Belleza',
                    'slugEn' => 'beauty-salons',
                    'slugEs' => 'salones-de-belleza',
                ),
            306 =>
                array (
                    'en' => 'Beauty Salons - Equipment & Supplies',
                    'es' => 'Salones De Belleza-Efectos Y Equipo',
                    'slugEn' => 'beauty-salons-equipment-supplies',
                    'slugEs' => 'salones-de-belleza-efectos-y-equipo',
                ),
            307 =>
                array (
                    'en' => 'Beauty Salons - Manicure - Pedicure',
                    'es' => 'Salones de Belleza - Manicura - Pedicura',
                    'slugEn' => 'beauty-salons-manicure-pedicure',
                    'slugEs' => 'salones-de-belleza-manicura-pedicura',
                ),
            308 =>
                array (
                    'en' => 'Bed & Breakfast',
                    'es' => 'Hospederías',
                    'slugEn' => 'bed-breakfast',
                    'slugEs' => 'hospederias',
                ),
            309 =>
                array (
                    'en' => 'Bedroom Sets',
                    'es' => 'Juegos de Dormitorio',
                    'slugEn' => 'bedroom-sets',
                    'slugEs' => 'juegos-de-dormitorio',
                ),
            310 =>
                array (
                    'en' => 'Beds',
                    'es' => 'Camas',
                    'slugEn' => 'beds',
                    'slugEs' => 'camas',
                ),
            311 =>
                array (
                    'en' => 'Beepers',
                    'es' => 'Beepers',
                    'slugEn' => 'beepers',
                    'slugEs' => 'beepers',
                ),
            312 =>
                array (
                    'en' => 'Beer - Breweries',
                    'es' => 'Cervecerías',
                    'slugEn' => 'beer-breweries',
                    'slugEs' => 'cervecerias',
                ),
            313 =>
                array (
                    'en' => 'Bellas Artes',
                    'es' => 'Bellas Artes',
                    'slugEn' => 'bellas-artes',
                    'slugEs' => 'bellas-artes',
                ),
            314 =>
                array (
                    'en' => 'Belts-Industrial',
                    'es' => 'Correas-Industriales',
                    'slugEn' => 'belts-industrial',
                    'slugEs' => 'correas-industriales',
                ),
            315 =>
                array (
                    'en' => 'Bible Institute',
                    'es' => 'Institutos Bíblicos',
                    'slugEn' => 'bible-institute',
                    'slugEs' => 'institutos-biblicos',
                ),
            316 =>
                array (
                    'en' => 'Bicycle Parts and Service',
                    'es' => 'Bicicletas Piezas y Servicio',
                    'slugEn' => 'bicycle-parts-and-service',
                    'slugEs' => 'bicicletas-piezas-y-servicio',
                ),
            317 =>
                array (
                    'en' => 'Bicycles',
                    'es' => 'Bicicletas',
                    'slugEn' => 'bicycles',
                    'slugEs' => 'bicicletas',
                ),
            318 =>
                array (
                    'en' => 'Bicycles- Accessories and Equipment',
                    'es' => 'Bicicletas - Accesorios y Equipo',
                    'slugEn' => 'bicycles-accessories-and-equipment',
                    'slugEs' => 'bicicletas-accesorios-y-equipo',
                ),
            319 =>
                array (
                    'en' => 'Bicycles-Rent',
                    'es' => 'Bicicletas-Alquiler',
                    'slugEn' => 'bicycles-rent',
                    'slugEs' => 'bicicletas-alquiler',
                ),
            320 =>
                array (
                    'en' => 'Bike Repair',
                    'es' => 'Bicicletas Reparación',
                    'slugEn' => 'bike-repair',
                    'slugEs' => 'bicicletas-reparacion',
                ),
            321 =>
                array (
                    'en' => 'Bike Tours',
                    'es' => 'Bicicletas Tours',
                    'slugEn' => 'bike-tours',
                    'slugEs' => 'bicicletas-tours',
                ),
            322 =>
                array (
                    'en' => 'Bilingual School',
                    'es' => 'Colegios Bilingües',
                    'slugEn' => 'bilingual-school',
                    'slugEs' => 'colegios-bilingues',
                ),
            323 =>
                array (
                    'en' => 'Billard',
                    'es' => 'Salon de Billar',
                    'slugEn' => 'billard',
                    'slugEs' => 'salon-de-billar',
                ),
            324 =>
                array (
                    'en' => 'Bioenergetic Science',
                    'es' => 'Bioenergética',
                    'slugEn' => 'bioenergetic-science',
                    'slugEs' => 'bioenergetica',
                ),
            325 =>
                array (
                    'en' => 'Biomedical Engineering',
                    'es' => 'Ingeniería Biomédica',
                    'slugEn' => 'biomedical-engineering',
                    'slugEs' => 'ingenieria-biomedica',
                ),
            326 =>
                array (
                    'en' => 'Biomedical Waste',
                    'es' => 'Desperdicios Biomédicos',
                    'slugEn' => 'biomedical-waste',
                    'slugEs' => 'desperdicios-biomedicos',
                ),
            327 =>
                array (
                    'en' => 'Bird - Control',
                    'es' => 'Aves-Control',
                    'slugEn' => 'bird-control',
                    'slugEs' => 'aves-control',
                ),
            328 =>
                array (
                    'en' => 'Birthday Balloons',
                    'es' => 'Globos de Cumpleaños',
                    'slugEn' => 'birthday-balloons',
                    'slugEs' => 'globos-de-cumpleanos',
                ),
            329 =>
                array (
                    'en' => 'Birthdays',
                    'es' => 'Cumpleaños',
                    'slugEn' => 'birthdays',
                    'slugEs' => 'cumpleanos',
                ),
            330 =>
                array (
                    'en' => 'Bistro',
                    'es' => 'Bistro',
                    'slugEn' => 'bistro',
                    'slugEs' => 'bistro',
                ),
            331 =>
                array (
                    'en' => 'Blacksmith',
                    'es' => 'Herrerías',
                    'slugEn' => 'blacksmith',
                    'slugEs' => 'herrerias',
                ),
            332 =>
                array (
                    'en' => 'Blasting',
                    'es' => 'Demoliciones',
                    'slugEn' => 'blasting',
                    'slugEs' => 'demoliciones',
                ),
            333 =>
                array (
                    'en' => 'Blocks',
                    'es' => 'Bloques',
                    'slugEn' => 'blocks',
                    'slugEs' => 'bloques',
                ),
            334 =>
                array (
                    'en' => 'Blood-Bank',
                    'es' => 'Sangre-Bancos',
                    'slugEn' => 'blood-bank',
                    'slugEs' => 'sangre-bancos',
                ),
            335 =>
                array (
                    'en' => 'Blue Print',
                    'es' => 'Copia Planos',
                    'slugEn' => 'blue-print',
                    'slugEs' => 'copia-planos',
                ),
            336 =>
                array (
                    'en' => 'Boats',
                    'es' => 'Botes',
                    'slugEn' => 'boats',
                    'slugEs' => 'botes',
                ),
            337 =>
                array (
                    'en' => 'Boats - Air Conditioning',
                    'es' => 'Botes - Aires Acondicionados',
                    'slugEn' => 'boats-air-conditioning',
                    'slugEs' => 'botes-aires-acondicionados',
                ),
            338 =>
                array (
                    'en' => 'Boats - Manufacture',
                    'es' => 'Botes-Manufactura',
                    'slugEn' => 'boats-manufacture',
                    'slugEs' => 'botes-manufactura',
                ),
            339 =>
                array (
                    'en' => 'Boats - Repair',
                    'es' => 'Botes-Reparación',
                    'slugEn' => 'boats-repair',
                    'slugEs' => 'botes-reparacion',
                ),
            340 =>
                array (
                    'en' => 'Boats - Transportation',
                    'es' => 'Botes-Transportación',
                    'slugEn' => 'boats-transportation',
                    'slugEs' => 'botes-transportacion',
                ),
            341 =>
                array (
                    'en' => 'Boats- Parts And Equipment',
                    'es' => 'Botes-Efectos Y Equipo',
                    'slugEn' => 'boats-parts-and-equipment',
                    'slugEs' => 'botes-efectos-y-equipo',
                ),
            342 =>
                array (
                    'en' => 'Boatswain\'s Chair',
                    'es' => 'Guiandolas',
                    'slugEn' => 'boatswain-s-chair',
                    'slugEs' => 'guiandolas',
                ),
            343 =>
                array (
                    'en' => 'Body Art',
                    'es' => 'Arte Corporal',
                    'slugEn' => 'body-art',
                    'slugEs' => 'arte-corporal',
                ),
            344 =>
                array (
                    'en' => 'Body Piercing',
                    'es' => 'Body Piercing',
                    'slugEn' => 'body-piercing',
                    'slugEs' => 'body-piercing',
                ),
            345 =>
                array (
                    'en' => 'Body Therapies',
                    'es' => 'Terapias Corporales',
                    'slugEn' => 'body-therapies',
                    'slugEs' => 'terapias-corporales',
                ),
            346 =>
                array (
                    'en' => 'Body Treatment',
                    'es' => 'Tratamiento Corporal',
                    'slugEn' => 'body-treatment',
                    'slugEs' => 'tratamiento-corporal',
                ),
            347 =>
                array (
                    'en' => 'Body Waxing',
                    'es' => 'Depilación Cera',
                    'slugEn' => 'body-waxing',
                    'slugEs' => 'depilacion-cera',
                ),
            348 =>
                array (
                    'en' => 'Boilers',
                    'es' => 'Calderas',
                    'slugEn' => 'boilers',
                    'slugEs' => 'calderas',
                ),
            349 =>
                array (
                    'en' => 'Bone Densitometry',
                    'es' => 'Densitometria Osea',
                    'slugEn' => 'bone-densitometry',
                    'slugEs' => 'densitometria-osea',
                ),
            350 =>
                array (
                    'en' => 'Books - Law',
                    'es' => 'Libros-Leyes',
                    'slugEn' => 'books-law',
                    'slugEs' => 'libros-leyes',
                ),
            351 =>
                array (
                    'en' => 'Books - Wholesale',
                    'es' => 'Libros-Distribuidores',
                    'slugEn' => 'books-wholesale',
                    'slugEs' => 'libros-distribuidores',
                ),
            352 =>
                array (
                    'en' => 'Books-Binding',
                    'es' => 'Libros-Encuadernación',
                    'slugEn' => 'books-binding',
                    'slugEs' => 'libros-encuadernacion',
                ),
            353 =>
                array (
                    'en' => 'Bookstores',
                    'es' => 'Librerías',
                    'slugEn' => 'bookstores',
                    'slugEs' => 'librerias',
                ),
            354 =>
                array (
                    'en' => 'Boot Camp',
                    'es' => 'Boot Camp',
                    'slugEn' => 'boot-camp',
                    'slugEs' => 'boot-camp',
                ),
            355 =>
                array (
                    'en' => 'Botanical Garden',
                    'es' => 'Jardin Botanico',
                    'slugEn' => 'botanical-garden',
                    'slugEs' => 'jardin-botanico',
                ),
            356 =>
                array (
                    'en' => 'Botox',
                    'es' => 'Botox',
                    'slugEn' => 'botox',
                    'slugEs' => 'botox',
                ),
            357 =>
                array (
                    'en' => 'Bottlers',
                    'es' => 'Embotelladores',
                    'slugEn' => 'bottlers',
                    'slugEs' => 'embotelladores',
                ),
            358 =>
                array (
                    'en' => 'Bottles',
                    'es' => 'Botellas',
                    'slugEn' => 'bottles',
                    'slugEs' => 'botellas',
                ),
            359 =>
                array (
                    'en' => 'Bounce house',
                    'es' => 'Casa de brincos',
                    'slugEn' => 'bounce-house',
                    'slugEs' => 'casa-de-brincos',
                ),
            360 =>
                array (
                    'en' => 'Boutiques',
                    'es' => 'Boutiques',
                    'slugEn' => 'boutiques',
                    'slugEs' => 'boutiques',
                ),
            361 =>
                array (
                    'en' => 'Boutiques Accesories',
                    'es' => 'Boutiques Accesorios',
                    'slugEn' => 'boutiques-accesories',
                    'slugEs' => 'boutiques-accesorios',
                ),
            362 =>
                array (
                    'en' => 'Boxes',
                    'es' => 'Cajas-Cartón',
                    'slugEn' => 'boxes',
                    'slugEs' => 'cajas-carton',
                ),
            363 =>
                array (
                    'en' => 'Boxes-Folding or Collapsible',
                    'es' => 'Cajas-Plegadizas',
                    'slugEn' => 'boxes-folding-or-collapsible',
                    'slugEs' => 'cajas-plegadizas',
                ),
            364 =>
                array (
                    'en' => 'Braces',
                    'es' => 'Braces',
                    'slugEn' => 'braces',
                    'slugEs' => 'braces',
                ),
            365 =>
                array (
                    'en' => 'Brass & Bronze - Products',
                    'es' => 'Bronce-Artículos',
                    'slugEn' => 'brass-bronze-products',
                    'slugEs' => 'bronce-articulos',
                ),
            366 =>
                array (
                    'en' => 'Bread - Distributors',
                    'es' => 'Pan - Distribuidores',
                    'slugEn' => 'bread-distributors',
                    'slugEs' => 'pan-distribuidores',
                ),
            367 =>
                array (
                    'en' => 'Break Line',
                    'es' => 'Mangas de Frenos',
                    'slugEn' => 'break-line',
                    'slugEs' => 'mangas-de-frenos',
                ),
            368 =>
                array (
                    'en' => 'Breakfast',
                    'es' => 'Desayuno',
                    'slugEn' => 'breakfast',
                    'slugEs' => 'desayuno',
                ),
            369 =>
                array (
                    'en' => 'Breast Feeding - Guidance, Equipment & Accesories',
                    'es' => 'Lactancia-Orientación Equipo Y Accesorios',
                    'slugEn' => 'breast-feeding-guidance-equipment-accesories',
                    'slugEs' => 'lactancia-orientacion-equipo-y-accesorios',
                ),
            370 =>
                array (
                    'en' => 'Breast Feeding Groups',
                    'es' => 'Grupos de Lactancia',
                    'slugEn' => 'breast-feeding-groups',
                    'slugEs' => 'grupos-de-lactancia',
                ),
            371 =>
                array (
                    'en' => 'Breast Ultrasound',
                    'es' => 'Sonomamografia',
                    'slugEn' => 'breast-ultrasound',
                    'slugEs' => 'sonomamografia',
                ),
            372 =>
                array (
                    'en' => 'Bridal Shops',
                    'es' => 'Novias-Artículos',
                    'slugEn' => 'bridal-shops',
                    'slugEs' => 'novias-articulos',
                ),
            373 =>
                array (
                    'en' => 'Buffers & Polishers',
                    'es' => 'Pulidoras',
                    'slugEn' => 'buffers-polishers',
                    'slugEs' => 'pulidoras',
                ),
            374 =>
                array (
                    'en' => 'Building Forms',
                    'es' => 'Formaletas',
                    'slugEn' => 'building-forms',
                    'slugEs' => 'formaletas',
                ),
            375 =>
                array (
                    'en' => 'Building Rental - Industrial',
                    'es' => 'Edificios-Industriales-Alquiler',
                    'slugEn' => 'building-rental-industrial',
                    'slugEs' => 'edificios-industriales-alquiler',
                ),
            376 =>
                array (
                    'en' => 'Buildings - Maintenance',
                    'es' => 'Edificios-Mantenimiento',
                    'slugEn' => 'buildings-maintenance',
                    'slugEs' => 'edificios-mantenimiento',
                ),
            377 =>
                array (
                    'en' => 'Buildings - Prefabricated',
                    'es' => 'Edificios-Prefabricados',
                    'slugEn' => 'buildings-prefabricated',
                    'slugEs' => 'edificios-prefabricados',
                ),
            378 =>
                array (
                    'en' => 'Buses Lines',
                    'es' => 'Guaguas-Líneas',
                    'slugEn' => 'buses-lines',
                    'slugEs' => 'guaguas-lineas',
                ),
            379 =>
                array (
                    'en' => 'Business - Services and Consulting',
                    'es' => 'Negocios - Servicios y Asesoría',
                    'slugEn' => 'business-services-and-consulting',
                    'slugEs' => 'negocios-servicios-y-asesoria',
                ),
            380 =>
                array (
                    'en' => 'Business Brokers',
                    'es' => 'Corredores-Negocios',
                    'slugEn' => 'business-brokers',
                    'slugEs' => 'corredores-negocios',
                ),
            381 =>
                array (
                    'en' => 'Business Consulting',
                    'es' => 'Consultoría de Negocios',
                    'slugEn' => 'business-consulting',
                    'slugEs' => 'consultoria-de-negocios',
                ),
            382 =>
                array (
                    'en' => 'Business Forms - Manufacturing Company',
                    'es' => 'Formas-Contínuas',
                    'slugEn' => 'business-forms-manufacturing-company',
                    'slugEs' => 'formas-continuas',
                ),
            383 =>
                array (
                    'en' => 'Butcher',
                    'es' => 'Carnicerías',
                    'slugEn' => 'butcher',
                    'slugEs' => 'carnicerias',
                ),
            384 =>
                array (
                    'en' => 'Cabinetmakers',
                    'es' => 'Ebanisterías',
                    'slugEn' => 'cabinetmakers',
                    'slugEs' => 'ebanisterias',
                ),
            385 =>
                array (
                    'en' => 'Cabinets - Cupboards',
                    'es' => 'Gabinetes - Alacenas',
                    'slugEn' => 'cabinets-cupboards',
                    'slugEs' => 'gabinetes-alacenas',
                ),
            386 =>
                array (
                    'en' => 'Cabinets - Kitchen',
                    'es' => 'Gabinetes-Cocina',
                    'slugEn' => 'cabinets-kitchen',
                    'slugEs' => 'gabinetes-cocina',
                ),
            387 =>
                array (
                    'en' => 'Cabinets-Kitchen - Manufacture',
                    'es' => 'Gabinetes Cocina-Fabricantes',
                    'slugEn' => 'cabinets-kitchen-manufacture',
                    'slugEs' => 'gabinetes-cocina-fabricantes',
                ),
            388 =>
                array (
                    'en' => 'Cabins - Rental',
                    'es' => 'Cabañas-Alquiler',
                    'slugEn' => 'cabins-rental',
                    'slugEs' => 'cabanas-alquiler',
                ),
            389 =>
                array (
                    'en' => 'Cable - Companies',
                    'es' => 'Cable - Compañías',
                    'slugEn' => 'cable-companies',
                    'slugEs' => 'cable-companias',
                ),
            390 =>
                array (
                    'en' => 'Cable - Steel',
                    'es' => 'Cables - Acero',
                    'slugEn' => 'cable-steel',
                    'slugEs' => 'cables-acero',
                ),
            391 =>
                array (
                    'en' => 'Cafeterias',
                    'es' => 'Cafeterías',
                    'slugEn' => 'cafeterias',
                    'slugEs' => 'cafeterias',
                ),
            392 =>
                array (
                    'en' => 'Cafeterias - Management',
                    'es' => 'Cafeterías-Administración',
                    'slugEn' => 'cafeterias-management',
                    'slugEs' => 'cafeterias-administracion',
                ),
            393 =>
                array (
                    'en' => 'Cakes',
                    'es' => 'Bizcochos',
                    'slugEn' => 'cakes',
                    'slugEs' => 'bizcochos',
                ),
            394 =>
                array (
                    'en' => 'Calendars',
                    'es' => 'Calendarios-Almanaques',
                    'slugEn' => 'calendars',
                    'slugEs' => 'calendarios-almanaques',
                ),
            395 =>
                array (
                    'en' => 'Calibration - Certifiers',
                    'es' => 'Calibración-Certificadores',
                    'slugEn' => 'calibration-certifiers',
                    'slugEs' => 'calibracion-certificadores',
                ),
            396 =>
                array (
                    'en' => 'Camping-Equipment',
                    'es' => 'Acampar-Equipo',
                    'slugEn' => 'camping-equipment',
                    'slugEs' => 'acampar-equipo',
                ),
            397 =>
                array (
                    'en' => 'Camps - Summer',
                    'es' => 'Campamentos-Verano',
                    'slugEn' => 'camps-summer',
                    'slugEs' => 'campamentos-verano',
                ),
            398 =>
                array (
                    'en' => 'Candles',
                    'es' => 'Velas',
                    'slugEn' => 'candles',
                    'slugEs' => 'velas',
                ),
            399 =>
                array (
                    'en' => 'Candy',
                    'es' => 'Dulces-Distribuidores',
                    'slugEn' => 'candy',
                    'slugEs' => 'dulces-distribuidores',
                ),
            400 =>
                array (
                    'en' => 'Candy Confectionery',
                    'es' => 'Dulcerías',
                    'slugEn' => 'candy-confectionery',
                    'slugEs' => 'dulcerias',
                ),
            401 =>
                array (
                    'en' => 'Candy Manufacture',
                    'es' => 'Dulces-Fábricas',
                    'slugEn' => 'candy-manufacture',
                    'slugEs' => 'dulces-fabricas',
                ),
            402 =>
                array (
                    'en' => 'Canvas Curtains',
                    'es' => 'Cortinas de Lona',
                    'slugEn' => 'canvas-curtains',
                    'slugEs' => 'cortinas-de-lona',
                ),
            403 =>
                array (
                    'en' => 'Car Body Repair & Painting Distributors',
                    'es' => 'Hojalatería y Pintura Distribuidores',
                    'slugEn' => 'car-body-repair-painting-distributors',
                    'slugEs' => 'hojalateria-y-pintura-distribuidores',
                ),
            404 =>
                array (
                    'en' => 'Car Computer Repair',
                    'es' => 'Computadoras de Autos - Reparación',
                    'slugEn' => 'car-computer-repair',
                    'slugEs' => 'computadoras-de-autos-reparacion',
                ),
            405 =>
                array (
                    'en' => 'Car Dealers',
                    'es' => 'Car Dealers',
                    'slugEn' => 'car-dealers',
                    'slugEs' => 'car-dealers',
                ),
            406 =>
                array (
                    'en' => 'Car distributors',
                    'es' => 'Distribuidores de autos',
                    'slugEn' => 'car-distributors',
                    'slugEs' => 'distribuidores-de-autos',
                ),
            407 =>
                array (
                    'en' => 'Car Labeling',
                    'es' => 'Rotulación Vehicular',
                    'slugEn' => 'car-labeling',
                    'slugEs' => 'rotulacion-vehicular',
                ),
            408 =>
                array (
                    'en' => 'Car License',
                    'es' => 'Marbete',
                    'slugEn' => 'car-license',
                    'slugEs' => 'marbete',
                ),
            409 =>
                array (
                    'en' => 'Car Painting Equipment & Supplies',
                    'es' => 'Hojalatería y Pintura Efectos y Equipo',
                    'slugEn' => 'car-painting-equipment-supplies',
                    'slugEs' => 'hojalateria-y-pintura-efectos-y-equipo',
                ),
            410 =>
                array (
                    'en' => 'Car Policy',
                    'es' => 'Pólizas de Carro',
                    'slugEn' => 'car-policy',
                    'slugEs' => 'polizas-de-carro',
                ),
            411 =>
                array (
                    'en' => 'Car Wash',
                    'es' => 'Lavado de autos',
                    'slugEn' => 'car-wash',
                    'slugEs' => 'lavado-de-autos',
                ),
            412 =>
                array (
                    'en' => 'Cards',
                    'es' => 'Tarjetas',
                    'slugEn' => 'cards',
                    'slugEs' => 'tarjetas',
                ),
            413 =>
                array (
                    'en' => 'Cargo - Marine - Service',
                    'es' => 'Carga Marítima-Servicio',
                    'slugEn' => 'cargo-marine-service',
                    'slugEs' => 'carga-maritima-servicio',
                ),
            414 =>
                array (
                    'en' => 'Carpets',
                    'es' => 'Alfombras',
                    'slugEn' => 'carpets',
                    'slugEs' => 'alfombras',
                ),
            415 =>
                array (
                    'en' => 'Carpets - Fire Restoration',
                    'es' => 'Alfombras - Restauración por Fuego',
                    'slugEn' => 'carpets-fire-restoration',
                    'slugEs' => 'alfombras-restauracion-por-fuego',
                ),
            416 =>
                array (
                    'en' => 'Carpets - Washing',
                    'es' => 'Alfombras - Lavado',
                    'slugEn' => 'carpets-washing',
                    'slugEs' => 'alfombras-lavado',
                ),
            417 =>
                array (
                    'en' => 'Carpets & Rugs - Rental',
                    'es' => 'Alfombras-Alquiler',
                    'slugEn' => 'carpets-rugs-rental',
                    'slugEs' => 'alfombras-alquiler',
                ),
            418 =>
                array (
                    'en' => 'Carts - Food',
                    'es' => 'Alimentos-Al Por Mayor',
                    'slugEn' => 'carts-food',
                    'slugEs' => 'alimentos-al-por-mayor',
                ),
            419 =>
                array (
                    'en' => 'Cash & Carry',
                    'es' => 'Cash & Carry',
                    'slugEn' => 'cash-carry',
                    'slugEs' => 'cash-carry',
                ),
            420 =>
                array (
                    'en' => 'Cash Registers',
                    'es' => 'Cajas Registradoras',
                    'slugEn' => 'cash-registers',
                    'slugEs' => 'cajas-registradoras',
                ),
            421 =>
                array (
                    'en' => 'Cash Registers - IVU Lottery',
                    'es' => 'Loterías',
                    'slugEn' => 'cash-registers-ivu-lottery',
                    'slugEs' => 'loterias',
                ),
            422 =>
                array (
                    'en' => 'Cash Registers - Point of Sales (POS)',
                    'es' => 'Puntos de Venta (POS)',
                    'slugEn' => 'cash-registers-point-of-sales-pos',
                    'slugEs' => 'puntos-de-venta-pos',
                ),
            423 =>
                array (
                    'en' => 'Cash Registers Ivu Loto',
                    'es' => 'Cajas Registradoras Ivu Loto',
                    'slugEn' => 'cash-registers-ivu-loto',
                    'slugEs' => 'cajas-registradoras-ivu-loto',
                ),
            424 =>
                array (
                    'en' => 'Casinos',
                    'es' => 'Casinos',
                    'slugEn' => 'casinos',
                    'slugEs' => 'casinos',
                ),
            425 =>
                array (
                    'en' => 'Casters & Glides',
                    'es' => 'Ruedas-Industriales',
                    'slugEn' => 'casters-glides',
                    'slugEs' => 'ruedas-industriales',
                ),
            426 =>
                array (
                    'en' => 'Catering',
                    'es' => 'Catering',
                    'slugEn' => 'catering',
                    'slugEs' => 'catering',
                ),
            427 =>
                array (
                    'en' => 'Catholic bookstores',
                    'es' => 'Librerías Católicas',
                    'slugEn' => 'catholic-bookstores',
                    'slugEs' => 'librerias-catolicas',
                ),
            428 =>
                array (
                    'en' => 'Cattle - Trading',
                    'es' => 'Ganado-Compraventa',
                    'slugEn' => 'cattle-trading',
                    'slugEs' => 'ganado-compraventa',
                ),
            429 =>
                array (
                    'en' => 'Cattle Breeding',
                    'es' => 'Vaquerías',
                    'slugEn' => 'cattle-breeding',
                    'slugEs' => 'vaquerias',
                ),
            430 =>
                array (
                    'en' => 'Cattle Ranches',
                    'es' => 'Potreros',
                    'slugEn' => 'cattle-ranches',
                    'slugEs' => 'potreros',
                ),
            431 =>
                array (
                    'en' => 'Ceiling Cars',
                    'es' => 'Automóviles Plafones',
                    'slugEn' => 'ceiling-cars',
                    'slugEs' => 'automoviles-plafones',
                ),
            432 =>
                array (
                    'en' => 'Cell Phones - Service',
                    'es' => 'Teléfonos Celulares - Servicio',
                    'slugEn' => 'cell-phones-service',
                    'slugEs' => 'telefonos-celulares-servicio',
                ),
            433 =>
                array (
                    'en' => 'Cell Phones Recharge',
                    'es' => 'Teléfonos Celulares Recarga',
                    'slugEn' => 'cell-phones-recharge',
                    'slugEs' => 'telefonos-celulares-recarga',
                ),
            434 =>
                array (
                    'en' => 'Cement',
                    'es' => 'Cemento',
                    'slugEn' => 'cement',
                    'slugEs' => 'cemento',
                ),
            435 =>
                array (
                    'en' => 'Cement - Mortar',
                    'es' => 'Hormigón',
                    'slugEn' => 'cement-mortar',
                    'slugEs' => 'hormigon',
                ),
            436 =>
                array (
                    'en' => 'Cemeteries',
                    'es' => 'Cementerios',
                    'slugEn' => 'cemeteries',
                    'slugEs' => 'cementerios',
                ),
            437 =>
                array (
                    'en' => 'Cemeteries - Pet',
                    'es' => 'Cementerios - Mascotas',
                    'slugEn' => 'cemeteries-pet',
                    'slugEs' => 'cementerios-mascotas',
                ),
            438 =>
                array (
                    'en' => 'Centers - Rehab',
                    'es' => 'Centros - Rehabilitación',
                    'slugEn' => 'centers-rehab',
                    'slugEs' => 'centros-rehabilitacion',
                ),
            439 =>
                array (
                    'en' => 'Centers-Diagnostics',
                    'es' => 'Centros-Diagnósticos',
                    'slugEn' => 'centers-diagnostics',
                    'slugEs' => 'centros-diagnosticos',
                ),
            440 =>
                array (
                    'en' => 'Ceramics',
                    'es' => 'Cerámicas',
                    'slugEn' => 'ceramics',
                    'slugEs' => 'ceramicas',
                ),
            441 =>
                array (
                    'en' => 'Ceramics - Equipment & Supplies',
                    'es' => 'Cerámicas-Efectos Y Equipo',
                    'slugEn' => 'ceramics-equipment-supplies',
                    'slugEs' => 'ceramicas-efectos-y-equipo',
                ),
            442 =>
                array (
                    'en' => 'Certification - First Aid',
                    'es' => 'Certificaciones - Primeros Auxilios',
                    'slugEn' => 'certification-first-aid',
                    'slugEs' => 'certificaciones-primeros-auxilios',
                ),
            443 =>
                array (
                    'en' => 'Certifications',
                    'es' => 'Certificaciones',
                    'slugEn' => 'certifications',
                    'slugEs' => 'certificaciones',
                ),
            444 =>
                array (
                    'en' => 'Cha Cha Cha Lessons',
                    'es' => 'Clases de Cha Cha Chá',
                    'slugEn' => 'cha-cha-cha-lessons',
                    'slugEs' => 'clases-de-cha-cha-cha',
                ),
            445 =>
                array (
                    'en' => 'Change Oil and Filter',
                    'es' => 'Cambio de Aceite y Filtro',
                    'slugEn' => 'change-oil-and-filter',
                    'slugEs' => 'cambio-de-aceite-y-filtro',
                ),
            446 =>
                array (
                    'en' => 'Chapter 13',
                    'es' => 'Capítulo 13',
                    'slugEn' => 'chapter-13',
                    'slugEs' => 'capitulo-13',
                ),
            447 =>
                array (
                    'en' => 'Chapter 7',
                    'es' => 'Capítulo 7',
                    'slugEn' => 'chapter-7',
                    'slugEs' => 'capitulo-7',
                ),
            448 =>
                array (
                    'en' => 'Charcoal',
                    'es' => 'Carbón Vegetal',
                    'slugEn' => 'charcoal',
                    'slugEs' => 'carbon-vegetal',
                ),
            449 =>
                array (
                    'en' => 'Charitable Institutions',
                    'es' => 'Instituciones Benéficas',
                    'slugEn' => 'charitable-institutions',
                    'slugEs' => 'instituciones-beneficas',
                ),
            450 =>
                array (
                    'en' => 'Charter Bus Lines',
                    'es' => 'Líneas De Carros',
                    'slugEn' => 'charter-bus-lines',
                    'slugEs' => 'lineas-de-carros',
                ),
            451 =>
                array (
                    'en' => 'Charter Rental',
                    'es' => 'Guaguas-Alquiler',
                    'slugEn' => 'charter-rental',
                    'slugEs' => 'guaguas-alquiler',
                ),
            452 =>
                array (
                    'en' => 'Chassis Car Repair',
                    'es' => 'Automóviles Chasis Reparación',
                    'slugEn' => 'chassis-car-repair',
                    'slugEs' => 'automoviles-chasis-reparacion',
                ),
            453 =>
                array (
                    'en' => 'Chassis Repair',
                    'es' => 'Chasis Reparación',
                    'slugEn' => 'chassis-repair',
                    'slugEs' => 'chasis-reparacion',
                ),
            454 =>
                array (
                    'en' => 'Checks - Cashing Service',
                    'es' => 'Cheques-Servicio Cambio',
                    'slugEn' => 'checks-cashing-service',
                    'slugEs' => 'cheques-servicio-cambio',
                ),
            455 =>
                array (
                    'en' => 'Checks - Verification',
                    'es' => 'Cheques-Verificación',
                    'slugEn' => 'checks-verification',
                    'slugEs' => 'cheques-verificacion',
                ),
            456 =>
                array (
                    'en' => 'Cheese - Domestic',
                    'es' => 'Queso del Pais',
                    'slugEn' => 'cheese-domestic',
                    'slugEs' => 'queso-del-pais',
                ),
            457 =>
                array (
                    'en' => 'Chef Uniform',
                    'es' => 'Uniforme de Chef',
                    'slugEn' => 'chef-uniform',
                    'slugEs' => 'uniforme-de-chef',
                ),
            458 =>
                array (
                    'en' => 'Chemicals - Products',
                    'es' => 'Químicos-Productos',
                    'slugEn' => 'chemicals-products',
                    'slugEs' => 'quimicos-productos',
                ),
            459 =>
                array (
                    'en' => 'Child Care',
                    'es' => 'Cuido de Niños',
                    'slugEn' => 'child-care',
                    'slugEs' => 'cuido-de-ninos',
                ),
            460 =>
                array (
                    'en' => 'Childbirth Classes',
                    'es' => 'Clases De Parto',
                    'slugEn' => 'childbirth-classes',
                    'slugEs' => 'clases-de-parto',
                ),
            461 =>
                array (
                    'en' => 'Children Beauty Salon',
                    'es' => 'Salón de Belleza para Niños',
                    'slugEn' => 'children-beauty-salon',
                    'slugEs' => 'salon-de-belleza-para-ninos',
                ),
            462 =>
                array (
                    'en' => 'Chinchorreo',
                    'es' => 'Chinchorreo',
                    'slugEn' => 'chinchorreo',
                    'slugEs' => 'chinchorreo',
                ),
            463 =>
                array (
                    'en' => 'Chinchorro',
                    'es' => 'Chinchorro',
                    'slugEn' => 'chinchorro',
                    'slugEs' => 'chinchorro',
                ),
            464 =>
                array (
                    'en' => 'Chinese Restaurant',
                    'es' => 'Restaurante Comida China',
                    'slugEn' => 'chinese-restaurant',
                    'slugEs' => 'restaurante-comida-china',
                ),
            465 =>
                array (
                    'en' => 'Chiropractors',
                    'es' => 'Quiroprácticos',
                    'slugEn' => 'chiropractors',
                    'slugEs' => 'quiropracticos',
                ),
            466 =>
                array (
                    'en' => 'Chocolate - Manufacture',
                    'es' => 'Chocolate-Fábricas',
                    'slugEn' => 'chocolate-manufacture',
                    'slugEs' => 'chocolate-fabricas',
                ),
            467 =>
                array (
                    'en' => 'Christian - Music',
                    'es' => 'Cristiano - Música',
                    'slugEn' => 'christian-music',
                    'slugEs' => 'cristiano-musica',
                ),
            468 =>
                array (
                    'en' => 'Christian Bookstore',
                    'es' => 'Librería Cristiana',
                    'slugEn' => 'christian-bookstore',
                    'slugEs' => 'libreria-cristiana',
                ),
            469 =>
                array (
                    'en' => 'Church',
                    'es' => 'Iglesia',
                    'slugEn' => 'church',
                    'slugEs' => 'iglesia',
                ),
            470 =>
                array (
                    'en' => 'Church - Christian Science',
                    'es' => 'Iglesias - Ciencia Cristiana',
                    'slugEn' => 'church-christian-science',
                    'slugEs' => 'iglesias-ciencia-cristiana',
                ),
            471 =>
                array (
                    'en' => 'Churches',
                    'es' => 'Iglesias',
                    'slugEn' => 'churches',
                    'slugEs' => 'iglesias',
                ),
            472 =>
                array (
                    'en' => 'Churches - Advent',
                    'es' => 'Iglesias - Adventistas',
                    'slugEn' => 'churches-advent',
                    'slugEs' => 'iglesias-adventistas',
                ),
            473 =>
                array (
                    'en' => 'Churches - Assemblies of God',
                    'es' => 'Iglesias - Asambleas De Dios',
                    'slugEn' => 'churches-assemblies-of-god',
                    'slugEs' => 'iglesias-asambleas-de-dios',
                ),
            474 =>
                array (
                    'en' => 'Churches - Baptist',
                    'es' => 'Iglesias - Bautista',
                    'slugEn' => 'churches-baptist',
                    'slugEs' => 'iglesias-bautista',
                ),
            475 =>
                array (
                    'en' => 'Churches - Christian and Missionary Aliance',
                    'es' => 'Iglesias - Alianza Cristiana Y Misionera',
                    'slugEn' => 'churches-christian-and-missionary-aliance',
                    'slugEs' => 'iglesias-alianza-cristiana-y-misionera',
                ),
            476 =>
                array (
                    'en' => 'Churches - Disciples Of Christ',
                    'es' => 'Iglesias - Discípulos De Cristo',
                    'slugEn' => 'churches-disciples-of-christ',
                    'slugEs' => 'iglesias-discipulos-de-cristo',
                ),
            477 =>
                array (
                    'en' => 'Churches - Episcopal',
                    'es' => 'Iglesias - Episcopal',
                    'slugEn' => 'churches-episcopal',
                    'slugEs' => 'iglesias-episcopal',
                ),
            478 =>
                array (
                    'en' => 'Churches - Evangelical',
                    'es' => 'Iglesias - Evangélica',
                    'slugEn' => 'churches-evangelical',
                    'slugEs' => 'iglesias-evangelica',
                ),
            479 =>
                array (
                    'en' => 'Churches - Jehovah\'s Witnesses',
                    'es' => 'Iglesias - Testigos De Jehová',
                    'slugEn' => 'churches-jehovah-s-witnesses',
                    'slugEs' => 'iglesias-testigos-de-jehova',
                ),
            480 =>
                array (
                    'en' => 'Churches - Jesus Christ of the Later Day Saints (Mormons)',
                    'es' => 'Iglesias - Jesucristo De Los Santos De Los Ultimos Días (Mormones)',
                    'slugEn' => 'churches-jesus-christ-of-the-later-day-saints-mormons',
                    'slugEs' => 'iglesias-jesucristo-de-los-santos-de-los-ultimos-dias-mormones',
                ),
            481 =>
                array (
                    'en' => 'Churches - Lutheran',
                    'es' => 'Iglesias - Luterana',
                    'slugEn' => 'churches-lutheran',
                    'slugEs' => 'iglesias-luterana',
                ),
            482 =>
                array (
                    'en' => 'Churches - Mennonite',
                    'es' => 'Iglesias - Menonita',
                    'slugEn' => 'churches-mennonite',
                    'slugEs' => 'iglesias-menonita',
                ),
            483 =>
                array (
                    'en' => 'Churches - Methodist',
                    'es' => 'Iglesias - Metodista',
                    'slugEn' => 'churches-methodist',
                    'slugEs' => 'iglesias-metodista',
                ),
            484 =>
                array (
                    'en' => 'Churches - Mita',
                    'es' => 'Iglesias - Mita',
                    'slugEn' => 'churches-mita',
                    'slugEs' => 'iglesias-mita',
                ),
            485 =>
                array (
                    'en' => 'Churches - Other',
                    'es' => 'Iglesias - Otras',
                    'slugEn' => 'churches-other',
                    'slugEs' => 'iglesias-otras',
                ),
            486 =>
                array (
                    'en' => 'Churches - Pentecostal',
                    'es' => 'Iglesias - Pentecostal',
                    'slugEn' => 'churches-pentecostal',
                    'slugEs' => 'iglesias-pentecostal',
                ),
            487 =>
                array (
                    'en' => 'Churches - Presbyterian',
                    'es' => 'Iglesias - Presbiteriana',
                    'slugEn' => 'churches-presbyterian',
                    'slugEs' => 'iglesias-presbiteriana',
                ),
            488 =>
                array (
                    'en' => 'Churches - Roman Catholic',
                    'es' => 'Iglesias - Católica Romana',
                    'slugEn' => 'churches-roman-catholic',
                    'slugEs' => 'iglesias-catolica-romana',
                ),
            489 =>
                array (
                    'en' => 'Churches - Unity',
                    'es' => 'Iglesias - Unity',
                    'slugEn' => 'churches-unity',
                    'slugEs' => 'iglesias-unity',
                ),
            490 =>
                array (
                    'en' => 'Cigarettes',
                    'es' => 'Cigarros',
                    'slugEn' => 'cigarettes',
                    'slugEs' => 'cigarros',
                ),
            491 =>
                array (
                    'en' => 'Cigars & Cigarettes',
                    'es' => 'Cigarros Y Cigarrillos',
                    'slugEn' => 'cigars-cigarettes',
                    'slugEs' => 'cigarros-y-cigarrillos',
                ),
            492 =>
                array (
                    'en' => 'Classes - Music',
                    'es' => 'Clases - Música',
                    'slugEn' => 'classes-music',
                    'slugEs' => 'clases-musica',
                ),
            493 =>
                array (
                    'en' => 'Classes / Salsa',
                    'es' => 'Clases/Salsa',
                    'slugEn' => 'classes-salsa',
                    'slugEs' => 'clases-salsa',
                ),
            494 =>
                array (
                    'en' => 'Classified',
                    'es' => 'Clasificados',
                    'slugEn' => 'classified',
                    'slugEs' => 'clasificados',
                ),
            495 =>
                array (
                    'en' => 'Cleaning',
                    'es' => 'Limpieza',
                    'slugEn' => 'cleaning',
                    'slugEs' => 'limpieza',
                ),
            496 =>
                array (
                    'en' => 'Cleaning - Ducts',
                    'es' => 'Limpieza - Ductos',
                    'slugEn' => 'cleaning-ducts',
                    'slugEs' => 'limpieza-ductos',
                ),
            497 =>
                array (
                    'en' => 'Cleaning - Equipment & Supplies',
                    'es' => 'Limpieza-Efectos Y Equipo',
                    'slugEn' => 'cleaning-equipment-supplies',
                    'slugEs' => 'limpieza-efectos-y-equipo',
                ),
            498 =>
                array (
                    'en' => 'Cleaning - Industrial',
                    'es' => 'Limpieza-Industrial',
                    'slugEn' => 'cleaning-industrial',
                    'slugEs' => 'limpieza-industrial',
                ),
            499 =>
                array (
                    'en' => 'Cleaning - Products',
                    'es' => 'Limpieza-Productos',
                    'slugEn' => 'cleaning-products',
                    'slugEs' => 'limpieza-productos',
                ),
            500 =>
                array (
                    'en' => 'Cleaning-Green Areas',
                    'es' => 'Limpieza-Áreas Verdes',
                    'slugEn' => 'cleaning-green-areas',
                    'slugEs' => 'limpieza-areas-verdes',
                ),
            501 =>
                array (
                    'en' => 'Clinical Laboratory',
                    'es' => 'Laboratorios Clínicos',
                    'slugEn' => 'clinical-laboratory',
                    'slugEs' => 'laboratorios-clinicos',
                ),
            502 =>
                array (
                    'en' => 'Clinical Social Worker',
                    'es' => 'Trabajador Social Clínico',
                    'slugEn' => 'clinical-social-worker',
                    'slugEs' => 'trabajador-social-clinico',
                ),
            503 =>
                array (
                    'en' => 'Clinics',
                    'es' => 'Clínicas',
                    'slugEn' => 'clinics',
                    'slugEs' => 'clinicas',
                ),
            504 =>
                array (
                    'en' => 'Clinics - Specialized',
                    'es' => 'Consultorios-Especializados',
                    'slugEn' => 'clinics-specialized',
                    'slugEs' => 'consultorios-especializados',
                ),
            505 =>
                array (
                    'en' => 'Clocks - Electric Control',
                    'es' => 'Relojes-Eléctricos Control',
                    'slugEn' => 'clocks-electric-control',
                    'slugEs' => 'relojes-electricos-control',
                ),
            506 =>
                array (
                    'en' => 'Closed Circuit Sistems',
                    'es' => 'Circuito Cerrado-Sistemas',
                    'slugEn' => 'closed-circuit-sistems',
                    'slugEs' => 'circuito-cerrado-sistemas',
                ),
            507 =>
                array (
                    'en' => 'Closets',
                    'es' => 'Closets (Roperos)',
                    'slugEn' => 'closets',
                    'slugEs' => 'closets-roperos',
                ),
            508 =>
                array (
                    'en' => 'Closets doors',
                    'es' => 'Puertas closets',
                    'slugEn' => 'closets-doors',
                    'slugEs' => 'puertas-closets',
                ),
            509 =>
                array (
                    'en' => 'Cloth Diapers',
                    'es' => 'Pañales de Tela',
                    'slugEn' => 'cloth-diapers',
                    'slugEs' => 'panales-de-tela',
                ),
            510 =>
                array (
                    'en' => 'Cloth Fabrics',
                    'es' => 'Telas',
                    'slugEn' => 'cloth-fabrics',
                    'slugEs' => 'telas',
                ),
            511 =>
                array (
                    'en' => 'Clothing',
                    'es' => 'Ropa',
                    'slugEn' => 'clothing',
                    'slugEs' => 'ropa',
                ),
            512 =>
                array (
                    'en' => 'Clothing - Beach',
                    'es' => 'Ropa - Playa',
                    'slugEn' => 'clothing-beach',
                    'slugEs' => 'ropa-playa',
                ),
            513 =>
                array (
                    'en' => 'Clothing - Casual',
                    'es' => 'Ropa - Casual',
                    'slugEn' => 'clothing-casual',
                    'slugEs' => 'ropa-casual',
                ),
            514 =>
                array (
                    'en' => 'Clothing - Children and Infants - Manufacture',
                    'es' => 'Ropa - Niños-Fábricas',
                    'slugEn' => 'clothing-children-and-infants-manufacture',
                    'slugEs' => 'ropa-ninos-fabricas',
                ),
            515 =>
                array (
                    'en' => 'Clothing - Consignment Shops',
                    'es' => 'Ropa - Venta A Consignación',
                    'slugEn' => 'clothing-consignment-shops',
                    'slugEs' => 'ropa-venta-a-consignacion',
                ),
            516 =>
                array (
                    'en' => 'Clothing - Formal - Rental',
                    'es' => 'Ropa - Formal-Alquiler',
                    'slugEn' => 'clothing-formal-rental',
                    'slugEs' => 'ropa-formal-alquiler',
                ),
            517 =>
                array (
                    'en' => 'Clothing - Gentelman - Manufacture',
                    'es' => 'Ropa - Caballeros-Fábricas',
                    'slugEn' => 'clothing-gentelman-manufacture',
                    'slugEs' => 'ropa-caballeros-fabricas',
                ),
            518 =>
                array (
                    'en' => 'Clothing - Gentelman - Wholesale',
                    'es' => 'Ropa - Caballeros-Al Por Mayor',
                    'slugEn' => 'clothing-gentelman-wholesale',
                    'slugEs' => 'ropa-caballeros-al-por-mayor',
                ),
            519 =>
                array (
                    'en' => 'Clothing - Gentlemen',
                    'es' => 'Ropa - Caballeros',
                    'slugEn' => 'clothing-gentlemen',
                    'slugEs' => 'ropa-caballeros',
                ),
            520 =>
                array (
                    'en' => 'Clothing - Ladies',
                    'es' => 'Ropa - Damas',
                    'slugEn' => 'clothing-ladies',
                    'slugEs' => 'ropa-damas',
                ),
            521 =>
                array (
                    'en' => 'Clothing - Ladies - Manufacture',
                    'es' => 'Ropa - Damas-Fábricas',
                    'slugEn' => 'clothing-ladies-manufacture',
                    'slugEs' => 'ropa-damas-fabricas',
                ),
            522 =>
                array (
                    'en' => 'Clothing - Ladies - Wholesale',
                    'es' => 'Ropa - Damas-Al Por Mayor',
                    'slugEn' => 'clothing-ladies-wholesale',
                    'slugEs' => 'ropa-damas-al-por-mayor',
                ),
            523 =>
                array (
                    'en' => 'Clothing - Manufacture',
                    'es' => 'Ropa - Fábricas',
                    'slugEn' => 'clothing-manufacture',
                    'slugEs' => 'ropa-fabricas',
                ),
            524 =>
                array (
                    'en' => 'Clothing - Manufacturer',
                    'es' => 'Ropa - Manufactura',
                    'slugEn' => 'clothing-manufacturer',
                    'slugEs' => 'ropa-manufactura',
                ),
            525 =>
                array (
                    'en' => 'Clothing - Maternity',
                    'es' => 'Ropa - Maternidad',
                    'slugEn' => 'clothing-maternity',
                    'slugEs' => 'ropa-maternidad',
                ),
            526 =>
                array (
                    'en' => 'Clothing - Military - Manufacture',
                    'es' => 'Ropa - Militar-Fábricas',
                    'slugEn' => 'clothing-military-manufacture',
                    'slugEs' => 'ropa-militar-fabricas',
                ),
            527 =>
                array (
                    'en' => 'Clothing - Underwear',
                    'es' => 'Ropa - Interior',
                    'slugEn' => 'clothing-underwear',
                    'slugEs' => 'ropa-interior',
                ),
            528 =>
                array (
                    'en' => 'Clothing - Underwear - Men',
                    'es' => 'Ropa - Interior-Caballeros',
                    'slugEn' => 'clothing-underwear-men',
                    'slugEs' => 'ropa-interior-caballeros',
                ),
            529 =>
                array (
                    'en' => 'Clothing - Underwear - Men - Manufacture',
                    'es' => 'Ropa - Interior-Caballeros-Fábricas',
                    'slugEn' => 'clothing-underwear-men-manufacture',
                    'slugEs' => 'ropa-interior-caballeros-fabricas',
                ),
            530 =>
                array (
                    'en' => 'Clothing - Underwear - Women',
                    'es' => 'Ropa - Interior-Damas',
                    'slugEn' => 'clothing-underwear-women',
                    'slugEs' => 'ropa-interior-damas',
                ),
            531 =>
                array (
                    'en' => 'Clothing - Underwear - Women - Manufacture',
                    'es' => 'Ropa - Interior-Damas-Fábricas',
                    'slugEn' => 'clothing-underwear-women-manufacture',
                    'slugEs' => 'ropa-interior-damas-fabricas',
                ),
            532 =>
                array (
                    'en' => 'Clothing Cycling',
                    'es' => 'Ropa Para Ciclistas',
                    'slugEn' => 'clothing-cycling',
                    'slugEs' => 'ropa-para-ciclistas',
                ),
            533 =>
                array (
                    'en' => 'Clothing-Children And Infants',
                    'es' => 'Ropa - Niños',
                    'slugEn' => 'clothing-children-and-infants',
                    'slugEs' => 'ropa-ninos',
                ),
            534 =>
                array (
                    'en' => 'Clothing-Wholesale',
                    'es' => 'Ropa - Al Por Mayor',
                    'slugEn' => 'clothing-wholesale',
                    'slugEs' => 'ropa-al-por-mayor',
                ),
            535 =>
                array (
                    'en' => 'Clowns',
                    'es' => 'Payasos',
                    'slugEn' => 'clowns',
                    'slugEs' => 'payasos',
                ),
            536 =>
                array (
                    'en' => 'Clubs-Social',
                    'es' => 'Clubes-Sociales',
                    'slugEn' => 'clubs-social',
                    'slugEs' => 'clubes-sociales',
                ),
            537 =>
                array (
                    'en' => 'Cobblestone',
                    'es' => 'Adoquines',
                    'slugEn' => 'cobblestone',
                    'slugEs' => 'adoquines',
                ),
            538 =>
                array (
                    'en' => 'Cockfighting Arenas',
                    'es' => 'Galleras',
                    'slugEn' => 'cockfighting-arenas',
                    'slugEs' => 'galleras',
                ),
            539 =>
                array (
                    'en' => 'Coffee - Barista',
                    'es' => 'Café - Barista',
                    'slugEn' => 'coffee-barista',
                    'slugEs' => 'cafe-barista',
                ),
            540 =>
                array (
                    'en' => 'Coffee - Brewing Devices - Wholesale',
                    'es' => 'Cafeteras-Distribuidores',
                    'slugEn' => 'coffee-brewing-devices-wholesale',
                    'slugEs' => 'cafeteras-distribuidores',
                ),
            541 =>
                array (
                    'en' => 'Coffee - Distribution',
                    'es' => 'Café - Distribución',
                    'slugEn' => 'coffee-distribution',
                    'slugEs' => 'cafe-distribucion',
                ),
            542 =>
                array (
                    'en' => 'Coffee - Gourmet',
                    'es' => 'Café - Gourmet',
                    'slugEn' => 'coffee-gourmet',
                    'slugEs' => 'cafe-gourmet',
                ),
            543 =>
                array (
                    'en' => 'Coffee - Machinery',
                    'es' => 'Café-Maquinaria',
                    'slugEn' => 'coffee-machinery',
                    'slugEs' => 'cafe-maquinaria',
                ),
            544 =>
                array (
                    'en' => 'Coffee - Syrup',
                    'es' => 'Café -  Syrup',
                    'slugEn' => 'coffee-syrup',
                    'slugEs' => 'cafe-syrup',
                ),
            545 =>
                array (
                    'en' => 'Coffee Break - Service & Supplies',
                    'es' => 'Café-Servicio Para Oficina',
                    'slugEn' => 'coffee-break-service-supplies',
                    'slugEs' => 'cafe-servicio-para-oficina',
                ),
            546 =>
                array (
                    'en' => 'Coffee Distributor',
                    'es' => 'Distribuidor de Café',
                    'slugEn' => 'coffee-distributor',
                    'slugEs' => 'distribuidor-de-cafe',
                ),
            547 =>
                array (
                    'en' => 'Coffee Grinding',
                    'es' => 'Café Molido',
                    'slugEn' => 'coffee-grinding',
                    'slugEs' => 'cafe-molido',
                ),
            548 =>
                array (
                    'en' => 'Coffee Machine',
                    'es' => 'Máquinas de Cafe',
                    'slugEn' => 'coffee-machine',
                    'slugEs' => 'maquinas-de-cafe',
                ),
            549 =>
                array (
                    'en' => 'Coffee Roasting',
                    'es' => 'Café Torrefacción',
                    'slugEn' => 'coffee-roasting',
                    'slugEs' => 'cafe-torrefaccion',
                ),
            550 =>
                array (
                    'en' => 'Coffee Shop',
                    'es' => 'Coffee Shop',
                    'slugEn' => 'coffee-shop',
                    'slugEs' => 'coffee-shop',
                ),
            551 =>
                array (
                    'en' => 'Coffee Store',
                    'es' => 'Café - Tiendas especializadas',
                    'slugEn' => 'coffee-store',
                    'slugEs' => 'cafe-tiendas-especializadas',
                ),
            552 =>
                array (
                    'en' => 'Coins - Dealers',
                    'es' => 'Monedas-Compraventa',
                    'slugEn' => 'coins-dealers',
                    'slugEs' => 'monedas-compraventa',
                ),
            553 =>
                array (
                    'en' => 'Coliseums & Stadiums',
                    'es' => 'Coliseos Y Estadios',
                    'slugEn' => 'coliseums-stadiums',
                    'slugEs' => 'coliseos-y-estadios',
                ),
            554 =>
                array (
                    'en' => 'Collectibles',
                    'es' => 'Colección-Artículos',
                    'slugEn' => 'collectibles',
                    'slugEs' => 'coleccion-articulos',
                ),
            555 =>
                array (
                    'en' => 'Collection Agencies',
                    'es' => 'Cobros-Agencias',
                    'slugEn' => 'collection-agencies',
                    'slugEs' => 'cobros-agencias',
                ),
            556 =>
                array (
                    'en' => 'College - Task',
                    'es' => 'Universidad - Tareas',
                    'slugEn' => 'college-task',
                    'slugEs' => 'universidad-tareas',
                ),
            557 =>
                array (
                    'en' => 'Colombian Restaurants',
                    'es' => 'Restaurantes Colombianos',
                    'slugEn' => 'colombian-restaurants',
                    'slugEs' => 'restaurantes-colombianos',
                ),
            558 =>
                array (
                    'en' => 'Colon Hydrotherapy',
                    'es' => 'Hidroterapia del Colón',
                    'slugEn' => 'colon-hydrotherapy',
                    'slugEs' => 'hidroterapia-del-colon',
                ),
            559 =>
                array (
                    'en' => 'Commercial Electrician',
                    'es' => 'Electricista Comercial',
                    'slugEn' => 'commercial-electrician',
                    'slugEs' => 'electricista-comercial',
                ),
            560 =>
                array (
                    'en' => 'Commercial Electricity',
                    'es' => 'Electricidad Comercial',
                    'slugEn' => 'commercial-electricity',
                    'slugEs' => 'electricidad-comercial',
                ),
            561 =>
                array (
                    'en' => 'Commercial Facades',
                    'es' => 'Fachadas Comerciales',
                    'slugEn' => 'commercial-facades',
                    'slugEs' => 'fachadas-comerciales',
                ),
            562 =>
                array (
                    'en' => 'Commercial Kitchens - Parts and Service',
                    'es' => 'Cocinas Comercial - Piezas y Servicio',
                    'slugEn' => 'commercial-kitchens-parts-and-service',
                    'slugEs' => 'cocinas-comercial-piezas-y-servicio',
                ),
            563 =>
                array (
                    'en' => 'Commission Merchants',
                    'es' => 'Comisionistas',
                    'slugEn' => 'commission-merchants',
                    'slugEs' => 'comisionistas',
                ),
            564 =>
                array (
                    'en' => 'Communications',
                    'es' => 'Comunicaciones-Compañías',
                    'slugEn' => 'communications',
                    'slugEs' => 'comunicaciones-companias',
                ),
            565 =>
                array (
                    'en' => 'Communications - Equipment & Supplies-Services',
                    'es' => 'Comunicaciones-Efectos Y Equipo-Servicios',
                    'slugEn' => 'communications-equipment-supplies-services',
                    'slugEs' => 'comunicaciones-efectos-y-equipo-servicios',
                ),
            566 =>
                array (
                    'en' => 'Compressed Air Compressors',
                    'es' => 'Compresores de Aire Comprimido',
                    'slugEn' => 'compressed-air-compressors',
                    'slugEs' => 'compresores-de-aire-comprimido',
                ),
            567 =>
                array (
                    'en' => 'Compressors',
                    'es' => 'Compresores',
                    'slugEn' => 'compressors',
                    'slugEs' => 'compresores',
                ),
            568 =>
                array (
                    'en' => 'Computer Hardware and Distributors',
                    'es' => 'Equipo Informatico Y De Computadora Distribuidores',
                    'slugEn' => 'computer-hardware-and-distributors',
                    'slugEs' => 'equipo-informatico-y-de-computadora-distribuidores',
                ),
            569 =>
                array (
                    'en' => 'Computers',
                    'es' => 'Computadoras',
                    'slugEn' => 'computers',
                    'slugEs' => 'computadoras',
                ),
            570 =>
                array (
                    'en' => 'Computers - Communications',
                    'es' => 'Computadoras-Comunicaciones',
                    'slugEn' => 'computers-communications',
                    'slugEs' => 'computadoras-comunicaciones',
                ),
            571 =>
                array (
                    'en' => 'Computers - Multimedia',
                    'es' => 'Computadoras-Multimedios',
                    'slugEn' => 'computers-multimedia',
                    'slugEs' => 'computadoras-multimedios',
                ),
            572 =>
                array (
                    'en' => 'Computers - Networking - Service & Repair',
                    'es' => 'Computadoras-Redes - Servicio y Reparación',
                    'slugEn' => 'computers-networking-service-repair',
                    'slugEs' => 'computadoras-redes-servicio-y-reparacion',
                ),
            573 =>
                array (
                    'en' => 'Computers - Parts & Accessories',
                    'es' => 'Computadoras-Piezas Y Accesorios',
                    'slugEn' => 'computers-parts-accessories',
                    'slugEs' => 'computadoras-piezas-y-accesorios',
                ),
            574 =>
                array (
                    'en' => 'Computers - Programming - Services',
                    'es' => 'Computadoras-Programación-Servicios',
                    'slugEn' => 'computers-programming-services',
                    'slugEs' => 'computadoras-programacion-servicios',
                ),
            575 =>
                array (
                    'en' => 'Computers - Service & Repair',
                    'es' => 'Computadoras-Servicio Y Reparación',
                    'slugEn' => 'computers-service-repair',
                    'slugEs' => 'computadoras-servicio-y-reparacion',
                ),
            576 =>
                array (
                    'en' => 'Computers - Software',
                    'es' => 'Computadoras-Software',
                    'slugEn' => 'computers-software',
                    'slugEs' => 'computadoras-software',
                ),
            577 =>
                array (
                    'en' => 'Computers - Training',
                    'es' => 'Computadoras-Adiestramiento',
                    'slugEn' => 'computers-training',
                    'slugEs' => 'computadoras-adiestramiento',
                ),
            578 =>
                array (
                    'en' => 'Concerts',
                    'es' => 'Conciertos',
                    'slugEn' => 'concerts',
                    'slugEs' => 'conciertos',
                ),
            579 =>
                array (
                    'en' => 'Concierge Services',
                    'es' => 'Servicios Concierge',
                    'slugEn' => 'concierge-services',
                    'slugEs' => 'servicios-concierge',
                ),
            580 =>
                array (
                    'en' => 'Concrete',
                    'es' => 'Concreto',
                    'slugEn' => 'concrete',
                    'slugEs' => 'concreto',
                ),
            581 =>
                array (
                    'en' => 'Concrete Prefabricated',
                    'es' => 'Hormigón-Prefabricado',
                    'slugEn' => 'concrete-prefabricated',
                    'slugEs' => 'hormigon-prefabricado',
                ),
            582 =>
                array (
                    'en' => 'Condo Hotel',
                    'es' => 'Condominio Hotel',
                    'slugEn' => 'condo-hotel',
                    'slugEs' => 'condominio-hotel',
                ),
            583 =>
                array (
                    'en' => 'Condo Insurance',
                    'es' => 'Seguros Condominios',
                    'slugEn' => 'condo-insurance',
                    'slugEs' => 'seguros-condominios',
                ),
            584 =>
                array (
                    'en' => 'Condominium - Administration',
                    'es' => 'Administración De Condominios',
                    'slugEn' => 'condominium-administration',
                    'slugEs' => 'administracion-de-condominios',
                ),
            585 =>
                array (
                    'en' => 'Condominiums',
                    'es' => 'Condominios',
                    'slugEn' => 'condominiums',
                    'slugEs' => 'condominios',
                ),
            586 =>
                array (
                    'en' => 'Conduct Modification',
                    'es' => 'Modificación de Conducta',
                    'slugEn' => 'conduct-modification',
                    'slugEs' => 'modificacion-de-conducta',
                ),
            587 =>
                array (
                    'en' => 'Conflict Mediation',
                    'es' => 'Mediación de Conflictos',
                    'slugEn' => 'conflict-mediation',
                    'slugEs' => 'mediacion-de-conflictos',
                ),
            588 =>
                array (
                    'en' => 'Consignation Merchandise',
                    'es' => 'Mercancia Consignacion',
                    'slugEn' => 'consignation-merchandise',
                    'slugEs' => 'mercancia-consignacion',
                ),
            589 =>
                array (
                    'en' => 'Consignment Sale',
                    'es' => 'Venta a Consignacion',
                    'slugEn' => 'consignment-sale',
                    'slugEs' => 'venta-a-consignacion',
                ),
            590 =>
                array (
                    'en' => 'Construction',
                    'es' => 'Construcción',
                    'slugEn' => 'construction',
                    'slugEs' => 'construccion',
                ),
            591 =>
                array (
                    'en' => 'Construction - Equipment - Rental',
                    'es' => 'Construcción-Equipo-Alquiler',
                    'slugEn' => 'construction-equipment-rental',
                    'slugEs' => 'construccion-equipo-alquiler',
                ),
            592 =>
                array (
                    'en' => 'Construction - Management',
                    'es' => 'Construcción-Gerencia',
                    'slugEn' => 'construction-management',
                    'slugEs' => 'construccion-gerencia',
                ),
            593 =>
                array (
                    'en' => 'Construction Pools',
                    'es' => 'Construcción Piscinas',
                    'slugEn' => 'construction-pools',
                    'slugEs' => 'construccion-piscinas',
                ),
            594 =>
                array (
                    'en' => 'Construction- Tennis Court',
                    'es' => 'Construcción-Canchas de Tennis',
                    'slugEn' => 'construction-tennis-court',
                    'slugEs' => 'construccion-canchas-de-tennis',
                ),
            595 =>
                array (
                    'en' => 'Consulates',
                    'es' => 'Consulados',
                    'slugEn' => 'consulates',
                    'slugEs' => 'consulados',
                ),
            596 =>
                array (
                    'en' => 'Consuling',
                    'es' => 'Consejería',
                    'slugEn' => 'consuling',
                    'slugEs' => 'consejeria',
                ),
            597 =>
                array (
                    'en' => 'Consultant - Insurance',
                    'es' => 'Consultores-Seguros',
                    'slugEn' => 'consultant-insurance',
                    'slugEs' => 'consultores-seguros',
                ),
            598 =>
                array (
                    'en' => 'Consultant by Specialty-Federal Programs',
                    'es' => 'Consultores Por Especialidad -Programas Federales',
                    'slugEn' => 'consultant-by-specialty-federal-programs',
                    'slugEs' => 'consultores-por-especialidad-programas-federales',
                ),
            599 =>
                array (
                    'en' => 'Consultant by Specialty-Labor',
                    'es' => 'Consultores Por Especialidad -Laborales',
                    'slugEn' => 'consultant-by-specialty-labor',
                    'slugEs' => 'consultores-por-especialidad-laborales',
                ),
            600 =>
                array (
                    'en' => 'Consultant by Specialty-Motivation',
                    'es' => 'Consultores Por Especialidad - Motivación',
                    'slugEn' => 'consultant-by-specialty-motivation',
                    'slugEs' => 'consultores-por-especialidad-motivacion',
                ),
            601 =>
                array (
                    'en' => 'Consultant by Specialty-Tourism',
                    'es' => 'Consultores Por Especialidad - Turismo',
                    'slugEn' => 'consultant-by-specialty-tourism',
                    'slugEs' => 'consultores-por-especialidad-turismo',
                ),
            602 =>
                array (
                    'en' => 'Consultants',
                    'es' => 'Consultores',
                    'slugEn' => 'consultants',
                    'slugEs' => 'consultores',
                ),
            603 =>
                array (
                    'en' => 'Consultants - Information Systems',
                    'es' => 'Consultores Por Especialidad - Sistemas Información',
                    'slugEn' => 'consultants-information-systems',
                    'slugEs' => 'consultores-por-especialidad-sistemas-informacion',
                ),
            604 =>
                array (
                    'en' => 'Consultants - Labor',
                    'es' => 'Consultores Por Especialidad - Recursos Humanos',
                    'slugEn' => 'consultants-labor',
                    'slugEs' => 'consultores-por-especialidad-recursos-humanos',
                ),
            605 =>
                array (
                    'en' => 'Consultants - Pharmaceutic',
                    'es' => 'Consultores Por Especialidad - Farmacéuticas',
                    'slugEn' => 'consultants-pharmaceutic',
                    'slugEs' => 'consultores-por-especialidad-farmaceuticas',
                ),
            606 =>
                array (
                    'en' => 'Consultants -Administrative',
                    'es' => 'Consultores -Administrativos',
                    'slugEn' => 'consultants-administrative',
                    'slugEs' => 'consultores-administrativos',
                ),
            607 =>
                array (
                    'en' => 'Consultants By Specialty - Taxes',
                    'es' => 'Consultores Por Especialidad - Impuestos',
                    'slugEn' => 'consultants-by-specialty-taxes',
                    'slugEs' => 'consultores-por-especialidad-impuestos',
                ),
            608 =>
                array (
                    'en' => 'Consultants- Marketing',
                    'es' => 'Consultores Por Especialidad - Mercadeo',
                    'slugEn' => 'consultants-marketing',
                    'slugEs' => 'consultores-por-especialidad-mercadeo',
                ),
            609 =>
                array (
                    'en' => 'Consultants Taxes',
                    'es' => 'Consultores Por Especialidad Contribuciones',
                    'slugEn' => 'consultants-taxes',
                    'slugEs' => 'consultores-por-especialidad-contribuciones',
                ),
            610 =>
                array (
                    'en' => 'Consultants-Alimony',
                    'es' => 'Consultores Por Especialidad - Pensiones',
                    'slugEn' => 'consultants-alimony',
                    'slugEs' => 'consultores-por-especialidad-pensiones',
                ),
            611 =>
                array (
                    'en' => 'Consultants-Archeology',
                    'es' => 'Consultores Por Especialidad - Arqueología',
                    'slugEn' => 'consultants-archeology',
                    'slugEs' => 'consultores-por-especialidad-arqueologia',
                ),
            612 =>
                array (
                    'en' => 'Consultants-Ceiling',
                    'es' => 'Consultores Por Especialidad - Construcción',
                    'slugEn' => 'consultants-ceiling',
                    'slugEs' => 'consultores-por-especialidad-construccion',
                ),
            613 =>
                array (
                    'en' => 'Consultants-Communications',
                    'es' => 'Consultores Por Especialidad - Comunicaciones',
                    'slugEn' => 'consultants-communications',
                    'slugEs' => 'consultores-por-especialidad-comunicaciones',
                ),
            614 =>
                array (
                    'en' => 'Consultants-Computers',
                    'es' => 'Consultores Por Especialidad - Computadoras',
                    'slugEn' => 'consultants-computers',
                    'slugEs' => 'consultores-por-especialidad-computadoras',
                ),
            615 =>
                array (
                    'en' => 'Consultants-Corporations',
                    'es' => 'Consultores Por Especialidad - Corporaciones',
                    'slugEn' => 'consultants-corporations',
                    'slugEs' => 'consultores-por-especialidad-corporaciones',
                ),
            616 =>
                array (
                    'en' => 'Consultants-Developement Studies',
                    'es' => 'Consultores Por Especialidad - Estudios Desarrollo',
                    'slugEn' => 'consultants-developement-studies',
                    'slugEs' => 'consultores-por-especialidad-estudios-desarrollo',
                ),
            617 =>
                array (
                    'en' => 'Consultants-Ecology',
                    'es' => 'Consultores Por Especialidad - Ecología',
                    'slugEn' => 'consultants-ecology',
                    'slugEs' => 'consultores-por-especialidad-ecologia',
                ),
            618 =>
                array (
                    'en' => 'Consultants-Economy',
                    'es' => 'Consultores-Economía',
                    'slugEn' => 'consultants-economy',
                    'slugEs' => 'consultores-economia',
                ),
            619 =>
                array (
                    'en' => 'Consultants-Energy',
                    'es' => 'Consultores Por Especialidad - Energía',
                    'slugEn' => 'consultants-energy',
                    'slugEs' => 'consultores-por-especialidad-energia',
                ),
            620 =>
                array (
                    'en' => 'Consultants-Engineering',
                    'es' => 'Consultores Por Especialidad - Ingeniería',
                    'slugEn' => 'consultants-engineering',
                    'slugEs' => 'consultores-por-especialidad-ingenieria',
                ),
            621 =>
                array (
                    'en' => 'Consultants-Environmental',
                    'es' => 'Consultores Por Especialidad - Ambientales',
                    'slugEn' => 'consultants-environmental',
                    'slugEs' => 'consultores-por-especialidad-ambientales',
                ),
            622 =>
                array (
                    'en' => 'Consultants-Federal Programs',
                    'es' => 'Consultores-Programas Federales',
                    'slugEn' => 'consultants-federal-programs',
                    'slugEs' => 'consultores-programas-federales',
                ),
            623 =>
                array (
                    'en' => 'Consultants-Financial',
                    'es' => 'Consultores Por Especialidad - Financiamiento',
                    'slugEn' => 'consultants-financial',
                    'slugEs' => 'consultores-por-especialidad-financiamiento',
                ),
            624 =>
                array (
                    'en' => 'Consultants-Financial Planning',
                    'es' => 'Consultores Por Especialidad - Planificación Financiera',
                    'slugEn' => 'consultants-financial-planning',
                    'slugEs' => 'consultores-por-especialidad-planificacion-financiera',
                ),
            625 =>
                array (
                    'en' => 'Consultants-Food',
                    'es' => 'Consultores-Alimentos',
                    'slugEn' => 'consultants-food',
                    'slugEs' => 'consultores-alimentos',
                ),
            626 =>
                array (
                    'en' => 'Consultants-General',
                    'es' => 'Consultores Por Especialidad - Generales',
                    'slugEn' => 'consultants-general',
                    'slugEs' => 'consultores-por-especialidad-generales',
                ),
            627 =>
                array (
                    'en' => 'Consultants-Geology',
                    'es' => 'Consultores Por Especialidad - Geología',
                    'slugEn' => 'consultants-geology',
                    'slugEs' => 'consultores-por-especialidad-geologia',
                ),
            628 =>
                array (
                    'en' => 'Consultants-Health Services',
                    'es' => 'Consultores Por Especialidad - Servicios Salud',
                    'slugEn' => 'consultants-health-services',
                    'slugEs' => 'consultores-por-especialidad-servicios-salud',
                ),
            629 =>
                array (
                    'en' => 'Consultants-Industrial Developement',
                    'es' => 'Consultores Por Especialidad - Desarrollo Industrial',
                    'slugEn' => 'consultants-industrial-developement',
                    'slugEs' => 'consultores-por-especialidad-desarrollo-industrial',
                ),
            630 =>
                array (
                    'en' => 'Consultants-Management',
                    'es' => 'Consultores Por Especialidad - Gerencia',
                    'slugEn' => 'consultants-management',
                    'slugEs' => 'consultores-por-especialidad-gerencia',
                ),
            631 =>
                array (
                    'en' => 'Consultants-Planning',
                    'es' => 'Consultores Por Especialidad - Planificación',
                    'slugEn' => 'consultants-planning',
                    'slugEs' => 'consultores-por-especialidad-planificacion',
                ),
            632 =>
                array (
                    'en' => 'Consultants-Trainning',
                    'es' => 'Consultores Por Especialidad - Adiestramiento',
                    'slugEn' => 'consultants-trainning',
                    'slugEs' => 'consultores-por-especialidad-adiestramiento',
                ),
            633 =>
                array (
                    'en' => 'Consultants-Transportation',
                    'es' => 'Consultores Por Especialidad - Transportación',
                    'slugEn' => 'consultants-transportation',
                    'slugEs' => 'consultores-por-especialidad-transportacion',
                ),
            634 =>
                array (
                    'en' => 'Consulting and Training',
                    'es' => 'Consultoría y Adiestramiento',
                    'slugEn' => 'consulting-and-training',
                    'slugEs' => 'consultoria-y-adiestramiento',
                ),
            635 =>
                array (
                    'en' => 'Contact Lenses',
                    'es' => 'Lentes De Contacto',
                    'slugEn' => 'contact-lenses',
                    'slugEs' => 'lentes-de-contacto',
                ),
            636 =>
                array (
                    'en' => 'Containers',
                    'es' => 'Envases-Plásticos-Distribuidores',
                    'slugEn' => 'containers',
                    'slugEs' => 'envases-plasticos-distribuidores',
                ),
            637 =>
                array (
                    'en' => 'Containers - Manufacture',
                    'es' => 'Envases-Fábricas',
                    'slugEn' => 'containers-manufacture',
                    'slugEs' => 'envases-fabricas',
                ),
            638 =>
                array (
                    'en' => 'Contractor by Specialty - Air Conditioning',
                    'es' => 'Contratistas Por Especialidad - Aire Acondicionado',
                    'slugEn' => 'contractor-by-specialty-air-conditioning',
                    'slugEs' => 'contratistas-por-especialidad-aire-acondicionado',
                ),
            639 =>
                array (
                    'en' => 'Contractor by Specialty - Glassworks',
                    'es' => 'Contratistas Por Especialidad - Cristaleria',
                    'slugEn' => 'contractor-by-specialty-glassworks',
                    'slugEs' => 'contratistas-por-especialidad-cristaleria',
                ),
            640 =>
                array (
                    'en' => 'Contractor by Specialty - Residencial Maintenance',
                    'es' => 'Contratistas Por Especialidad - Mantenimiento Residencial',
                    'slugEn' => 'contractor-by-specialty-residencial-maintenance',
                    'slugEs' => 'contratistas-por-especialidad-mantenimiento-residencial',
                ),
            641 =>
                array (
                    'en' => 'Contractor- Maintenance',
                    'es' => 'Contratistas-Mantenimiento',
                    'slugEn' => 'contractor-maintenance',
                    'slugEs' => 'contratistas-mantenimiento',
                ),
            642 =>
                array (
                    'en' => 'Contractors',
                    'es' => 'Contratistas',
                    'slugEn' => 'contractors',
                    'slugEs' => 'contratistas',
                ),
            643 =>
                array (
                    'en' => 'Contractors - Communications',
                    'es' => 'Contratistas Por Especialidad - Comunicación',
                    'slugEn' => 'contractors-communications',
                    'slugEs' => 'contratistas-por-especialidad-comunicacion',
                ),
            644 =>
                array (
                    'en' => 'Contractors - Equipment & Supplies',
                    'es' => 'Contratistas-Efectos Y Equipo',
                    'slugEn' => 'contractors-equipment-supplies',
                    'slugEs' => 'contratistas-efectos-y-equipo',
                ),
            645 =>
                array (
                    'en' => 'Contractors - Equipment & Supplies - Rental',
                    'es' => 'Contratistas-Efectos Y Equipo-Alquiler',
                    'slugEn' => 'contractors-equipment-supplies-rental',
                    'slugEs' => 'contratistas-efectos-y-equipo-alquiler',
                ),
            646 =>
                array (
                    'en' => 'Contractors - Maintenance Services - Commercial',
                    'es' => 'Contratistas Por Especialidad - Mantenimiento Comercial',
                    'slugEn' => 'contractors-maintenance-services-commercial',
                    'slugEs' => 'contratistas-por-especialidad-mantenimiento-comercial',
                ),
            647 =>
                array (
                    'en' => 'Contractors - Maintenance Services - Industrial',
                    'es' => 'Contratistas Por Especialidad - Mantenimiento Industrial',
                    'slugEn' => 'contractors-maintenance-services-industrial',
                    'slugEs' => 'contratistas-por-especialidad-mantenimiento-industrial',
                ),
            648 =>
                array (
                    'en' => 'Contractors - Maritime',
                    'es' => 'Contratistas Por Especialidad - Marinos',
                    'slugEn' => 'contractors-maritime',
                    'slugEs' => 'contratistas-por-especialidad-marinos',
                ),
            649 =>
                array (
                    'en' => 'Contractors - Remodeling',
                    'es' => 'Contratistas Por Especialidad - Remodelación',
                    'slugEn' => 'contractors-remodeling',
                    'slugEs' => 'contratistas-por-especialidad-remodelacion',
                ),
            650 =>
                array (
                    'en' => 'Contractors By Specialty - Plumbing',
                    'es' => 'Contratistas por Especialidad - Plomería',
                    'slugEn' => 'contractors-by-specialty-plumbing',
                    'slugEs' => 'contratistas-por-especialidad-plomeria',
                ),
            651 =>
                array (
                    'en' => 'Contractors-Brickwork',
                    'es' => 'Contratistas Por Especialidad - Albañilería',
                    'slugEn' => 'contractors-brickwork',
                    'slugEs' => 'contratistas-por-especialidad-albanileria',
                ),
            652 =>
                array (
                    'en' => 'Contractors-Bridges',
                    'es' => 'Contratistas Por Especialidad - Puentes',
                    'slugEn' => 'contractors-bridges',
                    'slugEs' => 'contratistas-por-especialidad-puentes',
                ),
            653 =>
                array (
                    'en' => 'Contractors-Ceilings',
                    'es' => 'Contratistas Por Especialidad - Techos',
                    'slugEn' => 'contractors-ceilings',
                    'slugEs' => 'contratistas-por-especialidad-techos',
                ),
            654 =>
                array (
                    'en' => 'Contractors-Electric',
                    'es' => 'Contratistas Por Especialidad - Eléctricos',
                    'slugEn' => 'contractors-electric',
                    'slugEs' => 'contratistas-por-especialidad-electricos',
                ),
            655 =>
                array (
                    'en' => 'Contractors-Interiors',
                    'es' => 'Contratistas Por Especialidad - Interiores',
                    'slugEn' => 'contractors-interiors',
                    'slugEs' => 'contratistas-por-especialidad-interiores',
                ),
            656 =>
                array (
                    'en' => 'Contractors-Mechanics',
                    'es' => 'Contratistas Por Especialidad - Mecánicos',
                    'slugEn' => 'contractors-mechanics',
                    'slugEs' => 'contratistas-por-especialidad-mecanicos',
                ),
            657 =>
                array (
                    'en' => 'Contractors-Pavement',
                    'es' => 'Contratistas Por Especialidad - Pavimentación',
                    'slugEn' => 'contractors-pavement',
                    'slugEs' => 'contratistas-por-especialidad-pavimentacion',
                ),
            658 =>
                array (
                    'en' => 'Contractor-Sports Facilities',
                    'es' => 'Contratistas-Facilidades Deportivas',
                    'slugEn' => 'contractor-sports-facilities',
                    'slugEs' => 'contratistas-facilidades-deportivas',
                ),
            659 =>
                array (
                    'en' => 'Control and Weight Loss',
                    'es' => 'Control y Pérdida de Peso',
                    'slugEn' => 'control-and-weight-loss',
                    'slugEs' => 'control-y-perdida-de-peso',
                ),
            660 =>
                array (
                    'en' => 'Conventions - Services',
                    'es' => 'Convenciones-Servicio',
                    'slugEn' => 'conventions-services',
                    'slugEs' => 'convenciones-servicio',
                ),
            661 =>
                array (
                    'en' => 'Convents',
                    'es' => 'Conventos',
                    'slugEn' => 'convents',
                    'slugEs' => 'conventos',
                ),
            662 =>
                array (
                    'en' => 'Conveyors',
                    'es' => 'Conveyors (Transportadores)',
                    'slugEn' => 'conveyors',
                    'slugEs' => 'conveyors-transportadores',
                ),
            663 =>
                array (
                    'en' => 'Cookies & Crackers',
                    'es' => 'Galletas',
                    'slugEn' => 'cookies-crackers',
                    'slugEs' => 'galletas',
                ),
            664 =>
                array (
                    'en' => 'Cooking Utensils',
                    'es' => 'Cocina-Utensilios',
                    'slugEn' => 'cooking-utensils',
                    'slugEs' => 'cocina-utensilios',
                ),
            665 =>
                array (
                    'en' => 'Cooling - Air Conditioning',
                    'es' => 'Refrigeración - Aire Acondicionado',
                    'slugEn' => 'cooling-air-conditioning',
                    'slugEs' => 'refrigeracion-aire-acondicionado',
                ),
            666 =>
                array (
                    'en' => 'Cooling Towers - Water',
                    'es' => 'Agua Potable',
                    'slugEn' => 'cooling-towers-water',
                    'slugEs' => 'agua-potable',
                ),
            667 =>
                array (
                    'en' => 'Cooperatives',
                    'es' => 'Cooperativas',
                    'slugEn' => 'cooperatives',
                    'slugEs' => 'cooperativas',
                ),
            668 =>
                array (
                    'en' => 'Coopharma Pharmacies',
                    'es' => 'Farmacias Coopharma',
                    'slugEn' => 'coopharma-pharmacies',
                    'slugEs' => 'farmacias-coopharma',
                ),
            669 =>
                array (
                    'en' => 'Copies',
                    'es' => 'Copias',
                    'slugEn' => 'copies',
                    'slugEs' => 'copias',
                ),
            670 =>
                array (
                    'en' => 'Copies - Blueprints',
                    'es' => 'Estampados',
                    'slugEn' => 'copies-blueprints',
                    'slugEs' => 'estampados',
                ),
            671 =>
                array (
                    'en' => 'Copies - Color',
                    'es' => 'Copias-Color',
                    'slugEn' => 'copies-color',
                    'slugEs' => 'copias-color',
                ),
            672 =>
                array (
                    'en' => 'Copies - Digital',
                    'es' => 'Copias - Digital',
                    'slugEn' => 'copies-digital',
                    'slugEs' => 'copias-digital',
                ),
            673 =>
                array (
                    'en' => 'Copying Machines',
                    'es' => 'Copiadoras',
                    'slugEn' => 'copying-machines',
                    'slugEs' => 'copiadoras',
                ),
            674 =>
                array (
                    'en' => 'Copying Machines - Equipment & Supplies',
                    'es' => 'Copiadoras-Efectos Y Equipo',
                    'slugEn' => 'copying-machines-equipment-supplies',
                    'slugEs' => 'copiadoras-efectos-y-equipo',
                ),
            675 =>
                array (
                    'en' => 'Copying Machines - Printer',
                    'es' => 'Copiadoras - Imprentas',
                    'slugEn' => 'copying-machines-printer',
                    'slugEs' => 'copiadoras-imprentas',
                ),
            676 =>
                array (
                    'en' => 'Copying Machines - Repair',
                    'es' => 'Copiadoras-Reparación',
                    'slugEn' => 'copying-machines-repair',
                    'slugEs' => 'copiadoras-reparacion',
                ),
            677 =>
                array (
                    'en' => 'Corporate Activities',
                    'es' => 'Actividades Corporativas',
                    'slugEn' => 'corporate-activities',
                    'slugEs' => 'actividades-corporativas',
                ),
            678 =>
                array (
                    'en' => 'Cosmetic Dentists',
                    'es' => 'Dentistas Estética',
                    'slugEn' => 'cosmetic-dentists',
                    'slugEs' => 'dentistas-estetica',
                ),
            679 =>
                array (
                    'en' => 'Cosmetic Treatments',
                    'es' => 'Tratamientos Cosméticos',
                    'slugEn' => 'cosmetic-treatments',
                    'slugEs' => 'tratamientos-cosmeticos',
                ),
            680 =>
                array (
                    'en' => 'Cosmetics',
                    'es' => 'Cosméticos',
                    'slugEn' => 'cosmetics',
                    'slugEs' => 'cosmeticos',
                ),
            681 =>
                array (
                    'en' => 'Cosmetics & Perfume',
                    'es' => 'Perfumerías Y Cosméticos',
                    'slugEn' => 'cosmetics-perfume',
                    'slugEs' => 'perfumerias-y-cosmeticos',
                ),
            682 =>
                array (
                    'en' => 'Costumes',
                    'es' => 'Disfraces',
                    'slugEn' => 'costumes',
                    'slugEs' => 'disfraces',
                ),
            683 =>
                array (
                    'en' => 'Counseling - Career',
                    'es' => 'Educativa-Orientación',
                    'slugEn' => 'counseling-career',
                    'slugEs' => 'educativa-orientacion',
                ),
            684 =>
                array (
                    'en' => 'Countertops - Manufacture',
                    'es' => 'Topes-Fábricas',
                    'slugEn' => 'countertops-manufacture',
                    'slugEs' => 'topes-fabricas',
                ),
            685 =>
                array (
                    'en' => 'Countertops-Granite',
                    'es' => 'Topes-Granito',
                    'slugEn' => 'countertops-granite',
                    'slugEs' => 'topes-granito',
                ),
            686 =>
                array (
                    'en' => 'Courier',
                    'es' => 'Mensajeros',
                    'slugEn' => 'courier',
                    'slugEs' => 'mensajeros',
                ),
            687 =>
                array (
                    'en' => 'Courts',
                    'es' => 'Deportes - Tennis-Canchas',
                    'slugEn' => 'courts',
                    'slugEs' => 'deportes-tennis-canchas',
                ),
            688 =>
                array (
                    'en' => 'CPR - Courses',
                    'es' => 'Cursos - CPR',
                    'slugEn' => 'cpr-courses',
                    'slugEs' => 'cursos-cpr',
                ),
            689 =>
                array (
                    'en' => 'Craft Beers',
                    'es' => 'Cervezas Artesanales',
                    'slugEn' => 'craft-beers',
                    'slugEs' => 'cervezas-artesanales',
                ),
            690 =>
                array (
                    'en' => 'Crafts',
                    'es' => 'Manualidades',
                    'slugEn' => 'crafts',
                    'slugEs' => 'manualidades',
                ),
            691 =>
                array (
                    'en' => 'Craniosacral Therapy',
                    'es' => 'Terapia Craneosacral',
                    'slugEn' => 'craniosacral-therapy',
                    'slugEs' => 'terapia-craneosacral',
                ),
            692 =>
                array (
                    'en' => 'Creative Cuisine',
                    'es' => 'Cocina Creativa',
                    'slugEn' => 'creative-cuisine',
                    'slugEs' => 'cocina-creativa',
                ),
            693 =>
                array (
                    'en' => 'Credit - Information',
                    'es' => 'Crédito-Información',
                    'slugEn' => 'credit-information',
                    'slugEs' => 'credito-informacion',
                ),
            694 =>
                array (
                    'en' => 'Credit Cards',
                    'es' => 'Crédito-Tarjetas',
                    'slugEn' => 'credit-cards',
                    'slugEs' => 'credito-tarjetas',
                ),
            695 =>
                array (
                    'en' => 'Credit Restauration',
                    'es' => 'Crédito - Restauración',
                    'slugEn' => 'credit-restauration',
                    'slugEs' => 'credito-restauracion',
                ),
            696 =>
                array (
                    'en' => 'Cremation',
                    'es' => 'Cremación',
                    'slugEn' => 'cremation',
                    'slugEs' => 'cremacion',
                ),
            697 =>
                array (
                    'en' => 'Cremation - Services',
                    'es' => 'Cremación-Servicios',
                    'slugEn' => 'cremation-services',
                    'slugEs' => 'cremacion-servicios',
                ),
            698 =>
                array (
                    'en' => 'Creole Cuisine Restaurant',
                    'es' => 'Restaurante Comida Criolla',
                    'slugEn' => 'creole-cuisine-restaurant',
                    'slugEs' => 'restaurante-comida-criolla',
                ),
            699 =>
                array (
                    'en' => 'Crepes',
                    'es' => 'Crepes',
                    'slugEn' => 'crepes',
                    'slugEs' => 'crepes',
                ),
            700 =>
                array (
                    'en' => 'Crossfit',
                    'es' => 'Crossfit',
                    'slugEn' => 'crossfit',
                    'slugEs' => 'crossfit',
                ),
            701 =>
                array (
                    'en' => 'Crosstraining',
                    'es' => 'Crosstraining',
                    'slugEn' => 'crosstraining',
                    'slugEs' => 'crosstraining',
                ),
            702 =>
                array (
                    'en' => 'Cruises',
                    'es' => 'Cruceros',
                    'slugEn' => 'cruises',
                    'slugEs' => 'cruceros',
                ),
            703 =>
                array (
                    'en' => 'CT Scan',
                    'es' => 'CT Scan',
                    'slugEn' => 'ct-scan',
                    'slugEs' => 'ct-scan',
                ),
            704 =>
                array (
                    'en' => 'Cubicles',
                    'es' => 'Cubiculos',
                    'slugEn' => 'cubicles',
                    'slugEs' => 'cubiculos',
                ),
            705 =>
                array (
                    'en' => 'Cultural Events',
                    'es' => 'Eventos Artísticos',
                    'slugEn' => 'cultural-events',
                    'slugEs' => 'eventos-artisticos',
                ),
            706 =>
                array (
                    'en' => 'Cupcakes',
                    'es' => 'Cupcakes',
                    'slugEn' => 'cupcakes',
                    'slugEs' => 'cupcakes',
                ),
            707 =>
                array (
                    'en' => 'Currency - Exchange',
                    'es' => 'Dinero Extranjero-Cambio',
                    'slugEn' => 'currency-exchange',
                    'slugEs' => 'dinero-extranjero-cambio',
                ),
            708 =>
                array (
                    'en' => 'Curtain Repair',
                    'es' => 'Reparación de Cortinas',
                    'slugEn' => 'curtain-repair',
                    'slugEs' => 'reparacion-de-cortinas',
                ),
            709 =>
                array (
                    'en' => 'Curtain-Cleaning',
                    'es' => 'Cortinas-Limpieza',
                    'slugEn' => 'curtain-cleaning',
                    'slugEs' => 'cortinas-limpieza',
                ),
            710 =>
                array (
                    'en' => 'Curtains',
                    'es' => 'Cortinas',
                    'slugEn' => 'curtains',
                    'slugEs' => 'cortinas',
                ),
            711 =>
                array (
                    'en' => 'Curtains - Outdoor',
                    'es' => 'Cortinas - Exterior',
                    'slugEn' => 'curtains-outdoor',
                    'slugEs' => 'cortinas-exterior',
                ),
            712 =>
                array (
                    'en' => 'Curtains - Security - Roll Ups',
                    'es' => 'Cortinas - Seguridad - Roll Ups',
                    'slugEn' => 'curtains-security-roll-ups',
                    'slugEs' => 'cortinas-seguridad-roll-ups',
                ),
            713 =>
                array (
                    'en' => 'Curtains Interiors',
                    'es' => 'Cortinas de Interiores',
                    'slugEn' => 'curtains-interiors',
                    'slugEs' => 'cortinas-de-interiores',
                ),
            714 =>
                array (
                    'en' => 'Custard - Manufacture',
                    'es' => 'Flanes-Fábricas',
                    'slugEn' => 'custard-manufacture',
                    'slugEs' => 'flanes-fabricas',
                ),
            715 =>
                array (
                    'en' => 'Custom Medals',
                    'es' => 'Medallas Personalizadas',
                    'slugEn' => 'custom-medals',
                    'slugEs' => 'medallas-personalizadas',
                ),
            716 =>
                array (
                    'en' => 'Customs',
                    'es' => 'Aduanas-Agentes',
                    'slugEn' => 'customs',
                    'slugEs' => 'aduanas-agentes',
                ),
            717 =>
                array (
                    'en' => 'Dairies',
                    'es' => 'Lecherías',
                    'slugEn' => 'dairies',
                    'slugEs' => 'lecherias',
                ),
            718 =>
                array (
                    'en' => 'Dance - Accessories',
                    'es' => 'Accesorios de baile',
                    'slugEn' => 'dance-accessories',
                    'slugEs' => 'accesorios-de-baile',
                ),
            719 =>
                array (
                    'en' => 'Dance / Classes',
                    'es' => 'Baile/Clases',
                    'slugEn' => 'dance-classes',
                    'slugEs' => 'baile-clases',
                ),
            720 =>
                array (
                    'en' => 'Dance Clothing',
                    'es' => 'Ropa de Baile',
                    'slugEn' => 'dance-clothing',
                    'slugEs' => 'ropa-de-baile',
                ),
            721 =>
                array (
                    'en' => 'Dance Lessons',
                    'es' => 'Clases de Baile',
                    'slugEn' => 'dance-lessons',
                    'slugEs' => 'clases-de-baile',
                ),
            722 =>
                array (
                    'en' => 'Data Processing',
                    'es' => 'Data Processing (Procesamiento De Data)-Servicio',
                    'slugEn' => 'data-processing',
                    'slugEs' => 'data-processing-procesamiento-de-data-servicio',
                ),
            723 =>
                array (
                    'en' => 'Data Processing - Equipment & Supplies',
                    'es' => 'Data Processing (Procesamiento De Data)-Efectos Y Equipos',
                    'slugEn' => 'data-processing-equipment-supplies',
                    'slugEs' => 'data-processing-procesamiento-de-data-efectos-y-equipos',
                ),
            724 =>
                array (
                    'en' => 'Day Care',
                    'es' => 'Cuido',
                    'slugEn' => 'day-care',
                    'slugEs' => 'cuido',
                ),
            725 =>
                array (
                    'en' => 'Day Care - Centers',
                    'es' => 'Cuidado Diurno-Centros',
                    'slugEn' => 'day-care-centers',
                    'slugEs' => 'cuidado-diurno-centros',
                ),
            726 =>
                array (
                    'en' => 'Day Care - Centers - Special Education',
                    'es' => 'Cuidado Diurno - Centros - Educación Especial',
                    'slugEn' => 'day-care-centers-special-education',
                    'slugEs' => 'cuidado-diurno-centros-educacion-especial',
                ),
            727 =>
                array (
                    'en' => 'Dealer Nissan',
                    'es' => 'Dealer Nissan',
                    'slugEn' => 'dealer-nissan',
                    'slugEs' => 'dealer-nissan',
                ),
            728 =>
                array (
                    'en' => 'Dealers',
                    'es' => 'Dealers',
                    'slugEn' => 'dealers',
                    'slugEs' => 'dealers',
                ),
            729 =>
                array (
                    'en' => 'Decals',
                    'es' => 'Calcomanías',
                    'slugEn' => 'decals',
                    'slugEs' => 'calcomanias',
                ),
            730 =>
                array (
                    'en' => 'Decoration',
                    'es' => 'Decoración',
                    'slugEn' => 'decoration',
                    'slugEs' => 'decoracion',
                ),
            731 =>
                array (
                    'en' => 'Decoration - Materials & Supplies',
                    'es' => 'Decoración-Materiales Y Accesorios',
                    'slugEn' => 'decoration-materials-supplies',
                    'slugEs' => 'decoracion-materiales-y-accesorios',
                ),
            732 =>
                array (
                    'en' => 'Decoration - Parties & Conventions',
                    'es' => 'Decoración-Fiestas Y Convenciones',
                    'slugEn' => 'decoration-parties-conventions',
                    'slugEs' => 'decoracion-fiestas-y-convenciones',
                ),
            733 =>
                array (
                    'en' => 'Decorative Fabrics',
                    'es' => 'Telas Decorativas',
                    'slugEn' => 'decorative-fabrics',
                    'slugEs' => 'telas-decorativas',
                ),
            734 =>
                array (
                    'en' => 'Decorative Paper',
                    'es' => 'Papel Decorativo',
                    'slugEn' => 'decorative-paper',
                    'slugEs' => 'papel-decorativo',
                ),
            735 =>
                array (
                    'en' => 'Deep Well - Constractors',
                    'es' => 'Pozos Profundos-Contratistas',
                    'slugEn' => 'deep-well-constractors',
                    'slugEs' => 'pozos-profundos-contratistas',
                ),
            736 =>
                array (
                    'en' => 'Delicatessen',
                    'es' => 'Delicatessen',
                    'slugEn' => 'delicatessen',
                    'slugEs' => 'delicatessen',
                ),
            737 =>
                array (
                    'en' => 'Delivery Services - Documents & Boxes',
                    'es' => 'Delivery-Servicios - Documentos Y Cajas',
                    'slugEn' => 'delivery-services-documents-boxes',
                    'slugEs' => 'delivery-servicios-documentos-y-cajas',
                ),
            738 =>
                array (
                    'en' => 'Dementia',
                    'es' => 'Médicos Especialistas - Demencia',
                    'slugEn' => 'dementia',
                    'slugEs' => 'medicos-especialistas-demencia',
                ),
            739 =>
                array (
                    'en' => 'Dental - Equipment & Supplies',
                    'es' => 'Dentales-Efectos Y Equipo',
                    'slugEn' => 'dental-equipment-supplies',
                    'slugEs' => 'dentales-efectos-y-equipo',
                ),
            740 =>
                array (
                    'en' => 'Dental - Services',
                    'es' => 'Dentales-Servicios',
                    'slugEn' => 'dental-services',
                    'slugEs' => 'dentales-servicios',
                ),
            741 =>
                array (
                    'en' => 'Dental Clinic',
                    'es' => 'Clínica Dental',
                    'slugEn' => 'dental-clinic',
                    'slugEs' => 'clinica-dental',
                ),
            742 =>
                array (
                    'en' => 'Dentistry',
                    'es' => 'Odontología',
                    'slugEn' => 'dentistry',
                    'slugEs' => 'odontologia',
                ),
            743 =>
                array (
                    'en' => 'Dentists',
                    'es' => 'Dentistas',
                    'slugEn' => 'dentists',
                    'slugEs' => 'dentistas',
                ),
            744 =>
                array (
                    'en' => 'Dentists - Endodontics',
                    'es' => 'Dentistas Especialistas - Endodoncia',
                    'slugEn' => 'dentists-endodontics',
                    'slugEs' => 'dentistas-especialistas-endodoncia',
                ),
            745 =>
                array (
                    'en' => 'Dentists - Pedodontics',
                    'es' => 'Dentistas - Pedodoncia',
                    'slugEn' => 'dentists-pedodontics',
                    'slugEs' => 'dentistas-pedodoncia',
                ),
            746 =>
                array (
                    'en' => 'Dentists - Periodontics',
                    'es' => 'Dentistas Especialistas - Periodoncia',
                    'slugEn' => 'dentists-periodontics',
                    'slugEs' => 'dentistas-especialistas-periodoncia',
                ),
            747 =>
                array (
                    'en' => 'Dentists - Prosthodontics',
                    'es' => 'Dentistas Especialistas - Prostodoncia',
                    'slugEn' => 'dentists-prosthodontics',
                    'slugEs' => 'dentistas-especialistas-prostodoncia',
                ),
            748 =>
                array (
                    'en' => 'Dentists - Specialists - Implants',
                    'es' => 'Dentistas Especialistas - Implantes',
                    'slugEn' => 'dentists-specialists-implants',
                    'slugEs' => 'dentistas-especialistas-implantes',
                ),
            749 =>
                array (
                    'en' => 'Dentists - Specialists - Pedriatic',
                    'es' => 'Dentistas Especialistas - Dentistas Pediatricos',
                    'slugEn' => 'dentists-specialists-pedriatic',
                    'slugEs' => 'dentistas-especialistas-dentistas-pediatricos',
                ),
            750 =>
                array (
                    'en' => 'Dentists Specialists - Oral & Maxillofacial Surgery',
                    'es' => 'Dentistas Especialistas - Cirugía Oral Y Maxilofacial',
                    'slugEn' => 'dentists-specialists-oral-maxillofacial-surgery',
                    'slugEs' => 'dentistas-especialistas-cirugia-oral-y-maxilofacial',
                ),
            751 =>
                array (
                    'en' => 'Dentists Specialists - Orthodontics Braces',
                    'es' => 'Dentistas Especialistas - Ortodoncia Braces',
                    'slugEn' => 'dentists-specialists-orthodontics-braces',
                    'slugEs' => 'dentistas-especialistas-ortodoncia-braces',
                ),
            752 =>
                array (
                    'en' => 'Department Stores',
                    'es' => 'Tiendas Por Departamentos',
                    'slugEn' => 'department-stores',
                    'slugEs' => 'tiendas-por-departamentos',
                ),
            753 =>
                array (
                    'en' => 'Design - Computarized - Construction',
                    'es' => 'Dibujo-Computadorizado-Construcción',
                    'slugEn' => 'design-computarized-construction',
                    'slugEs' => 'dibujo-computadorizado-construccion',
                ),
            754 =>
                array (
                    'en' => 'Design and construction of gardens',
                    'es' => 'Diseño y Construcción de Jardines',
                    'slugEn' => 'design-and-construction-of-gardens',
                    'slugEs' => 'diseno-y-construccion-de-jardines',
                ),
            755 =>
                array (
                    'en' => 'Designers',
                    'es' => 'Diseñadores',
                    'slugEn' => 'designers',
                    'slugEs' => 'disenadores',
                ),
            756 =>
                array (
                    'en' => 'Designers - Industrial',
                    'es' => 'Diseñadores - Industriales',
                    'slugEn' => 'designers-industrial',
                    'slugEs' => 'disenadores-industriales',
                ),
            757 =>
                array (
                    'en' => 'Desserts',
                    'es' => 'Postres',
                    'slugEn' => 'desserts',
                    'slugEs' => 'postres',
                ),
            758 =>
                array (
                    'en' => 'Detective - Agencies',
                    'es' => 'Detectives-Agencias',
                    'slugEn' => 'detective-agencies',
                    'slugEs' => 'detectives-agencias',
                ),
            759 =>
                array (
                    'en' => 'Detective - Private',
                    'es' => 'Detectives Privados',
                    'slugEn' => 'detective-private',
                    'slugEs' => 'detectives-privados',
                ),
            760 =>
                array (
                    'en' => 'Detectives',
                    'es' => 'Detectives',
                    'slugEn' => 'detectives',
                    'slugEs' => 'detectives',
                ),
            761 =>
                array (
                    'en' => 'Detergents',
                    'es' => 'Detergentes',
                    'slugEn' => 'detergents',
                    'slugEs' => 'detergentes',
                ),
            762 =>
                array (
                    'en' => 'Developers',
                    'es' => 'Desarrolladores',
                    'slugEn' => 'developers',
                    'slugEs' => 'desarrolladores',
                ),
            763 =>
                array (
                    'en' => 'Development Center - Pre-School Education',
                    'es' => 'Centro de Desarrollo - Educación Pre-Escolar',
                    'slugEn' => 'development-center-pre-school-education',
                    'slugEs' => 'centro-de-desarrollo-educacion-pre-escolar',
                ),
            764 =>
                array (
                    'en' => 'Diabetic Foot Shoes',
                    'es' => 'Zapatos para Pie Diabético',
                    'slugEn' => 'diabetic-foot-shoes',
                    'slugEs' => 'zapatos-para-pie-diabetico',
                ),
            765 =>
                array (
                    'en' => 'Diapers - Adults',
                    'es' => 'Pañales - adultos',
                    'slugEn' => 'diapers-adults',
                    'slugEs' => 'panales-adultos',
                ),
            766 =>
                array (
                    'en' => 'Die Makers - Manufacture',
                    'es' => 'Troqueles-Fábricas',
                    'slugEn' => 'die-makers-manufacture',
                    'slugEs' => 'troqueles-fabricas',
                ),
            767 =>
                array (
                    'en' => 'Diesel Fuel',
                    'es' => 'Diesel',
                    'slugEn' => 'diesel-fuel',
                    'slugEs' => 'diesel',
                ),
            768 =>
                array (
                    'en' => 'Diet - Services',
                    'es' => 'Dietas-Servicios',
                    'slugEn' => 'diet-services',
                    'slugEs' => 'dietas-servicios',
                ),
            769 =>
                array (
                    'en' => 'Digital Printing',
                    'es' => 'Impresión Digital',
                    'slugEn' => 'digital-printing',
                    'slugEs' => 'impresion-digital',
                ),
            770 =>
                array (
                    'en' => 'Digitalization',
                    'es' => 'Digitalización',
                    'slugEn' => 'digitalization',
                    'slugEs' => 'digitalizacion',
                ),
            771 =>
                array (
                    'en' => 'Diploma - Diploma Holder',
                    'es' => 'Diplomas - Porta Diplomas',
                    'slugEn' => 'diploma-diploma-holder',
                    'slugEs' => 'diplomas-porta-diplomas',
                ),
            772 =>
                array (
                    'en' => 'Disability Insurance',
                    'es' => 'Seguros por Incapacidad',
                    'slugEn' => 'disability-insurance',
                    'slugEs' => 'seguros-por-incapacidad',
                ),
            773 =>
                array (
                    'en' => 'Disc Jockey',
                    'es' => 'Disc Jockey',
                    'slugEn' => 'disc-jockey',
                    'slugEs' => 'disc-jockey',
                ),
            774 =>
                array (
                    'en' => 'Discotheques',
                    'es' => 'Discotecas',
                    'slugEn' => 'discotheques',
                    'slugEs' => 'discotecas',
                ),
            775 =>
                array (
                    'en' => 'Discount Stores',
                    'es' => 'Discount Stores (Tiendas De Descuento)',
                    'slugEn' => 'discount-stores',
                    'slugEs' => 'discount-stores-tiendas-de-descuento',
                ),
            776 =>
                array (
                    'en' => 'Dish',
                    'es' => 'Dish',
                    'slugEn' => 'dish',
                    'slugEs' => 'dish',
                ),
            777 =>
                array (
                    'en' => 'Dish Network',
                    'es' => 'Dish Network',
                    'slugEn' => 'dish-network',
                    'slugEs' => 'dish-network',
                ),
            778 =>
                array (
                    'en' => 'Dish Puerto Rico',
                    'es' => 'Dish Puerto Rico',
                    'slugEn' => 'dish-puerto-rico',
                    'slugEs' => 'dish-puerto-rico',
                ),
            779 =>
                array (
                    'en' => 'Dispensaries',
                    'es' => 'Dispensarios',
                    'slugEn' => 'dispensaries',
                    'slugEs' => 'dispensarios',
                ),
            780 =>
                array (
                    'en' => 'Display - Racks',
                    'es' => 'Góndolas',
                    'slugEn' => 'display-racks',
                    'slugEs' => 'gondolas',
                ),
            781 =>
                array (
                    'en' => 'Display Windows',
                    'es' => 'Vitrinas',
                    'slugEn' => 'display-windows',
                    'slugEs' => 'vitrinas',
                ),
            782 =>
                array (
                    'en' => 'Distribution',
                    'es' => 'Distribución',
                    'slugEn' => 'distribution',
                    'slugEs' => 'distribucion',
                ),
            783 =>
                array (
                    'en' => 'Divers - Equipment & Supplies',
                    'es' => 'Buzos-Efectos Y Equipo',
                    'slugEn' => 'divers-equipment-supplies',
                    'slugEs' => 'buzos-efectos-y-equipo',
                ),
            784 =>
                array (
                    'en' => 'Divers - Service',
                    'es' => 'Buzos-Servicio',
                    'slugEn' => 'divers-service',
                    'slugEs' => 'buzos-servicio',
                ),
            785 =>
                array (
                    'en' => 'Divisions-Office',
                    'es' => 'Divisiones-Oficina',
                    'slugEn' => 'divisions-office',
                    'slugEs' => 'divisiones-oficina',
                ),
            786 =>
                array (
                    'en' => 'Divorce Lawyer',
                    'es' => 'Abogados Divorcios',
                    'slugEn' => 'divorce-lawyer',
                    'slugEs' => 'abogados-divorcios',
                ),
            787 =>
                array (
                    'en' => 'Doctors',
                    'es' => 'Médicos',
                    'slugEn' => 'doctors',
                    'slugEs' => 'medicos',
                ),
            788 =>
                array (
                    'en' => 'Doctors - Alzheimer',
                    'es' => 'Médicos Especialistas - Alzheimer',
                    'slugEn' => 'doctors-alzheimer',
                    'slugEs' => 'medicos-especialistas-alzheimer',
                ),
            789 =>
                array (
                    'en' => 'Doctors - General Medicine',
                    'es' => 'Médicos - Medicina General',
                    'slugEn' => 'doctors-general-medicine',
                    'slugEs' => 'medicos-medicina-general',
                ),
            790 =>
                array (
                    'en' => 'Doctors - Legal Representation',
                    'es' => 'Médicos - Representación Legal',
                    'slugEn' => 'doctors-legal-representation',
                    'slugEs' => 'medicos-representacion-legal',
                ),
            791 =>
                array (
                    'en' => 'Doctors Advanced Laparoscopic Surgery',
                    'es' => 'Médicos Cirugía Laparoscopía Avanzada',
                    'slugEn' => 'doctors-advanced-laparoscopic-surgery',
                    'slugEs' => 'medicos-cirugia-laparoscopia-avanzada',
                ),
            792 =>
                array (
                    'en' => 'Doctors General Surgery',
                    'es' => 'Médicos Cirugía General',
                    'slugEn' => 'doctors-general-surgery',
                    'slugEs' => 'medicos-cirugia-general',
                ),
            793 =>
                array (
                    'en' => 'Doctors Minimally Invasive Surgery',
                    'es' => 'Médicos Cirugía Mínimamente Invasiva',
                    'slugEn' => 'doctors-minimally-invasive-surgery',
                    'slugEs' => 'medicos-cirugia-minimamente-invasiva',
                ),
            794 =>
                array (
                    'en' => 'Doctors-Specialist',
                    'es' => 'Medicos-Especialistas',
                    'slugEn' => 'doctors-specialist',
                    'slugEs' => 'medicos-especialistas',
                ),
            795 =>
                array (
                    'en' => 'Documents-Digitalize',
                    'es' => 'Documentos-Digitalización',
                    'slugEn' => 'documents-digitalize',
                    'slugEs' => 'documentos-digitalizacion',
                ),
            796 =>
                array (
                    'en' => 'Dogs - Guard',
                    'es' => 'Perros Guardianes',
                    'slugEn' => 'dogs-guard',
                    'slugEs' => 'perros-guardianes',
                ),
            797 =>
                array (
                    'en' => 'Dogs - Security',
                    'es' => 'Perros Seguridad',
                    'slugEn' => 'dogs-security',
                    'slugEs' => 'perros-seguridad',
                ),
            798 =>
                array (
                    'en' => 'Domestic Supplies',
                    'es' => 'Hogar-Efectos',
                    'slugEn' => 'domestic-supplies',
                    'slugEs' => 'hogar-efectos',
                ),
            799 =>
                array (
                    'en' => 'Door - Repair',
                    'es' => 'Reparación - Puertas',
                    'slugEn' => 'door-repair',
                    'slugEs' => 'reparacion-puertas',
                ),
            800 =>
                array (
                    'en' => 'Doors',
                    'es' => 'Puertas',
                    'slugEn' => 'doors',
                    'slugEs' => 'puertas',
                ),
            801 =>
                array (
                    'en' => 'Doors - Aluminum',
                    'es' => 'Puertas-Aluminio',
                    'slugEn' => 'doors-aluminum',
                    'slugEs' => 'puertas-aluminio',
                ),
            802 =>
                array (
                    'en' => 'Doors - Automatic Closers',
                    'es' => 'Puertas-Operadores Automáticos',
                    'slugEn' => 'doors-automatic-closers',
                    'slugEs' => 'puertas-operadores-automaticos',
                ),
            803 =>
                array (
                    'en' => 'Doors - Closet',
                    'es' => 'Puertas-Closets (Roperos)',
                    'slugEn' => 'doors-closet',
                    'slugEs' => 'puertas-closets-roperos',
                ),
            804 =>
                array (
                    'en' => 'Doors - Folding',
                    'es' => 'Puertas-Plegadizas',
                    'slugEn' => 'doors-folding',
                    'slugEs' => 'puertas-plegadizas',
                ),
            805 =>
                array (
                    'en' => 'Doors - Garage',
                    'es' => 'Puertas-Garajes',
                    'slugEn' => 'doors-garage',
                    'slugEs' => 'puertas-garajes',
                ),
            806 =>
                array (
                    'en' => 'Doors - Glass',
                    'es' => 'Puertas-Cristal',
                    'slugEn' => 'doors-glass',
                    'slugEs' => 'puertas-cristal',
                ),
            807 =>
                array (
                    'en' => 'Doors - Manufacture',
                    'es' => 'Puertas-Fábricas',
                    'slugEn' => 'doors-manufacture',
                    'slugEs' => 'puertas-fabricas',
                ),
            808 =>
                array (
                    'en' => 'Doors - Mirrors',
                    'es' => 'Espejos',
                    'slugEn' => 'doors-mirrors',
                    'slugEs' => 'espejos',
                ),
            809 =>
                array (
                    'en' => 'Doors - Roller',
                    'es' => 'Puertas-Enrollables',
                    'slugEn' => 'doors-roller',
                    'slugEs' => 'puertas-enrollables',
                ),
            810 =>
                array (
                    'en' => 'Doors - Security',
                    'es' => 'Puertas-Seguridad',
                    'slugEn' => 'doors-security',
                    'slugEs' => 'puertas-seguridad',
                ),
            811 =>
                array (
                    'en' => 'Doors - Steel',
                    'es' => 'Puertas-Acero',
                    'slugEn' => 'doors-steel',
                    'slugEs' => 'puertas-acero',
                ),
            812 =>
                array (
                    'en' => 'Doors - Wood',
                    'es' => 'Puertas-Madera',
                    'slugEn' => 'doors-wood',
                    'slugEs' => 'puertas-madera',
                ),
            813 =>
                array (
                    'en' => 'Doors and Windows - Repairs',
                    'es' => 'Puertas y Ventanas - Reparaciones',
                    'slugEn' => 'doors-and-windows-repairs',
                    'slugEs' => 'puertas-y-ventanas-reparaciones',
                ),
            814 =>
                array (
                    'en' => 'Doors y Windows',
                    'es' => 'Puertas y Ventanas',
                    'slugEn' => 'doors-y-windows',
                    'slugEs' => 'puertas-y-ventanas',
                ),
            815 =>
                array (
                    'en' => 'Doping Test',
                    'es' => 'Prueba de Dopaje',
                    'slugEn' => 'doping-test',
                    'slugEs' => 'prueba-de-dopaje',
                ),
            816 =>
                array (
                    'en' => 'Doughnuts',
                    'es' => 'Donas',
                    'slugEn' => 'doughnuts',
                    'slugEs' => 'donas',
                ),
            817 =>
                array (
                    'en' => 'Draftsmen',
                    'es' => 'Delineantes',
                    'slugEn' => 'draftsmen',
                    'slugEs' => 'delineantes',
                ),
            818 =>
                array (
                    'en' => 'Dressmakers',
                    'es' => 'Costura-Talleres',
                    'slugEn' => 'dressmakers',
                    'slugEs' => 'costura-talleres',
                ),
            819 =>
                array (
                    'en' => 'Driving Test',
                    'es' => 'Exámen de conducir',
                    'slugEn' => 'driving-test',
                    'slugEs' => 'examen-de-conducir',
                ),
            820 =>
                array (
                    'en' => 'Drop-off Laundry Service',
                    'es' => 'Laundry Servicio Drop-off',
                    'slugEn' => 'drop-off-laundry-service',
                    'slugEs' => 'laundry-servicio-drop-off',
                ),
            821 =>
                array (
                    'en' => 'Drug Addiction - Information & Treatment Centers',
                    'es' => 'Drogadicción-Centro De Tratamiento-Información',
                    'slugEn' => 'drug-addiction-information-treatment-centers',
                    'slugEs' => 'drogadiccion-centro-de-tratamiento-informacion',
                ),
            822 =>
                array (
                    'en' => 'Dry Cleaning',
                    'es' => 'Dry Cleaning (Lavado En Seco)',
                    'slugEn' => 'dry-cleaning',
                    'slugEs' => 'dry-cleaning-lavado-en-seco',
                ),
            823 =>
                array (
                    'en' => 'Dumplings - Manufacture',
                    'es' => 'Pastelillos-Fábricas',
                    'slugEn' => 'dumplings-manufacture',
                    'slugEs' => 'pastelillos-fabricas',
                ),
            824 =>
                array (
                    'en' => 'Echocardiogram',
                    'es' => 'Ecocardiograma',
                    'slugEn' => 'echocardiogram',
                    'slugEs' => 'ecocardiograma',
                ),
            825 =>
                array (
                    'en' => 'Ecotourism',
                    'es' => 'Ecoturismo',
                    'slugEn' => 'ecotourism',
                    'slugEs' => 'ecoturismo',
                ),
            826 =>
                array (
                    'en' => 'Education',
                    'es' => 'Educación',
                    'slugEn' => 'education',
                    'slugEs' => 'educacion',
                ),
            827 =>
                array (
                    'en' => 'Education - Continued',
                    'es' => 'Educación Contínua',
                    'slugEn' => 'education-continued',
                    'slugEs' => 'educacion-continua',
                ),
            828 =>
                array (
                    'en' => 'Educational -Material',
                    'es' => 'Educativos-Materiales',
                    'slugEn' => 'educational-material',
                    'slugEs' => 'educativos-materiales',
                ),
            829 =>
                array (
                    'en' => 'Educational Summer',
                    'es' => 'Verano Educativo',
                    'slugEn' => 'educational-summer',
                    'slugEs' => 'verano-educativo',
                ),
            830 =>
                array (
                    'en' => 'Educational Toys',
                    'es' => 'Juguetes Educativos',
                    'slugEn' => 'educational-toys',
                    'slugEs' => 'juguetes-educativos',
                ),
            831 =>
                array (
                    'en' => 'Educators - Diabete',
                    'es' => 'Educadores - Diabete',
                    'slugEn' => 'educators-diabete',
                    'slugEs' => 'educadores-diabete',
                ),
            832 =>
                array (
                    'en' => 'Eggs',
                    'es' => 'Huevos',
                    'slugEn' => 'eggs',
                    'slugEs' => 'huevos',
                ),
            833 =>
                array (
                    'en' => 'Elderly',
                    'es' => 'Anciano',
                    'slugEn' => 'elderly',
                    'slugEs' => 'anciano',
                ),
            834 =>
                array (
                    'en' => 'Elderly - Recreation',
                    'es' => 'Envejecientes - Recreación',
                    'slugEn' => 'elderly-recreation',
                    'slugEs' => 'envejecientes-recreacion',
                ),
            835 =>
                array (
                    'en' => 'Elderly Care',
                    'es' => 'Cuido de Ancianos',
                    'slugEn' => 'elderly-care',
                    'slugEs' => 'cuido-de-ancianos',
                ),
            836 =>
                array (
                    'en' => 'Electric - Equipment & Supplies',
                    'es' => 'Eléctrico-Efectos Y Equipo',
                    'slugEn' => 'electric-equipment-supplies',
                    'slugEs' => 'electrico-efectos-y-equipo',
                ),
            837 =>
                array (
                    'en' => 'Electric Gates',
                    'es' => 'Portones Eléctricos',
                    'slugEn' => 'electric-gates',
                    'slugEs' => 'portones-electricos',
                ),
            838 =>
                array (
                    'en' => 'Electric Generators and repair',
                    'es' => 'Generadores Electricos Y Reparacion',
                    'slugEn' => 'electric-generators-and-repair',
                    'slugEs' => 'generadores-electricos-y-reparacion',
                ),
            839 =>
                array (
                    'en' => 'Electrical Shops',
                    'es' => 'Talleres-Eléctricos',
                    'slugEn' => 'electrical-shops',
                    'slugEs' => 'talleres-electricos',
                ),
            840 =>
                array (
                    'en' => 'Electrical Shops - Equipment & Supplies',
                    'es' => 'Talleres-Eléctricos-Efectos Y Equipo',
                    'slugEn' => 'electrical-shops-equipment-supplies',
                    'slugEs' => 'talleres-electricos-efectos-y-equipo',
                ),
            841 =>
                array (
                    'en' => 'Electricians',
                    'es' => 'Electricistas',
                    'slugEn' => 'electricians',
                    'slugEs' => 'electricistas',
                ),
            842 =>
                array (
                    'en' => 'Electricity - Electrical Wires',
                    'es' => 'Electricidad - Alambres Eléctricos',
                    'slugEn' => 'electricity-electrical-wires',
                    'slugEs' => 'electricidad-alambres-electricos',
                ),
            843 =>
                array (
                    'en' => 'Electricity - Industrial',
                    'es' => 'Electricidad Industrial',
                    'slugEn' => 'electricity-industrial',
                    'slugEs' => 'electricidad-industrial',
                ),
            844 =>
                array (
                    'en' => 'Electromechanical Systems',
                    'es' => 'Sistemas Electromecánicos',
                    'slugEn' => 'electromechanical-systems',
                    'slugEs' => 'sistemas-electromecanicos',
                ),
            845 =>
                array (
                    'en' => 'Electronic - Equipment & Supplies',
                    'es' => 'Electrónicos-Efectos Y Equipo',
                    'slugEn' => 'electronic-equipment-supplies',
                    'slugEs' => 'electronicos-efectos-y-equipo',
                ),
            846 =>
                array (
                    'en' => 'Electronic Cigarettes',
                    'es' => 'Cigarrillos Electrónicos',
                    'slugEn' => 'electronic-cigarettes',
                    'slugEs' => 'cigarrillos-electronicos',
                ),
            847 =>
                array (
                    'en' => 'Electronic Equipment',
                    'es' => 'Equipo Electrónico',
                    'slugEn' => 'electronic-equipment',
                    'slugEs' => 'equipo-electronico',
                ),
            848 =>
                array (
                    'en' => 'Electronic Medical Record',
                    'es' => 'Record Médico Electrónico',
                    'slugEn' => 'electronic-medical-record',
                    'slugEs' => 'record-medico-electronico',
                ),
            849 =>
                array (
                    'en' => 'Elevators - Freight & Passenger',
                    'es' => 'Elevadores',
                    'slugEn' => 'elevators-freight-passenger',
                    'slugEs' => 'elevadores',
                ),
            850 =>
                array (
                    'en' => 'Elevators - Maintenance and Service',
                    'es' => 'Elevadores - Servicio y Mantenimiento',
                    'slugEn' => 'elevators-maintenance-and-service',
                    'slugEs' => 'elevadores-servicio-y-mantenimiento',
                ),
            851 =>
                array (
                    'en' => 'Emblems',
                    'es' => 'Emblemas',
                    'slugEn' => 'emblems',
                    'slugEs' => 'emblemas',
                ),
            852 =>
                array (
                    'en' => 'Embroidery',
                    'es' => 'Bordados',
                    'slugEn' => 'embroidery',
                    'slugEs' => 'bordados',
                ),
            853 =>
                array (
                    'en' => 'Emergency',
                    'es' => 'Emergencias',
                    'slugEn' => 'emergency',
                    'slugEs' => 'emergencias',
                ),
            854 =>
                array (
                    'en' => 'Emergency Room',
                    'es' => 'Sala de Emergencia',
                    'slugEn' => 'emergency-room',
                    'slugEs' => 'sala-de-emergencia',
                ),
            855 =>
                array (
                    'en' => 'Empanadillas',
                    'es' => 'Empanadillas',
                    'slugEn' => 'empanadillas',
                    'slugEs' => 'empanadillas',
                ),
            856 =>
                array (
                    'en' => 'Employment - Agencies',
                    'es' => 'Empleos-Agencias',
                    'slugEn' => 'employment-agencies',
                    'slugEs' => 'empleos-agencias',
                ),
            857 =>
                array (
                    'en' => 'Employment - Consignment',
                    'es' => 'Empleos - Consignacion',
                    'slugEn' => 'employment-consignment',
                    'slugEs' => 'empleos-consignacion',
                ),
            858 =>
                array (
                    'en' => 'Employment - Temporary',
                    'es' => 'Empleos-Temporeros',
                    'slugEn' => 'employment-temporary',
                    'slugEs' => 'empleos-temporeros',
                ),
            859 =>
                array (
                    'en' => 'Encyclopedias',
                    'es' => 'Enciclopedias',
                    'slugEn' => 'encyclopedias',
                    'slugEs' => 'enciclopedias',
                ),
            860 =>
                array (
                    'en' => 'Endodontics',
                    'es' => 'Endodoncia',
                    'slugEn' => 'endodontics',
                    'slugEs' => 'endodoncia',
                ),
            861 =>
                array (
                    'en' => 'Endorse Events',
                    'es' => 'Eventos - Refrendar',
                    'slugEn' => 'endorse-events',
                    'slugEs' => 'eventos-refrendar',
                ),
            862 =>
                array (
                    'en' => 'Endoscopy - Colonoscopy',
                    'es' => 'Endoscopía - Colonoscopía',
                    'slugEn' => 'endoscopy-colonoscopy',
                    'slugEs' => 'endoscopia-colonoscopia',
                ),
            863 =>
                array (
                    'en' => 'Energy - Conservation',
                    'es' => 'Energía-Conservación-Efectos Y Equipo',
                    'slugEn' => 'energy-conservation',
                    'slugEs' => 'energia-conservacion-efectos-y-equipo',
                ),
            864 =>
                array (
                    'en' => 'Engineers',
                    'es' => 'Ingenieros',
                    'slugEn' => 'engineers',
                    'slugEs' => 'ingenieros',
                ),
            865 =>
                array (
                    'en' => 'Engineers - Chemical',
                    'es' => 'Ingenieros-Químicos',
                    'slugEn' => 'engineers-chemical',
                    'slugEs' => 'ingenieros-quimicos',
                ),
            866 =>
                array (
                    'en' => 'Engineers - Civil',
                    'es' => 'Ingenieros-Civiles',
                    'slugEn' => 'engineers-civil',
                    'slugEs' => 'ingenieros-civiles',
                ),
            867 =>
                array (
                    'en' => 'Engineers - Consulting',
                    'es' => 'Ingenieros-Consultores',
                    'slugEn' => 'engineers-consulting',
                    'slugEs' => 'ingenieros-consultores',
                ),
            868 =>
                array (
                    'en' => 'Engineers - Contractors',
                    'es' => 'Ingenieros-Contratistas',
                    'slugEn' => 'engineers-contractors',
                    'slugEs' => 'ingenieros-contratistas',
                ),
            869 =>
                array (
                    'en' => 'Engineers - Electrical',
                    'es' => 'Ingenieros-Electricistas',
                    'slugEn' => 'engineers-electrical',
                    'slugEs' => 'ingenieros-electricistas',
                ),
            870 =>
                array (
                    'en' => 'Engineers - Environmental',
                    'es' => 'Ingenieros Ambientales',
                    'slugEn' => 'engineers-environmental',
                    'slugEs' => 'ingenieros-ambientales',
                ),
            871 =>
                array (
                    'en' => 'Engineers - Industrial',
                    'es' => 'Ingenieros-Industriales',
                    'slugEn' => 'engineers-industrial',
                    'slugEs' => 'ingenieros-industriales',
                ),
            872 =>
                array (
                    'en' => 'Engineers - Mechanical',
                    'es' => 'Ingenieros-Mecánicos',
                    'slugEn' => 'engineers-mechanical',
                    'slugEs' => 'ingenieros-mecanicos',
                ),
            873 =>
                array (
                    'en' => 'Engineers - Planning',
                    'es' => 'Ingenieros-Planificadores',
                    'slugEn' => 'engineers-planning',
                    'slugEs' => 'ingenieros-planificadores',
                ),
            874 =>
                array (
                    'en' => 'Engineers - Soil',
                    'es' => 'Ingenieros-Suelos',
                    'slugEn' => 'engineers-soil',
                    'slugEs' => 'ingenieros-suelos',
                ),
            875 =>
                array (
                    'en' => 'Engineers - Structural',
                    'es' => 'Ingenieros-Estructurales',
                    'slugEn' => 'engineers-structural',
                    'slugEs' => 'ingenieros-estructurales',
                ),
            876 =>
                array (
                    'en' => 'Engineers - Traffic & Transportation',
                    'es' => 'Ingenieros-Tránsito Y Transportación',
                    'slugEn' => 'engineers-traffic-transportation',
                    'slugEs' => 'ingenieros-transito-y-transportacion',
                ),
            877 =>
                array (
                    'en' => 'Engineers & Architects',
                    'es' => 'Ingenieros Y Arquitectos',
                    'slugEn' => 'engineers-architects',
                    'slugEs' => 'ingenieros-y-arquitectos',
                ),
            878 =>
                array (
                    'en' => 'Engineers & Architects - Equipment & Supplies',
                    'es' => 'Ingenieros Y Arquitectos-Efectos Y Equipo',
                    'slugEn' => 'engineers-architects-equipment-supplies',
                    'slugEs' => 'ingenieros-y-arquitectos-efectos-y-equipo',
                ),
            879 =>
                array (
                    'en' => 'Engineers-Hydraulics',
                    'es' => 'Ingenieros-Hidraulicos',
                    'slugEn' => 'engineers-hydraulics',
                    'slugEs' => 'ingenieros-hidraulicos',
                ),
            880 =>
                array (
                    'en' => 'Engines',
                    'es' => 'Motores',
                    'slugEn' => 'engines',
                    'slugEs' => 'motores',
                ),
            881 =>
                array (
                    'en' => 'Engines - Diesel - Repair',
                    'es' => 'Motores-Diesel-Reparación',
                    'slugEn' => 'engines-diesel-repair',
                    'slugEs' => 'motores-diesel-reparacion',
                ),
            882 =>
                array (
                    'en' => 'Engines - Gasoline - Repair',
                    'es' => 'Motores-Gasolina-Reparación',
                    'slugEn' => 'engines-gasoline-repair',
                    'slugEs' => 'motores-gasolina-reparacion',
                ),
            883 =>
                array (
                    'en' => 'Engines - Parts & Accessories',
                    'es' => 'Motores-Accesorios Y Piezas',
                    'slugEn' => 'engines-parts-accessories',
                    'slugEs' => 'motores-accesorios-y-piezas',
                ),
            884 =>
                array (
                    'en' => 'Engines - Winding',
                    'es' => 'Motores-Embobinado',
                    'slugEn' => 'engines-winding',
                    'slugEs' => 'motores-embobinado',
                ),
            885 =>
                array (
                    'en' => 'English Classes',
                    'es' => 'Clases de Ingles',
                    'slugEn' => 'english-classes',
                    'slugEs' => 'clases-de-ingles',
                ),
            886 =>
                array (
                    'en' => 'Engraving - Equipment & Machinery',
                    'es' => 'Grabados-Maquinaria Y Equipo',
                    'slugEn' => 'engraving-equipment-machinery',
                    'slugEs' => 'grabados-maquinaria-y-equipo',
                ),
            887 =>
                array (
                    'en' => 'Engraving - Metal & Plastic',
                    'es' => 'Grabados-Metal Y Plástico',
                    'slugEn' => 'engraving-metal-plastic',
                    'slugEs' => 'grabados-metal-y-plastico',
                ),
            888 =>
                array (
                    'en' => 'Equestrian-Accesories and Equipment',
                    'es' => 'Ecuestre-Efectos y Equipo',
                    'slugEn' => 'equestrian-accesories-and-equipment',
                    'slugEs' => 'ecuestre-efectos-y-equipo',
                ),
            889 =>
                array (
                    'en' => 'Equine Articles',
                    'es' => 'Artículos Ecuestres',
                    'slugEn' => 'equine-articles',
                    'slugEs' => 'articulos-ecuestres',
                ),
            890 =>
                array (
                    'en' => 'Equipment',
                    'es' => 'Equipo',
                    'slugEn' => 'equipment',
                    'slugEs' => 'equipo',
                ),
            891 =>
                array (
                    'en' => 'Equipment - Commercial',
                    'es' => 'Equipo - Comercial',
                    'slugEn' => 'equipment-commercial',
                    'slugEs' => 'equipo-comercial',
                ),
            892 =>
                array (
                    'en' => 'Equipment-Validation',
                    'es' => 'Equipo-Validación',
                    'slugEn' => 'equipment-validation',
                    'slugEs' => 'equipo-validacion',
                ),
            893 =>
                array (
                    'en' => 'Essential Oils',
                    'es' => 'Aceites Esenciales',
                    'slugEn' => 'essential-oils',
                    'slugEs' => 'aceites-esenciales',
                ),
            894 =>
                array (
                    'en' => 'Estheticians - Clinics',
                    'es' => 'Estética-Clínicas',
                    'slugEn' => 'estheticians-clinics',
                    'slugEs' => 'estetica-clinicas',
                ),
            895 =>
                array (
                    'en' => 'European Automobile Workshop',
                    'es' => 'Automóviles Taller Autos Europeos',
                    'slugEn' => 'european-automobile-workshop',
                    'slugEs' => 'automoviles-taller-autos-europeos',
                ),
            896 =>
                array (
                    'en' => 'European Parts',
                    'es' => 'Piezas Europeas',
                    'slugEn' => 'european-parts',
                    'slugEs' => 'piezas-europeas',
                ),
            897 =>
                array (
                    'en' => 'Event Photographers',
                    'es' => 'Fotógrafos de eventos',
                    'slugEn' => 'event-photographers',
                    'slugEs' => 'fotografos-de-eventos',
                ),
            898 =>
                array (
                    'en' => 'Events - Coordination',
                    'es' => 'Eventos - Coordinación',
                    'slugEn' => 'events-coordination',
                    'slugEs' => 'eventos-coordinacion',
                ),
            899 =>
                array (
                    'en' => 'Events - Corporations',
                    'es' => 'Eventos - Corporativos',
                    'slugEn' => 'events-corporations',
                    'slugEs' => 'eventos-corporativos',
                ),
            900 =>
                array (
                    'en' => 'Events - Decoration',
                    'es' => 'Eventos - Decoración',
                    'slugEn' => 'events-decoration',
                    'slugEs' => 'eventos-decoracion',
                ),
            901 =>
                array (
                    'en' => 'Events - Promotion',
                    'es' => 'Eventos - Promoción',
                    'slugEn' => 'events-promotion',
                    'slugEs' => 'eventos-promocion',
                ),
            902 =>
                array (
                    'en' => 'Exhibition Booths',
                    'es' => 'Exhibidores',
                    'slugEn' => 'exhibition-booths',
                    'slugEs' => 'exhibidores',
                ),
            903 =>
                array (
                    'en' => 'Explosives',
                    'es' => 'Explosivos',
                    'slugEn' => 'explosives',
                    'slugEs' => 'explosivos',
                ),
            904 =>
                array (
                    'en' => 'Exterminator',
                    'es' => 'Exterminadores',
                    'slugEn' => 'exterminator',
                    'slugEs' => 'exterminadores',
                ),
            905 =>
                array (
                    'en' => 'Exterminators - Equipment & Supplies',
                    'es' => 'Exterminadores-Efectos Y Equipos',
                    'slugEn' => 'exterminators-equipment-supplies',
                    'slugEs' => 'exterminadores-efectos-y-equipos',
                ),
            906 =>
                array (
                    'en' => 'Exterminators - Pest Control',
                    'es' => 'Exterminadores - Control de Plagas',
                    'slugEn' => 'exterminators-pest-control',
                    'slugEs' => 'exterminadores-control-de-plagas',
                ),
            907 =>
                array (
                    'en' => 'Exterminators-Termite',
                    'es' => 'Exterminadores-Comején',
                    'slugEn' => 'exterminators-termite',
                    'slugEs' => 'exterminadores-comejen',
                ),
            908 =>
                array (
                    'en' => 'Extinguishers - Fire',
                    'es' => 'Extinguidores (Extintores)',
                    'slugEn' => 'extinguishers-fire',
                    'slugEs' => 'extinguidores-extintores',
                ),
            909 =>
                array (
                    'en' => 'Extractors - Air',
                    'es' => 'Extractores-Aire',
                    'slugEn' => 'extractors-air',
                    'slugEs' => 'extractores-aire',
                ),
            910 =>
                array (
                    'en' => 'Extreme Sports',
                    'es' => 'Deportes Extremos',
                    'slugEn' => 'extreme-sports',
                    'slugEs' => 'deportes-extremos',
                ),
            911 =>
                array (
                    'en' => 'Eyes - Human- Artificial',
                    'es' => 'Ojos Humanos-Artificiales',
                    'slugEn' => 'eyes-human-artificial',
                    'slugEs' => 'ojos-humanos-artificiales',
                ),
            912 =>
                array (
                    'en' => 'Eyewear',
                    'es' => 'Espejuelos',
                    'slugEn' => 'eyewear',
                    'slugEs' => 'espejuelos',
                ),
            913 =>
                array (
                    'en' => 'Facial',
                    'es' => 'Facial',
                    'slugEn' => 'facial',
                    'slugEs' => 'facial',
                ),
            914 =>
                array (
                    'en' => 'FACOG',
                    'es' => 'FACOG',
                    'slugEn' => 'facog',
                    'slugEs' => 'facog',
                ),
            915 =>
                array (
                    'en' => 'Facsimile',
                    'es' => 'Facsímiles-Servicio Transmisión',
                    'slugEn' => 'facsimile',
                    'slugEs' => 'facsimiles-servicio-transmision',
                ),
            916 =>
                array (
                    'en' => 'Facsimile - Comunication Equipment',
                    'es' => 'Facsímiles-Equipo Comunicación',
                    'slugEn' => 'facsimile-comunication-equipment',
                    'slugEs' => 'facsimiles-equipo-comunicacion',
                ),
            917 =>
                array (
                    'en' => 'Factories',
                    'es' => 'Fábricas',
                    'slugEn' => 'factories',
                    'slugEs' => 'fabricas',
                ),
            918 =>
                array (
                    'en' => 'Family Planning',
                    'es' => 'Planificación Familiar',
                    'slugEn' => 'family-planning',
                    'slugEs' => 'planificacion-familiar',
                ),
            919 =>
                array (
                    'en' => 'Fans',
                    'es' => 'Abanicos-Domésticos',
                    'slugEn' => 'fans',
                    'slugEs' => 'abanicos-domesticos',
                ),
            920 =>
                array (
                    'en' => 'Farm - Machinery',
                    'es' => 'Agrícola - Maquinaria',
                    'slugEn' => 'farm-machinery',
                    'slugEs' => 'agricola-maquinaria',
                ),
            921 =>
                array (
                    'en' => 'Farms - Poultry',
                    'es' => 'Aves',
                    'slugEn' => 'farms-poultry',
                    'slugEs' => 'aves',
                ),
            922 =>
                array (
                    'en' => 'Fast Foods (Comida Rápida)',
                    'es' => 'Fast Foods (Comida Rápida)',
                    'slugEn' => 'fast-foods-comida-rapida',
                    'slugEs' => 'fast-foods-comida-rapida',
                ),
            923 =>
                array (
                    'en' => 'Federal Lawyers Forum',
                    'es' => 'Abogados Foro Federal',
                    'slugEn' => 'federal-lawyers-forum',
                    'slugEs' => 'abogados-foro-federal',
                ),
            924 =>
                array (
                    'en' => 'Federations',
                    'es' => 'Federaciones',
                    'slugEn' => 'federations',
                    'slugEs' => 'federaciones',
                ),
            925 =>
                array (
                    'en' => 'Fences',
                    'es' => 'Verjas Y Portones',
                    'slugEn' => 'fences',
                    'slugEs' => 'verjas-y-portones',
                ),
            926 =>
                array (
                    'en' => 'Fences and Gates - Materials',
                    'es' => 'Verjas y Portones - Materiales',
                    'slugEn' => 'fences-and-gates-materials',
                    'slugEs' => 'verjas-y-portones-materiales',
                ),
            927 =>
                array (
                    'en' => 'Feng Shui',
                    'es' => 'Feng Shui',
                    'slugEn' => 'feng-shui',
                    'slugEs' => 'feng-shui',
                ),
            928 =>
                array (
                    'en' => 'Fiber Optic - Contractors',
                    'es' => 'Fibra Optica-Contratistas',
                    'slugEn' => 'fiber-optic-contractors',
                    'slugEs' => 'fibra-optica-contratistas',
                ),
            929 =>
                array (
                    'en' => 'Fiberglass Products',
                    'es' => 'Fibra De Cristal-Productos',
                    'slugEn' => 'fiberglass-products',
                    'slugEs' => 'fibra-de-cristal-productos',
                ),
            930 =>
                array (
                    'en' => 'Files',
                    'es' => 'Archivos-Sistemas Y Equipo',
                    'slugEn' => 'files',
                    'slugEs' => 'archivos-sistemas-y-equipo',
                ),
            931 =>
                array (
                    'en' => 'Filter-Air',
                    'es' => 'Filtros-Aire',
                    'slugEn' => 'filter-air',
                    'slugEs' => 'filtros-aire',
                ),
            932 =>
                array (
                    'en' => 'Filters',
                    'es' => 'Filtros-Industriales',
                    'slugEn' => 'filters',
                    'slugEs' => 'filtros-industriales',
                ),
            933 =>
                array (
                    'en' => 'Financing',
                    'es' => 'Financiamiento',
                    'slugEn' => 'financing',
                    'slugEs' => 'financiamiento',
                ),
            934 =>
                array (
                    'en' => 'Fine Art',
                    'es' => 'Artes Plásticas',
                    'slugEn' => 'fine-art',
                    'slugEs' => 'artes-plasticas',
                ),
            935 =>
                array (
                    'en' => 'Fingerlifts',
                    'es' => 'Fingerlifts (Montacarga)',
                    'slugEn' => 'fingerlifts',
                    'slugEs' => 'fingerlifts-montacarga',
                ),
            936 =>
                array (
                    'en' => 'Fingerlifts Repair',
                    'es' => 'Fingerlifts (Montacarga)-Reparación',
                    'slugEn' => 'fingerlifts-repair',
                    'slugEs' => 'fingerlifts-montacarga-reparacion',
                ),
            937 =>
                array (
                    'en' => 'Fingerlifts-Alquiler',
                    'es' => 'Fingerlifts-Alquiler',
                    'slugEn' => 'fingerlifts-alquiler',
                    'slugEs' => 'fingerlifts-alquiler',
                ),
            938 =>
                array (
                    'en' => 'Fingerprints - Services - Equipment',
                    'es' => 'Huellas Dactilares-Servicios Y Equipo',
                    'slugEn' => 'fingerprints-services-equipment',
                    'slugEs' => 'huellas-dactilares-servicios-y-equipo',
                ),
            939 =>
                array (
                    'en' => 'Fire - Damage - Restoration',
                    'es' => 'Incendio-Daños-Restauración',
                    'slugEn' => 'fire-damage-restoration',
                    'slugEs' => 'incendio-danos-restauracion',
                ),
            940 =>
                array (
                    'en' => 'Fire Protection - Equipment & Supplies',
                    'es' => 'Incendio-Equipo Contra',
                    'slugEn' => 'fire-protection-equipment-supplies',
                    'slugEs' => 'incendio-equipo-contra',
                ),
            941 =>
                array (
                    'en' => 'Fire Pumps',
                    'es' => 'Fire Pumps',
                    'slugEn' => 'fire-pumps',
                    'slugEs' => 'fire-pumps',
                ),
            942 =>
                array (
                    'en' => 'Fire Sprinkler',
                    'es' => 'Fire Sprinkler',
                    'slugEn' => 'fire-sprinkler',
                    'slugEs' => 'fire-sprinkler',
                ),
            943 =>
                array (
                    'en' => 'Fireworks',
                    'es' => 'Fuegos Artificiales',
                    'slugEn' => 'fireworks',
                    'slugEs' => 'fuegos-artificiales',
                ),
            944 =>
                array (
                    'en' => 'First Aid - Equipment & Supplies',
                    'es' => 'Primera Ayuda-Efectos Y Equipo',
                    'slugEn' => 'first-aid-equipment-supplies',
                    'slugEs' => 'primera-ayuda-efectos-y-equipo',
                ),
            945 =>
                array (
                    'en' => 'First Communion',
                    'es' => 'Primera Comunión',
                    'slugEn' => 'first-communion',
                    'slugEs' => 'primera-comunion',
                ),
            946 =>
                array (
                    'en' => 'Fish & Seafood',
                    'es' => 'Pescados Y Mariscos',
                    'slugEn' => 'fish-seafood',
                    'slugEs' => 'pescados-y-mariscos',
                ),
            947 =>
                array (
                    'en' => 'Fish & Seafood - Wholesale',
                    'es' => 'Pescados Y Mariscos-Al Por Mayor',
                    'slugEn' => 'fish-seafood-wholesale',
                    'slugEs' => 'pescados-y-mariscos-al-por-mayor',
                ),
            948 =>
                array (
                    'en' => 'Fishing',
                    'es' => 'Pesca',
                    'slugEn' => 'fishing',
                    'slugEs' => 'pesca',
                ),
            949 =>
                array (
                    'en' => 'Fishing Charters',
                    'es' => 'Fishing Charters',
                    'slugEn' => 'fishing-charters',
                    'slugEs' => 'fishing-charters',
                ),
            950 =>
                array (
                    'en' => 'Fitness',
                    'es' => 'Fitness',
                    'slugEn' => 'fitness',
                    'slugEs' => 'fitness',
                ),
            951 =>
                array (
                    'en' => 'Flags',
                    'es' => 'Banderas',
                    'slugEn' => 'flags',
                    'slugEs' => 'banderas',
                ),
            952 =>
                array (
                    'en' => 'Flamenco-Sevillanas',
                    'es' => 'Flamenco-Sevillanas',
                    'slugEn' => 'flamenco-sevillanas',
                    'slugEs' => 'flamenco-sevillanas',
                ),
            953 =>
                array (
                    'en' => 'Fleet Insurance',
                    'es' => 'Seguro Para Flotas',
                    'slugEn' => 'fleet-insurance',
                    'slugEs' => 'seguro-para-flotas',
                ),
            954 =>
                array (
                    'en' => 'Fleet Mechanics',
                    'es' => 'Mecánica de Flota',
                    'slugEn' => 'fleet-mechanics',
                    'slugEs' => 'mecanica-de-flota',
                ),
            955 =>
                array (
                    'en' => 'Flood - Damage - Restoration',
                    'es' => 'Inundaciones - Daños - Restauración',
                    'slugEn' => 'flood-damage-restoration',
                    'slugEs' => 'inundaciones-danos-restauracion',
                ),
            956 =>
                array (
                    'en' => 'Floors',
                    'es' => 'Pisos',
                    'slugEn' => 'floors',
                    'slugEs' => 'pisos',
                ),
            957 =>
                array (
                    'en' => 'Floors - Cleaning',
                    'es' => 'Pisos-Limpieza',
                    'slugEn' => 'floors-cleaning',
                    'slugEs' => 'pisos-limpieza',
                ),
            958 =>
                array (
                    'en' => 'Floors - Glazing',
                    'es' => 'Pisos-Cristalizacion',
                    'slugEn' => 'floors-glazing',
                    'slugEs' => 'pisos-cristalizacion',
                ),
            959 =>
                array (
                    'en' => 'Floors - Laying & Refinishing',
                    'es' => 'Pisos-Instalaciones Especiales',
                    'slugEn' => 'floors-laying-refinishing',
                    'slugEs' => 'pisos-instalaciones-especiales',
                ),
            960 =>
                array (
                    'en' => 'Floors - Polisher',
                    'es' => 'Pisos-Pulidoras',
                    'slugEn' => 'floors-polisher',
                    'slugEs' => 'pisos-pulidoras',
                ),
            961 =>
                array (
                    'en' => 'Floors - Treatment',
                    'es' => 'Pisos-Servicio De Pulir',
                    'slugEn' => 'floors-treatment',
                    'slugEs' => 'pisos-servicio-de-pulir',
                ),
            962 =>
                array (
                    'en' => 'Florists',
                    'es' => 'Floristerías',
                    'slugEn' => 'florists',
                    'slugEs' => 'floristerias',
                ),
            963 =>
                array (
                    'en' => 'Florists - Effects & Equipment',
                    'es' => 'Floristerías-Efectos Y Equipo',
                    'slugEn' => 'florists-effects-equipment',
                    'slugEs' => 'floristerias-efectos-y-equipo',
                ),
            964 =>
                array (
                    'en' => 'Florists - Wedding',
                    'es' => 'Floristería - Bodas',
                    'slugEn' => 'florists-wedding',
                    'slugEs' => 'floristeria-bodas',
                ),
            965 =>
                array (
                    'en' => 'Flour Distributors',
                    'es' => 'Harina-Distribuidores',
                    'slugEn' => 'flour-distributors',
                    'slugEs' => 'harina-distribuidores',
                ),
            966 =>
                array (
                    'en' => 'Flower Crown',
                    'es' => 'Corona de Flores',
                    'slugEn' => 'flower-crown',
                    'slugEs' => 'corona-de-flores',
                ),
            967 =>
                array (
                    'en' => 'Flower delivery',
                    'es' => 'Floristería delivery',
                    'slugEn' => 'flower-delivery',
                    'slugEs' => 'floristeria-delivery',
                ),
            968 =>
                array (
                    'en' => 'Flowers',
                    'es' => 'Flores',
                    'slugEn' => 'flowers',
                    'slugEs' => 'flores',
                ),
            969 =>
                array (
                    'en' => 'Flowers - Wholesale',
                    'es' => 'Flores-Al Por Mayor',
                    'slugEn' => 'flowers-wholesale',
                    'slugEs' => 'flores-al-por-mayor',
                ),
            970 =>
                array (
                    'en' => 'Foam Rubber',
                    'es' => 'Gomas-Productos Industriales',
                    'slugEn' => 'foam-rubber',
                    'slugEs' => 'gomas-productos-industriales',
                ),
            971 =>
                array (
                    'en' => 'Food',
                    'es' => 'Comida',
                    'slugEn' => 'food',
                    'slugEs' => 'comida',
                ),
            972 =>
                array (
                    'en' => 'Food - Animals',
                    'es' => 'Alimentos-Animales',
                    'slugEn' => 'food-animals',
                    'slugEs' => 'alimentos-animales',
                ),
            973 =>
                array (
                    'en' => 'Food - Baby',
                    'es' => 'Alimentos-Niños',
                    'slugEn' => 'food-baby',
                    'slugEs' => 'alimentos-ninos',
                ),
            974 =>
                array (
                    'en' => 'Food - Canned',
                    'es' => 'Alimentos-Enlatados',
                    'slugEn' => 'food-canned',
                    'slugEs' => 'alimentos-enlatados',
                ),
            975 =>
                array (
                    'en' => 'Food - Distributors',
                    'es' => 'Alimentos - Distribuidores',
                    'slugEn' => 'food-distributors',
                    'slugEs' => 'alimentos-distribuidores',
                ),
            976 =>
                array (
                    'en' => 'Food - Manufacture',
                    'es' => 'Alimentos-Fábricas',
                    'slugEn' => 'food-manufacture',
                    'slugEs' => 'alimentos-fabricas',
                ),
            977 =>
                array (
                    'en' => 'Food - Processed - Equipment & Supplies',
                    'es' => 'Alimentos Procesados-Efectos Y Equipo',
                    'slugEn' => 'food-processed-equipment-supplies',
                    'slugEs' => 'alimentos-procesados-efectos-y-equipo',
                ),
            978 =>
                array (
                    'en' => 'Food Delivery',
                    'es' => 'Comida a Domicilio',
                    'slugEn' => 'food-delivery',
                    'slugEs' => 'comida-a-domicilio',
                ),
            979 =>
                array (
                    'en' => 'Food Delivery Service',
                    'es' => 'Delivery-Servicios - Comida',
                    'slugEn' => 'food-delivery-service',
                    'slugEs' => 'delivery-servicios-comida',
                ),
            980 =>
                array (
                    'en' => 'Food Manufacturing',
                    'es' => 'Manufactura de Alimentos',
                    'slugEn' => 'food-manufacturing',
                    'slugEs' => 'manufactura-de-alimentos',
                ),
            981 =>
                array (
                    'en' => 'Food Trucks',
                    'es' => 'Carro de Comida',
                    'slugEn' => 'food-trucks',
                    'slugEs' => 'carro-de-comida',
                ),
            982 =>
                array (
                    'en' => 'Food Trucks Builders',
                    'es' => 'Fabricantes de Food Truck',
                    'slugEn' => 'food-trucks-builders',
                    'slugEs' => 'fabricantes-de-food-truck',
                ),
            983 =>
                array (
                    'en' => 'Footwear',
                    'es' => 'Calzado',
                    'slugEn' => 'footwear',
                    'slugEs' => 'calzado',
                ),
            984 =>
                array (
                    'en' => 'Footwear - Consignment Shops',
                    'es' => 'Calzado - A Consignación',
                    'slugEn' => 'footwear-consignment-shops',
                    'slugEs' => 'calzado-a-consignacion',
                ),
            985 =>
                array (
                    'en' => 'Foreclosure',
                    'es' => 'Ejecución de Hipotecas',
                    'slugEn' => 'foreclosure',
                    'slugEs' => 'ejecucion-de-hipotecas',
                ),
            986 =>
                array (
                    'en' => 'Forensic Expert',
                    'es' => 'Perito Forense',
                    'slugEn' => 'forensic-expert',
                    'slugEs' => 'perito-forense',
                ),
            987 =>
                array (
                    'en' => 'Forensic Expertise',
                    'es' => 'Peritaje Forense',
                    'slugEn' => 'forensic-expertise',
                    'slugEs' => 'peritaje-forense',
                ),
            988 =>
                array (
                    'en' => 'Formal Wear',
                    'es' => 'Ropa Formal',
                    'slugEn' => 'formal-wear',
                    'slugEs' => 'ropa-formal',
                ),
            989 =>
                array (
                    'en' => 'Foundations',
                    'es' => 'Fundaciones',
                    'slugEn' => 'foundations',
                    'slugEs' => 'fundaciones',
                ),
            990 =>
                array (
                    'en' => 'Fragances',
                    'es' => 'Fragancias',
                    'slugEn' => 'fragances',
                    'slugEs' => 'fragancias',
                ),
            991 =>
                array (
                    'en' => 'Frames',
                    'es' => 'Marcos-Cuadros',
                    'slugEn' => 'frames',
                    'slugEs' => 'marcos-cuadros',
                ),
            992 =>
                array (
                    'en' => 'Frames - Wholesale & Manufacturing',
                    'es' => 'Marcos Cuadros-Mayoristas Y Manufactura',
                    'slugEn' => 'frames-wholesale-manufacturing',
                    'slugEs' => 'marcos-cuadros-mayoristas-y-manufactura',
                ),
            993 =>
                array (
                    'en' => 'Framing',
                    'es' => 'Enmarcados',
                    'slugEn' => 'framing',
                    'slugEs' => 'enmarcados',
                ),
            994 =>
                array (
                    'en' => 'Franchises',
                    'es' => 'Franquicias',
                    'slugEn' => 'franchises',
                    'slugEs' => 'franquicias',
                ),
            995 =>
                array (
                    'en' => 'Frappe',
                    'es' => 'Frappés',
                    'slugEn' => 'frappe',
                    'slugEs' => 'frappes',
                ),
            996 =>
                array (
                    'en' => 'Fraternities & Sororities',
                    'es' => 'Fraternidades Y Sororidades',
                    'slugEn' => 'fraternities-sororities',
                    'slugEs' => 'fraternidades-y-sororidades',
                ),
            997 =>
                array (
                    'en' => 'Freight - Ocean',
                    'es' => 'Carga Marítima',
                    'slugEn' => 'freight-ocean',
                    'slugEs' => 'carga-maritima',
                ),
            998 =>
                array (
                    'en' => 'Freight - Trucking',
                    'es' => 'Carga-Terrestre',
                    'slugEn' => 'freight-trucking',
                    'slugEs' => 'carga-terrestre',
                ),
            999 =>
                array (
                    'en' => 'Fried Foods',
                    'es' => 'Frituras',
                    'slugEn' => 'fried-foods',
                    'slugEs' => 'frituras',
                ),
            1000 =>
                array (
                    'en' => 'Fruits',
                    'es' => 'Frutas',
                    'slugEn' => 'fruits',
                    'slugEs' => 'frutas',
                ),
            1001 =>
                array (
                    'en' => 'Fruits & Vegetables',
                    'es' => 'Vegetales',
                    'slugEn' => 'fruits-vegetables',
                    'slugEs' => 'vegetales',
                ),
            1002 =>
                array (
                    'en' => 'Fuel - Distribution',
                    'es' => 'Gasolina-Distribuidores',
                    'slugEn' => 'fuel-distribution',
                    'slugEs' => 'gasolina-distribuidores',
                ),
            1003 =>
                array (
                    'en' => 'Fuel Injection  - Diesel',
                    'es' => 'Inyección de Combustible Diesel',
                    'slugEn' => 'fuel-injection-diesel',
                    'slugEs' => 'inyeccion-de-combustible-diesel',
                ),
            1004 =>
                array (
                    'en' => 'Fumigation',
                    'es' => 'Fumigación',
                    'slugEn' => 'fumigation',
                    'slugEs' => 'fumigacion',
                ),
            1005 =>
                array (
                    'en' => 'Fumigators',
                    'es' => 'Fumigadores',
                    'slugEn' => 'fumigators',
                    'slugEs' => 'fumigadores',
                ),
            1006 =>
                array (
                    'en' => 'Fund Raising - Organizations',
                    'es' => 'Fondos-Recaudación',
                    'slugEn' => 'fund-raising-organizations',
                    'slugEs' => 'fondos-recaudacion',
                ),
            1007 =>
                array (
                    'en' => 'Funeral Arrangements',
                    'es' => 'Arreglos Funerarios',
                    'slugEn' => 'funeral-arrangements',
                    'slugEs' => 'arreglos-funerarios',
                ),
            1008 =>
                array (
                    'en' => 'Funerals',
                    'es' => 'Funerarias',
                    'slugEn' => 'funerals',
                    'slugEs' => 'funerarias',
                ),
            1009 =>
                array (
                    'en' => 'Funerals - Pre Arrangements',
                    'es' => 'Funerarias - Pre Arreglos Fúnebres',
                    'slugEn' => 'funerals-pre-arrangements',
                    'slugEs' => 'funerarias-pre-arreglos-funebres',
                ),
            1010 =>
                array (
                    'en' => 'Funerals-Equipment & Supplies',
                    'es' => 'Funerarias - Efectos y Equipos',
                    'slugEn' => 'funerals-equipment-supplies',
                    'slugEs' => 'funerarias-efectos-y-equipos',
                ),
            1011 =>
                array (
                    'en' => 'Furnishings Stores',
                    'es' => 'Decoración Tiendas',
                    'slugEn' => 'furnishings-stores',
                    'slugEs' => 'decoracion-tiendas',
                ),
            1012 =>
                array (
                    'en' => 'Furniture',
                    'es' => 'Mueblerías',
                    'slugEn' => 'furniture',
                    'slugEs' => 'mueblerias',
                ),
            1013 =>
                array (
                    'en' => 'Furniture - Cleaning',
                    'es' => 'Muebles-Limpieza',
                    'slugEn' => 'furniture-cleaning',
                    'slugEs' => 'muebles-limpieza',
                ),
            1014 =>
                array (
                    'en' => 'Furniture - Interior',
                    'es' => 'Muebles - Interior',
                    'slugEn' => 'furniture-interior',
                    'slugEs' => 'muebles-interior',
                ),
            1015 =>
                array (
                    'en' => 'Furniture - Manufacture',
                    'es' => 'Muebles-Fábricas',
                    'slugEn' => 'furniture-manufacture',
                    'slugEs' => 'muebles-fabricas',
                ),
            1016 =>
                array (
                    'en' => 'Furniture - Outdoor',
                    'es' => 'Muebles-Patio',
                    'slugEn' => 'furniture-outdoor',
                    'slugEs' => 'muebles-patio',
                ),
            1017 =>
                array (
                    'en' => 'Furniture - Rattan',
                    'es' => 'Muebles-Rattan',
                    'slugEn' => 'furniture-rattan',
                    'slugEs' => 'muebles-rattan',
                ),
            1018 =>
                array (
                    'en' => 'Furniture - Rental',
                    'es' => 'Muebles-Alquiler',
                    'slugEn' => 'furniture-rental',
                    'slugEs' => 'muebles-alquiler',
                ),
            1019 =>
                array (
                    'en' => 'Furniture - Repair',
                    'es' => 'Muebles-Reparación',
                    'slugEn' => 'furniture-repair',
                    'slugEs' => 'muebles-reparacion',
                ),
            1020 =>
                array (
                    'en' => 'Furniture - Wholesale',
                    'es' => 'Muebles-Al Por Mayor',
                    'slugEn' => 'furniture-wholesale',
                    'slugEs' => 'muebles-al-por-mayor',
                ),
            1021 =>
                array (
                    'en' => 'Futons',
                    'es' => 'Futones',
                    'slugEn' => 'futons',
                    'slugEs' => 'futones',
                ),
            1022 =>
                array (
                    'en' => 'Ganoderma Coffee',
                    'es' => 'Café Ganoderma',
                    'slugEn' => 'ganoderma-coffee',
                    'slugEs' => 'cafe-ganoderma',
                ),
            1023 =>
                array (
                    'en' => 'Gardening',
                    'es' => 'Jardinería',
                    'slugEn' => 'gardening',
                    'slugEs' => 'jardineria',
                ),
            1024 =>
                array (
                    'en' => 'Gardening - Commercial',
                    'es' => 'Jardinería - Comercial',
                    'slugEn' => 'gardening-commercial',
                    'slugEs' => 'jardineria-comercial',
                ),
            1025 =>
                array (
                    'en' => 'Gardening - Equipment & Supplies',
                    'es' => 'Jardinería-Efectos Y Equipo',
                    'slugEn' => 'gardening-equipment-supplies',
                    'slugEs' => 'jardineria-efectos-y-equipo',
                ),
            1026 =>
                array (
                    'en' => 'Gas - Industrial & Medical',
                    'es' => 'Gases-Industriales Y Medicinales',
                    'slugEn' => 'gas-industrial-medical',
                    'slugEs' => 'gases-industriales-y-medicinales',
                ),
            1027 =>
                array (
                    'en' => 'Gas - Liquified Petroleum',
                    'es' => 'Gas Fluído',
                    'slugEn' => 'gas-liquified-petroleum',
                    'slugEs' => 'gas-fluido',
                ),
            1028 =>
                array (
                    'en' => 'Gas - Liquified Petroleum - Equipment & Supplies',
                    'es' => 'Gas Fluído-Efectos Y Equipo',
                    'slugEn' => 'gas-liquified-petroleum-equipment-supplies',
                    'slugEs' => 'gas-fluido-efectos-y-equipo',
                ),
            1029 =>
                array (
                    'en' => 'Gastroenterologists',
                    'es' => 'Gastroenterologos',
                    'slugEn' => 'gastroenterologists',
                    'slugEs' => 'gastroenterologos',
                ),
            1030 =>
                array (
                    'en' => 'Gastroenterology',
                    'es' => 'Gastroenterología',
                    'slugEn' => 'gastroenterology',
                    'slugEs' => 'gastroenterologia',
                ),
            1031 =>
                array (
                    'en' => 'Gates - Stainless Steel',
                    'es' => 'Portones - Stainless Steel',
                    'slugEn' => 'gates-stainless-steel',
                    'slugEs' => 'portones-stainless-steel',
                ),
            1032 =>
                array (
                    'en' => 'Gates for Pools',
                    'es' => 'Verjas de Piscina',
                    'slugEn' => 'gates-for-pools',
                    'slugEs' => 'verjas-de-piscina',
                ),
            1033 =>
                array (
                    'en' => 'General Ultrasound and Doppler',
                    'es' => 'Ultrasonido General y Doppler',
                    'slugEn' => 'general-ultrasound-and-doppler',
                    'slugEs' => 'ultrasonido-general-y-doppler',
                ),
            1034 =>
                array (
                    'en' => 'General Warehouses',
                    'es' => 'Almacenes Generales',
                    'slugEn' => 'general-warehouses',
                    'slugEs' => 'almacenes-generales',
                ),
            1035 =>
                array (
                    'en' => 'Generators - Electric',
                    'es' => 'Generadores-Eléctricos',
                    'slugEn' => 'generators-electric',
                    'slugEs' => 'generadores-electricos',
                ),
            1036 =>
                array (
                    'en' => 'Generators - Electric - Repair',
                    'es' => 'Generadores-Eléctricos-Reparación',
                    'slugEn' => 'generators-electric-repair',
                    'slugEs' => 'generadores-electricos-reparacion',
                ),
            1037 =>
                array (
                    'en' => 'Geologists',
                    'es' => 'Geólogos',
                    'slugEn' => 'geologists',
                    'slugEs' => 'geologos',
                ),
            1038 =>
                array (
                    'en' => 'Geriatric - Services',
                    'es' => 'Geriatría-Servicios',
                    'slugEn' => 'geriatric-services',
                    'slugEs' => 'geriatria-servicios',
                ),
            1039 =>
                array (
                    'en' => 'Gift Shop',
                    'es' => 'Gift Shop',
                    'slugEn' => 'gift-shop',
                    'slugEs' => 'gift-shop',
                ),
            1040 =>
                array (
                    'en' => 'Gift Shops',
                    'es' => 'Regalos-Artículos',
                    'slugEn' => 'gift-shops',
                    'slugEs' => 'regalos-articulos',
                ),
            1041 =>
                array (
                    'en' => 'Girdle',
                    'es' => 'Faja',
                    'slugEn' => 'girdle',
                    'slugEs' => 'faja',
                ),
            1042 =>
                array (
                    'en' => 'Girdles - Underwear',
                    'es' => 'Fajas - Ropa Interior',
                    'slugEn' => 'girdles-underwear',
                    'slugEs' => 'fajas-ropa-interior',
                ),
            1043 =>
                array (
                    'en' => 'Glass - Displays',
                    'es' => 'Cristales-Vidrieras',
                    'slugEn' => 'glass-displays',
                    'slugEs' => 'cristales-vidrieras',
                ),
            1044 =>
                array (
                    'en' => 'Glass - Stained',
                    'es' => 'Vitrales',
                    'slugEn' => 'glass-stained',
                    'slugEs' => 'vitrales',
                ),
            1045 =>
                array (
                    'en' => 'Glass and Porcelain',
                    'es' => 'Vidrio y Porcelana',
                    'slugEn' => 'glass-and-porcelain',
                    'slugEs' => 'vidrio-y-porcelana',
                ),
            1046 =>
                array (
                    'en' => 'Glassware',
                    'es' => 'Cristalerías',
                    'slugEn' => 'glassware',
                    'slugEs' => 'cristalerias',
                ),
            1047 =>
                array (
                    'en' => 'Glassware-Tinted Glass - Protection',
                    'es' => 'Cristales-Tintes Y Protección',
                    'slugEn' => 'glassware-tinted-glass-protection',
                    'slugEs' => 'cristales-tintes-y-proteccion',
                ),
            1048 =>
                array (
                    'en' => 'Glaucoma',
                    'es' => 'Glaucoma',
                    'slugEn' => 'glaucoma',
                    'slugEs' => 'glaucoma',
                ),
            1049 =>
                array (
                    'en' => 'Gloves',
                    'es' => 'Guantes',
                    'slugEn' => 'gloves',
                    'slugEs' => 'guantes',
                ),
            1050 =>
                array (
                    'en' => 'Golf Carts',
                    'es' => 'Carros de Golf',
                    'slugEn' => 'golf-carts',
                    'slugEs' => 'carros-de-golf',
                ),
            1051 =>
                array (
                    'en' => 'Golf Carts - Rental',
                    'es' => 'Golf Carros - Alquiler',
                    'slugEn' => 'golf-carts-rental',
                    'slugEs' => 'golf-carros-alquiler',
                ),
            1052 =>
                array (
                    'en' => 'Golf Carts - Repair Services',
                    'es' => 'Golf Carros - Servicios Reparación',
                    'slugEn' => 'golf-carts-repair-services',
                    'slugEs' => 'golf-carros-servicios-reparacion',
                ),
            1053 =>
                array (
                    'en' => 'Golf Carts - Sale',
                    'es' => 'Golf Carros - Venta',
                    'slugEn' => 'golf-carts-sale',
                    'slugEs' => 'golf-carros-venta',
                ),
            1054 =>
                array (
                    'en' => 'Gourmet Food',
                    'es' => 'Alimentos Gourmet',
                    'slugEn' => 'gourmet-food',
                    'slugEs' => 'alimentos-gourmet',
                ),
            1055 =>
                array (
                    'en' => 'Government',
                    'es' => 'Gobierno',
                    'slugEn' => 'government',
                    'slugEs' => 'gobierno',
                ),
            1056 =>
                array (
                    'en' => 'Government Agencies',
                    'es' => 'Agencias de Gobierno',
                    'slugEn' => 'government-agencies',
                    'slugEs' => 'agencias-de-gobierno',
                ),
            1057 =>
                array (
                    'en' => 'GPS Tracker',
                    'es' => 'GPS Tracker',
                    'slugEn' => 'gps-tracker',
                    'slugEs' => 'gps-tracker',
                ),
            1058 =>
                array (
                    'en' => 'Graduation - Articles',
                    'es' => 'Graduacion-Articulos',
                    'slugEn' => 'graduation-articles',
                    'slugEs' => 'graduacion-articulos',
                ),
            1059 =>
                array (
                    'en' => 'Graduation - Articles - Rings and Champions',
                    'es' => 'Graduación - Artículos - Sortijas y Campeones',
                    'slugEn' => 'graduation-articles-rings-and-champions',
                    'slugEs' => 'graduacion-articulos-sortijas-y-campeones',
                ),
            1060 =>
                array (
                    'en' => 'Graduation Rings',
                    'es' => 'Sortijas-Graduación',
                    'slugEn' => 'graduation-rings',
                    'slugEs' => 'sortijas-graduacion',
                ),
            1061 =>
                array (
                    'en' => 'Grain Mills',
                    'es' => 'Molinos-Grano',
                    'slugEn' => 'grain-mills',
                    'slugEs' => 'molinos-grano',
                ),
            1062 =>
                array (
                    'en' => 'Granite',
                    'es' => 'Granito',
                    'slugEn' => 'granite',
                    'slugEs' => 'granito',
                ),
            1063 =>
                array (
                    'en' => 'Graphic Arts',
                    'es' => 'Artes Gráficas',
                    'slugEn' => 'graphic-arts',
                    'slugEs' => 'artes-graficas',
                ),
            1064 =>
                array (
                    'en' => 'Graphic Design',
                    'es' => 'Diseño Gráfico',
                    'slugEn' => 'graphic-design',
                    'slugEs' => 'diseno-grafico',
                ),
            1065 =>
                array (
                    'en' => 'Gravel',
                    'es' => 'Graveros',
                    'slugEn' => 'gravel',
                    'slugEs' => 'graveros',
                ),
            1066 =>
                array (
                    'en' => 'Grease Traps',
                    'es' => 'Trampas-Grasa',
                    'slugEn' => 'grease-traps',
                    'slugEs' => 'trampas-grasa',
                ),
            1067 =>
                array (
                    'en' => 'Greenhouses',
                    'es' => 'Invernaderos',
                    'slugEn' => 'greenhouses',
                    'slugEs' => 'invernaderos',
                ),
            1068 =>
                array (
                    'en' => 'Greeting Cards',
                    'es' => 'Tarjetas-Ocasiones',
                    'slugEn' => 'greeting-cards',
                    'slugEs' => 'tarjetas-ocasiones',
                ),
            1069 =>
                array (
                    'en' => 'Grills Ornamental',
                    'es' => 'Rejas Ornamentales',
                    'slugEn' => 'grills-ornamental',
                    'slugEs' => 'rejas-ornamentales',
                ),
            1070 =>
                array (
                    'en' => 'Grocery Store',
                    'es' => 'Colmados',
                    'slugEn' => 'grocery-store',
                    'slugEs' => 'colmados',
                ),
            1071 =>
                array (
                    'en' => 'Grocery Store - Supermarket',
                    'es' => 'Colmados Y Supermercados-Efectos Y Equipo',
                    'slugEn' => 'grocery-store-supermarket',
                    'slugEs' => 'colmados-y-supermercados-efectos-y-equipo',
                ),
            1072 =>
                array (
                    'en' => 'Grooming',
                    'es' => 'Grooming',
                    'slugEn' => 'grooming',
                    'slugEs' => 'grooming',
                ),
            1073 =>
                array (
                    'en' => 'Grooming - Cats & Dogs',
                    'es' => 'Perros Y Gatos-Recortes',
                    'slugEn' => 'grooming-cats-dogs',
                    'slugEs' => 'perros-y-gatos-recortes',
                ),
            1074 =>
                array (
                    'en' => 'Guard & Patrol - Agencies',
                    'es' => 'Guardias De Seguridad-Agencias',
                    'slugEn' => 'guard-patrol-agencies',
                    'slugEs' => 'guardias-de-seguridad-agencias',
                ),
            1075 =>
                array (
                    'en' => 'Guest Houses',
                    'es' => 'Casas De Hospedaje',
                    'slugEn' => 'guest-houses',
                    'slugEs' => 'casas-de-hospedaje',
                ),
            1076 =>
                array (
                    'en' => 'Guitar',
                    'es' => 'Guitarra',
                    'slugEn' => 'guitar',
                    'slugEs' => 'guitarra',
                ),
            1077 =>
                array (
                    'en' => 'Gym',
                    'es' => 'Gimnasios',
                    'slugEn' => 'gym',
                    'slugEs' => 'gimnasios',
                ),
            1078 =>
                array (
                    'en' => 'Gynecologists',
                    'es' => 'Ginecólogos',
                    'slugEn' => 'gynecologists',
                    'slugEs' => 'ginecologos',
                ),
            1079 =>
                array (
                    'en' => 'Gynecology',
                    'es' => 'Ginecología',
                    'slugEn' => 'gynecology',
                    'slugEs' => 'ginecologia',
                ),
            1080 =>
                array (
                    'en' => 'Hair extensions',
                    'es' => 'Extensiones de cabello',
                    'slugEn' => 'hair-extensions',
                    'slugEs' => 'extensiones-de-cabello',
                ),
            1081 =>
                array (
                    'en' => 'Hair Removal',
                    'es' => 'Depilación Permanente',
                    'slugEn' => 'hair-removal',
                    'slugEs' => 'depilacion-permanente',
                ),
            1082 =>
                array (
                    'en' => 'Hair Replacement',
                    'es' => 'Cabello-Reemplazo (Transplante)',
                    'slugEn' => 'hair-replacement',
                    'slugEs' => 'cabello-reemplazo-transplante',
                ),
            1083 =>
                array (
                    'en' => 'Halls - Activities',
                    'es' => 'Salones-Actividades',
                    'slugEn' => 'halls-activities',
                    'slugEs' => 'salones-actividades',
                ),
            1084 =>
                array (
                    'en' => 'Halogen for cars',
                    'es' => 'Halógenos para autos',
                    'slugEn' => 'halogen-for-cars',
                    'slugEs' => 'halogenos-para-autos',
                ),
            1085 =>
                array (
                    'en' => 'Hamburguers - Manufacture',
                    'es' => 'Alimentos-Congelados',
                    'slugEn' => 'hamburguers-manufacture',
                    'slugEs' => 'alimentos-congelados',
                ),
            1086 =>
                array (
                    'en' => 'Handbags',
                    'es' => 'Carteras',
                    'slugEn' => 'handbags',
                    'slugEs' => 'carteras',
                ),
            1087 =>
                array (
                    'en' => 'Handbags - Wholesale',
                    'es' => 'Carteras - Al Por Mayor',
                    'slugEn' => 'handbags-wholesale',
                    'slugEs' => 'carteras-al-por-mayor',
                ),
            1088 =>
                array (
                    'en' => 'Handbags & Briefcases',
                    'es' => 'Bultos y Maletines',
                    'slugEn' => 'handbags-briefcases',
                    'slugEs' => 'bultos-y-maletines',
                ),
            1089 =>
                array (
                    'en' => 'Handicapped',
                    'es' => 'Impedidos-Efectos Y Equipo',
                    'slugEn' => 'handicapped',
                    'slugEs' => 'impedidos-efectos-y-equipo',
                ),
            1090 =>
                array (
                    'en' => 'Handling and Transportation-Disposal of non-hazardous waste',
                    'es' => 'Manejo y Transporte-Disposición de desperdicios no-peligrosos',
                    'slugEn' => 'handling-and-transportation-disposal-of-non-hazardous-waste',
                    'slugEs' => 'manejo-y-transporte-disposicion-de-desperdicios-no-peligrosos',
                ),
            1091 =>
                array (
                    'en' => 'Handyman',
                    'es' => 'Handyman',
                    'slugEn' => 'handyman',
                    'slugEs' => 'handyman',
                ),
            1092 =>
                array (
                    'en' => 'Hangers - Garment',
                    'es' => 'Ganchos-Ropa',
                    'slugEn' => 'hangers-garment',
                    'slugEs' => 'ganchos-ropa',
                ),
            1093 =>
                array (
                    'en' => 'Hardware',
                    'es' => 'Ferreterías',
                    'slugEn' => 'hardware',
                    'slugEs' => 'ferreterias',
                ),
            1094 =>
                array (
                    'en' => 'Hardware - Wholesale',
                    'es' => 'Ferreterías-Al Por Mayor',
                    'slugEn' => 'hardware-wholesale',
                    'slugEs' => 'ferreterias-al-por-mayor',
                ),
            1095 =>
                array (
                    'en' => 'Hardware and Materials',
                    'es' => 'Ferretería Efectos y Materiales',
                    'slugEn' => 'hardware-and-materials',
                    'slugEs' => 'ferreteria-efectos-y-materiales',
                ),
            1096 =>
                array (
                    'en' => 'Hatcheries',
                    'es' => 'Viveros',
                    'slugEn' => 'hatcheries',
                    'slugEs' => 'viveros',
                ),
            1097 =>
                array (
                    'en' => 'Hato Rey Salones De Belleza',
                    'es' => 'Hato Rey Salones De Belleza',
                    'slugEn' => 'hato-rey-salones-de-belleza',
                    'slugEs' => 'hato-rey-salones-de-belleza',
                ),
            1098 =>
                array (
                    'en' => 'Hats',
                    'es' => 'Sombreros',
                    'slugEn' => 'hats',
                    'slugEs' => 'sombreros',
                ),
            1099 =>
                array (
                    'en' => 'Hay - Supplies',
                    'es' => 'Heno - Suplidores',
                    'slugEn' => 'hay-supplies',
                    'slugEs' => 'heno-suplidores',
                ),
            1100 =>
                array (
                    'en' => 'Hay - Transport',
                    'es' => 'Heno - Transporte',
                    'slugEn' => 'hay-transport',
                    'slugEs' => 'heno-transporte',
                ),
            1101 =>
                array (
                    'en' => 'HCG Diet',
                    'es' => 'Dieta HCG',
                    'slugEn' => 'hcg-diet',
                    'slugEs' => 'dieta-hcg',
                ),
            1102 =>
                array (
                    'en' => 'Health - Food',
                    'es' => 'Alimentos - Salud',
                    'slugEn' => 'health-food',
                    'slugEs' => 'alimentos-salud',
                ),
            1103 =>
                array (
                    'en' => 'Health - Services - Home',
                    'es' => 'Servicios-Salud-Domicilio',
                    'slugEn' => 'health-services-home',
                    'slugEs' => 'servicios-salud-domicilio',
                ),
            1104 =>
                array (
                    'en' => 'Health - Services & Plans',
                    'es' => 'Servicios-Salud',
                    'slugEn' => 'health-services-plans',
                    'slugEs' => 'servicios-salud',
                ),
            1105 =>
                array (
                    'en' => 'Health Certificates',
                    'es' => 'Certificados de Salud',
                    'slugEn' => 'health-certificates',
                    'slugEs' => 'certificados-de-salud',
                ),
            1106 =>
                array (
                    'en' => 'Health Plans',
                    'es' => 'Planes Médicos',
                    'slugEn' => 'health-plans',
                    'slugEs' => 'planes-medicos',
                ),
            1107 =>
                array (
                    'en' => 'Health Plans - Billing',
                    'es' => 'Planes Médicos-Facturación',
                    'slugEn' => 'health-plans-billing',
                    'slugEs' => 'planes-medicos-facturacion',
                ),
            1108 =>
                array (
                    'en' => 'Health Products - Natural',
                    'es' => 'Naturales-Productos Salud',
                    'slugEn' => 'health-products-natural',
                    'slugEs' => 'naturales-productos-salud',
                ),
            1109 =>
                array (
                    'en' => 'Healthy Restaurant',
                    'es' => 'Restaurante Saludable',
                    'slugEn' => 'healthy-restaurant',
                    'slugEs' => 'restaurante-saludable',
                ),
            1110 =>
                array (
                    'en' => 'Healthy Restaurants',
                    'es' => 'Restaurantes Saludables',
                    'slugEn' => 'healthy-restaurants',
                    'slugEs' => 'restaurantes-saludables',
                ),
            1111 =>
                array (
                    'en' => 'Hearing Aids - Devices & Equipment',
                    'es' => 'Audífonos Auditivos-Aparatos Equipos',
                    'slugEn' => 'hearing-aids-devices-equipment',
                    'slugEs' => 'audifonos-auditivos-aparatos-equipos',
                ),
            1112 =>
                array (
                    'en' => 'Heat Treatment - Metals',
                    'es' => 'Metales-Tratamiento',
                    'slugEn' => 'heat-treatment-metals',
                    'slugEs' => 'metales-tratamiento',
                ),
            1113 =>
                array (
                    'en' => 'Heaters - Domestic',
                    'es' => 'Calentadores-Domésticos',
                    'slugEn' => 'heaters-domestic',
                    'slugEs' => 'calentadores-domesticos',
                ),
            1114 =>
                array (
                    'en' => 'Heaters - Industry',
                    'es' => 'Calentadores-Industriales',
                    'slugEn' => 'heaters-industry',
                    'slugEs' => 'calentadores-industriales',
                ),
            1115 =>
                array (
                    'en' => 'Heaters - Repair',
                    'es' => 'Calentadores-Reparacion',
                    'slugEn' => 'heaters-repair',
                    'slugEs' => 'calentadores-reparacion',
                ),
            1116 =>
                array (
                    'en' => 'Heavy Equipment',
                    'es' => 'Equipo Pesado',
                    'slugEn' => 'heavy-equipment',
                    'slugEs' => 'equipo-pesado',
                ),
            1117 =>
                array (
                    'en' => 'Heavy Equipment - Parts & Accessories',
                    'es' => 'Equipo Pesado-Accesorios Y Piezas',
                    'slugEn' => 'heavy-equipment-parts-accessories',
                    'slugEs' => 'equipo-pesado-accesorios-y-piezas',
                ),
            1118 =>
                array (
                    'en' => 'Heavy Equipment - Rental',
                    'es' => 'Equipo Pesado-Alquiler',
                    'slugEn' => 'heavy-equipment-rental',
                    'slugEs' => 'equipo-pesado-alquiler',
                ),
            1119 =>
                array (
                    'en' => 'Heavy Equipment - Repair',
                    'es' => 'Equipo Pesado-Reparación',
                    'slugEn' => 'heavy-equipment-repair',
                    'slugEs' => 'equipo-pesado-reparacion',
                ),
            1120 =>
                array (
                    'en' => 'Helados Y Mantecados-Fábricas',
                    'es' => 'Helados Y Mantecados-Fábricas',
                    'slugEn' => 'helados-y-mantecados-fabricas',
                    'slugEs' => 'helados-y-mantecados-fabricas',
                ),
            1121 =>
                array (
                    'en' => 'Helicopter',
                    'es' => 'Helicóptero',
                    'slugEn' => 'helicopter',
                    'slugEs' => 'helicoptero',
                ),
            1122 =>
                array (
                    'en' => 'Helicopters-Rental',
                    'es' => 'Helicópteros-Alquiler',
                    'slugEn' => 'helicopters-rental',
                    'slugEs' => 'helicopteros-alquiler',
                ),
            1123 =>
                array (
                    'en' => 'Hematologist',
                    'es' => 'Hepatólogo',
                    'slugEn' => 'hematologist',
                    'slugEs' => 'hepatologo',
                ),
            1124 =>
                array (
                    'en' => 'Hobbies',
                    'es' => 'Pasatiempos',
                    'slugEn' => 'hobbies',
                    'slugEs' => 'pasatiempos',
                ),
            1125 =>
                array (
                    'en' => 'Holistic Health',
                    'es' => 'Salud Holística',
                    'slugEn' => 'holistic-health',
                    'slugEs' => 'salud-holistica',
                ),
            1126 =>
                array (
                    'en' => 'Home',
                    'es' => 'Hogar',
                    'slugEn' => 'home',
                    'slugEs' => 'hogar',
                ),
            1127 =>
                array (
                    'en' => 'Home - Automation',
                    'es' => 'Hogar-Automatización',
                    'slugEn' => 'home-automation',
                    'slugEs' => 'hogar-automatizacion',
                ),
            1128 =>
                array (
                    'en' => 'Home - Children',
                    'es' => 'Hogares-Niños',
                    'slugEn' => 'home-children',
                    'slugEs' => 'hogares-ninos',
                ),
            1129 =>
                array (
                    'en' => 'Home Care',
                    'es' => 'Hogar-Salud',
                    'slugEn' => 'home-care',
                    'slugEs' => 'hogar-salud',
                ),
            1130 =>
                array (
                    'en' => 'Home Care for Elderly',
                    'es' => 'Cuidado en el Hogar',
                    'slugEn' => 'home-care-for-elderly',
                    'slugEs' => 'cuidado-en-el-hogar',
                ),
            1131 =>
                array (
                    'en' => 'Home Delivery',
                    'es' => 'Servicio a Domicilio',
                    'slugEn' => 'home-delivery',
                    'slugEs' => 'servicio-a-domicilio',
                ),
            1132 =>
                array (
                    'en' => 'Home Entertainment-Supplies & Equipment',
                    'es' => 'Home Entertainment-Efectos Y Equipo',
                    'slugEn' => 'home-entertainment-supplies-equipment',
                    'slugEs' => 'home-entertainment-efectos-y-equipo',
                ),
            1133 =>
                array (
                    'en' => 'Home Improvement',
                    'es' => 'Mejoras para el hogar',
                    'slugEn' => 'home-improvement',
                    'slugEs' => 'mejoras-para-el-hogar',
                ),
            1134 =>
                array (
                    'en' => 'Home Rental',
                    'es' => 'Alquiler de Casa',
                    'slugEn' => 'home-rental',
                    'slugEs' => 'alquiler-de-casa',
                ),
            1135 =>
                array (
                    'en' => 'Home Schooling',
                    'es' => 'Home Schooling',
                    'slugEn' => 'home-schooling',
                    'slugEs' => 'home-schooling',
                ),
            1136 =>
                array (
                    'en' => 'Home Service - Mental Health',
                    'es' => 'Hogares Salud Mental',
                    'slugEn' => 'home-service-mental-health',
                    'slugEs' => 'hogares-salud-mental',
                ),
            1137 =>
                array (
                    'en' => 'Homebirth',
                    'es' => 'Parto en casa',
                    'slugEn' => 'homebirth',
                    'slugEs' => 'parto-en-casa',
                ),
            1138 =>
                array (
                    'en' => 'Homebuilders',
                    'es' => 'Constructores Hogares',
                    'slugEn' => 'homebuilders',
                    'slugEs' => 'constructores-hogares',
                ),
            1139 =>
                array (
                    'en' => 'Homes Aged People',
                    'es' => 'Hogares Envejecientes',
                    'slugEn' => 'homes-aged-people',
                    'slugEs' => 'hogares-envejecientes',
                ),
            1140 =>
                array (
                    'en' => 'Honda Parts',
                    'es' => 'Piezas Honda',
                    'slugEn' => 'honda-parts',
                    'slugEs' => 'piezas-honda',
                ),
            1141 =>
                array (
                    'en' => 'Honey',
                    'es' => 'Mieles',
                    'slugEn' => 'honey',
                    'slugEs' => 'mieles',
                ),
            1142 =>
                array (
                    'en' => 'Hors D Oeuvres',
                    'es' => 'Entremeses',
                    'slugEn' => 'hors-d-oeuvres',
                    'slugEs' => 'entremeses',
                ),
            1143 =>
                array (
                    'en' => 'Horse Chairs',
                    'es' => 'Silla de Caballos',
                    'slugEn' => 'horse-chairs',
                    'slugEs' => 'silla-de-caballos',
                ),
            1144 =>
                array (
                    'en' => 'Horse Mounts',
                    'es' => 'Monturas de Caballos',
                    'slugEn' => 'horse-mounts',
                    'slugEs' => 'monturas-de-caballos',
                ),
            1145 =>
                array (
                    'en' => 'Horseback Riding',
                    'es' => 'Caballos-Aperos',
                    'slugEn' => 'horseback-riding',
                    'slugEs' => 'caballos-aperos',
                ),
            1146 =>
                array (
                    'en' => 'Hose - Hydraulic',
                    'es' => 'Mangas - Hidráulicas',
                    'slugEn' => 'hose-hydraulic',
                    'slugEs' => 'mangas-hidraulicas',
                ),
            1147 =>
                array (
                    'en' => 'Hoses',
                    'es' => 'Mangueras-Industriales',
                    'slugEn' => 'hoses',
                    'slugEs' => 'mangueras-industriales',
                ),
            1148 =>
                array (
                    'en' => 'Hosiery',
                    'es' => 'Medias',
                    'slugEn' => 'hosiery',
                    'slugEs' => 'medias',
                ),
            1149 =>
                array (
                    'en' => 'Hospice',
                    'es' => 'Hospicio',
                    'slugEn' => 'hospice',
                    'slugEs' => 'hospicio',
                ),
            1150 =>
                array (
                    'en' => 'Hospitals',
                    'es' => 'Hospitales',
                    'slugEn' => 'hospitals',
                    'slugEs' => 'hospitales',
                ),
            1151 =>
                array (
                    'en' => 'Hospitals - Administration',
                    'es' => 'Hospitales-Administración',
                    'slugEn' => 'hospitals-administration',
                    'slugEs' => 'hospitales-administracion',
                ),
            1152 =>
                array (
                    'en' => 'Hospitals - Equipment & Supplies',
                    'es' => 'Hospitales-Efectos Y Equipo',
                    'slugEn' => 'hospitals-equipment-supplies',
                    'slugEs' => 'hospitales-efectos-y-equipo',
                ),
            1153 =>
                array (
                    'en' => 'Hospitals - Equipment & Supplies - Rental',
                    'es' => 'Hospitales-Efectos Y Equipo-Alquiler',
                    'slugEn' => 'hospitals-equipment-supplies-rental',
                    'slugEs' => 'hospitales-efectos-y-equipo-alquiler',
                ),
            1154 =>
                array (
                    'en' => 'Hospitals Equipment And Supplies',
                    'es' => 'Hospitals Equipment And Supplies',
                    'slugEn' => 'hospitals-equipment-and-supplies',
                    'slugEs' => 'hospitals-equipment-and-supplies',
                ),
            1155 =>
                array (
                    'en' => 'Hospitals Sale and Repair',
                    'es' => 'Hospitales Venta y Reparación',
                    'slugEn' => 'hospitals-sale-and-repair',
                    'slugEs' => 'hospitales-venta-y-reparacion',
                ),
            1156 =>
                array (
                    'en' => 'Hot Dogs',
                    'es' => 'Salchichas',
                    'slugEn' => 'hot-dogs',
                    'slugEs' => 'salchichas',
                ),
            1157 =>
                array (
                    'en' => 'Hotels',
                    'es' => 'Hoteles',
                    'slugEn' => 'hotels',
                    'slugEs' => 'hoteles',
                ),
            1158 =>
                array (
                    'en' => 'Hotels - Equipment & Supplies',
                    'es' => 'Hoteles-Efectos Y Equipo',
                    'slugEn' => 'hotels-equipment-supplies',
                    'slugEs' => 'hoteles-efectos-y-equipo',
                ),
            1159 =>
                array (
                    'en' => 'House - Pre-designed',
                    'es' => 'Casas-Prediseñadas',
                    'slugEn' => 'house-pre-designed',
                    'slugEs' => 'casas-predisenadas',
                ),
            1160 =>
                array (
                    'en' => 'Housekeeping',
                    'es' => 'Ama de Llaves',
                    'slugEn' => 'housekeeping',
                    'slugEs' => 'ama-de-llaves',
                ),
            1161 =>
                array (
                    'en' => 'Houses',
                    'es' => 'Casas',
                    'slugEn' => 'houses',
                    'slugEs' => 'casas',
                ),
            1162 =>
                array (
                    'en' => 'Houses - Prefabricated',
                    'es' => 'Casas-Prefabricadas',
                    'slugEn' => 'houses-prefabricated',
                    'slugEs' => 'casas-prefabricadas',
                ),
            1163 =>
                array (
                    'en' => 'Houses - Wood',
                    'es' => 'Casas-Madera',
                    'slugEn' => 'houses-wood',
                    'slugEs' => 'casas-madera',
                ),
            1164 =>
                array (
                    'en' => 'Housing',
                    'es' => 'Viviendas',
                    'slugEn' => 'housing',
                    'slugEs' => 'viviendas',
                ),
            1165 =>
                array (
                    'en' => 'Housing - Inspection',
                    'es' => 'Vivienda-Inspección',
                    'slugEn' => 'housing-inspection',
                    'slugEs' => 'vivienda-inspeccion',
                ),
            1166 =>
                array (
                    'en' => 'Human Resources',
                    'es' => 'Recursos Humanos',
                    'slugEn' => 'human-resources',
                    'slugEs' => 'recursos-humanos',
                ),
            1167 =>
                array (
                    'en' => 'Hurricane Protectors',
                    'es' => 'Huracanes-Protectores',
                    'slugEn' => 'hurricane-protectors',
                    'slugEs' => 'huracanes-protectores',
                ),
            1168 =>
                array (
                    'en' => 'Hydraulic - Equipment',
                    'es' => 'Hidráulico-Equipos',
                    'slugEn' => 'hydraulic-equipment',
                    'slugEs' => 'hidraulico-equipos',
                ),
            1169 =>
                array (
                    'en' => 'Hydraulic Seals',
                    'es' => 'Sellos Hidráulicos',
                    'slugEn' => 'hydraulic-seals',
                    'slugEs' => 'sellos-hidraulicos',
                ),
            1170 =>
                array (
                    'en' => 'Hydroponic - Product',
                    'es' => 'Producto - Hidropónico',
                    'slugEn' => 'hydroponic-product',
                    'slugEs' => 'producto-hidroponico',
                ),
            1171 =>
                array (
                    'en' => 'Hypnotists',
                    'es' => 'Hipnólogos',
                    'slugEn' => 'hypnotists',
                    'slugEs' => 'hipnologos',
                ),
            1172 =>
                array (
                    'en' => 'Ice',
                    'es' => 'Hielo',
                    'slugEn' => 'ice',
                    'slugEs' => 'hielo',
                ),
            1173 =>
                array (
                    'en' => 'Ice - Manufacture',
                    'es' => 'Hielo-Fábricas',
                    'slugEn' => 'ice-manufacture',
                    'slugEs' => 'hielo-fabricas',
                ),
            1174 =>
                array (
                    'en' => 'Ice Cream',
                    'es' => 'Helados Y Mantecados',
                    'slugEn' => 'ice-cream',
                    'slugEs' => 'helados-y-mantecados',
                ),
            1175 =>
                array (
                    'en' => 'Ice Making Equipment & Supplies',
                    'es' => 'Máquinas-Hielo',
                    'slugEn' => 'ice-making-equipment-supplies',
                    'slugEs' => 'maquinas-hielo',
                ),
            1176 =>
                array (
                    'en' => 'Ice Skating Rink',
                    'es' => 'Pista de Patinaje',
                    'slugEn' => 'ice-skating-rink',
                    'slugEs' => 'pista-de-patinaje',
                ),
            1177 =>
                array (
                    'en' => 'Identification-Service',
                    'es' => 'Identificación-Servicio',
                    'slugEn' => 'identification-service',
                    'slugEs' => 'identificacion-servicio',
                ),
            1178 =>
                array (
                    'en' => 'Ilumination - Equipment & Supplies',
                    'es' => 'Iluminación-Efectos Y Equipo',
                    'slugEn' => 'ilumination-equipment-supplies',
                    'slugEs' => 'iluminacion-efectos-y-equipo',
                ),
            1179 =>
                array (
                    'en' => 'Immigration Services',
                    'es' => 'Inmigración-Servicio',
                    'slugEn' => 'immigration-services',
                    'slugEs' => 'inmigracion-servicio',
                ),
            1180 =>
                array (
                    'en' => 'Importers - Exporters',
                    'es' => 'Importadores Y Exportadores',
                    'slugEn' => 'importers-exporters',
                    'slugEs' => 'importadores-y-exportadores',
                ),
            1181 =>
                array (
                    'en' => 'Individualized Education',
                    'es' => 'Educación Individualizada',
                    'slugEn' => 'individualized-education',
                    'slugEs' => 'educacion-individualizada',
                ),
            1182 =>
                array (
                    'en' => 'Industrial Audiology',
                    'es' => 'Audiología Industrial',
                    'slugEn' => 'industrial-audiology',
                    'slugEs' => 'audiologia-industrial',
                ),
            1183 =>
                array (
                    'en' => 'Industrial Control',
                    'es' => 'Controles Industriales',
                    'slugEn' => 'industrial-control',
                    'slugEs' => 'controles-industriales',
                ),
            1184 =>
                array (
                    'en' => 'Industrial Equipment',
                    'es' => 'Equipo Industrial',
                    'slugEn' => 'industrial-equipment',
                    'slugEs' => 'equipo-industrial',
                ),
            1185 =>
                array (
                    'en' => 'Industrial Hardware',
                    'es' => 'Ferreteria Industrial',
                    'slugEn' => 'industrial-hardware',
                    'slugEs' => 'ferreteria-industrial',
                ),
            1186 =>
                array (
                    'en' => 'Industrial Moving',
                    'es' => 'Mudanza Industrial',
                    'slugEn' => 'industrial-moving',
                    'slugEs' => 'mudanza-industrial',
                ),
            1187 =>
                array (
                    'en' => 'Industrial Parts',
                    'es' => 'Piezas Industriales',
                    'slugEn' => 'industrial-parts',
                    'slugEs' => 'piezas-industriales',
                ),
            1188 =>
                array (
                    'en' => 'Industrial Refrigeration',
                    'es' => 'Refrigeración Industrial',
                    'slugEn' => 'industrial-refrigeration',
                    'slugEs' => 'refrigeracion-industrial',
                ),
            1189 =>
                array (
                    'en' => 'Industrial Tints',
                    'es' => 'Tintes Industriales',
                    'slugEn' => 'industrial-tints',
                    'slugEs' => 'tintes-industriales',
                ),
            1190 =>
                array (
                    'en' => 'Industrial-Instruments',
                    'es' => 'Instrumentos-Industriales',
                    'slugEn' => 'industrial-instruments',
                    'slugEs' => 'instrumentos-industriales',
                ),
            1191 =>
                array (
                    'en' => 'Inflatables',
                    'es' => 'Inflables',
                    'slugEn' => 'inflatables',
                    'slugEs' => 'inflables',
                ),
            1192 =>
                array (
                    'en' => 'Infusion Center',
                    'es' => 'Centro de Infusión',
                    'slugEn' => 'infusion-center',
                    'slugEs' => 'centro-de-infusion',
                ),
            1193 =>
                array (
                    'en' => 'Inheritance - Powers',
                    'es' => 'Herencias - Poderes',
                    'slugEn' => 'inheritance-powers',
                    'slugEs' => 'herencias-poderes',
                ),
            1194 =>
                array (
                    'en' => 'Inheritances',
                    'es' => 'Herencias',
                    'slugEn' => 'inheritances',
                    'slugEs' => 'herencias',
                ),
            1195 =>
                array (
                    'en' => 'Ink and Toner - Supply',
                    'es' => 'Tintas y Toner - Suplidores',
                    'slugEn' => 'ink-and-toner-supply',
                    'slugEs' => 'tintas-y-toner-suplidores',
                ),
            1196 =>
                array (
                    'en' => 'Inline Skates',
                    'es' => 'Patines',
                    'slugEn' => 'inline-skates',
                    'slugEs' => 'patines',
                ),
            1197 =>
                array (
                    'en' => 'Insecticides',
                    'es' => 'Insecticidas',
                    'slugEn' => 'insecticides',
                    'slugEs' => 'insecticidas',
                ),
            1198 =>
                array (
                    'en' => 'Inspection Services',
                    'es' => 'Inspección-Servicios',
                    'slugEn' => 'inspection-services',
                    'slugEs' => 'inspeccion-servicios',
                ),
            1199 =>
                array (
                    'en' => 'Institutional Electrician',
                    'es' => 'Electricista Institucional',
                    'slugEn' => 'institutional-electrician',
                    'slugEs' => 'electricista-institucional',
                ),
            1200 =>
                array (
                    'en' => 'Instruments-Precission',
                    'es' => 'Instrumentos-Precision',
                    'slugEn' => 'instruments-precission',
                    'slugEs' => 'instrumentos-precision',
                ),
            1201 =>
                array (
                    'en' => 'Insulation - Materials',
                    'es' => 'Aislación-Materiales',
                    'slugEn' => 'insulation-materials',
                    'slugEs' => 'aislacion-materiales',
                ),
            1202 =>
                array (
                    'en' => 'Insurance',
                    'es' => 'Seguros',
                    'slugEn' => 'insurance',
                    'slugEs' => 'seguros',
                ),
            1203 =>
                array (
                    'en' => 'Insurance - Accidents And Health',
                    'es' => 'Seguros Por Especialidad - Salud Y Accidentes',
                    'slugEn' => 'insurance-accidents-and-health',
                    'slugEs' => 'seguros-por-especialidad-salud-y-accidentes',
                ),
            1204 =>
                array (
                    'en' => 'Insurance - Adjusters',
                    'es' => 'Seguros-Ajustadores',
                    'slugEn' => 'insurance-adjusters',
                    'slugEs' => 'seguros-ajustadores',
                ),
            1205 =>
                array (
                    'en' => 'Insurance - Automobile',
                    'es' => 'Seguros Por Especialidad - Automóviles',
                    'slugEn' => 'insurance-automobile',
                    'slugEs' => 'seguros-por-especialidad-automoviles',
                ),
            1206 =>
                array (
                    'en' => 'Insurance - Cancer',
                    'es' => 'Seguros Por Especialidad - Cáncer',
                    'slugEn' => 'insurance-cancer',
                    'slugEs' => 'seguros-por-especialidad-cancer',
                ),
            1207 =>
                array (
                    'en' => 'Insurance - Dental Services',
                    'es' => 'Seguros Médicos',
                    'slugEn' => 'insurance-dental-services',
                    'slugEs' => 'seguros-medicos',
                ),
            1208 =>
                array (
                    'en' => 'Insurance - Flooding',
                    'es' => 'Seguros-Financiamiento',
                    'slugEn' => 'insurance-flooding',
                    'slugEs' => 'seguros-financiamiento',
                ),
            1209 =>
                array (
                    'en' => 'Insurance - Life',
                    'es' => 'Seguros Por Especialidad - Vida',
                    'slugEn' => 'insurance-life',
                    'slugEs' => 'seguros-por-especialidad-vida',
                ),
            1210 =>
                array (
                    'en' => 'Insurance - Medical',
                    'es' => 'Seguros - Médico',
                    'slugEn' => 'insurance-medical',
                    'slugEs' => 'seguros-medico',
                ),
            1211 =>
                array (
                    'en' => 'Insurance - Property Titles',
                    'es' => 'Seguros-Corredores',
                    'slugEn' => 'insurance-property-titles',
                    'slugEs' => 'seguros-corredores',
                ),
            1212 =>
                array (
                    'en' => 'Insurance By Specialty - Business',
                    'es' => 'Seguros Por Especialidad - Negocio',
                    'slugEn' => 'insurance-by-specialty-business',
                    'slugEs' => 'seguros-por-especialidad-negocio',
                ),
            1213 =>
                array (
                    'en' => 'Insurance by Specialty - Flood',
                    'es' => 'Seguros Por Especialidad - Inundaciones',
                    'slugEn' => 'insurance-by-specialty-flood',
                    'slugEs' => 'seguros-por-especialidad-inundaciones',
                ),
            1214 =>
                array (
                    'en' => 'Insurance by Specialty - Title Deed',
                    'es' => 'Seguros Por Especialidad - Titulos',
                    'slugEn' => 'insurance-by-specialty-title-deed',
                    'slugEs' => 'seguros-por-especialidad-titulos',
                ),
            1215 =>
                array (
                    'en' => 'Insurance-Agents And Companies',
                    'es' => 'Seguros-Agentes Y Compañías',
                    'slugEn' => 'insurance-agents-and-companies',
                    'slugEs' => 'seguros-agentes-y-companias',
                ),
            1216 =>
                array (
                    'en' => 'Insurance-Investigations',
                    'es' => 'Seguros-Investigaciones',
                    'slugEn' => 'insurance-investigations',
                    'slugEs' => 'seguros-investigaciones',
                ),
            1217 =>
                array (
                    'en' => 'Insurance-Property & Casualty',
                    'es' => 'Seguros-Propiedad Y Responsabilidad',
                    'slugEn' => 'insurance-property-casualty',
                    'slugEs' => 'seguros-propiedad-y-responsabilidad',
                ),
            1218 =>
                array (
                    'en' => 'Intensive Care',
                    'es' => 'Terapia Intensiva',
                    'slugEn' => 'intensive-care',
                    'slugEs' => 'terapia-intensiva',
                ),
            1219 =>
                array (
                    'en' => 'Intercommunication-Systems',
                    'es' => 'Intercomunicación-Sistemas',
                    'slugEn' => 'intercommunication-systems',
                    'slugEs' => 'intercomunicacion-sistemas',
                ),
            1220 =>
                array (
                    'en' => 'Interior Design',
                    'es' => 'Decoración-Interiores',
                    'slugEn' => 'interior-design',
                    'slugEs' => 'decoracion-interiores',
                ),
            1221 =>
                array (
                    'en' => 'Interior Designers',
                    'es' => 'Decoradores Interiores',
                    'slugEn' => 'interior-designers',
                    'slugEs' => 'decoradores-interiores',
                ),
            1222 =>
                array (
                    'en' => 'Internal Medicine',
                    'es' => 'Medicina Integral',
                    'slugEn' => 'internal-medicine',
                    'slugEs' => 'medicina-integral',
                ),
            1223 =>
                array (
                    'en' => 'Internet',
                    'es' => 'Internet',
                    'slugEn' => 'internet',
                    'slugEs' => 'internet',
                ),
            1224 =>
                array (
                    'en' => 'Internet - Web Site Design',
                    'es' => 'Internet-Diseño De Páginas',
                    'slugEn' => 'internet-web-site-design',
                    'slugEs' => 'internet-diseno-de-paginas',
                ),
            1225 =>
                array (
                    'en' => 'Interventional Radiology',
                    'es' => 'Radiologo Intervencional',
                    'slugEn' => 'interventional-radiology',
                    'slugEs' => 'radiologo-intervencional',
                ),
            1226 =>
                array (
                    'en' => 'Inventory - Service',
                    'es' => 'Inventario-Servicio',
                    'slugEn' => 'inventory-service',
                    'slugEs' => 'inventario-servicio',
                ),
            1227 =>
                array (
                    'en' => 'Investigators',
                    'es' => 'Investigadores',
                    'slugEn' => 'investigators',
                    'slugEs' => 'investigadores',
                ),
            1228 =>
                array (
                    'en' => 'Investments',
                    'es' => 'Inversiones',
                    'slugEn' => 'investments',
                    'slugEs' => 'inversiones',
                ),
            1229 =>
                array (
                    'en' => 'Investors',
                    'es' => 'Inversionistas',
                    'slugEn' => 'investors',
                    'slugEs' => 'inversionistas',
                ),
            1230 =>
                array (
                    'en' => 'Invisalign',
                    'es' => 'Invisalign',
                    'slugEn' => 'invisalign',
                    'slugEs' => 'invisalign',
                ),
            1231 =>
                array (
                    'en' => 'Invitations',
                    'es' => 'Invitaciones',
                    'slugEn' => 'invitations',
                    'slugEs' => 'invitaciones',
                ),
            1232 =>
                array (
                    'en' => 'Invitations - Weddings',
                    'es' => 'Invitaciones - Bodas',
                    'slugEn' => 'invitations-weddings',
                    'slugEs' => 'invitaciones-bodas',
                ),
            1233 =>
                array (
                    'en' => 'Iphone Repair',
                    'es' => 'Reparaciones de Iphone',
                    'slugEn' => 'iphone-repair',
                    'slugEs' => 'reparaciones-de-iphone',
                ),
            1234 =>
                array (
                    'en' => 'Iridology',
                    'es' => 'Iridología',
                    'slugEn' => 'iridology',
                    'slugEs' => 'iridologia',
                ),
            1235 =>
                array (
                    'en' => 'Iron Work',
                    'es' => 'Rejas',
                    'slugEn' => 'iron-work',
                    'slugEs' => 'rejas',
                ),
            1236 =>
                array (
                    'en' => 'Irrigation - Systems',
                    'es' => 'Riego-Sistemas',
                    'slugEn' => 'irrigation-systems',
                    'slugEs' => 'riego-sistemas',
                ),
            1237 =>
                array (
                    'en' => 'Irrigation and Drainage System',
                    'es' => 'Sistema de Riego y Drenaje',
                    'slugEn' => 'irrigation-and-drainage-system',
                    'slugEs' => 'sistema-de-riego-y-drenaje',
                ),
            1238 =>
                array (
                    'en' => 'Isla Verde Hotels',
                    'es' => 'Isla Verde Hospederias',
                    'slugEn' => 'isla-verde-hotels',
                    'slugEs' => 'isla-verde-hospederias',
                ),
            1239 =>
                array (
                    'en' => 'Isolation',
                    'es' => 'Aislación-Contratistas',
                    'slugEn' => 'isolation',
                    'slugEs' => 'aislacion-contratistas',
                ),
            1240 =>
                array (
                    'en' => 'Issuing Agent',
                    'es' => 'Agente Expedidor',
                    'slugEn' => 'issuing-agent',
                    'slugEs' => 'agente-expedidor',
                ),
            1241 =>
                array (
                    'en' => 'IVA',
                    'es' => 'IVA',
                    'slugEn' => 'iva',
                    'slugEs' => 'iva',
                ),
            1242 =>
                array (
                    'en' => 'Jacks',
                    'es' => 'Gatos Hidráulicos',
                    'slugEn' => 'jacks',
                    'slugEs' => 'gatos-hidraulicos',
                ),
            1243 =>
                array (
                    'en' => 'Japanese Restaurant',
                    'es' => 'Restaurantes - Tipos De Comida - Japonesa',
                    'slugEn' => 'japanese-restaurant',
                    'slugEs' => 'restaurantes-tipos-de-comida-japonesa',
                ),
            1244 =>
                array (
                    'en' => 'Jazz Classes',
                    'es' => 'Clases de Jazz',
                    'slugEn' => 'jazz-classes',
                    'slugEs' => 'clases-de-jazz',
                ),
            1245 =>
                array (
                    'en' => 'Jet Ski',
                    'es' => 'Jet Ski (Motoras Acuticas)',
                    'slugEn' => 'jet-ski',
                    'slugEs' => 'jet-ski-motoras-acuticas',
                ),
            1246 =>
                array (
                    'en' => 'Jewelers',
                    'es' => 'Joyerías',
                    'slugEn' => 'jewelers',
                    'slugEs' => 'joyerias',
                ),
            1247 =>
                array (
                    'en' => 'Jewelers - Equipment & Supplies',
                    'es' => 'Joyerías-Efectos Y Equipo',
                    'slugEn' => 'jewelers-equipment-supplies',
                    'slugEs' => 'joyerias-efectos-y-equipo',
                ),
            1248 =>
                array (
                    'en' => 'Jewelers-Repair-Cleaning',
                    'es' => 'Joyería-Reparación-Limpieza',
                    'slugEn' => 'jewelers-repair-cleaning',
                    'slugEs' => 'joyeria-reparacion-limpieza',
                ),
            1249 =>
                array (
                    'en' => 'Jewelry Accessories',
                    'es' => 'Joyería Accesorios',
                    'slugEn' => 'jewelry-accessories',
                    'slugEs' => 'joyeria-accesorios',
                ),
            1250 =>
                array (
                    'en' => 'Jewelry for Consignment',
                    'es' => 'Prendas a Consignación',
                    'slugEn' => 'jewelry-for-consignment',
                    'slugEs' => 'prendas-a-consignacion',
                ),
            1251 =>
                array (
                    'en' => 'Jewels',
                    'es' => 'Joyas',
                    'slugEn' => 'jewels',
                    'slugEs' => 'joyas',
                ),
            1252 =>
                array (
                    'en' => 'Jewels - Designers',
                    'es' => 'Joyas - Diseñadores',
                    'slugEn' => 'jewels-designers',
                    'slugEs' => 'joyas-disenadores',
                ),
            1253 =>
                array (
                    'en' => 'Juice Bar',
                    'es' => 'Juice Bar',
                    'slugEn' => 'juice-bar',
                    'slugEs' => 'juice-bar',
                ),
            1254 =>
                array (
                    'en' => 'Juices',
                    'es' => 'Jugos',
                    'slugEn' => 'juices',
                    'slugEs' => 'jugos',
                ),
            1255 =>
                array (
                    'en' => 'Juke Boxes',
                    'es' => 'Velloneras',
                    'slugEn' => 'juke-boxes',
                    'slugEs' => 'velloneras',
                ),
            1256 =>
                array (
                    'en' => 'Junkers',
                    'es' => 'Junkers (Depósito De Chatarra)',
                    'slugEn' => 'junkers',
                    'slugEs' => 'junkers-deposito-de-chatarra',
                ),
            1257 =>
                array (
                    'en' => 'Karaoke',
                    'es' => 'Karaoke',
                    'slugEn' => 'karaoke',
                    'slugEs' => 'karaoke',
                ),
            1258 =>
                array (
                    'en' => 'Kennels',
                    'es' => 'Perros-Kennels (Perreras)',
                    'slugEn' => 'kennels',
                    'slugEs' => 'perros-kennels-perreras',
                ),
            1259 =>
                array (
                    'en' => 'Keratin',
                    'es' => 'Keratina',
                    'slugEn' => 'keratin',
                    'slugEs' => 'keratina',
                ),
            1260 =>
                array (
                    'en' => 'Keys',
                    'es' => 'Llaves',
                    'slugEn' => 'keys',
                    'slugEs' => 'llaves',
                ),
            1261 =>
                array (
                    'en' => 'Kindergarten',
                    'es' => 'Kindergarten',
                    'slugEn' => 'kindergarten',
                    'slugEs' => 'kindergarten',
                ),
            1262 =>
                array (
                    'en' => 'Kitchen Cabinets & Accessories',
                    'es' => 'Gabinetes De Cocina Y Accesorios',
                    'slugEn' => 'kitchen-cabinets-accessories',
                    'slugEs' => 'gabinetes-de-cocina-y-accesorios',
                ),
            1263 =>
                array (
                    'en' => 'Kitchen-Countertops',
                    'es' => 'Cocinas - Topes',
                    'slugEn' => 'kitchen-countertops',
                    'slugEs' => 'cocinas-topes',
                ),
            1264 =>
                array (
                    'en' => 'Kitchen-Cupboards',
                    'es' => 'Cocinas-Alacenas',
                    'slugEn' => 'kitchen-cupboards',
                    'slugEs' => 'cocinas-alacenas',
                ),
            1265 =>
                array (
                    'en' => 'Kitchens',
                    'es' => 'Cocinas',
                    'slugEn' => 'kitchens',
                    'slugEs' => 'cocinas',
                ),
            1266 =>
                array (
                    'en' => 'Labels',
                    'es' => 'Etiquetas',
                    'slugEn' => 'labels',
                    'slugEs' => 'etiquetas',
                ),
            1267 =>
                array (
                    'en' => 'Labor Organizations',
                    'es' => 'Uniones Obreras',
                    'slugEn' => 'labor-organizations',
                    'slugEs' => 'uniones-obreras',
                ),
            1268 =>
                array (
                    'en' => 'Laboratories',
                    'es' => 'Laboratorios',
                    'slugEn' => 'laboratories',
                    'slugEs' => 'laboratorios',
                ),
            1269 =>
                array (
                    'en' => 'Laboratories - Bacteriological',
                    'es' => 'Laboratorios - Bacteriológicos',
                    'slugEn' => 'laboratories-bacteriological',
                    'slugEs' => 'laboratorios-bacteriologicos',
                ),
            1270 =>
                array (
                    'en' => 'Laboratories - Cardiovascular',
                    'es' => 'Laboratorios Por Especialidad - Cardiovasculares',
                    'slugEn' => 'laboratories-cardiovascular',
                    'slugEs' => 'laboratorios-por-especialidad-cardiovasculares',
                ),
            1271 =>
                array (
                    'en' => 'Laboratories - Clinical',
                    'es' => 'Laboratorios Por Especialidad - Clínicos',
                    'slugEn' => 'laboratories-clinical',
                    'slugEs' => 'laboratorios-por-especialidad-clinicos',
                ),
            1272 =>
                array (
                    'en' => 'Laboratories - Construction Materials',
                    'es' => 'Laboratorios-Materiales De Construcción',
                    'slugEn' => 'laboratories-construction-materials',
                    'slugEs' => 'laboratorios-materiales-de-construccion',
                ),
            1273 =>
                array (
                    'en' => 'Laboratories - Dental',
                    'es' => 'Laboratorios Por Especialidad - Dentales',
                    'slugEn' => 'laboratories-dental',
                    'slugEs' => 'laboratorios-por-especialidad-dentales',
                ),
            1274 =>
                array (
                    'en' => 'Laboratories - Dna Testings',
                    'es' => 'Laboratorios Por Especialidad - Pruebas Adn',
                    'slugEn' => 'laboratories-dna-testings',
                    'slugEs' => 'laboratorios-por-especialidad-pruebas-adn',
                ),
            1275 =>
                array (
                    'en' => 'Laboratories - Environmental',
                    'es' => 'Laboratorios-Ambientales',
                    'slugEn' => 'laboratories-environmental',
                    'slugEs' => 'laboratorios-ambientales',
                ),
            1276 =>
                array (
                    'en' => 'Laboratories - Equipment - Repair',
                    'es' => 'Laboratorios - Equipos - Reparación',
                    'slugEn' => 'laboratories-equipment-repair',
                    'slugEs' => 'laboratorios-equipos-reparacion',
                ),
            1277 =>
                array (
                    'en' => 'Laboratories - Industrial',
                    'es' => 'Laboratorios-Industriales',
                    'slugEn' => 'laboratories-industrial',
                    'slugEs' => 'laboratorios-industriales',
                ),
            1278 =>
                array (
                    'en' => 'Laboratories - Investigation',
                    'es' => 'Laboratorios Por Especialidad - Investigación',
                    'slugEn' => 'laboratories-investigation',
                    'slugEs' => 'laboratorios-por-especialidad-investigacion',
                ),
            1279 =>
                array (
                    'en' => 'Laboratories - Nuclear Medicine',
                    'es' => 'Laboratorios Por Especialidad - Medicina Nuclear',
                    'slugEn' => 'laboratories-nuclear-medicine',
                    'slugEs' => 'laboratorios-por-especialidad-medicina-nuclear',
                ),
            1280 =>
                array (
                    'en' => 'Laboratories - Pathology',
                    'es' => 'Laboratorios Por Especialidad - Patología',
                    'slugEn' => 'laboratories-pathology',
                    'slugEs' => 'laboratorios-por-especialidad-patologia',
                ),
            1281 =>
                array (
                    'en' => 'Laboratories - Pulmonary',
                    'es' => 'Laboratorios Por Especialidad - Pulmonar',
                    'slugEn' => 'laboratories-pulmonary',
                    'slugEs' => 'laboratorios-por-especialidad-pulmonar',
                ),
            1282 =>
                array (
                    'en' => 'Laboratories - Vascular',
                    'es' => 'Laboratorios Por Especialidad - Vascular',
                    'slugEn' => 'laboratories-vascular',
                    'slugEs' => 'laboratorios-por-especialidad-vascular',
                ),
            1283 =>
                array (
                    'en' => 'Laboratories By Specialty - Bacteriological',
                    'es' => 'Laboratorios Por Especialidad - Bacteriologico',
                    'slugEn' => 'laboratories-by-specialty-bacteriological',
                    'slugEs' => 'laboratorios-por-especialidad-bacteriologico',
                ),
            1284 =>
                array (
                    'en' => 'Laboratories By Specialty - Tests for Marriage',
                    'es' => 'Laboratorios por Especialidad - Pruebas para Matrimonio',
                    'slugEn' => 'laboratories-by-specialty-tests-for-marriage',
                    'slugEs' => 'laboratorios-por-especialidad-pruebas-para-matrimonio',
                ),
            1285 =>
                array (
                    'en' => 'Laboratories -Optics',
                    'es' => 'Laboratorios Por Especialidad - Opticas',
                    'slugEn' => 'laboratories-optics',
                    'slugEs' => 'laboratorios-por-especialidad-opticas',
                ),
            1286 =>
                array (
                    'en' => 'Laboratories Test for Marriage',
                    'es' => 'Laboratorios Pruebas para Matrimonio',
                    'slugEn' => 'laboratories-test-for-marriage',
                    'slugEs' => 'laboratorios-pruebas-para-matrimonio',
                ),
            1287 =>
                array (
                    'en' => 'Laboratories-Services',
                    'es' => 'Laboratorios-Servicios',
                    'slugEn' => 'laboratories-services',
                    'slugEs' => 'laboratorios-servicios',
                ),
            1288 =>
                array (
                    'en' => 'Laboratory Fertility Test',
                    'es' => 'Laboratorio Pruebas de Fertilidad',
                    'slugEn' => 'laboratory-fertility-test',
                    'slugEs' => 'laboratorio-pruebas-de-fertilidad',
                ),
            1289 =>
                array (
                    'en' => 'Laminations - Plastic & Paper',
                    'es' => 'Laminación',
                    'slugEn' => 'laminations-plastic-paper',
                    'slugEs' => 'laminacion',
                ),
            1290 =>
                array (
                    'en' => 'Lamps',
                    'es' => 'Lámparas',
                    'slugEn' => 'lamps',
                    'slugEs' => 'lamparas',
                ),
            1291 =>
                array (
                    'en' => 'Lamps - Emergency - Equipment & Supplies',
                    'es' => 'Lámparas De Emergencia-Efectos Y Equipo',
                    'slugEn' => 'lamps-emergency-equipment-supplies',
                    'slugEs' => 'lamparas-de-emergencia-efectos-y-equipo',
                ),
            1292 =>
                array (
                    'en' => 'Lamps - Flourescent',
                    'es' => 'Bombillas Y Tubos Fluorescentes',
                    'slugEn' => 'lamps-flourescent',
                    'slugEs' => 'bombillas-y-tubos-fluorescentes',
                ),
            1293 =>
                array (
                    'en' => 'Land Freight Transport',
                    'es' => 'Transporte de Carga Terrestre',
                    'slugEn' => 'land-freight-transport',
                    'slugEs' => 'transporte-de-carga-terrestre',
                ),
            1294 =>
                array (
                    'en' => 'Land Sale',
                    'es' => 'Terrenos',
                    'slugEn' => 'land-sale',
                    'slugEs' => 'terrenos',
                ),
            1295 =>
                array (
                    'en' => 'Landscaping',
                    'es' => 'Landscaping (Jardinería Paisajista)',
                    'slugEn' => 'landscaping',
                    'slugEs' => 'landscaping-jardineria-paisajista',
                ),
            1296 =>
                array (
                    'en' => 'Laser Hair Removal',
                    'es' => 'Depilación Láser',
                    'slugEn' => 'laser-hair-removal',
                    'slugEs' => 'depilacion-laser',
                ),
            1297 =>
                array (
                    'en' => 'Laundries',
                    'es' => 'Lavanderías',
                    'slugEn' => 'laundries',
                    'slugEs' => 'lavanderias',
                ),
            1298 =>
                array (
                    'en' => 'Laundries - Coin',
                    'es' => 'Lavanderías-Automáticas',
                    'slugEn' => 'laundries-coin',
                    'slugEs' => 'lavanderias-automaticas',
                ),
            1299 =>
                array (
                    'en' => 'Laundries - Equipment & Supplies',
                    'es' => 'Lavanderías-Efectos Y Equipo',
                    'slugEn' => 'laundries-equipment-supplies',
                    'slugEs' => 'lavanderias-efectos-y-equipo',
                ),
            1300 =>
                array (
                    'en' => 'Laundries - Industrial',
                    'es' => 'Lavanderías-Industriales',
                    'slugEn' => 'laundries-industrial',
                    'slugEs' => 'lavanderias-industriales',
                ),
            1301 =>
                array (
                    'en' => 'Laundry',
                    'es' => 'Laundry',
                    'slugEn' => 'laundry',
                    'slugEs' => 'laundry',
                ),
            1302 =>
                array (
                    'en' => 'Lawn',
                    'es' => 'Grama',
                    'slugEn' => 'lawn',
                    'slugEs' => 'grama',
                ),
            1303 =>
                array (
                    'en' => 'Lawn - Mowers',
                    'es' => 'Grama-Cortadoras',
                    'slugEn' => 'lawn-mowers',
                    'slugEs' => 'grama-cortadoras',
                ),
            1304 =>
                array (
                    'en' => 'Lawn-Artificial',
                    'es' => 'Grama-Artificial',
                    'slugEn' => 'lawn-artificial',
                    'slugEs' => 'grama-artificial',
                ),
            1305 =>
                array (
                    'en' => 'Lawyer - Inheritage',
                    'es' => 'Abogados - Herencias',
                    'slugEn' => 'lawyer-inheritage',
                    'slugEs' => 'abogados-herencias',
                ),
            1306 =>
                array (
                    'en' => 'Lawyers',
                    'es' => 'Abogados',
                    'slugEn' => 'lawyers',
                    'slugEs' => 'abogados',
                ),
            1307 =>
                array (
                    'en' => 'Lawyers - Accidents',
                    'es' => 'Abogados - Accidentes',
                    'slugEn' => 'lawyers-accidents',
                    'slugEs' => 'abogados-accidentes',
                ),
            1308 =>
                array (
                    'en' => 'Lawyers - Bankruptcy',
                    'es' => 'Abogados - Por Práctica - Quiebra',
                    'slugEn' => 'lawyers-bankruptcy',
                    'slugEs' => 'abogados-por-practica-quiebra',
                ),
            1309 =>
                array (
                    'en' => 'Lawyers - By Pracitce - Banking',
                    'es' => 'Abogados - Por Práctica - Banca',
                    'slugEn' => 'lawyers-by-pracitce-banking',
                    'slugEs' => 'abogados-por-practica-banca',
                ),
            1310 =>
                array (
                    'en' => 'Lawyers - By Practice - Civil',
                    'es' => 'Abogados - Por Práctica - Civil',
                    'slugEn' => 'lawyers-by-practice-civil',
                    'slugEs' => 'abogados-por-practica-civil',
                ),
            1311 =>
                array (
                    'en' => 'Lawyers - By Practice - Commercial Law',
                    'es' => 'Abogados - Por Práctica - Derecho Comercial',
                    'slugEn' => 'lawyers-by-practice-commercial-law',
                    'slugEs' => 'abogados-por-practica-derecho-comercial',
                ),
            1312 =>
                array (
                    'en' => 'Lawyers - By Practice - Corporate Law',
                    'es' => 'Abogados - Por Práctica - Corporaciones',
                    'slugEn' => 'lawyers-by-practice-corporate-law',
                    'slugEs' => 'abogados-por-practica-corporaciones',
                ),
            1313 =>
                array (
                    'en' => 'Lawyers - By Practice - Evirnomental',
                    'es' => 'Abogados - Por Práctica - Ambiental',
                    'slugEn' => 'lawyers-by-practice-evirnomental',
                    'slugEs' => 'abogados-por-practica-ambiental',
                ),
            1314 =>
                array (
                    'en' => 'Lawyers - By Practice - Federal Forum',
                    'es' => 'Abogados - Por Práctica - Foro Federal',
                    'slugEn' => 'lawyers-by-practice-federal-forum',
                    'slugEs' => 'abogados-por-practica-foro-federal',
                ),
            1315 =>
                array (
                    'en' => 'Lawyers - By Practice - Foreclosure',
                    'es' => 'Abogados - Por Práctica - Expropiación Forzosa',
                    'slugEn' => 'lawyers-by-practice-foreclosure',
                    'slugEs' => 'abogados-por-practica-expropiacion-forzosa',
                ),
            1316 =>
                array (
                    'en' => 'Lawyers - By Practice - Incapacity',
                    'es' => 'Abogados - Por Práctica - Incapacidad',
                    'slugEn' => 'lawyers-by-practice-incapacity',
                    'slugEs' => 'abogados-por-practica-incapacidad',
                ),
            1317 =>
                array (
                    'en' => 'Lawyers - By Practice - Incapacity of Veterans',
                    'es' => 'Abogados - Por Práctica - Incapacidad de Veteranos',
                    'slugEn' => 'lawyers-by-practice-incapacity-of-veterans',
                    'slugEs' => 'abogados-por-practica-incapacidad-de-veteranos',
                ),
            1318 =>
                array (
                    'en' => 'Lawyers - By Practice - Legislative Affairs',
                    'es' => 'Abogados - Por Práctica - Legislación',
                    'slugEn' => 'lawyers-by-practice-legislative-affairs',
                    'slugEs' => 'abogados-por-practica-legislacion',
                ),
            1319 =>
                array (
                    'en' => 'Lawyers - By Practice - Notary',
                    'es' => 'Abogados - Por Práctica - Notarios',
                    'slugEn' => 'lawyers-by-practice-notary',
                    'slugEs' => 'abogados-por-practica-notarios',
                ),
            1320 =>
                array (
                    'en' => 'Lawyers - By Practice - Seccured Home',
                    'es' => 'Abogados por práctica - Hogar Seguro',
                    'slugEn' => 'lawyers-by-practice-seccured-home',
                    'slugEs' => 'abogados-por-practica-hogar-seguro',
                ),
            1321 =>
                array (
                    'en' => 'Lawyers - By Practice - Shared Custody',
                    'es' => 'Abogados - Por Práctica - Custodia Compartida',
                    'slugEn' => 'lawyers-by-practice-shared-custody',
                    'slugEs' => 'abogados-por-practica-custodia-compartida',
                ),
            1322 =>
                array (
                    'en' => 'Lawyers - By Practice - Veterans',
                    'es' => 'Abogados - Por Práctica - Veteranos',
                    'slugEn' => 'lawyers-by-practice-veterans',
                    'slugEs' => 'abogados-por-practica-veteranos',
                ),
            1323 =>
                array (
                    'en' => 'Lawyers - By Practice- Inheritance',
                    'es' => 'Abogados - Por Práctica - Sucesiones (Herencia)',
                    'slugEn' => 'lawyers-by-practice-inheritance',
                    'slugEs' => 'abogados-por-practica-sucesiones-herencia',
                ),
            1324 =>
                array (
                    'en' => 'Lawyers - Carrying of Weapons',
                    'es' => 'Abogados - Portación de Armas',
                    'slugEn' => 'lawyers-carrying-of-weapons',
                    'slugEs' => 'abogados-portacion-de-armas',
                ),
            1325 =>
                array (
                    'en' => 'Lawyers - Child Support',
                    'es' => 'Abogados - Por Práctica - Pensión Alimenticia',
                    'slugEn' => 'lawyers-child-support',
                    'slugEs' => 'abogados-por-practica-pension-alimenticia',
                ),
            1326 =>
                array (
                    'en' => 'Lawyers - Consumer Rights',
                    'es' => 'Abogados - Derechos de Consumidores',
                    'slugEn' => 'lawyers-consumer-rights',
                    'slugEs' => 'abogados-derechos-de-consumidores',
                ),
            1327 =>
                array (
                    'en' => 'Lawyers - Criminal',
                    'es' => 'Abogados - Por Práctica - Criminal',
                    'slugEn' => 'lawyers-criminal',
                    'slugEs' => 'abogados-por-practica-criminal',
                ),
            1328 =>
                array (
                    'en' => 'Lawyers - Damages',
                    'es' => 'Abogados - Por Práctica - Daños Y Perjuicios',
                    'slugEn' => 'lawyers-damages',
                    'slugEs' => 'abogados-por-practica-danos-y-perjuicios',
                ),
            1329 =>
                array (
                    'en' => 'Lawyers - Family Matters',
                    'es' => 'Abogados - Por Práctica - Familia',
                    'slugEn' => 'lawyers-family-matters',
                    'slugEs' => 'abogados-por-practica-familia',
                ),
            1330 =>
                array (
                    'en' => 'Lawyers - Immigration',
                    'es' => 'Abogados - Por Práctica - Inmigración',
                    'slugEn' => 'lawyers-immigration',
                    'slugEs' => 'abogados-por-practica-inmigracion',
                ),
            1331 =>
                array (
                    'en' => 'Lawyers - Investment Fraud',
                    'es' => 'Abogados - Fraude Inversiones',
                    'slugEn' => 'lawyers-investment-fraud',
                    'slugEs' => 'abogados-fraude-inversiones',
                ),
            1332 =>
                array (
                    'en' => 'Lawyers - Labor',
                    'es' => 'Abogados - Por Práctica - Laboral',
                    'slugEn' => 'lawyers-labor',
                    'slugEs' => 'abogados-por-practica-laboral',
                ),
            1333 =>
                array (
                    'en' => 'Lawyers - Service Intitutions',
                    'es' => 'Abogados - Instituciones De Servicio',
                    'slugEn' => 'lawyers-service-intitutions',
                    'slugEs' => 'abogados-instituciones-de-servicio',
                ),
            1334 =>
                array (
                    'en' => 'Lawyers - The State Insurance Fund',
                    'es' => 'Abogados - Fondo del Seguro del Estado',
                    'slugEn' => 'lawyers-the-state-insurance-fund',
                    'slugEs' => 'abogados-fondo-del-seguro-del-estado',
                ),
            1335 =>
                array (
                    'en' => 'Lawyers By Practice - Health',
                    'es' => 'Abogados Por Práctica - Salud',
                    'slugEn' => 'lawyers-by-practice-health',
                    'slugEs' => 'abogados-por-practica-salud',
                ),
            1336 =>
                array (
                    'en' => 'Lawyers By Practice - Insurance',
                    'es' => 'Abogados Por Práctica - Seguros',
                    'slugEn' => 'lawyers-by-practice-insurance',
                    'slugEs' => 'abogados-por-practica-seguros',
                ),
            1337 =>
                array (
                    'en' => 'Lawyers By Practice - Mediation',
                    'es' => 'Abogados Por Práctica - Mediación (Mediadores)',
                    'slugEn' => 'lawyers-by-practice-mediation',
                    'slugEs' => 'abogados-por-practica-mediacion-mediadores',
                ),
            1338 =>
                array (
                    'en' => 'Lawyers by Practice - Medical Malpractice',
                    'es' => 'Abogados por Práctica - Impericia Médica',
                    'slugEn' => 'lawyers-by-practice-medical-malpractice',
                    'slugEs' => 'abogados-por-practica-impericia-medica',
                ),
            1339 =>
                array (
                    'en' => 'Lawyers By Practice - Social Security',
                    'es' => 'Abogados Por Práctica - Seguro Social',
                    'slugEn' => 'lawyers-by-practice-social-security',
                    'slugEs' => 'abogados-por-practica-seguro-social',
                ),
            1340 =>
                array (
                    'en' => 'Lawyers Contributions',
                    'es' => 'Abogados Contribuciones',
                    'slugEn' => 'lawyers-contributions',
                    'slugEs' => 'abogados-contribuciones',
                ),
            1341 =>
                array (
                    'en' => 'Lawyers Notaries',
                    'es' => 'Abogados Notarios',
                    'slugEn' => 'lawyers-notaries',
                    'slugEs' => 'abogados-notarios',
                ),
            1342 =>
                array (
                    'en' => 'Lawyers-Service Bureaus',
                    'es' => 'Abogados-Servicios',
                    'slugEn' => 'lawyers-service-bureaus',
                    'slugEs' => 'abogados-servicios',
                ),
            1343 =>
                array (
                    'en' => 'Leaf Springs',
                    'es' => 'Sopandas',
                    'slugEn' => 'leaf-springs',
                    'slugEs' => 'sopandas',
                ),
            1344 =>
                array (
                    'en' => 'Leasing',
                    'es' => 'Arrendamiento',
                    'slugEn' => 'leasing',
                    'slugEs' => 'arrendamiento',
                ),
            1345 =>
                array (
                    'en' => 'Leather Goods',
                    'es' => 'Cuero Y Piel-Artículos',
                    'slugEn' => 'leather-goods',
                    'slugEs' => 'cuero-y-piel-articulos',
                ),
            1346 =>
                array (
                    'en' => 'LED',
                    'es' => 'LED',
                    'slugEn' => 'led',
                    'slugEs' => 'led',
                ),
            1347 =>
                array (
                    'en' => 'LED Lamps',
                    'es' => 'Lámparas LED',
                    'slugEn' => 'led-lamps',
                    'slugEs' => 'lamparas-led',
                ),
            1348 =>
                array (
                    'en' => 'Legal Service',
                    'es' => 'Servicios Legales',
                    'slugEn' => 'legal-service',
                    'slugEs' => 'servicios-legales',
                ),
            1349 =>
                array (
                    'en' => 'Legislative - Information',
                    'es' => 'Legislación-Información',
                    'slugEn' => 'legislative-information',
                    'slugEs' => 'legislacion-informacion',
                ),
            1350 =>
                array (
                    'en' => 'Levittown Barbers Shop',
                    'es' => 'Levittown Barberias',
                    'slugEn' => 'levittown-barbers-shop',
                    'slugEs' => 'levittown-barberias',
                ),
            1351 =>
                array (
                    'en' => 'Libraries',
                    'es' => 'Bibliotecas',
                    'slugEn' => 'libraries',
                    'slugEs' => 'bibliotecas',
                ),
            1352 =>
                array (
                    'en' => 'Licenses Service',
                    'es' => 'Licencias Servicios',
                    'slugEn' => 'licenses-service',
                    'slugEs' => 'licencias-servicios',
                ),
            1353 =>
                array (
                    'en' => 'Lifeguards - Services',
                    'es' => 'Salvavidas - Servicios',
                    'slugEn' => 'lifeguards-services',
                    'slugEs' => 'salvavidas-servicios',
                ),
            1354 =>
                array (
                    'en' => 'Light Mechanics',
                    'es' => 'Mecánica Liviana',
                    'slugEn' => 'light-mechanics',
                    'slugEs' => 'mecanica-liviana',
                ),
            1355 =>
                array (
                    'en' => 'Limousine',
                    'es' => 'Limosinas',
                    'slugEn' => 'limousine',
                    'slugEs' => 'limosinas',
                ),
            1356 =>
                array (
                    'en' => 'Linen',
                    'es' => 'Ropa De Casa',
                    'slugEn' => 'linen',
                    'slugEs' => 'ropa-de-casa',
                ),
            1357 =>
                array (
                    'en' => 'Liquors',
                    'es' => 'Licores',
                    'slugEn' => 'liquors',
                    'slugEs' => 'licores',
                ),
            1358 =>
                array (
                    'en' => 'Liquors - Distilleries',
                    'es' => 'Licores-Destilerías',
                    'slugEn' => 'liquors-distilleries',
                    'slugEs' => 'licores-destilerias',
                ),
            1359 =>
                array (
                    'en' => 'Liquors - Wholesale',
                    'es' => 'Licores-Importadores',
                    'slugEn' => 'liquors-wholesale',
                    'slugEs' => 'licores-importadores',
                ),
            1360 =>
                array (
                    'en' => 'Literacy',
                    'es' => 'Lectoescritura',
                    'slugEn' => 'literacy',
                    'slugEs' => 'lectoescritura',
                ),
            1361 =>
                array (
                    'en' => 'Lithotripsy',
                    'es' => 'Litotricia',
                    'slugEn' => 'lithotripsy',
                    'slugEs' => 'litotricia',
                ),
            1362 =>
                array (
                    'en' => 'Live Music',
                    'es' => 'Música en Vivo',
                    'slugEn' => 'live-music',
                    'slugEs' => 'musica-en-vivo',
                ),
            1363 =>
                array (
                    'en' => 'Loans',
                    'es' => 'Préstamos',
                    'slugEn' => 'loans',
                    'slugEs' => 'prestamos',
                ),
            1364 =>
                array (
                    'en' => 'Lockers',
                    'es' => 'Lockers (Armarios)',
                    'slugEn' => 'lockers',
                    'slugEs' => 'lockers-armarios',
                ),
            1365 =>
                array (
                    'en' => 'Locks',
                    'es' => 'Cerraduras',
                    'slugEn' => 'locks',
                    'slugEs' => 'cerraduras',
                ),
            1366 =>
                array (
                    'en' => 'Locks-Electric',
                    'es' => 'Cerraduras-Eléctricas',
                    'slugEn' => 'locks-electric',
                    'slugEs' => 'cerraduras-electricas',
                ),
            1367 =>
                array (
                    'en' => 'Locksmiths',
                    'es' => 'Cerrajeros',
                    'slugEn' => 'locksmiths',
                    'slugEs' => 'cerrajeros',
                ),
            1368 =>
                array (
                    'en' => 'Lodges',
                    'es' => 'Logias',
                    'slugEn' => 'lodges',
                    'slugEs' => 'logias',
                ),
            1369 =>
                array (
                    'en' => 'Logistics',
                    'es' => 'Logística',
                    'slugEn' => 'logistics',
                    'slugEs' => 'logistica',
                ),
            1370 =>
                array (
                    'en' => 'Long Distance - Telephone Companies',
                    'es' => 'Larga Distancia-Telefónica Compañías',
                    'slugEn' => 'long-distance-telephone-companies',
                    'slugEs' => 'larga-distancia-telefonica-companias',
                ),
            1371 =>
                array (
                    'en' => 'Longboards Skate',
                    'es' => 'Longboards',
                    'slugEn' => 'longboards-skate',
                    'slugEs' => 'longboards',
                ),
            1372 =>
                array (
                    'en' => 'Lottery',
                    'es' => 'Lotería',
                    'slugEn' => 'lottery',
                    'slugEs' => 'loteria',
                ),
            1373 =>
                array (
                    'en' => 'Lubricating Oil',
                    'es' => 'Aceites Y Grasas Lubricantes',
                    'slugEn' => 'lubricating-oil',
                    'slugEs' => 'aceites-y-grasas-lubricantes',
                ),
            1374 =>
                array (
                    'en' => 'Luggage',
                    'es' => 'Equipaje',
                    'slugEn' => 'luggage',
                    'slugEs' => 'equipaje',
                ),
            1375 =>
                array (
                    'en' => 'Lumber',
                    'es' => 'Maderas',
                    'slugEn' => 'lumber',
                    'slugEs' => 'maderas',
                ),
            1376 =>
                array (
                    'en' => 'Lumber - Workshops',
                    'es' => 'Maderas-Talleres',
                    'slugEn' => 'lumber-workshops',
                    'slugEs' => 'maderas-talleres',
                ),
            1377 =>
                array (
                    'en' => 'Lunch Specials',
                    'es' => 'Especiales de Almuerzo',
                    'slugEn' => 'lunch-specials',
                    'slugEs' => 'especiales-de-almuerzo',
                ),
            1378 =>
                array (
                    'en' => 'Lymphatic Drainage',
                    'es' => 'Drenaje Linfático',
                    'slugEn' => 'lymphatic-drainage',
                    'slugEs' => 'drenaje-linfatico',
                ),
            1379 =>
                array (
                    'en' => 'Machine - Tools',
                    'es' => 'Maquinaria-Herramientas',
                    'slugEn' => 'machine-tools',
                    'slugEs' => 'maquinaria-herramientas',
                ),
            1380 =>
                array (
                    'en' => 'Machine Shop',
                    'es' => 'Machine Shop',
                    'slugEn' => 'machine-shop',
                    'slugEs' => 'machine-shop',
                ),
            1381 =>
                array (
                    'en' => 'Machine Shops',
                    'es' => 'Talleres-Mecánicos',
                    'slugEn' => 'machine-shops',
                    'slugEs' => 'talleres-mecanicos',
                ),
            1382 =>
                array (
                    'en' => 'Machine Shops - Equipment & Supplies',
                    'es' => 'Talleres-Mecánicos-Efectos Y Equipo',
                    'slugEn' => 'machine-shops-equipment-supplies',
                    'slugEs' => 'talleres-mecanicos-efectos-y-equipo',
                ),
            1383 =>
                array (
                    'en' => 'Machinery',
                    'es' => 'Maquinaria',
                    'slugEn' => 'machinery',
                    'slugEs' => 'maquinaria',
                ),
            1384 =>
                array (
                    'en' => 'Machinery - Industrial',
                    'es' => 'Maquinaria-Industrial',
                    'slugEn' => 'machinery-industrial',
                    'slugEs' => 'maquinaria-industrial',
                ),
            1385 =>
                array (
                    'en' => 'Machinery - Parts & Accessories',
                    'es' => 'Maquinaria-Accesorios Y Piezas',
                    'slugEn' => 'machinery-parts-accessories',
                    'slugEs' => 'maquinaria-accesorios-y-piezas',
                ),
            1386 =>
                array (
                    'en' => 'Machinery - Rental',
                    'es' => 'Maquinaria-Alquiler',
                    'slugEn' => 'machinery-rental',
                    'slugEs' => 'maquinaria-alquiler',
                ),
            1387 =>
                array (
                    'en' => 'Machinery - Repair',
                    'es' => 'Maquinaria-Reparación',
                    'slugEn' => 'machinery-repair',
                    'slugEs' => 'maquinaria-reparacion',
                ),
            1388 =>
                array (
                    'en' => 'Machines -Entertainment',
                    'es' => 'Máquinas-Diversiones',
                    'slugEn' => 'machines-entertainment',
                    'slugEs' => 'maquinas-diversiones',
                ),
            1389 =>
                array (
                    'en' => 'Magazines',
                    'es' => 'Revistas',
                    'slugEn' => 'magazines',
                    'slugEs' => 'revistas',
                ),
            1390 =>
                array (
                    'en' => 'Magazines - Wholesale',
                    'es' => 'Revistas-Distribuidores',
                    'slugEn' => 'magazines-wholesale',
                    'slugEs' => 'revistas-distribuidores',
                ),
            1391 =>
                array (
                    'en' => 'Magic',
                    'es' => 'Magos',
                    'slugEn' => 'magic',
                    'slugEs' => 'magos',
                ),
            1392 =>
                array (
                    'en' => 'Magic - Equipment & Supplies',
                    'es' => 'Magia-Efectos Y Equipo',
                    'slugEn' => 'magic-equipment-supplies',
                    'slugEs' => 'magia-efectos-y-equipo',
                ),
            1393 =>
                array (
                    'en' => 'Magnetic Resonance',
                    'es' => 'Resonancia Magnética',
                    'slugEn' => 'magnetic-resonance',
                    'slugEs' => 'resonancia-magnetica',
                ),
            1394 =>
                array (
                    'en' => 'Mail Order - Companies',
                    'es' => 'Ventas Por Catálogo',
                    'slugEn' => 'mail-order-companies',
                    'slugEs' => 'ventas-por-catalogo',
                ),
            1395 =>
                array (
                    'en' => 'Mailboxes - Retail',
                    'es' => 'Buzones-Ventas',
                    'slugEn' => 'mailboxes-retail',
                    'slugEs' => 'buzones-ventas',
                ),
            1396 =>
                array (
                    'en' => 'Mailboxes - Stainless Steel',
                    'es' => 'Buzones - Acero Inoxidable',
                    'slugEn' => 'mailboxes-stainless-steel',
                    'slugEs' => 'buzones-acero-inoxidable',
                ),
            1397 =>
                array (
                    'en' => 'Maintenance',
                    'es' => 'Maintenance',
                    'slugEn' => 'maintenance',
                    'slugEs' => 'maintenance',
                ),
            1398 =>
                array (
                    'en' => 'Maintenance - Home and Office',
                    'es' => 'Mantenimiento - Hogar y Oficina',
                    'slugEn' => 'maintenance-home-and-office',
                    'slugEs' => 'mantenimiento-hogar-y-oficina',
                ),
            1399 =>
                array (
                    'en' => 'Maintenance - Service',
                    'es' => 'Mantenimiento - Servicio',
                    'slugEn' => 'maintenance-service',
                    'slugEs' => 'mantenimiento-servicio',
                ),
            1400 =>
                array (
                    'en' => 'Maintenance Green Areas',
                    'es' => 'Mantenimiento Áreas Verdes',
                    'slugEn' => 'maintenance-green-areas',
                    'slugEs' => 'mantenimiento-areas-verdes',
                ),
            1401 =>
                array (
                    'en' => 'Maintenance Service - Industrial',
                    'es' => 'Mantenimiento Industrial',
                    'slugEn' => 'maintenance-service-industrial',
                    'slugEs' => 'mantenimiento-industrial',
                ),
            1402 =>
                array (
                    'en' => 'Maintenance-Services',
                    'es' => 'Servicios-Mantenimiento',
                    'slugEn' => 'maintenance-services',
                    'slugEs' => 'servicios-mantenimiento',
                ),
            1403 =>
                array (
                    'en' => 'Makeup Artist',
                    'es' => 'Maquillista',
                    'slugEn' => 'makeup-artist',
                    'slugEs' => 'maquillista',
                ),
            1404 =>
                array (
                    'en' => 'Mamography',
                    'es' => 'Mamografías',
                    'slugEn' => 'mamography',
                    'slugEs' => 'mamografias',
                ),
            1405 =>
                array (
                    'en' => 'Man Suit',
                    'es' => 'Traje de Hombre',
                    'slugEn' => 'man-suit',
                    'slugEs' => 'traje-de-hombre',
                ),
            1406 =>
                array (
                    'en' => 'Manicure',
                    'es' => 'Manicure',
                    'slugEn' => 'manicure',
                    'slugEs' => 'manicure',
                ),
            1407 =>
                array (
                    'en' => 'Manicure - Pedicure (Nails)',
                    'es' => 'Manicura - Pedicura (Uñas)',
                    'slugEn' => 'manicure-pedicure-nails',
                    'slugEs' => 'manicura-pedicura-unas',
                ),
            1408 =>
                array (
                    'en' => 'Manufacture - Agents',
                    'es' => 'Fábricas-Representantes',
                    'slugEn' => 'manufacture-agents',
                    'slugEs' => 'fabricas-representantes',
                ),
            1409 =>
                array (
                    'en' => 'Maps',
                    'es' => 'Mapas',
                    'slugEn' => 'maps',
                    'slugEs' => 'mapas',
                ),
            1410 =>
                array (
                    'en' => 'Marble',
                    'es' => 'Mármol',
                    'slugEn' => 'marble',
                    'slugEs' => 'marmol',
                ),
            1411 =>
                array (
                    'en' => 'Marcial Arts',
                    'es' => 'Artes Marciales',
                    'slugEn' => 'marcial-arts',
                    'slugEs' => 'artes-marciales',
                ),
            1412 =>
                array (
                    'en' => 'Mariachi',
                    'es' => 'Mariachi',
                    'slugEn' => 'mariachi',
                    'slugEs' => 'mariachi',
                ),
            1413 =>
                array (
                    'en' => 'Marinas',
                    'es' => 'Marinas',
                    'slugEn' => 'marinas',
                    'slugEs' => 'marinas',
                ),
            1414 =>
                array (
                    'en' => 'Marinas - Equipment & Supplies',
                    'es' => 'Marinas-Efectos Y Equipo',
                    'slugEn' => 'marinas-equipment-supplies',
                    'slugEs' => 'marinas-efectos-y-equipo',
                ),
            1415 =>
                array (
                    'en' => 'Marine - Electrical Equipment - Repair',
                    'es' => 'Marino-Equipo',
                    'slugEn' => 'marine-electrical-equipment-repair',
                    'slugEs' => 'marino-equipo',
                ),
            1416 =>
                array (
                    'en' => 'Marine - Surveyors',
                    'es' => 'Marítima-Inspección',
                    'slugEn' => 'marine-surveyors',
                    'slugEs' => 'maritima-inspeccion',
                ),
            1417 =>
                array (
                    'en' => 'Marine Refrigeration',
                    'es' => 'Refrigeración Marina',
                    'slugEn' => 'marine-refrigeration',
                    'slugEs' => 'refrigeracion-marina',
                ),
            1418 =>
                array (
                    'en' => 'Maritime Terminal',
                    'es' => 'Terminal Maritimo',
                    'slugEn' => 'maritime-terminal',
                    'slugEs' => 'terminal-maritimo',
                ),
            1419 =>
                array (
                    'en' => 'Marketing - Direct',
                    'es' => 'Mercadeo Directo',
                    'slugEn' => 'marketing-direct',
                    'slugEs' => 'mercadeo-directo',
                ),
            1420 =>
                array (
                    'en' => 'Marketing - Services',
                    'es' => 'Mercadeo-Servicios',
                    'slugEn' => 'marketing-services',
                    'slugEs' => 'mercadeo-servicios',
                ),
            1421 =>
                array (
                    'en' => 'Marketing Research & Analysis',
                    'es' => 'Mercadeo-Estudios',
                    'slugEn' => 'marketing-research-analysis',
                    'slugEs' => 'mercadeo-estudios',
                ),
            1422 =>
                array (
                    'en' => 'Massage',
                    'es' => 'Masajistas',
                    'slugEn' => 'massage',
                    'slugEs' => 'masajistas',
                ),
            1423 =>
                array (
                    'en' => 'Massage Therapy',
                    'es' => 'Masajes Terapéuticos',
                    'slugEn' => 'massage-therapy',
                    'slugEs' => 'masajes-terapeuticos',
                ),
            1424 =>
                array (
                    'en' => 'Massages',
                    'es' => 'Masajes',
                    'slugEn' => 'massages',
                    'slugEs' => 'masajes',
                ),
            1425 =>
                array (
                    'en' => 'Materials-Construction',
                    'es' => 'Construcción-Materiales',
                    'slugEn' => 'materials-construction',
                    'slugEs' => 'construccion-materiales',
                ),
            1426 =>
                array (
                    'en' => 'Mattresses',
                    'es' => 'Mattresses (Colchones)',
                    'slugEn' => 'mattresses',
                    'slugEs' => 'mattresses-colchones',
                ),
            1427 =>
                array (
                    'en' => 'Mattresses - Manufacture',
                    'es' => 'Mattresses (Colchones)-Fábricas',
                    'slugEn' => 'mattresses-manufacture',
                    'slugEs' => 'mattresses-colchones-fabricas',
                ),
            1428 =>
                array (
                    'en' => 'Meat',
                    'es' => 'Carnes',
                    'slugEn' => 'meat',
                    'slugEs' => 'carnes',
                ),
            1429 =>
                array (
                    'en' => 'Meat - Brokers',
                    'es' => 'Carnes-Importadores',
                    'slugEn' => 'meat-brokers',
                    'slugEs' => 'carnes-importadores',
                ),
            1430 =>
                array (
                    'en' => 'Mechanical Seals',
                    'es' => 'Sellos Mecánicos',
                    'slugEn' => 'mechanical-seals',
                    'slugEs' => 'sellos-mecanicos',
                ),
            1431 =>
                array (
                    'en' => 'Mechanics',
                    'es' => 'Mecánica',
                    'slugEn' => 'mechanics',
                    'slugEs' => 'mecanica',
                ),
            1432 =>
                array (
                    'en' => 'Mechanics Trucks',
                    'es' => 'Mecánica de Camiones',
                    'slugEn' => 'mechanics-trucks',
                    'slugEs' => 'mecanica-de-camiones',
                ),
            1433 =>
                array (
                    'en' => 'Mechinery - Construction',
                    'es' => 'Maquinaria-Construcción',
                    'slugEn' => 'mechinery-construction',
                    'slugEs' => 'maquinaria-construccion',
                ),
            1434 =>
                array (
                    'en' => 'Medical - Equipment & Supplies',
                    'es' => 'Médicos - Efectos y Equipo',
                    'slugEn' => 'medical-equipment-supplies',
                    'slugEs' => 'medicos-efectos-y-equipo',
                ),
            1435 =>
                array (
                    'en' => 'Medical Centers - Services',
                    'es' => 'Médicos-Centros',
                    'slugEn' => 'medical-centers-services',
                    'slugEs' => 'medicos-centros',
                ),
            1436 =>
                array (
                    'en' => 'Medical Equipment',
                    'es' => 'Equipos Médicos',
                    'slugEn' => 'medical-equipment',
                    'slugEs' => 'equipos-medicos',
                ),
            1437 =>
                array (
                    'en' => 'Medical Groups',
                    'es' => 'Médicos-Grupos',
                    'slugEn' => 'medical-groups',
                    'slugEs' => 'medicos-grupos',
                ),
            1438 =>
                array (
                    'en' => 'Medical Services',
                    'es' => 'Médicos-Servicios',
                    'slugEn' => 'medical-services',
                    'slugEs' => 'medicos-servicios',
                ),
            1439 =>
                array (
                    'en' => 'Medical Specialist Transplant Liver and Pancreas',
                    'es' => 'Médico Especialista Trasplante de Hígado y Páncreas',
                    'slugEn' => 'medical-specialist-transplant-liver-and-pancreas',
                    'slugEs' => 'medico-especialista-trasplante-de-higado-y-pancreas',
                ),
            1440 =>
                array (
                    'en' => 'Medical Specialists - Dizziness and Imbalances',
                    'es' => 'Médicos Especialistas - Mareos y Desbalances',
                    'slugEn' => 'medical-specialists-dizziness-and-imbalances',
                    'slugEs' => 'medicos-especialistas-mareos-y-desbalances',
                ),
            1441 =>
                array (
                    'en' => 'Medical Specialists - Interventional Nephrology',
                    'es' => 'Médicos Especialistas - Nefrología Intervencional',
                    'slugEn' => 'medical-specialists-interventional-nephrology',
                    'slugEs' => 'medicos-especialistas-nefrologia-intervencional',
                ),
            1442 =>
                array (
                    'en' => 'Medical Specialists - Medical Genetics',
                    'es' => 'Médicos Especilistas - Génetica Médica',
                    'slugEn' => 'medical-specialists-medical-genetics',
                    'slugEs' => 'medicos-especilistas-genetica-medica',
                ),
            1443 =>
                array (
                    'en' => 'Medical Specialists - Neurophthalmology',
                    'es' => 'Médicos Especialistas - Neuroftalmología',
                    'slugEn' => 'medical-specialists-neurophthalmology',
                    'slugEs' => 'medicos-especialistas-neuroftalmologia',
                ),
            1444 =>
                array (
                    'en' => 'Medical Specialists - Physical Therapy (Physical Medicine & Rehabilitation)',
                    'es' => 'Médicos Especialistas - Fisiatría (Medicina Física y Rehabilitación)',
                    'slugEn' => 'medical-specialists-physical-therapy-physical-medicine-rehabilitation',
                    'slugEs' => 'medicos-especialistas-fisiatria-medicina-fisica-y-rehabilitacion',
                ),
            1445 =>
                array (
                    'en' => 'Medical Specialists - Plastic Surgery',
                    'es' => 'Médicos Especialistas - Cirugía Plástica',
                    'slugEn' => 'medical-specialists-plastic-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-plastica',
                ),
            1446 =>
                array (
                    'en' => 'Medical Specialists Liver and Pancreas',
                    'es' => 'Médicos Especialistas Hígado y Páncreas',
                    'slugEn' => 'medical-specialists-liver-and-pancreas',
                    'slugEs' => 'medicos-especialistas-higado-y-pancreas',
                ),
            1447 =>
                array (
                    'en' => 'Medical Specialists Otolaryngology',
                    'es' => 'Médicos Especialistas Otorrinolaringología',
                    'slugEn' => 'medical-specialists-otolaryngology',
                    'slugEs' => 'medicos-especialistas-otorrinolaringologia',
                ),
            1448 =>
                array (
                    'en' => 'Medical Specialists -Psychiatry',
                    'es' => 'Médicos Especialistas - Siquiatría',
                    'slugEn' => 'medical-specialists-psychiatry',
                    'slugEs' => 'medicos-especialistas-siquiatria',
                ),
            1449 =>
                array (
                    'en' => 'Medicinal Cannabis',
                    'es' => 'Cannabis Medicinal',
                    'slugEn' => 'medicinal-cannabis',
                    'slugEs' => 'cannabis-medicinal',
                ),
            1450 =>
                array (
                    'en' => 'Medicinal Herbs',
                    'es' => 'Botánicas',
                    'slugEn' => 'medicinal-herbs',
                    'slugEs' => 'botanicas',
                ),
            1451 =>
                array (
                    'en' => 'Medicinal Products',
                    'es' => 'Productos Medicinales',
                    'slugEn' => 'medicinal-products',
                    'slugEs' => 'productos-medicinales',
                ),
            1452 =>
                array (
                    'en' => 'Medicine',
                    'es' => 'Medicina',
                    'slugEn' => 'medicine',
                    'slugEs' => 'medicina',
                ),
            1453 =>
                array (
                    'en' => 'Medicine - Natural',
                    'es' => 'Medicina Natural',
                    'slugEn' => 'medicine-natural',
                    'slugEs' => 'medicina-natural',
                ),
            1454 =>
                array (
                    'en' => 'Medicine - Regenerative',
                    'es' => 'Medicina Antienvejecimiento Y Regenerativa',
                    'slugEn' => 'medicine-regenerative',
                    'slugEs' => 'medicina-antienvejecimiento-y-regenerativa',
                ),
            1455 =>
                array (
                    'en' => 'Medicine - Sports',
                    'es' => 'Medicina Deportiva',
                    'slugEn' => 'medicine-sports',
                    'slugEs' => 'medicina-deportiva',
                ),
            1456 =>
                array (
                    'en' => 'Medicine Specialist - Laser Surgery',
                    'es' => 'Médicos Especialistas - Cirugia Laser',
                    'slugEn' => 'medicine-specialist-laser-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-laser',
                ),
            1457 =>
                array (
                    'en' => 'Medicine Specialist - Parapsychologist',
                    'es' => 'Médicos Especialistas - Parasicologos',
                    'slugEn' => 'medicine-specialist-parapsychologist',
                    'slugEs' => 'medicos-especialistas-parasicologos',
                ),
            1458 =>
                array (
                    'en' => 'Medicine Specialist - Phlebology',
                    'es' => 'Médicos Especialistas - Flebología',
                    'slugEn' => 'medicine-specialist-phlebology',
                    'slugEs' => 'medicos-especialistas-flebologia',
                ),
            1459 =>
                array (
                    'en' => 'Medicine Specialist - Proctology',
                    'es' => 'Medicos Especialistas - Proctologia',
                    'slugEn' => 'medicine-specialist-proctology',
                    'slugEs' => 'medicos-especialistas-proctologia',
                ),
            1460 =>
                array (
                    'en' => 'Medicine Specialist - Radiology-Pediatric',
                    'es' => 'Medicos Especialistas - Radiologia-Pediatrica',
                    'slugEn' => 'medicine-specialist-radiology-pediatric',
                    'slugEs' => 'medicos-especialistas-radiologia-pediatrica',
                ),
            1461 =>
                array (
                    'en' => 'Medicine Specialist - Reumatology-Pediatric',
                    'es' => 'Medicos Especialistas - Reumatologia-Pediatrica',
                    'slugEn' => 'medicine-specialist-reumatology-pediatric',
                    'slugEs' => 'medicos-especialistas-reumatologia-pediatrica',
                ),
            1462 =>
                array (
                    'en' => 'Medicine Specialist - Sexology',
                    'es' => 'Médicos Especialistas - Sexologia',
                    'slugEn' => 'medicine-specialist-sexology',
                    'slugEs' => 'medicos-especialistas-sexologia',
                ),
            1463 =>
                array (
                    'en' => 'Medicine Specialist - Uroginecology',
                    'es' => 'Medicos Especialistas - Uroginecologia',
                    'slugEn' => 'medicine-specialist-uroginecology',
                    'slugEs' => 'medicos-especialistas-uroginecologia',
                ),
            1464 =>
                array (
                    'en' => 'Men\'s Boutique',
                    'es' => 'Boutique Caballeros',
                    'slugEn' => 'men-s-boutique',
                    'slugEs' => 'boutique-caballeros',
                ),
            1465 =>
                array (
                    'en' => 'Mesh',
                    'es' => 'Mesh',
                    'slugEn' => 'mesh',
                    'slugEs' => 'mesh',
                ),
            1466 =>
                array (
                    'en' => 'Messenger Service',
                    'es' => 'Mensajería',
                    'slugEn' => 'messenger-service',
                    'slugEs' => 'mensajeria',
                ),
            1467 =>
                array (
                    'en' => 'Metal doors',
                    'es' => 'Puertas de Metal',
                    'slugEn' => 'metal-doors',
                    'slugEs' => 'puertas-de-metal',
                ),
            1468 =>
                array (
                    'en' => 'Metals - Buying & Selling',
                    'es' => 'Metales-Compraventa',
                    'slugEn' => 'metals-buying-selling',
                    'slugEs' => 'metales-compraventa',
                ),
            1469 =>
                array (
                    'en' => 'Metals Recycling',
                    'es' => 'Reciclaje-Metales',
                    'slugEn' => 'metals-recycling',
                    'slugEs' => 'reciclaje-metales',
                ),
            1470 =>
                array (
                    'en' => 'Mexican Food',
                    'es' => 'Comida Mexicana',
                    'slugEn' => 'mexican-food',
                    'slugEs' => 'comida-mexicana',
                ),
            1471 =>
                array (
                    'en' => 'Mexican Restaurants',
                    'es' => 'Restaurantes Mexicanos',
                    'slugEn' => 'mexican-restaurants',
                    'slugEs' => 'restaurantes-mexicanos',
                ),
            1472 =>
                array (
                    'en' => 'Microfilm-Materials and Equipment',
                    'es' => 'Microfilmacion-Materiales Y Equipo',
                    'slugEn' => 'microfilm-materials-and-equipment',
                    'slugEs' => 'microfilmacion-materiales-y-equipo',
                ),
            1473 =>
                array (
                    'en' => 'Microfilm-Services',
                    'es' => 'Microfilmacion-Servicios',
                    'slugEn' => 'microfilm-services',
                    'slugEs' => 'microfilmacion-servicios',
                ),
            1474 =>
                array (
                    'en' => 'Microscopes',
                    'es' => 'Microscopios',
                    'slugEn' => 'microscopes',
                    'slugEs' => 'microscopios',
                ),
            1475 =>
                array (
                    'en' => 'Milkshakes',
                    'es' => 'Batidas',
                    'slugEn' => 'milkshakes',
                    'slugEs' => 'batidas',
                ),
            1476 =>
                array (
                    'en' => 'Mirrors',
                    'es' => 'Mirrors',
                    'slugEn' => 'mirrors',
                    'slugEs' => 'mirrors',
                ),
            1477 =>
                array (
                    'en' => 'Mixology',
                    'es' => 'Mixología',
                    'slugEn' => 'mixology',
                    'slugEs' => 'mixologia',
                ),
            1478 =>
                array (
                    'en' => 'Modeling Agency',
                    'es' => 'Agencia de Modelaje',
                    'slugEn' => 'modeling-agency',
                    'slugEs' => 'agencia-de-modelaje',
                ),
            1479 =>
                array (
                    'en' => 'Modular Systems',
                    'es' => 'Sistemas Modulares',
                    'slugEn' => 'modular-systems',
                    'slugEs' => 'sistemas-modulares',
                ),
            1480 =>
                array (
                    'en' => 'Mofongos',
                    'es' => 'Mofongos',
                    'slugEn' => 'mofongos',
                    'slugEs' => 'mofongos',
                ),
            1481 =>
                array (
                    'en' => 'Mold- Remediation',
                    'es' => 'Hongo-Remediación',
                    'slugEn' => 'mold-remediation',
                    'slugEs' => 'hongo-remediacion',
                ),
            1482 =>
                array (
                    'en' => 'Moldings',
                    'es' => 'Molduras',
                    'slugEn' => 'moldings',
                    'slugEs' => 'molduras',
                ),
            1483 =>
                array (
                    'en' => 'Molecular Gastronomy',
                    'es' => 'Gastronomía Molecular',
                    'slugEn' => 'molecular-gastronomy',
                    'slugEs' => 'gastronomia-molecular',
                ),
            1484 =>
                array (
                    'en' => 'Mortgage Loans',
                    'es' => 'Préstamos Hipotecarios',
                    'slugEn' => 'mortgage-loans',
                    'slugEs' => 'prestamos-hipotecarios',
                ),
            1485 =>
                array (
                    'en' => 'Mortgages',
                    'es' => 'Hipotecas',
                    'slugEn' => 'mortgages',
                    'slugEs' => 'hipotecas',
                ),
            1486 =>
                array (
                    'en' => 'Mosaics',
                    'es' => 'Mosaicos',
                    'slugEn' => 'mosaics',
                    'slugEs' => 'mosaicos',
                ),
            1487 =>
                array (
                    'en' => 'Motels',
                    'es' => 'Moteles',
                    'slugEn' => 'motels',
                    'slugEs' => 'moteles',
                ),
            1488 =>
                array (
                    'en' => 'Motivation Workshops',
                    'es' => 'Talleres de Motivación',
                    'slugEn' => 'motivation-workshops',
                    'slugEs' => 'talleres-de-motivacion',
                ),
            1489 =>
                array (
                    'en' => 'Motorcycle- Rent',
                    'es' => 'Motocicletas-Alquiler',
                    'slugEn' => 'motorcycle-rent',
                    'slugEs' => 'motocicletas-alquiler',
                ),
            1490 =>
                array (
                    'en' => 'Motorcycles',
                    'es' => 'Motoras',
                    'slugEn' => 'motorcycles',
                    'slugEs' => 'motoras',
                ),
            1491 =>
                array (
                    'en' => 'Motorcycles - Parts & Accessories',
                    'es' => 'Motocicletas-Accesorios Y Piezas',
                    'slugEn' => 'motorcycles-parts-accessories',
                    'slugEs' => 'motocicletas-accesorios-y-piezas',
                ),
            1492 =>
                array (
                    'en' => 'Motorized Curtains',
                    'es' => 'Cortinas Motorizadas',
                    'slugEn' => 'motorized-curtains',
                    'slugEs' => 'cortinas-motorizadas',
                ),
            1493 =>
                array (
                    'en' => 'Motors - Electric',
                    'es' => 'Motores-Eléctricos',
                    'slugEn' => 'motors-electric',
                    'slugEs' => 'motores-electricos',
                ),
            1494 =>
                array (
                    'en' => 'Motors - Electric - Repair',
                    'es' => 'Motores-Eléctricos-Reparación',
                    'slugEn' => 'motors-electric-repair',
                    'slugEs' => 'motores-electricos-reparacion',
                ),
            1495 =>
                array (
                    'en' => 'Movie-Production',
                    'es' => 'Producción de Películas',
                    'slugEn' => 'movie-production',
                    'slugEs' => 'produccion-de-peliculas',
                ),
            1496 =>
                array (
                    'en' => 'Movies',
                    'es' => 'Cine',
                    'slugEn' => 'movies',
                    'slugEs' => 'cine',
                ),
            1497 =>
                array (
                    'en' => 'Movies - Distributors',
                    'es' => 'Películas-Distribuidores',
                    'slugEn' => 'movies-distributors',
                    'slugEs' => 'peliculas-distribuidores',
                ),
            1498 =>
                array (
                    'en' => 'Movies - Services',
                    'es' => 'Películas-Alquiler Equipo',
                    'slugEn' => 'movies-services',
                    'slugEs' => 'peliculas-alquiler-equipo',
                ),
            1499 =>
                array (
                    'en' => 'Movies-Producers',
                    'es' => 'Peliculas-Productores',
                    'slugEn' => 'movies-producers',
                    'slugEs' => 'peliculas-productores',
                ),
            1500 =>
                array (
                    'en' => 'Moving - Industrial',
                    'es' => 'Moving - Industrial',
                    'slugEn' => 'moving-industrial',
                    'slugEs' => 'moving-industrial',
                ),
            1501 =>
                array (
                    'en' => 'Moving & Storage',
                    'es' => 'Mudanzas',
                    'slugEn' => 'moving-storage',
                    'slugEs' => 'mudanzas',
                ),
            1502 =>
                array (
                    'en' => 'Moving Equipment & Supplies',
                    'es' => 'Mudanzas Efectos y Equipo',
                    'slugEn' => 'moving-equipment-supplies',
                    'slugEs' => 'mudanzas-efectos-y-equipo',
                ),
            1503 =>
                array (
                    'en' => 'Moving-Relocation of Vehicles',
                    'es' => 'Mudanzas-Relocalización de Vehículos',
                    'slugEn' => 'moving-relocation-of-vehicles',
                    'slugEs' => 'mudanzas-relocalizacion-de-vehiculos',
                ),
            1504 =>
                array (
                    'en' => 'MRI',
                    'es' => 'MRI',
                    'slugEn' => 'mri',
                    'slugEs' => 'mri',
                ),
            1505 =>
                array (
                    'en' => 'MRI - Equipment Sales & Service',
                    'es' => 'MRI - Equipo Venta y Servicio',
                    'slugEn' => 'mri-equipment-sales-service',
                    'slugEs' => 'mri-equipo-venta-y-servicio',
                ),
            1506 =>
                array (
                    'en' => 'Municipality',
                    'es' => 'Municipio',
                    'slugEn' => 'municipality',
                    'slugEs' => 'municipio',
                ),
            1507 =>
                array (
                    'en' => 'Museums',
                    'es' => 'Museos',
                    'slugEn' => 'museums',
                    'slugEs' => 'museos',
                ),
            1508 =>
                array (
                    'en' => 'Music',
                    'es' => 'Música',
                    'slugEn' => 'music',
                    'slugEs' => 'musica',
                ),
            1509 =>
                array (
                    'en' => 'Music - Distributors',
                    'es' => 'Música-Distribuidores',
                    'slugEn' => 'music-distributors',
                    'slugEs' => 'musica-distribuidores',
                ),
            1510 =>
                array (
                    'en' => 'Music - Instruments',
                    'es' => 'Música-Instrumentos',
                    'slugEn' => 'music-instruments',
                    'slugEs' => 'musica-instrumentos',
                ),
            1511 =>
                array (
                    'en' => 'Music - Music Bands',
                    'es' => 'Música - Grupos Musicales',
                    'slugEn' => 'music-music-bands',
                    'slugEs' => 'musica-grupos-musicales',
                ),
            1512 =>
                array (
                    'en' => 'Music - Religious',
                    'es' => 'Música Religiosa',
                    'slugEn' => 'music-religious',
                    'slugEs' => 'musica-religiosa',
                ),
            1513 =>
                array (
                    'en' => 'Music -Parties',
                    'es' => 'Música - Fiestas',
                    'slugEn' => 'music-parties',
                    'slugEs' => 'musica-fiestas',
                ),
            1514 =>
                array (
                    'en' => 'Music Shop',
                    'es' => 'Tienda de Discos',
                    'slugEn' => 'music-shop',
                    'slugEs' => 'tienda-de-discos',
                ),
            1515 =>
                array (
                    'en' => 'Music Store',
                    'es' => 'Tienda de Música',
                    'slugEn' => 'music-store',
                    'slugEs' => 'tienda-de-musica',
                ),
            1516 =>
                array (
                    'en' => 'Música - Bookstores',
                    'es' => 'Música-Librerías',
                    'slugEn' => 'musica-bookstores',
                    'slugEs' => 'musica-librerias',
                ),
            1517 =>
                array (
                    'en' => 'Música - Systems',
                    'es' => 'Música-Sistemas',
                    'slugEn' => 'musica-systems',
                    'slugEs' => 'musica-sistemas',
                ),
            1518 =>
                array (
                    'en' => 'Musical Education Services',
                    'es' => 'Servicios Educativos Musicales',
                    'slugEn' => 'musical-education-services',
                    'slugEs' => 'servicios-educativos-musicales',
                ),
            1519 =>
                array (
                    'en' => 'Musical Instrument Repair',
                    'es' => 'Reparación Instrumentos Musicales',
                    'slugEn' => 'musical-instrument-repair',
                    'slugEs' => 'reparacion-instrumentos-musicales',
                ),
            1520 =>
                array (
                    'en' => 'Music-Class',
                    'es' => 'Música-Clases',
                    'slugEn' => 'music-class',
                    'slugEs' => 'musica-clases',
                ),
            1521 =>
                array (
                    'en' => 'Nails - Salons',
                    'es' => 'Uñas-Estética',
                    'slugEn' => 'nails-salons',
                    'slugEs' => 'unas-estetica',
                ),
            1522 =>
                array (
                    'en' => 'Name Plates',
                    'es' => 'Plásticos-Laminados',
                    'slugEn' => 'name-plates',
                    'slugEs' => 'plasticos-laminados',
                ),
            1523 =>
                array (
                    'en' => 'Natural Gas',
                    'es' => 'Gas Natural',
                    'slugEn' => 'natural-gas',
                    'slugEs' => 'gas-natural',
                ),
            1524 =>
                array (
                    'en' => 'Natural Juice',
                    'es' => 'Jugos Naturales',
                    'slugEn' => 'natural-juice',
                    'slugEs' => 'jugos-naturales',
                ),
            1525 =>
                array (
                    'en' => 'Natural Products',
                    'es' => 'Productos Naturales',
                    'slugEn' => 'natural-products',
                    'slugEs' => 'productos-naturales',
                ),
            1526 =>
                array (
                    'en' => 'Natural Products - Supermarkets',
                    'es' => 'Productos Naturales - Supermercados',
                    'slugEn' => 'natural-products-supermarkets',
                    'slugEs' => 'productos-naturales-supermercados',
                ),
            1527 =>
                array (
                    'en' => 'Natural Soaps',
                    'es' => 'Jabones Naturales',
                    'slugEn' => 'natural-soaps',
                    'slugEs' => 'jabones-naturales',
                ),
            1528 =>
                array (
                    'en' => 'Natural Stone- Products and Services',
                    'es' => 'Piedra Natural - Productos y Servicios',
                    'slugEn' => 'natural-stone-products-and-services',
                    'slugEs' => 'piedra-natural-productos-y-servicios',
                ),
            1529 =>
                array (
                    'en' => 'Naturopathic Doctors',
                    'es' => 'Naturopatía-Doctores',
                    'slugEn' => 'naturopathic-doctors',
                    'slugEs' => 'naturopatia-doctores',
                ),
            1530 =>
                array (
                    'en' => 'Naturopathy',
                    'es' => 'Naturopatía',
                    'slugEn' => 'naturopathy',
                    'slugEs' => 'naturopatia',
                ),
            1531 =>
                array (
                    'en' => 'Nephrology',
                    'es' => 'Nefrología',
                    'slugEn' => 'nephrology',
                    'slugEs' => 'nefrologia',
                ),
            1532 =>
                array (
                    'en' => 'Neuropsychology',
                    'es' => 'Médicos Especialistas - Neuropsicología',
                    'slugEn' => 'neuropsychology',
                    'slugEs' => 'medicos-especialistas-neuropsicologia',
                ),
            1533 =>
                array (
                    'en' => 'News-Agency',
                    'es' => 'Noticias-Agencias',
                    'slugEn' => 'news-agency',
                    'slugEs' => 'noticias-agencias',
                ),
            1534 =>
                array (
                    'en' => 'Newspapers',
                    'es' => 'Periódicos',
                    'slugEn' => 'newspapers',
                    'slugEs' => 'periodicos',
                ),
            1535 =>
                array (
                    'en' => 'Night Clubs',
                    'es' => 'Clubes-Nocturnos',
                    'slugEn' => 'night-clubs',
                    'slugEs' => 'clubes-nocturnos',
                ),
            1536 =>
                array (
                    'en' => 'Nissan',
                    'es' => 'Nissan',
                    'slugEn' => 'nissan',
                    'slugEs' => 'nissan',
                ),
            1537 =>
                array (
                    'en' => 'Nissan Auto Parts',
                    'es' => 'Piezas Nissan',
                    'slugEn' => 'nissan-auto-parts',
                    'slugEs' => 'piezas-nissan',
                ),
            1538 =>
                array (
                    'en' => 'Nonprofit Organization',
                    'es' => 'Organización Sin Fines de Lucro',
                    'slugEn' => 'nonprofit-organization',
                    'slugEs' => 'organizacion-sin-fines-de-lucro',
                ),
            1539 =>
                array (
                    'en' => 'Novelties',
                    'es' => 'Novedades',
                    'slugEn' => 'novelties',
                    'slugEs' => 'novedades',
                ),
            1540 =>
                array (
                    'en' => 'Nurseries & Kindergartens',
                    'es' => 'Nurseries (Guarderías)',
                    'slugEn' => 'nurseries-kindergartens',
                    'slugEs' => 'nurseries-guarderias',
                ),
            1541 =>
                array (
                    'en' => 'Nurses Uniforms',
                    'es' => 'Uniformes de Enfermera',
                    'slugEn' => 'nurses-uniforms',
                    'slugEs' => 'uniformes-de-enfermera',
                ),
            1542 =>
                array (
                    'en' => 'Nursing - Services',
                    'es' => 'Enfermería-Servicios',
                    'slugEn' => 'nursing-services',
                    'slugEs' => 'enfermeria-servicios',
                ),
            1543 =>
                array (
                    'en' => 'Nutritionist',
                    'es' => 'Nutricionista',
                    'slugEn' => 'nutritionist',
                    'slugEs' => 'nutricionista',
                ),
            1544 =>
                array (
                    'en' => 'Nutritionist - Clinical',
                    'es' => 'Nutricionistas-Clínicos',
                    'slugEn' => 'nutritionist-clinical',
                    'slugEs' => 'nutricionistas-clinicos',
                ),
            1545 =>
                array (
                    'en' => 'Nuts, Bolts & Screws',
                    'es' => 'Tornillos Y Tuercas',
                    'slugEn' => 'nuts-bolts-screws',
                    'slugEs' => 'tornillos-y-tuercas',
                ),
            1546 =>
                array (
                    'en' => 'Nuts, Bolts & Screws Manufacture',
                    'es' => 'Tornillos Y Tuercas-Manufactura',
                    'slugEn' => 'nuts-bolts-screws-manufacture',
                    'slugEs' => 'tornillos-y-tuercas-manufactura',
                ),
            1547 =>
                array (
                    'en' => 'Obesity - Treatments',
                    'es' => 'Obesidad-Tratamiento',
                    'slugEn' => 'obesity-treatments',
                    'slugEs' => 'obesidad-tratamiento',
                ),
            1548 =>
                array (
                    'en' => 'Occupational Medicine',
                    'es' => 'Medicina Ocupacional',
                    'slugEn' => 'occupational-medicine',
                    'slugEs' => 'medicina-ocupacional',
                ),
            1549 =>
                array (
                    'en' => 'Off Track Betting - Agencies',
                    'es' => 'Hípicas-Agencias',
                    'slugEn' => 'off-track-betting-agencies',
                    'slugEs' => 'hipicas-agencias',
                ),
            1550 =>
                array (
                    'en' => 'Office - Supplies - Manufacture',
                    'es' => 'Oficinas-Efectos-Manufactura',
                    'slugEn' => 'office-supplies-manufacture',
                    'slugEs' => 'oficinas-efectos-manufactura',
                ),
            1551 =>
                array (
                    'en' => 'Office Chair',
                    'es' => 'Sillas de Oficina',
                    'slugEn' => 'office-chair',
                    'slugEs' => 'sillas-de-oficina',
                ),
            1552 =>
                array (
                    'en' => 'Office Furniture',
                    'es' => 'Muebles de Oficina',
                    'slugEn' => 'office-furniture',
                    'slugEs' => 'muebles-de-oficina',
                ),
            1553 =>
                array (
                    'en' => 'Office Furniture Rental',
                    'es' => 'Alquiler de Muebles de Oficina',
                    'slugEn' => 'office-furniture-rental',
                    'slugEs' => 'alquiler-de-muebles-de-oficina',
                ),
            1554 =>
                array (
                    'en' => 'Office Machines - Repair',
                    'es' => 'Máquinas De Oficina-Reparación',
                    'slugEn' => 'office-machines-repair',
                    'slugEs' => 'maquinas-de-oficina-reparacion',
                ),
            1555 =>
                array (
                    'en' => 'Office Modules',
                    'es' => 'Oficinas Módulos',
                    'slugEn' => 'office-modules',
                    'slugEs' => 'oficinas-modulos',
                ),
            1556 =>
                array (
                    'en' => 'Office-Effects And Equipment',
                    'es' => 'Oficinas-Efectos Y Equipo',
                    'slugEn' => 'office-effects-and-equipment',
                    'slugEs' => 'oficinas-efectos-y-equipo',
                ),
            1557 =>
                array (
                    'en' => 'Offices - Rental',
                    'es' => 'Oficinas-Alquiler',
                    'slugEn' => 'offices-rental',
                    'slugEs' => 'oficinas-alquiler',
                ),
            1558 =>
                array (
                    'en' => 'Oil - Refiners',
                    'es' => 'Petróleo-Refinerías',
                    'slugEn' => 'oil-refiners',
                    'slugEs' => 'petroleo-refinerias',
                ),
            1559 =>
                array (
                    'en' => 'Ophthalmologists',
                    'es' => 'Oftalmólogos',
                    'slugEn' => 'ophthalmologists',
                    'slugEs' => 'oftalmologos',
                ),
            1560 =>
                array (
                    'en' => 'Ophthalmology',
                    'es' => 'Oftalmología',
                    'slugEn' => 'ophthalmology',
                    'slugEs' => 'oftalmologia',
                ),
            1561 =>
                array (
                    'en' => 'Ophthalmology Cornea',
                    'es' => 'Oftalmología Córnea',
                    'slugEn' => 'ophthalmology-cornea',
                    'slugEs' => 'oftalmologia-cornea',
                ),
            1562 =>
                array (
                    'en' => 'Ophthalmology Glaucoma',
                    'es' => 'Oftalmologia Glaucoma',
                    'slugEn' => 'ophthalmology-glaucoma',
                    'slugEs' => 'oftalmologia-glaucoma',
                ),
            1563 =>
                array (
                    'en' => 'Opticians',
                    'es' => 'Opticas',
                    'slugEn' => 'opticians',
                    'slugEs' => 'opticas',
                ),
            1564 =>
                array (
                    'en' => 'Opticians - Equipment & Supplies',
                    'es' => 'Opticas-Efectos Y Equipo',
                    'slugEn' => 'opticians-equipment-supplies',
                    'slugEs' => 'opticas-efectos-y-equipo',
                ),
            1565 =>
                array (
                    'en' => 'Opticians - Visual Correction',
                    'es' => 'Ópticas - Corrección Visual',
                    'slugEn' => 'opticians-visual-correction',
                    'slugEs' => 'opticas-correccion-visual',
                ),
            1566 =>
                array (
                    'en' => 'Opticians-Glasses',
                    'es' => 'Opticas-Espejuelos',
                    'slugEn' => 'opticians-glasses',
                    'slugEs' => 'opticas-espejuelos',
                ),
            1567 =>
                array (
                    'en' => 'Optometrists',
                    'es' => 'Optómetras',
                    'slugEn' => 'optometrists',
                    'slugEs' => 'optometras',
                ),
            1568 =>
                array (
                    'en' => 'Oral and Maxillofacial Surgery',
                    'es' => 'Cirugía Oral Y Maxilofacial',
                    'slugEn' => 'oral-and-maxillofacial-surgery',
                    'slugEs' => 'cirugia-oral-y-maxilofacial',
                ),
            1569 =>
                array (
                    'en' => 'Orator',
                    'es' => 'Orador',
                    'slugEn' => 'orator',
                    'slugEs' => 'orador',
                ),
            1570 =>
                array (
                    'en' => 'Orchestras & Bands',
                    'es' => 'Orquestas',
                    'slugEn' => 'orchestras-bands',
                    'slugEs' => 'orquestas',
                ),
            1571 =>
                array (
                    'en' => 'Organic Products',
                    'es' => 'Productos Orgánicos',
                    'slugEn' => 'organic-products',
                    'slugEs' => 'productos-organicos',
                ),
            1572 =>
                array (
                    'en' => 'Oriental Medicine',
                    'es' => 'Medicina Oriental',
                    'slugEn' => 'oriental-medicine',
                    'slugEs' => 'medicina-oriental',
                ),
            1573 =>
                array (
                    'en' => 'Original Auto Parts',
                    'es' => 'Automóviles - Piezas Originales',
                    'slugEn' => 'original-auto-parts',
                    'slugEs' => 'automoviles-piezas-originales',
                ),
            1574 =>
                array (
                    'en' => 'Ornamentation',
                    'es' => 'Ornamentación',
                    'slugEn' => 'ornamentation',
                    'slugEs' => 'ornamentacion',
                ),
            1575 =>
                array (
                    'en' => 'Orthodontic Braces',
                    'es' => 'Ortodoncia Braces',
                    'slugEn' => 'orthodontic-braces',
                    'slugEs' => 'ortodoncia-braces',
                ),
            1576 =>
                array (
                    'en' => 'Orthodontist Invisalign',
                    'es' => 'Ortodoncia Invisalign',
                    'slugEn' => 'orthodontist-invisalign',
                    'slugEs' => 'ortodoncia-invisalign',
                ),
            1577 =>
                array (
                    'en' => 'Orthodontists',
                    'es' => 'Ortodoncistas',
                    'slugEn' => 'orthodontists',
                    'slugEs' => 'ortodoncistas',
                ),
            1578 =>
                array (
                    'en' => 'Orthopedic - Appliances',
                    'es' => 'Ortopédicos-Efectos',
                    'slugEn' => 'orthopedic-appliances',
                    'slugEs' => 'ortopedicos-efectos',
                ),
            1579 =>
                array (
                    'en' => 'Orthopedic - Prosthetics - Devices',
                    'es' => 'Ortopédicos-Prótesis-Efectos',
                    'slugEn' => 'orthopedic-prosthetics-devices',
                    'slugEs' => 'ortopedicos-protesis-efectos',
                ),
            1580 =>
                array (
                    'en' => 'Osmosis- Water',
                    'es' => 'Agua - Osmosis',
                    'slugEn' => 'osmosis-water',
                    'slugEs' => 'agua-osmosis',
                ),
            1581 =>
                array (
                    'en' => 'Otolaryngology Head and Neck Surgery',
                    'es' => 'Otorrinolaringología Cirugía de Cabeza y Cuello',
                    'slugEn' => 'otolaryngology-head-and-neck-surgery',
                    'slugEs' => 'otorrinolaringologia-cirugia-de-cabeza-y-cuello',
                ),
            1582 =>
                array (
                    'en' => 'Outdoor Shop',
                    'es' => 'Tienda al Aire Libre',
                    'slugEn' => 'outdoor-shop',
                    'slugEs' => 'tienda-al-aire-libre',
                ),
            1583 =>
                array (
                    'en' => 'Outsourcing Services',
                    'es' => 'Outsourcing Services',
                    'slugEn' => 'outsourcing-services',
                    'slugEs' => 'outsourcing-services',
                ),
            1584 =>
                array (
                    'en' => 'Oxygen',
                    'es' => 'Oxígeno',
                    'slugEn' => 'oxygen',
                    'slugEs' => 'oxigeno',
                ),
            1585 =>
                array (
                    'en' => 'Packaging- Wrapping',
                    'es' => 'Flejes-Embalar',
                    'slugEn' => 'packaging-wrapping',
                    'slugEs' => 'flejes-embalar',
                ),
            1586 =>
                array (
                    'en' => 'Packing',
                    'es' => 'Empaque-Efectos Y Equipos',
                    'slugEn' => 'packing',
                    'slugEs' => 'empaque-efectos-y-equipos',
                ),
            1587 =>
                array (
                    'en' => 'Paellas-Catering',
                    'es' => 'Paellas-Catering',
                    'slugEn' => 'paellas-catering',
                    'slugEs' => 'paellas-catering',
                ),
            1588 =>
                array (
                    'en' => 'Pain Management',
                    'es' => 'Manejo de Dolor',
                    'slugEn' => 'pain-management',
                    'slugEs' => 'manejo-de-dolor',
                ),
            1589 =>
                array (
                    'en' => 'Paint',
                    'es' => 'Pintura',
                    'slugEn' => 'paint',
                    'slugEs' => 'pintura',
                ),
            1590 =>
                array (
                    'en' => 'Paint - Equipment & Supplies',
                    'es' => 'Pintura-Efectos Y Equipo',
                    'slugEn' => 'paint-equipment-supplies',
                    'slugEs' => 'pintura-efectos-y-equipo',
                ),
            1591 =>
                array (
                    'en' => 'Paint - Industrial',
                    'es' => 'Pintura-Industrial',
                    'slugEn' => 'paint-industrial',
                    'slugEs' => 'pintura-industrial',
                ),
            1592 =>
                array (
                    'en' => 'Paint - Manufactures',
                    'es' => 'Pintura-Fábricas',
                    'slugEn' => 'paint-manufactures',
                    'slugEs' => 'pintura-fabricas',
                ),
            1593 =>
                array (
                    'en' => 'Painting - Contractors - Houses',
                    'es' => 'Pintura-Casas',
                    'slugEn' => 'painting-contractors-houses',
                    'slugEs' => 'pintura-casas',
                ),
            1594 =>
                array (
                    'en' => 'Painting Contractors',
                    'es' => 'Pintura-Contratistas',
                    'slugEn' => 'painting-contractors',
                    'slugEs' => 'pintura-contratistas',
                ),
            1595 =>
                array (
                    'en' => 'Painting- Contractors - Boats',
                    'es' => 'Pintura-Botes',
                    'slugEn' => 'painting-contractors-boats',
                    'slugEs' => 'pintura-botes',
                ),
            1596 =>
                array (
                    'en' => 'Pallet - Racks',
                    'es' => 'Paletas-Anaqueles',
                    'slugEn' => 'pallet-racks',
                    'slugEs' => 'paletas-anaqueles',
                ),
            1597 =>
                array (
                    'en' => 'Pallets',
                    'es' => 'Paletas',
                    'slugEn' => 'pallets',
                    'slugEs' => 'paletas',
                ),
            1598 =>
                array (
                    'en' => 'Panels-Decorative',
                    'es' => 'Paneles-Decorativos',
                    'slugEn' => 'panels-decorative',
                    'slugEs' => 'paneles-decorativos',
                ),
            1599 =>
                array (
                    'en' => 'Paper - Distributors',
                    'es' => 'Papel-Distribuidores',
                    'slugEn' => 'paper-distributors',
                    'slugEs' => 'papel-distribuidores',
                ),
            1600 =>
                array (
                    'en' => 'Paper - Manufacture',
                    'es' => 'Papel-Fábricas',
                    'slugEn' => 'paper-manufacture',
                    'slugEs' => 'papel-fabricas',
                ),
            1601 =>
                array (
                    'en' => 'Paper - Products',
                    'es' => 'Papel-Productos',
                    'slugEn' => 'paper-products',
                    'slugEs' => 'papel-productos',
                ),
            1602 =>
                array (
                    'en' => 'Paper - Shredding - Machines',
                    'es' => 'Trituradoras-Papel',
                    'slugEn' => 'paper-shredding-machines',
                    'slugEs' => 'trituradoras-papel',
                ),
            1603 =>
                array (
                    'en' => 'Paradores (Country Inns)',
                    'es' => 'Paradores',
                    'slugEn' => 'paradores-country-inns',
                    'slugEs' => 'paradores',
                ),
            1604 =>
                array (
                    'en' => 'Paraffin Manicure',
                    'es' => 'Manicure Parafina',
                    'slugEn' => 'paraffin-manicure',
                    'slugEs' => 'manicure-parafina',
                ),
            1605 =>
                array (
                    'en' => 'Parking',
                    'es' => 'Estacionamientos',
                    'slugEn' => 'parking',
                    'slugEs' => 'estacionamientos',
                ),
            1606 =>
                array (
                    'en' => 'Parking - Equipment & Supplies',
                    'es' => 'Estacionamientos-Efectos Y Equipo',
                    'slugEn' => 'parking-equipment-supplies',
                    'slugEs' => 'estacionamientos-efectos-y-equipo',
                ),
            1607 =>
                array (
                    'en' => 'Partitions - Office',
                    'es' => 'Divisiones - Oficinas',
                    'slugEn' => 'partitions-office',
                    'slugEs' => 'divisiones-oficinas',
                ),
            1608 =>
                array (
                    'en' => 'Party',
                    'es' => 'Fiestas',
                    'slugEn' => 'party',
                    'slugEs' => 'fiestas',
                ),
            1609 =>
                array (
                    'en' => 'Party - Disc Jockey',
                    'es' => 'Fiestas - Disc Jockey',
                    'slugEn' => 'party-disc-jockey',
                    'slugEs' => 'fiestas-disc-jockey',
                ),
            1610 =>
                array (
                    'en' => 'Party - Supplies',
                    'es' => 'Fiestas-Efectos Y Equipo',
                    'slugEn' => 'party-supplies',
                    'slugEs' => 'fiestas-efectos-y-equipo',
                ),
            1611 =>
                array (
                    'en' => 'Party Bus',
                    'es' => 'Party Bus',
                    'slugEn' => 'party-bus',
                    'slugEs' => 'party-bus',
                ),
            1612 =>
                array (
                    'en' => 'Party Dresses',
                    'es' => 'Trajes de Quinceañera',
                    'slugEn' => 'party-dresses',
                    'slugEs' => 'trajes-de-quinceanera',
                ),
            1613 =>
                array (
                    'en' => 'Party Services',
                    'es' => 'Fiestas-Servicios',
                    'slugEn' => 'party-services',
                    'slugEs' => 'fiestas-servicios',
                ),
            1614 =>
                array (
                    'en' => 'Passport & Visa - Services',
                    'es' => 'Pasaporte Y Visa-Servicios',
                    'slugEn' => 'passport-visa-services',
                    'slugEs' => 'pasaporte-y-visa-servicios',
                ),
            1615 =>
                array (
                    'en' => 'Pastelillos',
                    'es' => 'Pastelillos',
                    'slugEn' => 'pastelillos',
                    'slugEs' => 'pastelillos',
                ),
            1616 =>
                array (
                    'en' => 'Pastor',
                    'es' => 'Pastor',
                    'slugEn' => 'pastor',
                    'slugEs' => 'pastor',
                ),
            1617 =>
                array (
                    'en' => 'Pastry - Suppliers',
                    'es' => 'Reposterías-Suplidores',
                    'slugEn' => 'pastry-suppliers',
                    'slugEs' => 'reposterias-suplidores',
                ),
            1618 =>
                array (
                    'en' => 'Pastry Classes',
                    'es' => 'Clases de Repostería',
                    'slugEn' => 'pastry-classes',
                    'slugEs' => 'clases-de-reposteria',
                ),
            1619 =>
                array (
                    'en' => 'Pastry Craft',
                    'es' => 'Repostería Artesanal',
                    'slugEn' => 'pastry-craft',
                    'slugEs' => 'reposteria-artesanal',
                ),
            1620 =>
                array (
                    'en' => 'Pavement-Marking Paint',
                    'es' => 'Pavimento-Pintura De Marcado',
                    'slugEn' => 'pavement-marking-paint',
                    'slugEs' => 'pavimento-pintura-de-marcado',
                ),
            1621 =>
                array (
                    'en' => 'Pawnbrokers',
                    'es' => 'Casas De Empeño',
                    'slugEn' => 'pawnbrokers',
                    'slugEs' => 'casas-de-empeno',
                ),
            1622 =>
                array (
                    'en' => 'Pawnbrokers - Gold Dealers',
                    'es' => 'Casas de Empeño - Compra de Oro',
                    'slugEn' => 'pawnbrokers-gold-dealers',
                    'slugEs' => 'casas-de-empeno-compra-de-oro',
                ),
            1623 =>
                array (
                    'en' => 'Payroll Timeclocks',
                    'es' => 'Ponchadores (Control De Asistencia)',
                    'slugEn' => 'payroll-timeclocks',
                    'slugEs' => 'ponchadores-control-de-asistencia',
                ),
            1624 =>
                array (
                    'en' => 'Payroll-Service',
                    'es' => 'Nóminas-Servicio',
                    'slugEn' => 'payroll-service',
                    'slugEs' => 'nominas-servicio',
                ),
            1625 =>
                array (
                    'en' => 'Pediatric Audiology',
                    'es' => 'Audiología Pediátrica',
                    'slugEn' => 'pediatric-audiology',
                    'slugEs' => 'audiologia-pediatrica',
                ),
            1626 =>
                array (
                    'en' => 'Pediatric Dentist',
                    'es' => 'Dentista Pediatrico',
                    'slugEn' => 'pediatric-dentist',
                    'slugEs' => 'dentista-pediatrico',
                ),
            1627 =>
                array (
                    'en' => 'Pediatric Optometry',
                    'es' => 'Optometria Pediátrica',
                    'slugEn' => 'pediatric-optometry',
                    'slugEs' => 'optometria-pediatrica',
                ),
            1628 =>
                array (
                    'en' => 'Pediatric Otolaryngology',
                    'es' => 'Otorrinolaringología Pediátrica',
                    'slugEn' => 'pediatric-otolaryngology',
                    'slugEs' => 'otorrinolaringologia-pediatrica',
                ),
            1629 =>
                array (
                    'en' => 'Pedicure',
                    'es' => 'Pedicure',
                    'slugEn' => 'pedicure',
                    'slugEs' => 'pedicure',
                ),
            1630 =>
                array (
                    'en' => 'Pension Plans - Companies',
                    'es' => 'Pensiones Planes-Compañías',
                    'slugEn' => 'pension-plans-companies',
                    'slugEs' => 'pensiones-planes-companias',
                ),
            1631 =>
                array (
                    'en' => 'Perfumes',
                    'es' => 'Perfumes',
                    'slugEn' => 'perfumes',
                    'slugEs' => 'perfumes',
                ),
            1632 =>
                array (
                    'en' => 'Perfumes - Wholesale',
                    'es' => 'Perfumes - Al Por Mayor',
                    'slugEn' => 'perfumes-wholesale',
                    'slugEs' => 'perfumes-al-por-mayor',
                ),
            1633 =>
                array (
                    'en' => 'Perfumes and Fragances',
                    'es' => 'Perfumes y Fragancias',
                    'slugEn' => 'perfumes-and-fragances',
                    'slugEs' => 'perfumes-y-fragancias',
                ),
            1634 =>
                array (
                    'en' => 'Perfumes and Makeup',
                    'es' => 'Perfumes y Cosméticos',
                    'slugEn' => 'perfumes-and-makeup',
                    'slugEs' => 'perfumes-y-cosmeticos',
                ),
            1635 =>
                array (
                    'en' => 'Perfumes and Purses',
                    'es' => 'Perfumes y Carteras',
                    'slugEn' => 'perfumes-and-purses',
                    'slugEs' => 'perfumes-y-carteras',
                ),
            1636 =>
                array (
                    'en' => 'Permanent Makeup',
                    'es' => 'Maquillaje Permanente',
                    'slugEn' => 'permanent-makeup',
                    'slugEs' => 'maquillaje-permanente',
                ),
            1637 =>
                array (
                    'en' => 'Permission Services',
                    'es' => 'Permisos-Gestoría',
                    'slugEn' => 'permission-services',
                    'slugEs' => 'permisos-gestoria',
                ),
            1638 =>
                array (
                    'en' => 'Personal Trainer',
                    'es' => 'Entrenador Personal',
                    'slugEn' => 'personal-trainer',
                    'slugEs' => 'entrenador-personal',
                ),
            1639 =>
                array (
                    'en' => 'Personal-Agencies',
                    'es' => 'Personales-Agencias',
                    'slugEn' => 'personal-agencies',
                    'slugEs' => 'personales-agencias',
                ),
            1640 =>
                array (
                    'en' => 'Peruvian Food Restaurant',
                    'es' => 'Restaurante Comida Peruana',
                    'slugEn' => 'peruvian-food-restaurant',
                    'slugEs' => 'restaurante-comida-peruana',
                ),
            1641 =>
                array (
                    'en' => 'Peruvian Food Restaurants',
                    'es' => 'Restaurantes Comida Peruana',
                    'slugEn' => 'peruvian-food-restaurants',
                    'slugEs' => 'restaurantes-comida-peruana',
                ),
            1642 =>
                array (
                    'en' => 'Pest Control',
                    'es' => 'Control de Plagas',
                    'slugEn' => 'pest-control',
                    'slugEs' => 'control-de-plagas',
                ),
            1643 =>
                array (
                    'en' => 'Pet Cremation Services',
                    'es' => 'Cremación de Mascotas',
                    'slugEn' => 'pet-cremation-services',
                    'slugEs' => 'cremacion-de-mascotas',
                ),
            1644 =>
                array (
                    'en' => 'Petroleum - Products And Services',
                    'es' => 'Petróleo-Productos',
                    'slugEn' => 'petroleum-products-and-services',
                    'slugEs' => 'petroleo-productos',
                ),
            1645 =>
                array (
                    'en' => 'Petroleum - Spills - Services',
                    'es' => 'Petróleo-Recogido-Derrames',
                    'slugEn' => 'petroleum-spills-services',
                    'slugEs' => 'petroleo-recogido-derrames',
                ),
            1646 =>
                array (
                    'en' => 'Petroleum - Wells - Machinery',
                    'es' => 'Petróleo-Maquinaria Pozos',
                    'slugEn' => 'petroleum-wells-machinery',
                    'slugEs' => 'petroleo-maquinaria-pozos',
                ),
            1647 =>
                array (
                    'en' => 'Pharmaceutical',
                    'es' => 'Farmaceutica',
                    'slugEn' => 'pharmaceutical',
                    'slugEs' => 'farmaceutica',
                ),
            1648 =>
                array (
                    'en' => 'Pharmaceutical - Products - Processing Equipment',
                    'es' => 'Farmacéuticos-Productos-Equipo Procesador',
                    'slugEn' => 'pharmaceutical-products-processing-equipment',
                    'slugEs' => 'farmaceuticos-productos-equipo-procesador',
                ),
            1649 =>
                array (
                    'en' => 'Pharmacies',
                    'es' => 'Farmacias',
                    'slugEn' => 'pharmacies',
                    'slugEs' => 'farmacias',
                ),
            1650 =>
                array (
                    'en' => 'Pharmacies-Product Distributors',
                    'es' => 'Farmacias-Distribuidores Productos',
                    'slugEn' => 'pharmacies-product-distributors',
                    'slugEs' => 'farmacias-distribuidores-productos',
                ),
            1651 =>
                array (
                    'en' => 'Pharmacies-Products Manufacturing',
                    'es' => 'Farmacias-Manufactura Productos',
                    'slugEn' => 'pharmacies-products-manufacturing',
                    'slugEs' => 'farmacias-manufactura-productos',
                ),
            1652 =>
                array (
                    'en' => 'Pharmacies-Suppliers',
                    'es' => 'Farmacias-Suplidores',
                    'slugEn' => 'pharmacies-suppliers',
                    'slugEs' => 'farmacias-suplidores',
                ),
            1653 =>
                array (
                    'en' => 'Philately (stamp collecting)',
                    'es' => 'Filatelia',
                    'slugEn' => 'philately-stamp-collecting',
                    'slugEs' => 'filatelia',
                ),
            1654 =>
                array (
                    'en' => 'Phone Books',
                    'es' => 'Guías-Telefónicas',
                    'slugEn' => 'phone-books',
                    'slugEs' => 'guias-telefonicas',
                ),
            1655 =>
                array (
                    'en' => 'Phones - Chargers',
                    'es' => 'Celulares - Cargadores',
                    'slugEn' => 'phones-chargers',
                    'slugEs' => 'celulares-cargadores',
                ),
            1656 =>
                array (
                    'en' => 'Phonograph Records',
                    'es' => 'Discos',
                    'slugEn' => 'phonograph-records',
                    'slugEs' => 'discos',
                ),
            1657 =>
                array (
                    'en' => 'Phonograph Records - Distributors',
                    'es' => 'Discos-Distribuidores',
                    'slugEn' => 'phonograph-records-distributors',
                    'slugEs' => 'discos-distribuidores',
                ),
            1658 =>
                array (
                    'en' => 'Phonograph Records - Manufacture',
                    'es' => 'Discos-Fábricas',
                    'slugEn' => 'phonograph-records-manufacture',
                    'slugEs' => 'discos-fabricas',
                ),
            1659 =>
                array (
                    'en' => 'Photo Revealed',
                    'es' => 'Foto Revelado',
                    'slugEn' => 'photo-revealed',
                    'slugEs' => 'foto-revelado',
                ),
            1660 =>
                array (
                    'en' => 'Photogrammetry',
                    'es' => 'Fotogrametria',
                    'slugEn' => 'photogrammetry',
                    'slugEs' => 'fotogrametria',
                ),
            1661 =>
                array (
                    'en' => 'Photographers',
                    'es' => 'Fotógrafos',
                    'slugEn' => 'photographers',
                    'slugEs' => 'fotografos',
                ),
            1662 =>
                array (
                    'en' => 'Photographers - Aerial',
                    'es' => 'Fotógrafos-Aéreos',
                    'slugEn' => 'photographers-aerial',
                    'slugEs' => 'fotografos-aereos',
                ),
            1663 =>
                array (
                    'en' => 'Photographers - Portrait',
                    'es' => 'Fotógrafos-Artísticos',
                    'slugEn' => 'photographers-portrait',
                    'slugEs' => 'fotografos-artisticos',
                ),
            1664 =>
                array (
                    'en' => 'Photographer-Video',
                    'es' => 'Fotógrafo-Video',
                    'slugEn' => 'photographer-video',
                    'slugEs' => 'fotografo-video',
                ),
            1665 =>
                array (
                    'en' => 'Photographic - Equipment & Supplies - Repair',
                    'es' => 'Fotografía-Efectos Y Equipo',
                    'slugEn' => 'photographic-equipment-supplies-repair',
                    'slugEs' => 'fotografia-efectos-y-equipo',
                ),
            1666 =>
                array (
                    'en' => 'Photographic - Laboratories',
                    'es' => 'Fotografía-Laboratorios',
                    'slugEn' => 'photographic-laboratories',
                    'slugEs' => 'fotografia-laboratorios',
                ),
            1667 =>
                array (
                    'en' => 'Physiatrist',
                    'es' => 'Fisiatra',
                    'slugEn' => 'physiatrist',
                    'slugEs' => 'fisiatra',
                ),
            1668 =>
                array (
                    'en' => 'Physiatry',
                    'es' => 'Fisiatría',
                    'slugEn' => 'physiatry',
                    'slugEs' => 'fisiatria',
                ),
            1669 =>
                array (
                    'en' => 'Physical Medicine And Rehabilitation',
                    'es' => 'Medicina Fisica Y Rehabilitacion',
                    'slugEn' => 'physical-medicine-and-rehabilitation',
                    'slugEs' => 'medicina-fisica-y-rehabilitacion',
                ),
            1670 =>
                array (
                    'en' => 'Physicians - ENT',
                    'es' => 'Medicos Especialistas - Otorrinolaringólogo',
                    'slugEn' => 'physicians-ent',
                    'slugEs' => 'medicos-especialistas-otorrinolaringologo',
                ),
            1671 =>
                array (
                    'en' => 'Physicians - Specialists - Clinical Cardiac Electrophysiology',
                    'es' => 'Médicos Especialistas - Electrofisiología Cardiaca Clínica',
                    'slugEn' => 'physicians-specialists-clinical-cardiac-electrophysiology',
                    'slugEs' => 'medicos-especialistas-electrofisiologia-cardiaca-clinica',
                ),
            1672 =>
                array (
                    'en' => 'Physicians - Specialists - Obstetrics and Gynecology',
                    'es' => 'Medicos Especialistas - Obstetricia Y Ginecologia',
                    'slugEn' => 'physicians-specialists-obstetrics-and-gynecology',
                    'slugEs' => 'medicos-especialistas-obstetricia-y-ginecologia',
                ),
            1673 =>
                array (
                    'en' => 'Physicians / Specialists - Hypertension',
                    'es' => 'Médicos Especialistas - Hipertensión',
                    'slugEn' => 'physicians-specialists-hypertension',
                    'slugEs' => 'medicos-especialistas-hipertension',
                ),
            1674 =>
                array (
                    'en' => 'Physicians and Pediatric Optometry',
                    'es' => 'Médicos y Optometría Pediátrica',
                    'slugEn' => 'physicians-and-pediatric-optometry',
                    'slugEs' => 'medicos-y-optometria-pediatrica',
                ),
            1675 =>
                array (
                    'en' => 'Physicians And Surgeons - Allergy',
                    'es' => 'Medicos Especialistas - Alergia',
                    'slugEn' => 'physicians-and-surgeons-allergy',
                    'slugEs' => 'medicos-especialistas-alergia',
                ),
            1676 =>
                array (
                    'en' => 'Physicians And Surgeons - Allergy - Pediatric',
                    'es' => 'Medicos Especialistas - Alergia - Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-allergy-pediatric',
                    'slugEs' => 'medicos-especialistas-alergia-pediatrica',
                ),
            1677 =>
                array (
                    'en' => 'Physicians And Surgeons - Alternative Medicine',
                    'es' => 'Médicos Especialistas - Medicina Alterna',
                    'slugEn' => 'physicians-and-surgeons-alternative-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-alterna',
                ),
            1678 =>
                array (
                    'en' => 'Physicians And Surgeons - Anesthesiology',
                    'es' => 'Medicos Especialistas - Anestesiologia',
                    'slugEn' => 'physicians-and-surgeons-anesthesiology',
                    'slugEs' => 'medicos-especialistas-anestesiologia',
                ),
            1679 =>
                array (
                    'en' => 'Physicians And Surgeons - Audiology - Pediatric',
                    'es' => 'Médicos Especialistas - Audiología Pediátrica',
                    'slugEn' => 'physicians-and-surgeons-audiology-pediatric',
                    'slugEs' => 'medicos-especialistas-audiologia-pediatrica',
                ),
            1680 =>
                array (
                    'en' => 'Physicians And Surgeons - Bariatric',
                    'es' => 'Medicos Especialistas - Bariatria',
                    'slugEn' => 'physicians-and-surgeons-bariatric',
                    'slugEs' => 'medicos-especialistas-bariatria',
                ),
            1681 =>
                array (
                    'en' => 'Physicians And Surgeons - Breast-Surgery',
                    'es' => 'Medicos Especialistas - Cirugia-Senos',
                    'slugEn' => 'physicians-and-surgeons-breast-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-senos',
                ),
            1682 =>
                array (
                    'en' => 'Physicians And Surgeons - Cancer (Oncology)',
                    'es' => 'Medicos Especialistas - Cancer (Oncologia)',
                    'slugEn' => 'physicians-and-surgeons-cancer-oncology',
                    'slugEs' => 'medicos-especialistas-cancer-oncologia',
                ),
            1683 =>
                array (
                    'en' => 'Physicians And Surgeons - Cardiology',
                    'es' => 'Medicos Especialistas - Cardiología',
                    'slugEn' => 'physicians-and-surgeons-cardiology',
                    'slugEs' => 'medicos-especialistas-cardiologia',
                ),
            1684 =>
                array (
                    'en' => 'Physicians And Surgeons - Cardiology-Intervencional',
                    'es' => 'Médicos Especialistas - Cardiología Intervencional',
                    'slugEn' => 'physicians-and-surgeons-cardiology-intervencional',
                    'slugEs' => 'medicos-especialistas-cardiologia-intervencional',
                ),
            1685 =>
                array (
                    'en' => 'Physicians And Surgeons - Cardiology-Pediatric',
                    'es' => 'Medicos Especialistas - Cardiología-Pediátrica',
                    'slugEn' => 'physicians-and-surgeons-cardiology-pediatric',
                    'slugEs' => 'medicos-especialistas-cardiologia-pediatrica',
                ),
            1686 =>
                array (
                    'en' => 'Physicians And Surgeons - Cardiovascular And Thoraxic Surgery',
                    'es' => 'Medicos Especialistas - Cirugia-Cardiovascular Y Toráxica',
                    'slugEn' => 'physicians-and-surgeons-cardiovascular-and-thoraxic-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-cardiovascular-y-toraxica',
                ),
            1687 =>
                array (
                    'en' => 'Physicians And Surgeons - Cutaneous Surgery',
                    'es' => 'Médicos Especialistas - Cirugia Cutanea',
                    'slugEn' => 'physicians-and-surgeons-cutaneous-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-cutanea',
                ),
            1688 =>
                array (
                    'en' => 'Physicians And Surgeons - Dermatology',
                    'es' => 'Medicos Especialistas - Dermatologia',
                    'slugEn' => 'physicians-and-surgeons-dermatology',
                    'slugEs' => 'medicos-especialistas-dermatologia',
                ),
            1689 =>
                array (
                    'en' => 'Physicians And Surgeons - Emergency Medicine',
                    'es' => 'Medicos Especialistas - Medicina De Emergencia',
                    'slugEn' => 'physicians-and-surgeons-emergency-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-de-emergencia',
                ),
            1690 =>
                array (
                    'en' => 'Physicians And Surgeons - Endocrinology And Diabetes',
                    'es' => 'Medicos Especialistas - Endocrinologia Y Diabetes',
                    'slugEn' => 'physicians-and-surgeons-endocrinology-and-diabetes',
                    'slugEs' => 'medicos-especialistas-endocrinologia-y-diabetes',
                ),
            1691 =>
                array (
                    'en' => 'Physicians and Surgeons - Epilepsy',
                    'es' => 'Médicos Especialistas - Epilepsia',
                    'slugEn' => 'physicians-and-surgeons-epilepsy',
                    'slugEs' => 'medicos-especialistas-epilepsia',
                ),
            1692 =>
                array (
                    'en' => 'Physicians And Surgeons - Esthetic And Reconstructive - Plastic Surgery',
                    'es' => 'Médicos Especialistas - Cirugia Plastica-Estetica Y Reconstructiva',
                    'slugEn' => 'physicians-and-surgeons-esthetic-and-reconstructive-plastic-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-plastica-estetica-y-reconstructiva',
                ),
            1693 =>
                array (
                    'en' => 'Physicians And Surgeons - General Medicine',
                    'es' => 'Médicos Especialistas - Medicina De Familia',
                    'slugEn' => 'physicians-and-surgeons-general-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-de-familia',
                ),
            1694 =>
                array (
                    'en' => 'Physicians And Surgeons - Geriatry',
                    'es' => 'Medicos Especialistas - Geriatria',
                    'slugEn' => 'physicians-and-surgeons-geriatry',
                    'slugEs' => 'medicos-especialistas-geriatria',
                ),
            1695 =>
                array (
                    'en' => 'Physicians And Surgeons - Hematology And Oncology',
                    'es' => 'Medicos Especialistas - Hematologia Y Oncologia',
                    'slugEn' => 'physicians-and-surgeons-hematology-and-oncology',
                    'slugEs' => 'medicos-especialistas-hematologia-y-oncologia',
                ),
            1696 =>
                array (
                    'en' => 'Physicians and Surgeons - Hepatology',
                    'es' => 'Médicos Especialistas - Hepatología',
                    'slugEn' => 'physicians-and-surgeons-hepatology',
                    'slugEs' => 'medicos-especialistas-hepatologia',
                ),
            1697 =>
                array (
                    'en' => 'Physicians And Surgeons - Immunology',
                    'es' => 'Médicos Especialistas - Inmunología',
                    'slugEn' => 'physicians-and-surgeons-immunology',
                    'slugEs' => 'medicos-especialistas-inmunologia',
                ),
            1698 =>
                array (
                    'en' => 'Physicians And Surgeons - Industrial Medicine',
                    'es' => 'Médicos Especialistas - Medicina Industrial',
                    'slugEn' => 'physicians-and-surgeons-industrial-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-industrial',
                ),
            1699 =>
                array (
                    'en' => 'Physicians And Surgeons - Infectology',
                    'es' => 'Medicos Especialistas - Infectologia',
                    'slugEn' => 'physicians-and-surgeons-infectology',
                    'slugEs' => 'medicos-especialistas-infectologia',
                ),
            1700 =>
                array (
                    'en' => 'Physicians And Surgeons - Infertility',
                    'es' => 'Medicos Especialistas - Infertilidad',
                    'slugEn' => 'physicians-and-surgeons-infertility',
                    'slugEs' => 'medicos-especialistas-infertilidad',
                ),
            1701 =>
                array (
                    'en' => 'Physicians And Surgeons - Integrated Medicine',
                    'es' => 'Medicos Especialistas - Medicina Integrada',
                    'slugEn' => 'physicians-and-surgeons-integrated-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-integrada',
                ),
            1702 =>
                array (
                    'en' => 'Physicians And Surgeons - Internal Medicine',
                    'es' => 'Medicos Especialistas - Medicina Interna',
                    'slugEn' => 'physicians-and-surgeons-internal-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-interna',
                ),
            1703 =>
                array (
                    'en' => 'Physicians And Surgeons - Invasive - Non Invasive',
                    'es' => 'Médicos Especialistas - Cardiología - Invasiva - No Invasiva',
                    'slugEn' => 'physicians-and-surgeons-invasive-non-invasive',
                    'slugEs' => 'medicos-especialistas-cardiologia-invasiva-no-invasiva',
                ),
            1704 =>
                array (
                    'en' => 'Physicians And Surgeons - Medicine Genetics',
                    'es' => 'Médicos Especialistas - Genética Médica',
                    'slugEn' => 'physicians-and-surgeons-medicine-genetics',
                    'slugEs' => 'medicos-especialistas-genetica-medica',
                ),
            1705 =>
                array (
                    'en' => 'Physicians And Surgeons - Minor Surgery',
                    'es' => 'Médico Especialistas - Cirugía Menor',
                    'slugEn' => 'physicians-and-surgeons-minor-surgery',
                    'slugEs' => 'medico-especialistas-cirugia-menor',
                ),
            1706 =>
                array (
                    'en' => 'Physicians And Surgeons - Neonatal Medicine',
                    'es' => 'Medicos Especialistas - Neonatologia',
                    'slugEn' => 'physicians-and-surgeons-neonatal-medicine',
                    'slugEs' => 'medicos-especialistas-neonatologia',
                ),
            1707 =>
                array (
                    'en' => 'Physicians And Surgeons - Neurology',
                    'es' => 'Medicos Especialistas - Neurologia',
                    'slugEn' => 'physicians-and-surgeons-neurology',
                    'slugEs' => 'medicos-especialistas-neurologia',
                ),
            1708 =>
                array (
                    'en' => 'Physicians And Surgeons - Neurosurgery',
                    'es' => 'Medicos Especialistas - Neurocirugia',
                    'slugEn' => 'physicians-and-surgeons-neurosurgery',
                    'slugEs' => 'medicos-especialistas-neurocirugia',
                ),
            1709 =>
                array (
                    'en' => 'Physicians And Surgeons - Neurotology',
                    'es' => 'Medicos Especialistas - Neurotologia',
                    'slugEn' => 'physicians-and-surgeons-neurotology',
                    'slugEs' => 'medicos-especialistas-neurotologia',
                ),
            1710 =>
                array (
                    'en' => 'Physicians And Surgeons - Nuclear Medicine',
                    'es' => 'Medicos Especialistas - Medicina Nuclear',
                    'slugEn' => 'physicians-and-surgeons-nuclear-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-nuclear',
                ),
            1711 =>
                array (
                    'en' => 'Physicians And Surgeons - Ophthalmology (Eyes)',
                    'es' => 'Medicos Especialistas - Oftalmologia (Ojos)',
                    'slugEn' => 'physicians-and-surgeons-ophthalmology-eyes',
                    'slugEs' => 'medicos-especialistas-oftalmologia-ojos',
                ),
            1712 =>
                array (
                    'en' => 'Physicians And Surgeons - Ophthalmology-Retina',
                    'es' => 'Medicos Especialistas - Oftalmologia-Retina',
                    'slugEn' => 'physicians-and-surgeons-ophthalmology-retina',
                    'slugEs' => 'medicos-especialistas-oftalmologia-retina',
                ),
            1713 =>
                array (
                    'en' => 'Physicians And Surgeons - Oral And Maxillofacial-Surgery',
                    'es' => 'Medicos Especialistas - Cirugia-Oral Y Maxilofacial',
                    'slugEn' => 'physicians-and-surgeons-oral-and-maxillofacial-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-oral-y-maxilofacial',
                ),
            1714 =>
                array (
                    'en' => 'Physicians And Surgeons - Orthopaedic Surgery',
                    'es' => 'Médicos Especialistas - Cirugía Ortopédica',
                    'slugEn' => 'physicians-and-surgeons-orthopaedic-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-ortopedica',
                ),
            1715 =>
                array (
                    'en' => 'Physicians And Surgeons - Orthopedic Medicine',
                    'es' => 'Medicos Especialistas - Ortopedia',
                    'slugEn' => 'physicians-and-surgeons-orthopedic-medicine',
                    'slugEs' => 'medicos-especialistas-ortopedia',
                ),
            1716 =>
                array (
                    'en' => 'Physicians And Surgeons - Orthopedics-Pediatric',
                    'es' => 'Medicos Especialistas - Ortopedia-Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-orthopedics-pediatric',
                    'slugEs' => 'medicos-especialistas-ortopedia-pediatrica',
                ),
            1717 =>
                array (
                    'en' => 'Physicians And Surgeons - Pain Control',
                    'es' => 'Médicos Especialistas - Manejo del Dolor',
                    'slugEn' => 'physicians-and-surgeons-pain-control',
                    'slugEs' => 'medicos-especialistas-manejo-del-dolor',
                ),
            1718 =>
                array (
                    'en' => 'Physicians And Surgeons - Pathology',
                    'es' => 'Medicos Especialistas - Patologia',
                    'slugEn' => 'physicians-and-surgeons-pathology',
                    'slugEs' => 'medicos-especialistas-patologia',
                ),
            1719 =>
                array (
                    'en' => 'Physicians And Surgeons - Pediatric Gastroenterology',
                    'es' => 'Medicos Especialistas - Gastroenterologia-Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-pediatric-gastroenterology',
                    'slugEs' => 'medicos-especialistas-gastroenterologia-pediatrica',
                ),
            1720 =>
                array (
                    'en' => 'Physicians And Surgeons - Pediatric -Neurology',
                    'es' => 'Medicos Especialistas - Neurologia-Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-pediatric-neurology',
                    'slugEs' => 'medicos-especialistas-neurologia-pediatrica',
                ),
            1721 =>
                array (
                    'en' => 'Physicians And Surgeons - Pediatric-Ophtalmology',
                    'es' => 'Medicos Especialistas - Oftalmologia-Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-pediatric-ophtalmology',
                    'slugEs' => 'medicos-especialistas-oftalmologia-pediatrica',
                ),
            1722 =>
                array (
                    'en' => 'Physicians And Surgeons - Pediatric-Pneumology',
                    'es' => 'Medicos Especialistas - Neumologia-Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-pediatric-pneumology',
                    'slugEs' => 'medicos-especialistas-neumologia-pediatrica',
                ),
            1723 =>
                array (
                    'en' => 'Physicians And Surgeons - Pediatrics',
                    'es' => 'Medicos Especialistas - Pediatria',
                    'slugEn' => 'physicians-and-surgeons-pediatrics',
                    'slugEs' => 'medicos-especialistas-pediatria',
                ),
            1724 =>
                array (
                    'en' => 'Physicians And Surgeons - Pediatric-Surgery',
                    'es' => 'Medicos Especialistas - Cirugia-Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-pediatric-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-pediatrica',
                ),
            1725 =>
                array (
                    'en' => 'Physicians And Surgeons - Periferovascular Surgery',
                    'es' => 'Medicos Especialistas - Cirugia-Periferovascular',
                    'slugEn' => 'physicians-and-surgeons-periferovascular-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-periferovascular',
                ),
            1726 =>
                array (
                    'en' => 'Physicians And Surgeons - Perinatology',
                    'es' => 'Medicos Especialistas - Perinatologia',
                    'slugEn' => 'physicians-and-surgeons-perinatology',
                    'slugEs' => 'medicos-especialistas-perinatologia',
                ),
            1727 =>
                array (
                    'en' => 'Physicians And Surgeons - Physiatry (Physical Medicine And Rehabilitation)',
                    'es' => 'Rehabilitación',
                    'slugEn' => 'physicians-and-surgeons-physiatry-physical-medicine-and-rehabilitation',
                    'slugEs' => 'rehabilitacion',
                ),
            1728 =>
                array (
                    'en' => 'Physicians And Surgeons - Plastic Surgery-Esthetic And Reconstructive-Head And Neck Area',
                    'es' => 'Médicos Especialistas - Cirugía Plástica-Estética Y Reconstructiva-Area Cabeza Y Cuello',
                    'slugEn' => 'physicians-and-surgeons-plastic-surgery-esthetic-and-reconstructive-head-and-neck-area',
                    'slugEs' => 'medicos-especialistas-cirugia-plastica-estetica-y-reconstructiva-area-cabeza-y-cuello',
                ),
            1729 =>
                array (
                    'en' => 'Physicians And Surgeons - Pneumology',
                    'es' => 'Médicos Especialistas - Neumología',
                    'slugEn' => 'physicians-and-surgeons-pneumology',
                    'slugEs' => 'medicos-especialistas-neumologia',
                ),
            1730 =>
                array (
                    'en' => 'Physicians And Surgeons - Primary Medicine',
                    'es' => 'Médicos Especialistas - Medicina Primaria',
                    'slugEn' => 'physicians-and-surgeons-primary-medicine',
                    'slugEs' => 'medicos-especialistas-medicina-primaria',
                ),
            1731 =>
                array (
                    'en' => 'Physicians And Surgeons - Psychiatrist-Family',
                    'es' => 'Médicos Especialistas - Siquiatría-Familia',
                    'slugEn' => 'physicians-and-surgeons-psychiatrist-family',
                    'slugEs' => 'medicos-especialistas-siquiatria-familia',
                ),
            1732 =>
                array (
                    'en' => 'Physicians And Surgeons - Psychiatry - Forensic',
                    'es' => 'Médicos Especialistas - Siquiatría-Forense',
                    'slugEn' => 'physicians-and-surgeons-psychiatry-forensic',
                    'slugEs' => 'medicos-especialistas-siquiatria-forense',
                ),
            1733 =>
                array (
                    'en' => 'Physicians And Surgeons - Psychiatry-Children And Teenagers',
                    'es' => 'Medicos Especialistas - Siquiatria-Niños Y Adolescentes',
                    'slugEn' => 'physicians-and-surgeons-psychiatry-children-and-teenagers',
                    'slugEs' => 'medicos-especialistas-siquiatria-ninos-y-adolescentes',
                ),
            1734 =>
                array (
                    'en' => 'Physicians And Surgeons - Psychiatry-Geriatric',
                    'es' => 'Medicos Especialistas - Siquiatria-Geriatrica',
                    'slugEn' => 'physicians-and-surgeons-psychiatry-geriatric',
                    'slugEs' => 'medicos-especialistas-siquiatria-geriatrica',
                ),
            1735 =>
                array (
                    'en' => 'Physicians And Surgeons - Radiology',
                    'es' => 'Médicos Especialistas - Radiología',
                    'slugEn' => 'physicians-and-surgeons-radiology',
                    'slugEs' => 'medicos-especialistas-radiologia',
                ),
            1736 =>
                array (
                    'en' => 'Physicians And Surgeons - Radiotherapy-Cancer-Oncology',
                    'es' => 'Médicos Especialistas - Radioterapia-Cáncer (Oncología)',
                    'slugEn' => 'physicians-and-surgeons-radiotherapy-cancer-oncology',
                    'slugEs' => 'medicos-especialistas-radioterapia-cancer-oncologia',
                ),
            1737 =>
                array (
                    'en' => 'Physicians And Surgeons - Reumatology',
                    'es' => 'Médicos Especialistas - Reumatología',
                    'slugEn' => 'physicians-and-surgeons-reumatology',
                    'slugEs' => 'medicos-especialistas-reumatologia',
                ),
            1738 =>
                array (
                    'en' => 'Physicians And Surgeons - Sleep Disorder',
                    'es' => 'Medicos Especialistas - Desórdenes de Sueño',
                    'slugEn' => 'physicians-and-surgeons-sleep-disorder',
                    'slugEs' => 'medicos-especialistas-desordenes-de-sueno',
                ),
            1739 =>
                array (
                    'en' => 'Physicians And Surgeons - Surgery',
                    'es' => 'Medicos Especialistas - Cirugia',
                    'slugEn' => 'physicians-and-surgeons-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia',
                ),
            1740 =>
                array (
                    'en' => 'Physicians And Surgeons - Surgery - Colorectal',
                    'es' => 'Médicos Especialistas - Cirugía - Colorectal',
                    'slugEn' => 'physicians-and-surgeons-surgery-colorectal',
                    'slugEs' => 'medicos-especialistas-cirugia-colorectal',
                ),
            1741 =>
                array (
                    'en' => 'Physicians And Surgeons - Surgery-Cancer (Oncology)',
                    'es' => 'Médicos Especialistas - Cirugía-Cáncer (Oncología)',
                    'slugEn' => 'physicians-and-surgeons-surgery-cancer-oncology',
                    'slugEs' => 'medicos-especialistas-cirugia-cancer-oncologia',
                ),
            1742 =>
                array (
                    'en' => 'Physicians And Surgeons - Urology',
                    'es' => 'Médicos Especialistas - Urología',
                    'slugEn' => 'physicians-and-surgeons-urology',
                    'slugEs' => 'medicos-especialistas-urologia',
                ),
            1743 =>
                array (
                    'en' => 'Physicians And Surgeons -Hand-Surgery',
                    'es' => 'Medicos Especialistas - Cirugia-Mano',
                    'slugEn' => 'physicians-and-surgeons-hand-surgery',
                    'slugEs' => 'medicos-especialistas-cirugia-mano',
                ),
            1744 =>
                array (
                    'en' => 'Physicians And Surgeons- Pediatric-Endocrinology And Diabetes',
                    'es' => 'Medicos Especialistas - Endocrinologia Y Diabetes-Pediatrica',
                    'slugEn' => 'physicians-and-surgeons-pediatric-endocrinology-and-diabetes',
                    'slugEs' => 'medicos-especialistas-endocrinologia-y-diabetes-pediatrica',
                ),
            1745 =>
                array (
                    'en' => 'Physicians And Surgeons- Sub-Specialist - Hand-Surgery',
                    'es' => 'Medicos Sub-Especialistas - Cirugia de Mano',
                    'slugEn' => 'physicians-and-surgeons-sub-specialist-hand-surgery',
                    'slugEs' => 'medicos-sub-especialistas-cirugia-de-mano',
                ),
            1746 =>
                array (
                    'en' => 'Physicians Gastroenterology',
                    'es' => 'Medicos Especialistas Gastroenterologia',
                    'slugEn' => 'physicians-gastroenterology',
                    'slugEs' => 'medicos-especialistas-gastroenterologia',
                ),
            1747 =>
                array (
                    'en' => 'Physicians Nephrology',
                    'es' => 'Medicos Especialistas Nefrologia',
                    'slugEn' => 'physicians-nephrology',
                    'slugEs' => 'medicos-especialistas-nefrologia',
                ),
            1748 =>
                array (
                    'en' => 'Physicians Otolaryngology',
                    'es' => 'Medicos Especialistas Otolaringologia',
                    'slugEn' => 'physicians-otolaryngology',
                    'slugEs' => 'medicos-especialistas-otolaringologia',
                ),
            1749 =>
                array (
                    'en' => 'Physicians-Equipment-Rental',
                    'es' => 'Médicos-Efectos Y Equipo-Alquiler',
                    'slugEn' => 'physicians-equipment-rental',
                    'slugEs' => 'medicos-efectos-y-equipo-alquiler',
                ),
            1750 =>
                array (
                    'en' => 'Physicians-Equipment-Repair',
                    'es' => 'Médicos-Efectos Y Equipo-Reparación',
                    'slugEn' => 'physicians-equipment-repair',
                    'slugEs' => 'medicos-efectos-y-equipo-reparacion',
                ),
            1751 =>
                array (
                    'en' => 'Piano',
                    'es' => 'Piano',
                    'slugEn' => 'piano',
                    'slugEs' => 'piano',
                ),
            1752 =>
                array (
                    'en' => 'Pianos',
                    'es' => 'Pianos',
                    'slugEn' => 'pianos',
                    'slugEs' => 'pianos',
                ),
            1753 =>
                array (
                    'en' => 'Pile - Driving',
                    'es' => 'Pilotes-Hinca',
                    'slugEn' => 'pile-driving',
                    'slugEs' => 'pilotes-hinca',
                ),
            1754 =>
                array (
                    'en' => 'Pipe',
                    'es' => 'Tuberia',
                    'slugEn' => 'pipe',
                    'slugEs' => 'tuberia',
                ),
            1755 =>
                array (
                    'en' => 'Pipe - Fittings & Accessories',
                    'es' => 'Tubería-Conexiones Y Accesorios',
                    'slugEn' => 'pipe-fittings-accessories',
                    'slugEs' => 'tuberia-conexiones-y-accesorios',
                ),
            1756 =>
                array (
                    'en' => 'Pipe Unclogging',
                    'es' => 'Plomería - Destapes',
                    'slugEn' => 'pipe-unclogging',
                    'slugEs' => 'plomeria-destapes',
                ),
            1757 =>
                array (
                    'en' => 'Pizza',
                    'es' => 'Pizzerías',
                    'slugEn' => 'pizza',
                    'slugEs' => 'pizzerias',
                ),
            1758 =>
                array (
                    'en' => 'Pizza on Wood Fire',
                    'es' => 'Pizza en Horno de Leña',
                    'slugEn' => 'pizza-on-wood-fire',
                    'slugEs' => 'pizza-en-horno-de-lena',
                ),
            1759 =>
                array (
                    'en' => 'Plants - Ornamental',
                    'es' => 'Plantas Ornamentales',
                    'slugEn' => 'plants-ornamental',
                    'slugEs' => 'plantas-ornamentales',
                ),
            1760 =>
                array (
                    'en' => 'Plastics',
                    'es' => 'Plásticos',
                    'slugEn' => 'plastics',
                    'slugEs' => 'plasticos',
                ),
            1761 =>
                array (
                    'en' => 'Plastics - Banners',
                    'es' => 'Rótulos-Plásticos',
                    'slugEn' => 'plastics-banners',
                    'slugEs' => 'rotulos-plasticos',
                ),
            1762 =>
                array (
                    'en' => 'Plastics - Manufacture',
                    'es' => 'Plásticos-Fábricas',
                    'slugEn' => 'plastics-manufacture',
                    'slugEs' => 'plasticos-fabricas',
                ),
            1763 =>
                array (
                    'en' => 'Plates - Names',
                    'es' => 'Placas-Nombres',
                    'slugEn' => 'plates-names',
                    'slugEs' => 'placas-nombres',
                ),
            1764 =>
                array (
                    'en' => 'Plating - Metal',
                    'es' => 'Metal-Talleres',
                    'slugEn' => 'plating-metal',
                    'slugEs' => 'metal-talleres',
                ),
            1765 =>
                array (
                    'en' => 'Playground',
                    'es' => 'Playground',
                    'slugEn' => 'playground',
                    'slugEs' => 'playground',
                ),
            1766 =>
                array (
                    'en' => 'Plumbing',
                    'es' => 'Plomería',
                    'slugEn' => 'plumbing',
                    'slugEs' => 'plomeria',
                ),
            1767 =>
                array (
                    'en' => 'Plumbing - Equipment & Supplies',
                    'es' => 'Plomería-Efectos Y Equipo',
                    'slugEn' => 'plumbing-equipment-supplies',
                    'slugEs' => 'plomeria-efectos-y-equipo',
                ),
            1768 =>
                array (
                    'en' => 'Plumbing - Fixtures',
                    'es' => 'Sanitarios-Efectos Y Equipo',
                    'slugEn' => 'plumbing-fixtures',
                    'slugEs' => 'sanitarios-efectos-y-equipo',
                ),
            1769 =>
                array (
                    'en' => 'Plumbing-Certification',
                    'es' => 'Plomería-Certificación',
                    'slugEn' => 'plumbing-certification',
                    'slugEs' => 'plomeria-certificacion',
                ),
            1770 =>
                array (
                    'en' => 'Plumbing-Commercial',
                    'es' => 'Plomería-Comercial',
                    'slugEn' => 'plumbing-commercial',
                    'slugEs' => 'plomeria-comercial',
                ),
            1771 =>
                array (
                    'en' => 'Plus Size Clothes',
                    'es' => 'Ropa Plus',
                    'slugEn' => 'plus-size-clothes',
                    'slugEs' => 'ropa-plus',
                ),
            1772 =>
                array (
                    'en' => 'Podiatry Physicians',
                    'es' => 'Medicos Podiatras',
                    'slugEn' => 'podiatry-physicians',
                    'slugEs' => 'medicos-podiatras',
                ),
            1773 =>
                array (
                    'en' => 'Point of Sales (POS)',
                    'es' => 'Punto de Venta (POS)',
                    'slugEn' => 'point-of-sales-pos',
                    'slugEs' => 'punto-de-venta-pos',
                ),
            1774 =>
                array (
                    'en' => 'Police - Equipment & Supplies',
                    'es' => 'Policía-Efectos Y Equipo',
                    'slugEn' => 'police-equipment-supplies',
                    'slugEs' => 'policia-efectos-y-equipo',
                ),
            1775 =>
                array (
                    'en' => 'Police Uniforms',
                    'es' => 'Uniforme de Policia',
                    'slugEn' => 'police-uniforms',
                    'slugEs' => 'uniforme-de-policia',
                ),
            1776 =>
                array (
                    'en' => 'Political Parties',
                    'es' => 'Partidos Políticos',
                    'slugEn' => 'political-parties',
                    'slugEs' => 'partidos-politicos',
                ),
            1777 =>
                array (
                    'en' => 'Pollution - Control',
                    'es' => 'Contaminación-Control',
                    'slugEn' => 'pollution-control',
                    'slugEs' => 'contaminacion-control',
                ),
            1778 =>
                array (
                    'en' => 'Polyclinics',
                    'es' => 'Policlínicas',
                    'slugEn' => 'polyclinics',
                    'slugEs' => 'policlinicas',
                ),
            1779 =>
                array (
                    'en' => 'Pool-Party',
                    'es' => 'Fiestas-Piscinas',
                    'slugEn' => 'pool-party',
                    'slugEs' => 'fiestas-piscinas',
                ),
            1780 =>
                array (
                    'en' => 'Pork - Roasted',
                    'es' => 'Lechoneras',
                    'slugEn' => 'pork-roasted',
                    'slugEs' => 'lechoneras',
                ),
            1781 =>
                array (
                    'en' => 'POS Cash Registers',
                    'es' => 'Cajas Registradoras Puntos de Venta',
                    'slugEn' => 'pos-cash-registers',
                    'slugEs' => 'cajas-registradoras-puntos-de-venta',
                ),
            1782 =>
                array (
                    'en' => 'Postal Boxes - Service',
                    'es' => 'Postal-Apartados-Servicios',
                    'slugEn' => 'postal-boxes-service',
                    'slugEs' => 'postal-apartados-servicios',
                ),
            1783 =>
                array (
                    'en' => 'Postal Services',
                    'es' => 'Servicios-Postales',
                    'slugEn' => 'postal-services',
                    'slugEs' => 'servicios-postales',
                ),
            1784 =>
                array (
                    'en' => 'Posts',
                    'es' => 'Postes',
                    'slugEn' => 'posts',
                    'slugEs' => 'postes',
                ),
            1785 =>
                array (
                    'en' => 'Postural Therapy',
                    'es' => 'Terapia Postural',
                    'slugEn' => 'postural-therapy',
                    'slugEs' => 'terapia-postural',
                ),
            1786 =>
                array (
                    'en' => 'Poultry',
                    'es' => 'Carnes Blancas',
                    'slugEn' => 'poultry',
                    'slugEs' => 'carnes-blancas',
                ),
            1787 =>
                array (
                    'en' => 'Power Generators',
                    'es' => 'Plantas Eléctricas',
                    'slugEn' => 'power-generators',
                    'slugEs' => 'plantas-electricas',
                ),
            1788 =>
                array (
                    'en' => 'Power Generators Repair',
                    'es' => 'Plantas Eléctricas - Reparación',
                    'slugEn' => 'power-generators-repair',
                    'slugEs' => 'plantas-electricas-reparacion',
                ),
            1789 =>
                array (
                    'en' => 'Power Steering Line',
                    'es' => 'Mangas de Power Steering',
                    'slugEn' => 'power-steering-line',
                    'slugEs' => 'mangas-de-power-steering',
                ),
            1790 =>
                array (
                    'en' => 'Power Window Repair',
                    'es' => 'Reparación de Power Window',
                    'slugEn' => 'power-window-repair',
                    'slugEs' => 'reparacion-de-power-window',
                ),
            1791 =>
                array (
                    'en' => 'Pregnancy Termination',
                    'es' => 'Terminación de Embarazo',
                    'slugEn' => 'pregnancy-termination',
                    'slugEs' => 'terminacion-de-embarazo',
                ),
            1792 =>
                array (
                    'en' => 'Pregnancy Test',
                    'es' => 'Pruebas de Embarazo',
                    'slugEn' => 'pregnancy-test',
                    'slugEs' => 'pruebas-de-embarazo',
                ),
            1793 =>
                array (
                    'en' => 'Pre-school',
                    'es' => 'Pre-escolar',
                    'slugEn' => 'pre-school',
                    'slugEs' => 'pre-escolar',
                ),
            1794 =>
                array (
                    'en' => 'Preschool Center',
                    'es' => 'Centro Preescolar',
                    'slugEn' => 'preschool-center',
                    'slugEs' => 'centro-preescolar',
                ),
            1795 =>
                array (
                    'en' => 'Pressure Washer-Equipment-Rental',
                    'es' => 'Lavado A Presión-Equipo-Alquiler',
                    'slugEn' => 'pressure-washer-equipment-rental',
                    'slugEs' => 'lavado-a-presion-equipo-alquiler',
                ),
            1796 =>
                array (
                    'en' => 'Preventative Medicine',
                    'es' => 'Medicina Preventiva',
                    'slugEn' => 'preventative-medicine',
                    'slugEs' => 'medicina-preventiva',
                ),
            1797 =>
                array (
                    'en' => 'Price Marking - Equipment & Supplies',
                    'es' => 'Marcadoras-Precios',
                    'slugEn' => 'price-marking-equipment-supplies',
                    'slugEs' => 'marcadoras-precios',
                ),
            1798 =>
                array (
                    'en' => 'Print',
                    'es' => 'Impresión',
                    'slugEn' => 'print',
                    'slugEs' => 'impresion',
                ),
            1799 =>
                array (
                    'en' => 'Print - Digital',
                    'es' => 'Imprenta - Digital',
                    'slugEn' => 'print-digital',
                    'slugEs' => 'imprenta-digital',
                ),
            1800 =>
                array (
                    'en' => 'Print-Commercial',
                    'es' => 'Imprenta - Comercial',
                    'slugEn' => 'print-commercial',
                    'slugEs' => 'imprenta-comercial',
                ),
            1801 =>
                array (
                    'en' => 'Printed Shirts',
                    'es' => 'Camisetas Impresas',
                    'slugEn' => 'printed-shirts',
                    'slugEs' => 'camisetas-impresas',
                ),
            1802 =>
                array (
                    'en' => 'Printers',
                    'es' => 'Imprentas',
                    'slugEn' => 'printers',
                    'slugEs' => 'imprentas',
                ),
            1803 =>
                array (
                    'en' => 'Printers - Equipment & Supplies',
                    'es' => 'Imprentas-Efectos Y Equipo',
                    'slugEn' => 'printers-equipment-supplies',
                    'slugEs' => 'imprentas-efectos-y-equipo',
                ),
            1804 =>
                array (
                    'en' => 'Printers - Silk Screen',
                    'es' => 'Imprentas-Serigráficas',
                    'slugEn' => 'printers-silk-screen',
                    'slugEs' => 'imprentas-serigraficas',
                ),
            1805 =>
                array (
                    'en' => 'Private School',
                    'es' => 'Escuelas Privadas',
                    'slugEn' => 'private-school',
                    'slugEs' => 'escuelas-privadas',
                ),
            1806 =>
                array (
                    'en' => 'Private Schools & Academies',
                    'es' => 'Escuelas Académicas',
                    'slugEn' => 'private-schools-academies',
                    'slugEs' => 'escuelas-academicas',
                ),
            1807 =>
                array (
                    'en' => 'Private Security',
                    'es' => 'Seguridad Privada',
                    'slugEn' => 'private-security',
                    'slugEs' => 'seguridad-privada',
                ),
            1808 =>
                array (
                    'en' => 'Product Management',
                    'es' => 'Gerencia de Productos',
                    'slugEn' => 'product-management',
                    'slugEs' => 'gerencia-de-productos',
                ),
            1809 =>
                array (
                    'en' => 'Production-Advertising',
                    'es' => 'Producción-Publicidad',
                    'slugEn' => 'production-advertising',
                    'slugEs' => 'produccion-publicidad',
                ),
            1810 =>
                array (
                    'en' => 'Promoters - Sports',
                    'es' => 'Espectáculos-Productores',
                    'slugEn' => 'promoters-sports',
                    'slugEs' => 'espectaculos-productores',
                ),
            1811 =>
                array (
                    'en' => 'Promotion - International - Television',
                    'es' => 'Promociones Internacionales-Televisión',
                    'slugEn' => 'promotion-international-television',
                    'slugEs' => 'promociones-internacionales-television',
                ),
            1812 =>
                array (
                    'en' => 'Promotions - Articles',
                    'es' => 'Promociones - Artículos',
                    'slugEn' => 'promotions-articles',
                    'slugEs' => 'promociones-articulos',
                ),
            1813 =>
                array (
                    'en' => 'Promotions - Specialties',
                    'es' => 'Promociones-Especialidades',
                    'slugEn' => 'promotions-specialties',
                    'slugEs' => 'promociones-especialidades',
                ),
            1814 =>
                array (
                    'en' => 'Proms - Graduations',
                    'es' => 'Proms - Graduaciones',
                    'slugEn' => 'proms-graduations',
                    'slugEs' => 'proms-graduaciones',
                ),
            1815 =>
                array (
                    'en' => 'Propane Gas',
                    'es' => 'Gas Propano',
                    'slugEn' => 'propane-gas',
                    'slugEs' => 'gas-propano',
                ),
            1816 =>
                array (
                    'en' => 'Property Foreclose',
                    'es' => 'Embargo Propiedades',
                    'slugEn' => 'property-foreclose',
                    'slugEs' => 'embargo-propiedades',
                ),
            1817 =>
                array (
                    'en' => 'Property Title',
                    'es' => 'Título de Propiedad',
                    'slugEn' => 'property-title',
                    'slugEs' => 'titulo-de-propiedad',
                ),
            1818 =>
                array (
                    'en' => 'Prosthesis',
                    'es' => 'Prótesis',
                    'slugEn' => 'prosthesis',
                    'slugEs' => 'protesis',
                ),
            1819 =>
                array (
                    'en' => 'Prosthodontist',
                    'es' => 'Prostodoncista',
                    'slugEn' => 'prosthodontist',
                    'slugEs' => 'prostodoncista',
                ),
            1820 =>
                array (
                    'en' => 'Protectors-Checks',
                    'es' => 'Protectores-Cheques',
                    'slugEn' => 'protectors-checks',
                    'slugEs' => 'protectores-cheques',
                ),
            1821 =>
                array (
                    'en' => 'Provisions - Warehouses',
                    'es' => 'Provisiones-Almacenes',
                    'slugEn' => 'provisions-warehouses',
                    'slugEs' => 'provisiones-almacenes',
                ),
            1822 =>
                array (
                    'en' => 'Psychiatrist',
                    'es' => 'Psiquiatra',
                    'slugEn' => 'psychiatrist',
                    'slugEs' => 'psiquiatra',
                ),
            1823 =>
                array (
                    'en' => 'Psychiatrists',
                    'es' => 'Psiquiatras',
                    'slugEn' => 'psychiatrists',
                    'slugEs' => 'psiquiatras',
                ),
            1824 =>
                array (
                    'en' => 'Psychiatry',
                    'es' => 'Siquiatría',
                    'slugEn' => 'psychiatry',
                    'slugEs' => 'siquiatria',
                ),
            1825 =>
                array (
                    'en' => 'Psychics',
                    'es' => 'Psíquicos',
                    'slugEn' => 'psychics',
                    'slugEs' => 'psiquicos',
                ),
            1826 =>
                array (
                    'en' => 'Psychological Counselling',
                    'es' => 'Consejería Psicológica',
                    'slugEn' => 'psychological-counselling',
                    'slugEs' => 'consejeria-psicologica',
                ),
            1827 =>
                array (
                    'en' => 'Psychologist',
                    'es' => 'Psicólogo',
                    'slugEn' => 'psychologist',
                    'slugEs' => 'psicologo',
                ),
            1828 =>
                array (
                    'en' => 'Psychologist - Children And Teenagers',
                    'es' => 'Sicologos Por Especialidad - Niños Y Adolescentes',
                    'slugEn' => 'psychologist-children-and-teenagers',
                    'slugEs' => 'sicologos-por-especialidad-ninos-y-adolescentes',
                ),
            1829 =>
                array (
                    'en' => 'Psychologist - Forensic',
                    'es' => 'Sicólogos Por Especialidad - Forense',
                    'slugEn' => 'psychologist-forensic',
                    'slugEs' => 'sicologos-por-especialidad-forense',
                ),
            1830 =>
                array (
                    'en' => 'Psychologist - General',
                    'es' => 'Sicólogos Por Especialidad - Generales',
                    'slugEn' => 'psychologist-general',
                    'slugEs' => 'sicologos-por-especialidad-generales',
                ),
            1831 =>
                array (
                    'en' => 'Psychologist - Industrial',
                    'es' => 'Sicólogos Por Especialidad - Industriales',
                    'slugEn' => 'psychologist-industrial',
                    'slugEs' => 'sicologos-por-especialidad-industriales',
                ),
            1832 =>
                array (
                    'en' => 'Psychologist - School',
                    'es' => 'Sicólogos Por Especialidad - Escolares',
                    'slugEn' => 'psychologist-school',
                    'slugEs' => 'sicologos-por-especialidad-escolares',
                ),
            1833 =>
                array (
                    'en' => 'Psychologist - Stress Management',
                    'es' => 'Sicólogos Por Especialidad - Estrés-Manejo',
                    'slugEn' => 'psychologist-stress-management',
                    'slugEs' => 'sicologos-por-especialidad-estres-manejo',
                ),
            1834 =>
                array (
                    'en' => 'Psychologist by Specialty -  The Elderly',
                    'es' => 'Psicólogo por Especialidad- Ancianos',
                    'slugEn' => 'psychologist-by-specialty-the-elderly',
                    'slugEs' => 'psicologo-por-especialidad-ancianos',
                ),
            1835 =>
                array (
                    'en' => 'Psychologist by Specialty - Social Worker',
                    'es' => 'Sicologos Por Especialidad - Sociales',
                    'slugEn' => 'psychologist-by-specialty-social-worker',
                    'slugEs' => 'sicologos-por-especialidad-sociales',
                ),
            1836 =>
                array (
                    'en' => 'Psychologist -Family',
                    'es' => 'Sicólogos Por Especialidad - Familia',
                    'slugEn' => 'psychologist-family',
                    'slugEs' => 'sicologos-por-especialidad-familia',
                ),
            1837 =>
                array (
                    'en' => 'Psychologist- Marriage Counseling',
                    'es' => 'Sicólogos Por Especialidad - Matrimonios',
                    'slugEn' => 'psychologist-marriage-counseling',
                    'slugEs' => 'sicologos-por-especialidad-matrimonios',
                ),
            1838 =>
                array (
                    'en' => 'Psychologist with Specialty - Geriatrics',
                    'es' => 'Sicólogos por Especialidad - Geriátricos',
                    'slugEn' => 'psychologist-with-specialty-geriatrics',
                    'slugEs' => 'sicologos-por-especialidad-geriatricos',
                ),
            1839 =>
                array (
                    'en' => 'Psychologist with Specialty in Psychological Counseling',
                    'es' => 'Psicólogo con Especialidad en Consejería Sicológica',
                    'slugEn' => 'psychologist-with-specialty-in-psychological-counseling',
                    'slugEs' => 'psicologo-con-especialidad-en-consejeria-sicologica',
                ),
            1840 =>
                array (
                    'en' => 'Psychologists',
                    'es' => 'Sicólogos',
                    'slugEn' => 'psychologists',
                    'slugEs' => 'sicologos',
                ),
            1841 =>
                array (
                    'en' => 'Psychologists - Clinical',
                    'es' => 'Sicólogos Por Especialidad - Clínicos',
                    'slugEn' => 'psychologists-clinical',
                    'slugEs' => 'sicologos-por-especialidad-clinicos',
                ),
            1842 =>
                array (
                    'en' => 'Psychologists By Specialty - Autism',
                    'es' => 'Sicólogos Por Especialidad - Autismo',
                    'slugEn' => 'psychologists-by-specialty-autism',
                    'slugEs' => 'sicologos-por-especialidad-autismo',
                ),
            1843 =>
                array (
                    'en' => 'Psychologists By Specialty-Clinical',
                    'es' => 'Clínicos',
                    'slugEn' => 'psychologists-by-specialty-clinical',
                    'slugEs' => 'clinicos',
                ),
            1844 =>
                array (
                    'en' => 'Psychotherapy',
                    'es' => 'Sicoterapia',
                    'slugEn' => 'psychotherapy',
                    'slugEs' => 'sicoterapia',
                ),
            1845 =>
                array (
                    'en' => 'Psycometric',
                    'es' => 'Sicometría',
                    'slugEn' => 'psycometric',
                    'slugEs' => 'sicometria',
                ),
            1846 =>
                array (
                    'en' => 'Public Phones-Companies',
                    'es' => 'Telefonos Publicos-Companias',
                    'slugEn' => 'public-phones-companies',
                    'slugEs' => 'telefonos-publicos-companias',
                ),
            1847 =>
                array (
                    'en' => 'Public Relations',
                    'es' => 'Relaciones Públicas',
                    'slugEn' => 'public-relations',
                    'slugEs' => 'relaciones-publicas',
                ),
            1848 =>
                array (
                    'en' => 'Public Warehouse',
                    'es' => 'Almacenes Públicos Afianzados',
                    'slugEn' => 'public-warehouse',
                    'slugEs' => 'almacenes-publicos-afianzados',
                ),
            1849 =>
                array (
                    'en' => 'Publications',
                    'es' => 'Publicaciones',
                    'slugEn' => 'publications',
                    'slugEs' => 'publicaciones',
                ),
            1850 =>
                array (
                    'en' => 'Publishers',
                    'es' => 'Editores',
                    'slugEn' => 'publishers',
                    'slugEs' => 'editores',
                ),
            1851 =>
                array (
                    'en' => 'Pubs',
                    'es' => 'Pubs',
                    'slugEn' => 'pubs',
                    'slugEs' => 'pubs',
                ),
            1852 =>
                array (
                    'en' => 'Puerto Rican Coffee',
                    'es' => 'Café Puertorriqueño',
                    'slugEn' => 'puerto-rican-coffee',
                    'slugEs' => 'cafe-puertorriqueno',
                ),
            1853 =>
                array (
                    'en' => 'Pumps',
                    'es' => 'Bombas',
                    'slugEn' => 'pumps',
                    'slugEs' => 'bombas',
                ),
            1854 =>
                array (
                    'en' => 'Pumps - Maintenance',
                    'es' => 'Bombas - Mantenimiento',
                    'slugEn' => 'pumps-maintenance',
                    'slugEs' => 'bombas-mantenimiento',
                ),
            1855 =>
                array (
                    'en' => 'Pumps - Repair',
                    'es' => 'Bombas-Reparación',
                    'slugEn' => 'pumps-repair',
                    'slugEs' => 'bombas-reparacion',
                ),
            1856 =>
                array (
                    'en' => 'Pumps - Wells - Instalation',
                    'es' => 'Bombas De Pozos-Instalación',
                    'slugEn' => 'pumps-wells-instalation',
                    'slugEs' => 'bombas-de-pozos-instalacion',
                ),
            1857 =>
                array (
                    'en' => 'Puppets And Marionette',
                    'es' => 'Títeres Y Marionetas',
                    'slugEn' => 'puppets-and-marionette',
                    'slugEs' => 'titeres-y-marionetas',
                ),
            1858 =>
                array (
                    'en' => 'Pure Water',
                    'es' => 'Agua Pura',
                    'slugEn' => 'pure-water',
                    'slugEs' => 'agua-pura',
                ),
            1859 =>
                array (
                    'en' => 'Purifiers',
                    'es' => 'Purificadores',
                    'slugEn' => 'purifiers',
                    'slugEs' => 'purificadores',
                ),
            1860 =>
                array (
                    'en' => 'Purifiers - Water',
                    'es' => 'Purificadores-Agua',
                    'slugEn' => 'purifiers-water',
                    'slugEs' => 'purificadores-agua',
                ),
            1861 =>
                array (
                    'en' => 'PVC Fences',
                    'es' => 'Verjas y Portones de PVC',
                    'slugEn' => 'pvc-fences',
                    'slugEs' => 'verjas-y-portones-de-pvc',
                ),
            1862 =>
                array (
                    'en' => 'Quarries',
                    'es' => 'Canteras',
                    'slugEn' => 'quarries',
                    'slugEs' => 'canteras',
                ),
            1863 =>
                array (
                    'en' => 'Quarries - Equipment & Supplies',
                    'es' => 'Canteras-Efectos Y Equipo',
                    'slugEn' => 'quarries-equipment-supplies',
                    'slugEs' => 'canteras-efectos-y-equipo',
                ),
            1864 =>
                array (
                    'en' => 'Quartz Cooktops',
                    'es' => 'Topes de Cuarzo',
                    'slugEn' => 'quartz-cooktops',
                    'slugEs' => 'topes-de-cuarzo',
                ),
            1865 =>
                array (
                    'en' => 'Quilt-Factory',
                    'es' => 'Colchas-Fábricas',
                    'slugEn' => 'quilt-factory',
                    'slugEs' => 'colchas-fabricas',
                ),
            1866 =>
                array (
                    'en' => 'Racecourse-Racetrack',
                    'es' => 'Hipódromos',
                    'slugEn' => 'racecourse-racetrack',
                    'slugEs' => 'hipodromos',
                ),
            1867 =>
                array (
                    'en' => 'Radiators - Repair',
                    'es' => 'Radiadores-Reparacion',
                    'slugEn' => 'radiators-repair',
                    'slugEs' => 'radiadores-reparacion',
                ),
            1868 =>
                array (
                    'en' => 'Radio - Stations',
                    'es' => 'Radio-Estaciones',
                    'slugEn' => 'radio-stations',
                    'slugEs' => 'radio-estaciones',
                ),
            1869 =>
                array (
                    'en' => 'Radio Communication',
                    'es' => 'Radiocomunicación-Compañías',
                    'slugEn' => 'radio-communication',
                    'slugEs' => 'radiocomunicacion-companias',
                ),
            1870 =>
                array (
                    'en' => 'Radio Communication - Equipment & Systems',
                    'es' => 'Radiocomunicación-Equipo Y Sistemas',
                    'slugEn' => 'radio-communication-equipment-systems',
                    'slugEs' => 'radiocomunicacion-equipo-y-sistemas',
                ),
            1871 =>
                array (
                    'en' => 'Radio Sets - Repair',
                    'es' => 'Radios-Reparación',
                    'slugEn' => 'radio-sets-repair',
                    'slugEs' => 'radios-reparacion',
                ),
            1872 =>
                array (
                    'en' => 'Radiology - Equipment Sales & Service',
                    'es' => 'Radiología - Equipo Venta y Servicio',
                    'slugEn' => 'radiology-equipment-sales-service',
                    'slugEs' => 'radiologia-equipo-venta-y-servicio',
                ),
            1873 =>
                array (
                    'en' => 'Radios',
                    'es' => 'Radios',
                    'slugEn' => 'radios',
                    'slugEs' => 'radios',
                ),
            1874 =>
                array (
                    'en' => 'Radios - Parts & Accessories',
                    'es' => 'Radios-Piezas Y Accesorios',
                    'slugEn' => 'radios-parts-accessories',
                    'slugEs' => 'radios-piezas-y-accesorios',
                ),
            1875 =>
                array (
                    'en' => 'Railing',
                    'es' => 'Pasamanos',
                    'slugEn' => 'railing',
                    'slugEs' => 'pasamanos',
                ),
            1876 =>
                array (
                    'en' => 'Razors & Blades',
                    'es' => 'Navajas - Afeitar',
                    'slugEn' => 'razors-blades',
                    'slugEs' => 'navajas-afeitar',
                ),
            1877 =>
                array (
                    'en' => 'Read - Stories',
                    'es' => 'Lectura - Cuentos',
                    'slugEn' => 'read-stories',
                    'slugEs' => 'lectura-cuentos',
                ),
            1878 =>
                array (
                    'en' => 'Real Estate',
                    'es' => 'Bienes Raíces-Administración',
                    'slugEn' => 'real-estate',
                    'slugEs' => 'bienes-raices-administracion',
                ),
            1879 =>
                array (
                    'en' => 'Real Estate - Appraisers',
                    'es' => 'Bienes Raíces-Tasadores',
                    'slugEn' => 'real-estate-appraisers',
                    'slugEs' => 'bienes-raices-tasadores',
                ),
            1880 =>
                array (
                    'en' => 'Real Estate - Brokers',
                    'es' => 'Bienes Raíces-Corredores',
                    'slugEn' => 'real-estate-brokers',
                    'slugEs' => 'bienes-raices-corredores',
                ),
            1881 =>
                array (
                    'en' => 'Real Estate - Commercial Spaces for Rent',
                    'es' => 'Bienes Raices - Alquiler Espacios Comerciales',
                    'slugEn' => 'real-estate-commercial-spaces-for-rent',
                    'slugEs' => 'bienes-raices-alquiler-espacios-comerciales',
                ),
            1882 =>
                array (
                    'en' => 'Real Estate - Consultants',
                    'es' => 'Bienes Raíces-Consultores',
                    'slugEn' => 'real-estate-consultants',
                    'slugEs' => 'bienes-raices-consultores',
                ),
            1883 =>
                array (
                    'en' => 'Real Estate - Developers',
                    'es' => 'Bienes Raíces-Desarrolladores',
                    'slugEn' => 'real-estate-developers',
                    'slugEs' => 'bienes-raices-desarrolladores',
                ),
            1884 =>
                array (
                    'en' => 'Real Estate - Inspection Services',
                    'es' => 'Bienes Raíces-Servicios De Inspección',
                    'slugEn' => 'real-estate-inspection-services',
                    'slugEs' => 'bienes-raices-servicios-de-inspeccion',
                ),
            1885 =>
                array (
                    'en' => 'Real Estate - Investment',
                    'es' => 'Bienes Raíces-Inversiones',
                    'slugEn' => 'real-estate-investment',
                    'slugEs' => 'bienes-raices-inversiones',
                ),
            1886 =>
                array (
                    'en' => 'Real Estate - Project Offices',
                    'es' => 'Urbanizaciones',
                    'slugEn' => 'real-estate-project-offices',
                    'slugEs' => 'urbanizaciones',
                ),
            1887 =>
                array (
                    'en' => 'Real Estate - Services',
                    'es' => 'Bienes Raíces-Servicios',
                    'slugEn' => 'real-estate-services',
                    'slugEs' => 'bienes-raices-servicios',
                ),
            1888 =>
                array (
                    'en' => 'Real Estate - Time Sharing',
                    'es' => 'Bienes Raíces-Alquiler De Verano',
                    'slugEn' => 'real-estate-time-sharing',
                    'slugEs' => 'bienes-raices-alquiler-de-verano',
                ),
            1889 =>
                array (
                    'en' => 'Recipes',
                    'es' => 'Recetario',
                    'slugEn' => 'recipes',
                    'slugEs' => 'recetario',
                ),
            1890 =>
                array (
                    'en' => 'Recording - Service',
                    'es' => 'Grabaciones',
                    'slugEn' => 'recording-service',
                    'slugEs' => 'grabaciones',
                ),
            1891 =>
                array (
                    'en' => 'Recreational - Centers',
                    'es' => 'Centros-Recreativos',
                    'slugEn' => 'recreational-centers',
                    'slugEs' => 'centros-recreativos',
                ),
            1892 =>
                array (
                    'en' => 'Recruitment-Military Service',
                    'es' => 'Reclutamiento-Servicio Militar',
                    'slugEn' => 'recruitment-military-service',
                    'slugEs' => 'reclutamiento-servicio-militar',
                ),
            1893 =>
                array (
                    'en' => 'Recycling',
                    'es' => 'Reciclaje',
                    'slugEn' => 'recycling',
                    'slugEs' => 'reciclaje',
                ),
            1894 =>
                array (
                    'en' => 'Recycling of Metals',
                    'es' => 'Reciclaje de Metales',
                    'slugEn' => 'recycling-of-metals',
                    'slugEs' => 'reciclaje-de-metales',
                ),
            1895 =>
                array (
                    'en' => 'Refractory - Materials',
                    'es' => 'Refractarios-Materiales',
                    'slugEn' => 'refractory-materials',
                    'slugEs' => 'refractarios-materiales',
                ),
            1896 =>
                array (
                    'en' => 'Refrigerating Equipment - Commercial - Repair',
                    'es' => 'Refrigeración Comercial-Reparación',
                    'slugEn' => 'refrigerating-equipment-commercial-repair',
                    'slugEs' => 'refrigeracion-comercial-reparacion',
                ),
            1897 =>
                array (
                    'en' => 'Refrigeration',
                    'es' => 'Refrigeracion',
                    'slugEn' => 'refrigeration',
                    'slugEs' => 'refrigeracion',
                ),
            1898 =>
                array (
                    'en' => 'Refrigeration - Commercial',
                    'es' => 'Refrigeracion Comercial',
                    'slugEn' => 'refrigeration-commercial',
                    'slugEs' => 'refrigeracion-comercial',
                ),
            1899 =>
                array (
                    'en' => 'Refrigeration - Domestic',
                    'es' => 'Refrigeración Doméstica',
                    'slugEn' => 'refrigeration-domestic',
                    'slugEs' => 'refrigeracion-domestica',
                ),
            1900 =>
                array (
                    'en' => 'Refrigeration - Domestic - Parts & Accessories',
                    'es' => 'Refrigeración Doméstica-Accesorios Y Piezas',
                    'slugEn' => 'refrigeration-domestic-parts-accessories',
                    'slugEs' => 'refrigeracion-domestica-accesorios-y-piezas',
                ),
            1901 =>
                array (
                    'en' => 'Refrigeration - Domestic - Repair',
                    'es' => 'Refrigeración Doméstica-Reparación',
                    'slugEn' => 'refrigeration-domestic-repair',
                    'slugEs' => 'refrigeracion-domestica-reparacion',
                ),
            1902 =>
                array (
                    'en' => 'Refrigerators',
                    'es' => 'Frigoríficos',
                    'slugEn' => 'refrigerators',
                    'slugEs' => 'frigorificos',
                ),
            1903 =>
                array (
                    'en' => 'Refrigetarion - Commercial - Parts & Accessories',
                    'es' => 'Refrigeración Comercial-Accesorios Y Piezas',
                    'slugEn' => 'refrigetarion-commercial-parts-accessories',
                    'slugEs' => 'refrigeracion-comercial-accesorios-y-piezas',
                ),
            1904 =>
                array (
                    'en' => 'Rehabilitation - Handicaped - Pensioned Workers',
                    'es' => 'Rehabilitación-Impedidos-Obreros Pensionados',
                    'slugEn' => 'rehabilitation-handicaped-pensioned-workers',
                    'slugEs' => 'rehabilitacion-impedidos-obreros-pensionados',
                ),
            1905 =>
                array (
                    'en' => 'Rehearsal Rooms',
                    'es' => 'Salones de Ensayos Alquiler',
                    'slugEn' => 'rehearsal-rooms',
                    'slugEs' => 'salones-de-ensayos-alquiler',
                ),
            1906 =>
                array (
                    'en' => 'Religious - Goods',
                    'es' => 'Religiosos-Efectos',
                    'slugEn' => 'religious-goods',
                    'slugEs' => 'religiosos-efectos',
                ),
            1907 =>
                array (
                    'en' => 'Religious Organizations',
                    'es' => 'Religiosas-Organizaciones',
                    'slugEn' => 'religious-organizations',
                    'slugEs' => 'religiosas-organizaciones',
                ),
            1908 =>
                array (
                    'en' => 'Remodeling - Construction',
                    'es' => 'Remodelación - Construcción',
                    'slugEn' => 'remodeling-construction',
                    'slugEs' => 'remodelacion-construccion',
                ),
            1909 =>
                array (
                    'en' => 'Remodeling - Construction - court',
                    'es' => 'Remodelación - Construcción - Cancha',
                    'slugEn' => 'remodeling-construction-court',
                    'slugEs' => 'remodelacion-construccion-cancha',
                ),
            1910 =>
                array (
                    'en' => 'Remodeling - Interior',
                    'es' => 'Remodelación - Interiores',
                    'slugEn' => 'remodeling-interior',
                    'slugEs' => 'remodelacion-interiores',
                ),
            1911 =>
                array (
                    'en' => 'Renal Center',
                    'es' => 'Centro Renal',
                    'slugEn' => 'renal-center',
                    'slugEs' => 'centro-renal',
                ),
            1912 =>
                array (
                    'en' => 'Renal Centers',
                    'es' => 'Centros Renales',
                    'slugEn' => 'renal-centers',
                    'slugEs' => 'centros-renales',
                ),
            1913 =>
                array (
                    'en' => 'Renewable Energy',
                    'es' => 'Energía Renovable',
                    'slugEn' => 'renewable-energy',
                    'slugEs' => 'energia-renovable',
                ),
            1914 =>
                array (
                    'en' => 'Renewable Energy - Green Projects',
                    'es' => 'Energía Renovable - Proyectos Verdes',
                    'slugEn' => 'renewable-energy-green-projects',
                    'slugEs' => 'energia-renovable-proyectos-verdes',
                ),
            1915 =>
                array (
                    'en' => 'Rent - Diving Equipment',
                    'es' => 'Alquiler - Equipos de Buceo',
                    'slugEn' => 'rent-diving-equipment',
                    'slugEs' => 'alquiler-equipos-de-buceo',
                ),
            1916 =>
                array (
                    'en' => 'Renting',
                    'es' => 'Alquiler',
                    'slugEn' => 'renting',
                    'slugEs' => 'alquiler',
                ),
            1917 =>
                array (
                    'en' => 'Repair - Doors trucks',
                    'es' => 'Reparación - Puertas camiones',
                    'slugEn' => 'repair-doors-trucks',
                    'slugEs' => 'reparacion-puertas-camiones',
                ),
            1918 =>
                array (
                    'en' => 'Repair Cell Phones',
                    'es' => 'Reparación de Celulares',
                    'slugEn' => 'repair-cell-phones',
                    'slugEs' => 'reparacion-de-celulares',
                ),
            1919 =>
                array (
                    'en' => 'Repair Shops',
                    'es' => 'Talleres de Reparación',
                    'slugEn' => 'repair-shops',
                    'slugEs' => 'talleres-de-reparacion',
                ),
            1920 =>
                array (
                    'en' => 'Repair Shops - Equipment & Supplies',
                    'es' => 'Garajes - Efectos Y Equipo',
                    'slugEn' => 'repair-shops-equipment-supplies',
                    'slugEs' => 'garajes-efectos-y-equipo',
                ),
            1921 =>
                array (
                    'en' => 'Repair Tanks',
                    'es' => 'Reparación de Cisternas',
                    'slugEn' => 'repair-tanks',
                    'slugEs' => 'reparacion-de-cisternas',
                ),
            1922 =>
                array (
                    'en' => 'Representative-Wood Factory',
                    'es' => 'Representantes-Fabricas Maderas',
                    'slugEn' => 'representative-wood-factory',
                    'slugEs' => 'representantes-fabricas-maderas',
                ),
            1923 =>
                array (
                    'en' => 'Rescue-Accessories and Equipment',
                    'es' => 'Rescate-Efectos Y Equipo',
                    'slugEn' => 'rescue-accessories-and-equipment',
                    'slugEs' => 'rescate-efectos-y-equipo',
                ),
            1924 =>
                array (
                    'en' => 'Residential Electrician',
                    'es' => 'Electricista Residencial',
                    'slugEn' => 'residential-electrician',
                    'slugEs' => 'electricista-residencial',
                ),
            1925 =>
                array (
                    'en' => 'Resorts',
                    'es' => 'Resorts (Complejos Vacacionales)',
                    'slugEn' => 'resorts',
                    'slugEs' => 'resorts-complejos-vacacionales',
                ),
            1926 =>
                array (
                    'en' => 'Restaurant - Grill',
                    'es' => 'Restaurantes - Tipos de Comida - Parrilla',
                    'slugEn' => 'restaurant-grill',
                    'slugEs' => 'restaurantes-tipos-de-comida-parrilla',
                ),
            1927 =>
                array (
                    'en' => 'Restaurants',
                    'es' => 'Restaurantes',
                    'slugEn' => 'restaurants',
                    'slugEs' => 'restaurantes',
                ),
            1928 =>
                array (
                    'en' => 'Restaurants - Arabian',
                    'es' => 'Restaurantes - Tipos de Comida - Arabe',
                    'slugEn' => 'restaurants-arabian',
                    'slugEs' => 'restaurantes-tipos-de-comida-arabe',
                ),
            1929 =>
                array (
                    'en' => 'Restaurants - Argentinian',
                    'es' => 'Restaurantes - Tipos de Comida - Argentina',
                    'slugEn' => 'restaurants-argentinian',
                    'slugEs' => 'restaurantes-tipos-de-comida-argentina',
                ),
            1930 =>
                array (
                    'en' => 'Restaurants - Asian',
                    'es' => 'Restaurantes - Comida Asiática',
                    'slugEn' => 'restaurants-asian',
                    'slugEs' => 'restaurantes-comida-asiatica',
                ),
            1931 =>
                array (
                    'en' => 'Restaurants - Caribbean',
                    'es' => 'Restaurantes - Tipos De Comida - Caribeña',
                    'slugEn' => 'restaurants-caribbean',
                    'slugEs' => 'restaurantes-tipos-de-comida-caribena',
                ),
            1932 =>
                array (
                    'en' => 'Restaurants - Chinese',
                    'es' => 'Restaurantes - Tipos De Comida - China',
                    'slugEn' => 'restaurants-chinese',
                    'slugEs' => 'restaurantes-tipos-de-comida-china',
                ),
            1933 =>
                array (
                    'en' => 'Restaurants - Creative Cuisine',
                    'es' => 'Restaurantes - Tipos de Comida - Creativa',
                    'slugEn' => 'restaurants-creative-cuisine',
                    'slugEs' => 'restaurantes-tipos-de-comida-creativa',
                ),
            1934 =>
                array (
                    'en' => 'Restaurants - Creole',
                    'es' => 'Restaurantes - Tipos De Comida - Criolla',
                    'slugEn' => 'restaurants-creole',
                    'slugEs' => 'restaurantes-tipos-de-comida-criolla',
                ),
            1935 =>
                array (
                    'en' => 'Restaurants - Cuban',
                    'es' => 'Restaurantes - Tipos de Comida - Cubana',
                    'slugEn' => 'restaurants-cuban',
                    'slugEs' => 'restaurantes-tipos-de-comida-cubana',
                ),
            1936 =>
                array (
                    'en' => 'Restaurants - Equipment',
                    'es' => 'Restaurantes - Efectos Y Equipo',
                    'slugEn' => 'restaurants-equipment',
                    'slugEs' => 'restaurantes-efectos-y-equipo',
                ),
            1937 =>
                array (
                    'en' => 'Restaurants - French',
                    'es' => 'Restaurantes - Tipos de Comida - Francesa',
                    'slugEn' => 'restaurants-french',
                    'slugEs' => 'restaurantes-tipos-de-comida-francesa',
                ),
            1938 =>
                array (
                    'en' => 'Restaurants - Indian',
                    'es' => 'Restaurantes- Comida India',
                    'slugEn' => 'restaurants-indian',
                    'slugEs' => 'restaurantes-comida-india',
                ),
            1939 =>
                array (
                    'en' => 'Restaurants - International',
                    'es' => 'Restaurantes - Tipos De Comida - Internacional',
                    'slugEn' => 'restaurants-international',
                    'slugEs' => 'restaurantes-tipos-de-comida-internacional',
                ),
            1940 =>
                array (
                    'en' => 'Restaurants - Italian',
                    'es' => 'Restaurantes - Tipos De Comida - Italiana',
                    'slugEn' => 'restaurants-italian',
                    'slugEs' => 'restaurantes-tipos-de-comida-italiana',
                ),
            1941 =>
                array (
                    'en' => 'Restaurants - Japanese',
                    'es' => 'Restaurantes - Japonés',
                    'slugEn' => 'restaurants-japanese',
                    'slugEs' => 'restaurantes-japones',
                ),
            1942 =>
                array (
                    'en' => 'Restaurants - Lebanese',
                    'es' => 'Restaurantes - Tipos de Comida - Libanesa',
                    'slugEn' => 'restaurants-lebanese',
                    'slugEs' => 'restaurantes-tipos-de-comida-libanesa',
                ),
            1943 =>
                array (
                    'en' => 'Restaurants - Meat',
                    'es' => 'Restaurantes - Tipos De Comida - Carnes',
                    'slugEn' => 'restaurants-meat',
                    'slugEs' => 'restaurantes-tipos-de-comida-carnes',
                ),
            1944 =>
                array (
                    'en' => 'Restaurants - Mexican',
                    'es' => 'Restaurantes - Tipos De Comida - Mejicana',
                    'slugEn' => 'restaurants-mexican',
                    'slugEs' => 'restaurantes-tipos-de-comida-mejicana',
                ),
            1945 =>
                array (
                    'en' => 'Restaurants - Oriental',
                    'es' => 'Restaurantes - Tipos de Comida - Oriental',
                    'slugEn' => 'restaurants-oriental',
                    'slugEs' => 'restaurantes-tipos-de-comida-oriental',
                ),
            1946 =>
                array (
                    'en' => 'Restaurants - Pacific Rim',
                    'es' => 'Restaurantes - Tipos de comida - Pacific Rim',
                    'slugEn' => 'restaurants-pacific-rim',
                    'slugEs' => 'restaurantes-tipos-de-comida-pacific-rim',
                ),
            1947 =>
                array (
                    'en' => 'Restaurants - Pizza',
                    'es' => 'Restaurantes - Tipos De Comida - Pizza',
                    'slugEn' => 'restaurants-pizza',
                    'slugEs' => 'restaurantes-tipos-de-comida-pizza',
                ),
            1948 =>
                array (
                    'en' => 'Restaurants - Puertorrican',
                    'es' => 'Restaurantes - Tipos De Comida - Puertorriqueña',
                    'slugEn' => 'restaurants-puertorrican',
                    'slugEs' => 'restaurantes-tipos-de-comida-puertorriquena',
                ),
            1949 =>
                array (
                    'en' => 'Restaurants - Roasted Pork',
                    'es' => 'Restaurantes - Tipos De Comida - Lechón Asado',
                    'slugEn' => 'restaurants-roasted-pork',
                    'slugEs' => 'restaurantes-tipos-de-comida-lechon-asado',
                ),
            1950 =>
                array (
                    'en' => 'Restaurants - Seafood',
                    'es' => 'Restaurantes - Tipos De Comida - Mariscos',
                    'slugEn' => 'restaurants-seafood',
                    'slugEs' => 'restaurantes-tipos-de-comida-mariscos',
                ),
            1951 =>
                array (
                    'en' => 'Restaurants - Spanish Cuisine',
                    'es' => 'Restaurantes - Tipo de Comida - Española',
                    'slugEn' => 'restaurants-spanish-cuisine',
                    'slugEs' => 'restaurantes-tipo-de-comida-espanola',
                ),
            1952 =>
                array (
                    'en' => 'Restaurants - Sushi',
                    'es' => 'Restaurantes - Tipos de Comida - Sushi',
                    'slugEn' => 'restaurants-sushi',
                    'slugEs' => 'restaurantes-tipos-de-comida-sushi',
                ),
            1953 =>
                array (
                    'en' => 'Restaurants - Types of Food - BBQ',
                    'es' => 'Restaurantes - Tipos de Comida - BBQ',
                    'slugEn' => 'restaurants-types-of-food-bbq',
                    'slugEs' => 'restaurantes-tipos-de-comida-bbq',
                ),
            1954 =>
                array (
                    'en' => 'Restaurants - Types of Food - Colombian',
                    'es' => 'Restaurantes - Tipos de Comida - Colombiana',
                    'slugEn' => 'restaurants-types-of-food-colombian',
                    'slugEs' => 'restaurantes-tipos-de-comida-colombiana',
                ),
            1955 =>
                array (
                    'en' => 'Restaurants - Types of Food - Tapas',
                    'es' => 'Restaurantes - Tipos de Comida - Tapas',
                    'slugEn' => 'restaurants-types-of-food-tapas',
                    'slugEs' => 'restaurantes-tipos-de-comida-tapas',
                ),
            1956 =>
                array (
                    'en' => 'Restaurants - Vegetarian',
                    'es' => 'Restaurantes - Tipos De Comida - Vegetariana',
                    'slugEn' => 'restaurants-vegetarian',
                    'slugEs' => 'restaurantes-tipos-de-comida-vegetariana',
                ),
            1957 =>
                array (
                    'en' => 'Restaurants - Venezuelan Food',
                    'es' => 'Restaurantes - Comida Venezolana',
                    'slugEn' => 'restaurants-venezuelan-food',
                    'slugEs' => 'restaurantes-comida-venezolana',
                ),
            1958 =>
                array (
                    'en' => 'Restaurants Suppliers',
                    'es' => 'Restaurantes-Suplidores',
                    'slugEn' => 'restaurants-suppliers',
                    'slugEs' => 'restaurantes-suplidores',
                ),
            1959 =>
                array (
                    'en' => 'Restoration of Artworks',
                    'es' => 'Restauración de Obras de Arte',
                    'slugEn' => 'restoration-of-artworks',
                    'slugEs' => 'restauracion-de-obras-de-arte',
                ),
            1960 =>
                array (
                    'en' => 'Resume Service',
                    'es' => 'Resumé-Servicios',
                    'slugEn' => 'resume-service',
                    'slugEs' => 'resume-servicios',
                ),
            1961 =>
                array (
                    'en' => 'Retail Shops',
                    'es' => 'Tiendas Al Detal',
                    'slugEn' => 'retail-shops',
                    'slugEs' => 'tiendas-al-detal',
                ),
            1962 =>
                array (
                    'en' => 'Retinologist',
                    'es' => 'Retinólogo',
                    'slugEn' => 'retinologist',
                    'slugEs' => 'retinologo',
                ),
            1963 =>
                array (
                    'en' => 'Retirement homes',
                    'es' => 'Egidas',
                    'slugEn' => 'retirement-homes',
                    'slugEs' => 'egidas',
                ),
            1964 =>
                array (
                    'en' => 'Retractable Curtains',
                    'es' => 'Cortinas Retractables',
                    'slugEn' => 'retractable-curtains',
                    'slugEs' => 'cortinas-retractables',
                ),
            1965 =>
                array (
                    'en' => 'Retreat Facilities',
                    'es' => 'Casas De Retiro',
                    'slugEn' => 'retreat-facilities',
                    'slugEs' => 'casas-de-retiro',
                ),
            1966 =>
                array (
                    'en' => 'Reviews',
                    'es' => 'Repasos',
                    'slugEn' => 'reviews',
                    'slugEs' => 'repasos',
                ),
            1967 =>
                array (
                    'en' => 'Rhinestones',
                    'es' => 'Pedrería',
                    'slugEn' => 'rhinestones',
                    'slugEs' => 'pedreria',
                ),
            1968 =>
                array (
                    'en' => 'Risks Insurance',
                    'es' => 'Seguridad Y Salud Ocupacional',
                    'slugEn' => 'risks-insurance',
                    'slugEs' => 'seguridad-y-salud-ocupacional',
                ),
            1969 =>
                array (
                    'en' => 'Road - Building - Contractors',
                    'es' => 'Carreteras-Construcción',
                    'slugEn' => 'road-building-contractors',
                    'slugEs' => 'carreteras-construccion',
                ),
            1970 =>
                array (
                    'en' => 'Roadside Assistance',
                    'es' => 'Asistencia en la Carretera',
                    'slugEn' => 'roadside-assistance',
                    'slugEs' => 'asistencia-en-la-carretera',
                ),
            1971 =>
                array (
                    'en' => 'Roadside Assistance - Changing Gums',
                    'es' => 'Asistencia en la Carretera - Cambio de Gomas',
                    'slugEn' => 'roadside-assistance-changing-gums',
                    'slugEs' => 'asistencia-en-la-carretera-cambio-de-gomas',
                ),
            1972 =>
                array (
                    'en' => 'Roadside Assistance - Charging Battery',
                    'es' => 'Asistencia en la Carretera - Carga de Bateria',
                    'slugEn' => 'roadside-assistance-charging-battery',
                    'slugEs' => 'asistencia-en-la-carretera-carga-de-bateria',
                ),
            1973 =>
                array (
                    'en' => 'Rolling Doors',
                    'es' => 'Rolling Doors',
                    'slugEn' => 'rolling-doors',
                    'slugEs' => 'rolling-doors',
                ),
            1974 =>
                array (
                    'en' => 'Roofers',
                    'es' => 'Techeros',
                    'slugEn' => 'roofers',
                    'slugEs' => 'techeros',
                ),
            1975 =>
                array (
                    'en' => 'Roofs - Aluminum',
                    'es' => 'Techos - Aluminio',
                    'slugEn' => 'roofs-aluminum',
                    'slugEs' => 'techos-aluminio',
                ),
            1976 =>
                array (
                    'en' => 'Roofs - Materials - Manufacture',
                    'es' => 'Techos-Fábricas Materiales',
                    'slugEn' => 'roofs-materials-manufacture',
                    'slugEs' => 'techos-fabricas-materiales',
                ),
            1977 =>
                array (
                    'en' => 'Roofs - Waterproofing - Materials',
                    'es' => 'Techos-Impermeabilizantes',
                    'slugEn' => 'roofs-waterproofing-materials',
                    'slugEs' => 'techos-impermeabilizantes',
                ),
            1978 =>
                array (
                    'en' => 'Roofs Consultants',
                    'es' => 'Techos Consultores',
                    'slugEn' => 'roofs-consultants',
                    'slugEs' => 'techos-consultores',
                ),
            1979 =>
                array (
                    'en' => 'Room - Lounge',
                    'es' => 'Salones - Lounge',
                    'slugEn' => 'room-lounge',
                    'slugEs' => 'salones-lounge',
                ),
            1980 =>
                array (
                    'en' => 'Safes & Vaults',
                    'es' => 'Cajas De Caudales',
                    'slugEn' => 'safes-vaults',
                    'slugEs' => 'cajas-de-caudales',
                ),
            1981 =>
                array (
                    'en' => 'Sale of Tools',
                    'es' => 'Venta de Herramientas',
                    'slugEn' => 'sale-of-tools',
                    'slugEs' => 'venta-de-herramientas',
                ),
            1982 =>
                array (
                    'en' => 'Sales Promotion',
                    'es' => 'Promoción De Ventas',
                    'slugEn' => 'sales-promotion',
                    'slugEs' => 'promocion-de-ventas',
                ),
            1983 =>
                array (
                    'en' => 'Salsa Lessons',
                    'es' => 'Clases de Salsa',
                    'slugEn' => 'salsa-lessons',
                    'slugEs' => 'clases-de-salsa',
                ),
            1984 =>
                array (
                    'en' => 'Salt',
                    'es' => 'Sal',
                    'slugEn' => 'salt',
                    'slugEs' => 'sal',
                ),
            1985 =>
                array (
                    'en' => 'Sand',
                    'es' => 'Arena',
                    'slugEn' => 'sand',
                    'slugEs' => 'arena',
                ),
            1986 =>
                array (
                    'en' => 'Sandblasting',
                    'es' => 'Sandblasting (Limpieza Con Arena A Presión)',
                    'slugEn' => 'sandblasting',
                    'slugEs' => 'sandblasting-limpieza-con-arena-a-presion',
                ),
            1987 =>
                array (
                    'en' => 'Satelite Systems',
                    'es' => 'Sistemas Satélite',
                    'slugEn' => 'satelite-systems',
                    'slugEs' => 'sistemas-satelite',
                ),
            1988 =>
                array (
                    'en' => 'Satellite',
                    'es' => 'Satélites',
                    'slugEn' => 'satellite',
                    'slugEs' => 'satelites',
                ),
            1989 =>
                array (
                    'en' => 'Sauna',
                    'es' => 'Sauna',
                    'slugEn' => 'sauna',
                    'slugEs' => 'sauna',
                ),
            1990 =>
                array (
                    'en' => 'Sausages',
                    'es' => 'Embutidos',
                    'slugEn' => 'sausages',
                    'slugEs' => 'embutidos',
                ),
            1991 =>
                array (
                    'en' => 'Sausages - Manufacture',
                    'es' => 'Embutidos-Fábricas',
                    'slugEn' => 'sausages-manufacture',
                    'slugEs' => 'embutidos-fabricas',
                ),
            1992 =>
                array (
                    'en' => 'Scaffolding',
                    'es' => 'Andamios',
                    'slugEn' => 'scaffolding',
                    'slugEs' => 'andamios',
                ),
            1993 =>
                array (
                    'en' => 'Scales',
                    'es' => 'Balanzas',
                    'slugEn' => 'scales',
                    'slugEs' => 'balanzas',
                ),
            1994 =>
                array (
                    'en' => 'Scalp- Treatment',
                    'es' => 'Cuero Cabelludo-Tratamientos',
                    'slugEn' => 'scalp-treatment',
                    'slugEs' => 'cuero-cabelludo-tratamientos',
                ),
            1995 =>
                array (
                    'en' => 'School - Photography',
                    'es' => 'Escuela-Fotografîa',
                    'slugEn' => 'school-photography',
                    'slugEs' => 'escuela-fotografia',
                ),
            1996 =>
                array (
                    'en' => 'School bus',
                    'es' => 'Guagua escolar',
                    'slugEn' => 'school-bus',
                    'slugEs' => 'guagua-escolar',
                ),
            1997 =>
                array (
                    'en' => 'School Desks',
                    'es' => 'Pupitres',
                    'slugEn' => 'school-desks',
                    'slugEs' => 'pupitres',
                ),
            1998 =>
                array (
                    'en' => 'School Supplies',
                    'es' => 'Materiales Escolares',
                    'slugEn' => 'school-supplies',
                    'slugEs' => 'materiales-escolares',
                ),
            1999 =>
                array (
                    'en' => 'School Supplies - Equipment',
                    'es' => 'Escolares Efectos - Equipo',
                    'slugEn' => 'school-supplies-equipment',
                    'slugEs' => 'escolares-efectos-equipo',
                ),
            2000 =>
                array (
                    'en' => 'School Supply',
                    'es' => 'Efectos Escolares',
                    'slugEn' => 'school-supply',
                    'slugEs' => 'efectos-escolares',
                ),
            2001 =>
                array (
                    'en' => 'School T-Shirts',
                    'es' => 'Camisetas Escolares',
                    'slugEn' => 'school-t-shirts',
                    'slugEs' => 'camisetas-escolares',
                ),
            2002 =>
                array (
                    'en' => 'Schools',
                    'es' => 'Colegios',
                    'slugEn' => 'schools',
                    'slugEs' => 'colegios',
                ),
            2003 =>
                array (
                    'en' => 'Schools -  Florists',
                    'es' => 'Colegios- Floristerías',
                    'slugEn' => 'schools-florists',
                    'slugEs' => 'colegios-floristerias',
                ),
            2004 =>
                array (
                    'en' => 'Schools - Academies',
                    'es' => 'Colegios - Academias',
                    'slugEn' => 'schools-academies',
                    'slugEs' => 'colegios-academias',
                ),
            2005 =>
                array (
                    'en' => 'Schools - Accredited',
                    'es' => 'Colegios - Acreditados',
                    'slugEn' => 'schools-accredited',
                    'slugEs' => 'colegios-acreditados',
                ),
            2006 =>
                array (
                    'en' => 'Schools - Barbershops',
                    'es' => 'Escuelas - Estética',
                    'slugEn' => 'schools-barbershops',
                    'slugEs' => 'escuelas-estetica',
                ),
            2007 =>
                array (
                    'en' => 'Schools - Catholic',
                    'es' => 'Colegios - Católicos',
                    'slugEn' => 'schools-catholic',
                    'slugEs' => 'colegios-catolicos',
                ),
            2008 =>
                array (
                    'en' => 'Schools - Christian',
                    'es' => 'Colegios - Cristianos',
                    'slugEn' => 'schools-christian',
                    'slugEs' => 'colegios-cristianos',
                ),
            2009 =>
                array (
                    'en' => 'Schools - Equipments & Supplies - Wholesale',
                    'es' => 'Escuelas - Efectos Y Equipo-Al Por Mayor',
                    'slugEn' => 'schools-equipments-supplies-wholesale',
                    'slugEs' => 'escuelas-efectos-y-equipo-al-por-mayor',
                ),
            2010 =>
                array (
                    'en' => 'Schools - Individualized Teaching',
                    'es' => 'Escuelas - Enseñanza Individualizada',
                    'slugEn' => 'schools-individualized-teaching',
                    'slugEs' => 'escuelas-ensenanza-individualizada',
                ),
            2011 =>
                array (
                    'en' => 'Schools - Nursing',
                    'es' => 'Escuelas - Enfermería',
                    'slugEn' => 'schools-nursing',
                    'slugEs' => 'escuelas-enfermeria',
                ),
            2012 =>
                array (
                    'en' => 'Schools - Online Courses',
                    'es' => 'Escuelas - Cursos en Línea',
                    'slugEn' => 'schools-online-courses',
                    'slugEs' => 'escuelas-cursos-en-linea',
                ),
            2013 =>
                array (
                    'en' => 'Schools - Real Estate',
                    'es' => 'Escuelas - Bienes Raíces',
                    'slugEn' => 'schools-real-estate',
                    'slugEs' => 'escuelas-bienes-raices',
                ),
            2014 =>
                array (
                    'en' => 'Schools - Special Education',
                    'es' => 'Escuelas - Educación Especial',
                    'slugEn' => 'schools-special-education',
                    'slugEs' => 'escuelas-educacion-especial',
                ),
            2015 =>
                array (
                    'en' => 'Schools - Supervised Studies',
                    'es' => 'Escuelas - Estudios Supervisados',
                    'slugEn' => 'schools-supervised-studies',
                    'slugEs' => 'escuelas-estudios-supervisados',
                ),
            2016 =>
                array (
                    'en' => 'Schools - Tennis',
                    'es' => 'Escuelas - Tennis - Schools',
                    'slugEn' => 'schools-tennis',
                    'slugEs' => 'escuelas-tennis-schools',
                ),
            2017 =>
                array (
                    'en' => 'Schools - Yoga',
                    'es' => 'Escuelas - Yoga',
                    'slugEn' => 'schools-yoga',
                    'slugEs' => 'escuelas-yoga',
                ),
            2018 =>
                array (
                    'en' => 'Schools- Aviation',
                    'es' => 'Escuelas - Aviación',
                    'slugEn' => 'schools-aviation',
                    'slugEs' => 'escuelas-aviacion',
                ),
            2019 =>
                array (
                    'en' => 'Schools Equipments & Supplies',
                    'es' => 'Escuelas Efectos Y Equipo',
                    'slugEn' => 'schools-equipments-supplies',
                    'slugEs' => 'escuelas-efectos-y-equipo',
                ),
            2020 =>
                array (
                    'en' => 'Schools/drivers',
                    'es' => 'Escuelas/choferes',
                    'slugEn' => 'schools-drivers',
                    'slugEs' => 'escuelas-choferes',
                ),
            2021 =>
                array (
                    'en' => 'Schools-Art',
                    'es' => 'Escuelas - Arte',
                    'slugEn' => 'schools-art',
                    'slugEs' => 'escuelas-arte',
                ),
            2022 =>
                array (
                    'en' => 'Schools-Beautician',
                    'es' => 'Escuelas - Belleza',
                    'slugEn' => 'schools-beautician',
                    'slugEs' => 'escuelas-belleza',
                ),
            2023 =>
                array (
                    'en' => 'Schools-Business & Secretarial',
                    'es' => 'Escuelas - Comerciales Y Secretariales',
                    'slugEn' => 'schools-business-secretarial',
                    'slugEs' => 'escuelas-comerciales-y-secretariales',
                ),
            2024 =>
                array (
                    'en' => 'Schools-Cooking',
                    'es' => 'Escuelas - Cocina',
                    'slugEn' => 'schools-cooking',
                    'slugEs' => 'escuelas-cocina',
                ),
            2025 =>
                array (
                    'en' => 'Schools-Dance',
                    'es' => 'Escuelas - Baile',
                    'slugEn' => 'schools-dance',
                    'slugEs' => 'escuelas-baile',
                ),
            2026 =>
                array (
                    'en' => 'Schools-Dressmaking',
                    'es' => 'Escuelas - Costura',
                    'slugEn' => 'schools-dressmaking',
                    'slugEs' => 'escuelas-costura',
                ),
            2027 =>
                array (
                    'en' => 'Schools-Driving',
                    'es' => 'Escuelas - Conducir',
                    'slugEn' => 'schools-driving',
                    'slugEs' => 'escuelas-conducir',
                ),
            2028 =>
                array (
                    'en' => 'Schools-Interior Decoration',
                    'es' => 'Escuelas - Decoración',
                    'slugEn' => 'schools-interior-decoration',
                    'slugEs' => 'escuelas-decoracion',
                ),
            2029 =>
                array (
                    'en' => 'Schools-Languages',
                    'es' => 'Escuelas - Idiomas',
                    'slugEn' => 'schools-languages',
                    'slugEs' => 'escuelas-idiomas',
                ),
            2030 =>
                array (
                    'en' => 'Schools-Modeling',
                    'es' => 'Escuelas - Modelaje',
                    'slugEn' => 'schools-modeling',
                    'slugEs' => 'escuelas-modelaje',
                ),
            2031 =>
                array (
                    'en' => 'Schools-Music',
                    'es' => 'Escuelas - Música',
                    'slugEn' => 'schools-music',
                    'slugEs' => 'escuelas-musica',
                ),
            2032 =>
                array (
                    'en' => 'Scientific - Apparatus',
                    'es' => 'Instrumentos-Científicos',
                    'slugEn' => 'scientific-apparatus',
                    'slugEs' => 'instrumentos-cientificos',
                ),
            2033 =>
                array (
                    'en' => 'Scientology - Church',
                    'es' => 'Iglesias - Scientology',
                    'slugEn' => 'scientology-church',
                    'slugEs' => 'iglesias-scientology',
                ),
            2034 =>
                array (
                    'en' => 'Scool Transportation',
                    'es' => 'Transporte Escolar',
                    'slugEn' => 'scool-transportation',
                    'slugEs' => 'transporte-escolar',
                ),
            2035 =>
                array (
                    'en' => 'Scrapbooks',
                    'es' => 'Scrapbooks (Albumes De Recuerdos)',
                    'slugEn' => 'scrapbooks',
                    'slugEs' => 'scrapbooks-albumes-de-recuerdos',
                ),
            2036 =>
                array (
                    'en' => 'Screen Doors',
                    'es' => 'Puertas de Screen',
                    'slugEn' => 'screen-doors',
                    'slugEs' => 'puertas-de-screen',
                ),
            2037 =>
                array (
                    'en' => 'Screen Windows',
                    'es' => 'Ventanas de Screen',
                    'slugEn' => 'screen-windows',
                    'slugEs' => 'ventanas-de-screen',
                ),
            2038 =>
                array (
                    'en' => 'Screens (Metallic Mesh or Plastic)',
                    'es' => 'Screens (Malla Metálica o de Plástico)',
                    'slugEn' => 'screens-metallic-mesh-or-plastic',
                    'slugEs' => 'screens-malla-metalica-o-de-plastico',
                ),
            2039 =>
                array (
                    'en' => 'Sculptors & Sculptures',
                    'es' => 'Escultores Y Esculturas',
                    'slugEn' => 'sculptors-sculptures',
                    'slugEs' => 'escultores-y-esculturas',
                ),
            2040 =>
                array (
                    'en' => 'Sealing roofs',
                    'es' => 'Sellado de techos',
                    'slugEn' => 'sealing-roofs',
                    'slugEs' => 'sellado-de-techos',
                ),
            2041 =>
                array (
                    'en' => 'Second hand items',
                    'es' => 'Artículos de Segunda Mano',
                    'slugEn' => 'second-hand-items',
                    'slugEs' => 'articulos-de-segunda-mano',
                ),
            2042 =>
                array (
                    'en' => 'Second Hand Store',
                    'es' => 'Tienda Artículos Segundas Manos',
                    'slugEn' => 'second-hand-store',
                    'slugEs' => 'tienda-articulos-segundas-manos',
                ),
            2043 =>
                array (
                    'en' => 'Secretarial Services',
                    'es' => 'Secretariales-Servicios',
                    'slugEn' => 'secretarial-services',
                    'slugEs' => 'secretariales-servicios',
                ),
            2044 =>
                array (
                    'en' => 'Security',
                    'es' => 'Seguridad',
                    'slugEn' => 'security',
                    'slugEs' => 'seguridad',
                ),
            2045 =>
                array (
                    'en' => 'Security - Access Control',
                    'es' => 'Seguridad - Control de Acceso',
                    'slugEn' => 'security-access-control',
                    'slugEs' => 'seguridad-control-de-acceso',
                ),
            2046 =>
                array (
                    'en' => 'Security - Equipment & Supplies',
                    'es' => 'Seguridad-Efectos Y Equipo',
                    'slugEn' => 'security-equipment-supplies',
                    'slugEs' => 'seguridad-efectos-y-equipo',
                ),
            2047 =>
                array (
                    'en' => 'Security - Fences',
                    'es' => 'Seguridad-Vallas',
                    'slugEn' => 'security-fences',
                    'slugEs' => 'seguridad-vallas',
                ),
            2048 =>
                array (
                    'en' => 'Security - Industrial - Consultants',
                    'es' => 'Seguridad Industrial-Consultores',
                    'slugEn' => 'security-industrial-consultants',
                    'slugEs' => 'seguridad-industrial-consultores',
                ),
            2049 =>
                array (
                    'en' => 'Security Agents and Companies',
                    'es' => 'Seguridad Agentes y Compañias',
                    'slugEn' => 'security-agents-and-companies',
                    'slugEs' => 'seguridad-agentes-y-companias',
                ),
            2050 =>
                array (
                    'en' => 'Security Cameras',
                    'es' => 'Seguridad Camaras',
                    'slugEn' => 'security-cameras',
                    'slugEs' => 'seguridad-camaras',
                ),
            2051 =>
                array (
                    'en' => 'Security Guard - Schools',
                    'es' => 'Guardias de Seguridad - Escuelas',
                    'slugEn' => 'security-guard-schools',
                    'slugEs' => 'guardias-de-seguridad-escuelas',
                ),
            2052 =>
                array (
                    'en' => 'Security Pool',
                    'es' => 'Seguridad para Piscina',
                    'slugEn' => 'security-pool',
                    'slugEs' => 'seguridad-para-piscina',
                ),
            2053 =>
                array (
                    'en' => 'Security Services',
                    'es' => 'Seguridad Servicios',
                    'slugEn' => 'security-services',
                    'slugEs' => 'seguridad-servicios',
                ),
            2054 =>
                array (
                    'en' => 'Security Windows',
                    'es' => 'Ventanas de Seguridad',
                    'slugEn' => 'security-windows',
                    'slugEs' => 'ventanas-de-seguridad',
                ),
            2055 =>
                array (
                    'en' => 'Seesaw',
                    'es' => 'Playground Sube y Baja',
                    'slugEn' => 'seesaw',
                    'slugEs' => 'playground-sube-y-baja',
                ),
            2056 =>
                array (
                    'en' => 'Self Defense',
                    'es' => 'Defensa Personal',
                    'slugEn' => 'self-defense',
                    'slugEs' => 'defensa-personal',
                ),
            2057 =>
                array (
                    'en' => 'Septic Tanks',
                    'es' => 'Tanques-Acero',
                    'slugEn' => 'septic-tanks',
                    'slugEs' => 'tanques-acero',
                ),
            2058 =>
                array (
                    'en' => 'Serenades',
                    'es' => 'Serenatas',
                    'slugEn' => 'serenades',
                    'slugEs' => 'serenatas',
                ),
            2059 =>
                array (
                    'en' => 'Service Station - Gasoline',
                    'es' => 'Estaciones De Servicio - Gasolina',
                    'slugEn' => 'service-station-gasoline',
                    'slugEs' => 'estaciones-de-servicio-gasolina',
                ),
            2060 =>
                array (
                    'en' => 'Service Stations',
                    'es' => 'Estaciones De Servicio',
                    'slugEn' => 'service-stations',
                    'slugEs' => 'estaciones-de-servicio',
                ),
            2061 =>
                array (
                    'en' => 'Service Stations - Maintenance',
                    'es' => 'Estaciones De Servicio-Mantenimiento',
                    'slugEn' => 'service-stations-maintenance',
                    'slugEs' => 'estaciones-de-servicio-mantenimiento',
                ),
            2062 =>
                array (
                    'en' => 'Services - Food - Administration',
                    'es' => 'Servicios - Alimentos - Administración',
                    'slugEn' => 'services-food-administration',
                    'slugEs' => 'servicios-alimentos-administracion',
                ),
            2063 =>
                array (
                    'en' => 'Services-Telephone Calls',
                    'es' => 'Telemercadeo-Servicios',
                    'slugEn' => 'services-telephone-calls',
                    'slugEs' => 'telemercadeo-servicios',
                ),
            2064 =>
                array (
                    'en' => 'Sewage - Treatment',
                    'es' => 'Aguas Negras-Tratamiento',
                    'slugEn' => 'sewage-treatment',
                    'slugEs' => 'aguas-negras-tratamiento',
                ),
            2065 =>
                array (
                    'en' => 'Sewing Alterations',
                    'es' => 'Costura Alteraciones',
                    'slugEn' => 'sewing-alterations',
                    'slugEs' => 'costura-alteraciones',
                ),
            2066 =>
                array (
                    'en' => 'Sewing Machines',
                    'es' => 'Máquinas De Coser',
                    'slugEn' => 'sewing-machines',
                    'slugEs' => 'maquinas-de-coser',
                ),
            2067 =>
                array (
                    'en' => 'Sewing Machines - Repair',
                    'es' => 'Máquinas De Coser-Reparación',
                    'slugEn' => 'sewing-machines-repair',
                    'slugEs' => 'maquinas-de-coser-reparacion',
                ),
            2068 =>
                array (
                    'en' => 'Sex Therapy',
                    'es' => 'Sexology-Therapy',
                    'slugEn' => 'sex-therapy',
                    'slugEs' => 'sexology-therapy',
                ),
            2069 =>
                array (
                    'en' => 'Shellac Nails',
                    'es' => 'Uñas Shellac',
                    'slugEn' => 'shellac-nails',
                    'slugEs' => 'unas-shellac',
                ),
            2070 =>
                array (
                    'en' => 'Shelving',
                    'es' => 'Anaqueles',
                    'slugEn' => 'shelving',
                    'slugEs' => 'anaqueles',
                ),
            2071 =>
                array (
                    'en' => 'Ship Chandlers',
                    'es' => 'Barcos-Suplidores',
                    'slugEn' => 'ship-chandlers',
                    'slugEs' => 'barcos-suplidores',
                ),
            2072 =>
                array (
                    'en' => 'Ship repair',
                    'es' => 'Reparacion Buques',
                    'slugEn' => 'ship-repair',
                    'slugEs' => 'reparacion-buques',
                ),
            2073 =>
                array (
                    'en' => 'Shipping',
                    'es' => 'Barcos-Compañías Y Agentes',
                    'slugEn' => 'shipping',
                    'slugEs' => 'barcos-companias-y-agentes',
                ),
            2074 =>
                array (
                    'en' => 'Shirts - T-Shirts',
                    'es' => 'Camisetas-Al Por Mayor',
                    'slugEn' => 'shirts-t-shirts',
                    'slugEs' => 'camisetas-al-por-mayor',
                ),
            2075 =>
                array (
                    'en' => 'Shoe Factories',
                    'es' => 'Calzado-Fábricas',
                    'slugEn' => 'shoe-factories',
                    'slugEs' => 'calzado-fabricas',
                ),
            2076 =>
                array (
                    'en' => 'Shoes - Repair',
                    'es' => 'Zapaterías',
                    'slugEn' => 'shoes-repair',
                    'slugEs' => 'zapaterias',
                ),
            2077 =>
                array (
                    'en' => 'Shoes - Safety',
                    'es' => 'Zapatos-Seguridad',
                    'slugEn' => 'shoes-safety',
                    'slugEs' => 'zapatos-seguridad',
                ),
            2078 =>
                array (
                    'en' => 'Shoes for Dancing',
                    'es' => 'Zapatos de Baile',
                    'slugEn' => 'shoes-for-dancing',
                    'slugEs' => 'zapatos-de-baile',
                ),
            2079 =>
                array (
                    'en' => 'Shopping Centers - Management',
                    'es' => 'Centros Comerciales-Administración',
                    'slugEn' => 'shopping-centers-management',
                    'slugEs' => 'centros-comerciales-administracion',
                ),
            2080 =>
                array (
                    'en' => 'Shops Equipment',
                    'es' => 'Equipo-Tiendas',
                    'slugEn' => 'shops-equipment',
                    'slugEs' => 'equipo-tiendas',
                ),
            2081 =>
                array (
                    'en' => 'Short Programs',
                    'es' => 'Programas Cortos',
                    'slugEn' => 'short-programs',
                    'slugEs' => 'programas-cortos',
                ),
            2082 =>
                array (
                    'en' => 'Shower Heaters',
                    'es' => 'Calentadores-Ducha',
                    'slugEn' => 'shower-heaters',
                    'slugEs' => 'calentadores-ducha',
                ),
            2083 =>
                array (
                    'en' => 'Shows - Concerts',
                    'es' => 'Espectáculos-Conciertos',
                    'slugEn' => 'shows-concerts',
                    'slugEs' => 'espectaculos-conciertos',
                ),
            2084 =>
                array (
                    'en' => 'Shutters',
                    'es' => 'Tormenteras',
                    'slugEn' => 'shutters',
                    'slugEs' => 'tormenteras',
                ),
            2085 =>
                array (
                    'en' => 'Signs',
                    'es' => 'Rótulos',
                    'slugEn' => 'signs',
                    'slugEs' => 'rotulos',
                ),
            2086 =>
                array (
                    'en' => 'Signs - Equipment & Supplies - Wholesale',
                    'es' => 'Rótulos-Artículos-Al Por Mayor',
                    'slugEn' => 'signs-equipment-supplies-wholesale',
                    'slugEs' => 'rotulos-articulos-al-por-mayor',
                ),
            2087 =>
                array (
                    'en' => 'Signs - Neon',
                    'es' => 'Rótulos-Neón',
                    'slugEn' => 'signs-neon',
                    'slugEs' => 'rotulos-neon',
                ),
            2088 =>
                array (
                    'en' => 'Signs - Street & Road',
                    'es' => 'Rótulos-Carreteras Y Calles',
                    'slugEn' => 'signs-street-road',
                    'slugEs' => 'rotulos-carreteras-y-calles',
                ),
            2089 =>
                array (
                    'en' => 'Silversmith',
                    'es' => 'Platerías',
                    'slugEn' => 'silversmith',
                    'slugEs' => 'platerias',
                ),
            2090 =>
                array (
                    'en' => 'Singing Lessons',
                    'es' => 'Clases de Canto',
                    'slugEn' => 'singing-lessons',
                    'slugEs' => 'clases-de-canto',
                ),
            2091 =>
                array (
                    'en' => 'Skateboards',
                    'es' => 'Patinetas',
                    'slugEn' => 'skateboards',
                    'slugEs' => 'patinetas',
                ),
            2092 =>
                array (
                    'en' => 'Skylights-Glass Ceiling',
                    'es' => 'Tragaluces-Techos de Cristal',
                    'slugEn' => 'skylights-glass-ceiling',
                    'slugEs' => 'tragaluces-techos-de-cristal',
                ),
            2093 =>
                array (
                    'en' => 'Slate',
                    'es' => 'Pizarras',
                    'slugEn' => 'slate',
                    'slugEs' => 'pizarras',
                ),
            2094 =>
                array (
                    'en' => 'Slaughterhouses',
                    'es' => 'Mataderos',
                    'slugEn' => 'slaughterhouses',
                    'slugEs' => 'mataderos',
                ),
            2095 =>
                array (
                    'en' => 'Sleep Apnea',
                    'es' => 'Médicos Especialistas - Apnea del Sueño',
                    'slugEn' => 'sleep-apnea',
                    'slugEs' => 'medicos-especialistas-apnea-del-sueno',
                ),
            2096 =>
                array (
                    'en' => 'Sleep Disorders',
                    'es' => 'Médicos - Desórdenes de Sueño',
                    'slugEn' => 'sleep-disorders',
                    'slugEs' => 'medicos-desordenes-de-sueno',
                ),
            2097 =>
                array (
                    'en' => 'Sleep Testing',
                    'es' => 'Pruebas del Sueño',
                    'slugEn' => 'sleep-testing',
                    'slugEs' => 'pruebas-del-sueno',
                ),
            2098 =>
                array (
                    'en' => 'Sleeves',
                    'es' => 'Mangas',
                    'slugEn' => 'sleeves',
                    'slugEs' => 'mangas',
                ),
            2099 =>
                array (
                    'en' => 'Smart Homes',
                    'es' => 'Casas Inteligentes',
                    'slugEn' => 'smart-homes',
                    'slugEs' => 'casas-inteligentes',
                ),
            2100 =>
                array (
                    'en' => 'Smart Keys',
                    'es' => 'Llaves inteligentes',
                    'slugEn' => 'smart-keys',
                    'slugEs' => 'llaves-inteligentes',
                ),
            2101 =>
                array (
                    'en' => 'Smoke Shop',
                    'es' => 'Tabaquería-Tabaco',
                    'slugEn' => 'smoke-shop',
                    'slugEs' => 'tabaqueria-tabaco',
                ),
            2102 =>
                array (
                    'en' => 'Snacks',
                    'es' => 'Snacks',
                    'slugEn' => 'snacks',
                    'slugEs' => 'snacks',
                ),
            2103 =>
                array (
                    'en' => 'Snorkeling',
                    'es' => 'Bucear',
                    'slugEn' => 'snorkeling',
                    'slugEs' => 'bucear',
                ),
            2104 =>
                array (
                    'en' => 'Snuff Distributor',
                    'es' => 'Distribuidor de Tabaco',
                    'slugEn' => 'snuff-distributor',
                    'slugEs' => 'distribuidor-de-tabaco',
                ),
            2105 =>
                array (
                    'en' => 'Snuff-Distributors',
                    'es' => 'Tabaco-Distribuidores',
                    'slugEn' => 'snuff-distributors',
                    'slugEs' => 'tabaco-distribuidores',
                ),
            2106 =>
                array (
                    'en' => 'Social Security - Claims',
                    'es' => 'Seguro Social - Reclamaciones',
                    'slugEn' => 'social-security-claims',
                    'slugEs' => 'seguro-social-reclamaciones',
                ),
            2107 =>
                array (
                    'en' => 'Social Services - Organizations',
                    'es' => 'Servicios - Sociales',
                    'slugEn' => 'social-services-organizations',
                    'slugEs' => 'servicios-sociales',
                ),
            2108 =>
                array (
                    'en' => 'Sofa Beds',
                    'es' => 'Sofás Cama',
                    'slugEn' => 'sofa-beds',
                    'slugEs' => 'sofas-cama',
                ),
            2109 =>
                array (
                    'en' => 'Soft Drinks',
                    'es' => 'Refrescos',
                    'slugEn' => 'soft-drinks',
                    'slugEs' => 'refrescos',
                ),
            2110 =>
                array (
                    'en' => 'Soft Drinks - Vending Machines',
                    'es' => 'Refrescos-Máquinas',
                    'slugEn' => 'soft-drinks-vending-machines',
                    'slugEs' => 'refrescos-maquinas',
                ),
            2111 =>
                array (
                    'en' => 'Soil - Testing',
                    'es' => 'Suelos-Investigaciones',
                    'slugEn' => 'soil-testing',
                    'slugEs' => 'suelos-investigaciones',
                ),
            2112 =>
                array (
                    'en' => 'Solar Energy - Equipment & Supplies',
                    'es' => 'Energía-Solar-Efectos Y Equipo',
                    'slugEn' => 'solar-energy-equipment-supplies',
                    'slugEs' => 'energia-solar-efectos-y-equipo',
                ),
            2113 =>
                array (
                    'en' => 'Solar Energy Air Conditioner',
                    'es' => 'Aires Acondicionados con Energía Solar',
                    'slugEn' => 'solar-energy-air-conditioner',
                    'slugEs' => 'aires-acondicionados-con-energia-solar',
                ),
            2114 =>
                array (
                    'en' => 'Solar Heaters',
                    'es' => 'Calentadores Solares',
                    'slugEn' => 'solar-heaters',
                    'slugEs' => 'calentadores-solares',
                ),
            2115 =>
                array (
                    'en' => 'Sonograms',
                    'es' => 'Sonogramas',
                    'slugEn' => 'sonograms',
                    'slugEs' => 'sonogramas',
                ),
            2116 =>
                array (
                    'en' => 'Sound Buses',
                    'es' => 'Guaguas de Sonido',
                    'slugEn' => 'sound-buses',
                    'slugEs' => 'guaguas-de-sonido',
                ),
            2117 =>
                array (
                    'en' => 'Sound Systems & Services',
                    'es' => 'Sonido Sistemas Y Servicios',
                    'slugEn' => 'sound-systems-services',
                    'slugEs' => 'sonido-sistemas-y-servicios',
                ),
            2118 =>
                array (
                    'en' => 'Souvenirs',
                    'es' => 'Turistas-Novedades',
                    'slugEn' => 'souvenirs',
                    'slugEs' => 'turistas-novedades',
                ),
            2119 =>
                array (
                    'en' => 'Spanish Dance Classes',
                    'es' => 'Clases Danza Española',
                    'slugEn' => 'spanish-dance-classes',
                    'slugEs' => 'clases-danza-espanola',
                ),
            2120 =>
                array (
                    'en' => 'Spanish Food Restaurants',
                    'es' => 'Restaurantes Comida Española',
                    'slugEn' => 'spanish-food-restaurants',
                    'slugEs' => 'restaurantes-comida-espanola',
                ),
            2121 =>
                array (
                    'en' => 'Spas',
                    'es' => 'Spas',
                    'slugEn' => 'spas',
                    'slugEs' => 'spas',
                ),
            2122 =>
                array (
                    'en' => 'Specialists - High Risk Pregnancies',
                    'es' => 'Médicos Especialistas - Embarazos Alto Riesgo',
                    'slugEn' => 'specialists-high-risk-pregnancies',
                    'slugEs' => 'medicos-especialistas-embarazos-alto-riesgo',
                ),
            2123 =>
                array (
                    'en' => 'Specialized Bakery',
                    'es' => 'Reposteria Fina',
                    'slugEn' => 'specialized-bakery',
                    'slugEs' => 'reposteria-fina',
                ),
            2124 =>
                array (
                    'en' => 'Specialized Lubricants',
                    'es' => 'Lubricantes Especializados',
                    'slugEn' => 'specialized-lubricants',
                    'slugEs' => 'lubricantes-especializados',
                ),
            2125 =>
                array (
                    'en' => 'Specialty Contractor - Courts',
                    'es' => 'Contratistia Especializado- Tribunales',
                    'slugEn' => 'specialty-contractor-courts',
                    'slugEs' => 'contratistia-especializado-tribunales',
                ),
            2126 =>
                array (
                    'en' => 'Specialty Pharmacy',
                    'es' => 'Farmacias Especializadas',
                    'slugEn' => 'specialty-pharmacy',
                    'slugEs' => 'farmacias-especializadas',
                ),
            2127 =>
                array (
                    'en' => 'Speech - Pathology',
                    'es' => 'Patología Del Habla',
                    'slugEn' => 'speech-pathology',
                    'slugEs' => 'patologia-del-habla',
                ),
            2128 =>
                array (
                    'en' => 'Spices',
                    'es' => 'Especias',
                    'slugEn' => 'spices',
                    'slugEs' => 'especias',
                ),
            2129 =>
                array (
                    'en' => 'Spinning',
                    'es' => 'Spinning',
                    'slugEn' => 'spinning',
                    'slugEs' => 'spinning',
                ),
            2130 =>
                array (
                    'en' => 'Spiritual Consultants',
                    'es' => 'Consultores Espirituales',
                    'slugEn' => 'spiritual-consultants',
                    'slugEs' => 'consultores-espirituales',
                ),
            2131 =>
                array (
                    'en' => 'Sport Wear',
                    'es' => 'Ropa Deportiva',
                    'slugEn' => 'sport-wear',
                    'slugEs' => 'ropa-deportiva',
                ),
            2132 =>
                array (
                    'en' => 'Sports',
                    'es' => 'Deportes',
                    'slugEn' => 'sports',
                    'slugEs' => 'deportes',
                ),
            2133 =>
                array (
                    'en' => 'Sports -  Gymnastics-Schools',
                    'es' => 'Deportes- Gimnasia- Escuelas',
                    'slugEn' => 'sports-gymnastics-schools',
                    'slugEs' => 'deportes-gimnasia-escuelas',
                ),
            2134 =>
                array (
                    'en' => 'Sports - Aerobics - Exercises',
                    'es' => 'Deportes - Aeróbicos-Ejercicios',
                    'slugEn' => 'sports-aerobics-exercises',
                    'slugEs' => 'deportes-aerobicos-ejercicios',
                ),
            2135 =>
                array (
                    'en' => 'Sports - Aerobics - Exercises - Accessories',
                    'es' => 'Deportes - Aeróbicos-Ejercicios-Accesorios Y Piezas',
                    'slugEn' => 'sports-aerobics-exercises-accessories',
                    'slugEs' => 'deportes-aerobicos-ejercicios-accesorios-y-piezas',
                ),
            2136 =>
                array (
                    'en' => 'Sports - Basketball',
                    'es' => 'Deportes-Baloncesto',
                    'slugEn' => 'sports-basketball',
                    'slugEs' => 'deportes-baloncesto',
                ),
            2137 =>
                array (
                    'en' => 'Sports - Billiards',
                    'es' => 'Deportes - Billares',
                    'slugEn' => 'sports-billiards',
                    'slugEs' => 'deportes-billares',
                ),
            2138 =>
                array (
                    'en' => 'Sports - Billiards - Equipment & Supplies',
                    'es' => 'Deportes - Billares-Efectos Y Equipo',
                    'slugEn' => 'sports-billiards-equipment-supplies',
                    'slugEs' => 'deportes-billares-efectos-y-equipo',
                ),
            2139 =>
                array (
                    'en' => 'Sports - Bowling Centers',
                    'es' => 'Deportes - Boleras',
                    'slugEn' => 'sports-bowling-centers',
                    'slugEs' => 'deportes-boleras',
                ),
            2140 =>
                array (
                    'en' => 'Sports - Clubes-Target Practice',
                    'es' => 'Deportes - Clubes-Tiro',
                    'slugEn' => 'sports-clubes-target-practice',
                    'slugEs' => 'deportes-clubes-tiro',
                ),
            2141 =>
                array (
                    'en' => 'Sports - Clubs-Nautical',
                    'es' => 'Deportes - Clubes-Náuticos',
                    'slugEn' => 'sports-clubs-nautical',
                    'slugEs' => 'deportes-clubes-nauticos',
                ),
            2142 =>
                array (
                    'en' => 'Sports - Clubs-Sports',
                    'es' => 'Deportes - Clubes-Deportivos',
                    'slugEn' => 'sports-clubs-sports',
                    'slugEs' => 'deportes-clubes-deportivos',
                ),
            2143 =>
                array (
                    'en' => 'Sports - Cycling',
                    'es' => 'Deportes - Ciclismo',
                    'slugEn' => 'sports-cycling',
                    'slugEs' => 'deportes-ciclismo',
                ),
            2144 =>
                array (
                    'en' => 'Sports - Diving',
                    'es' => 'Deportes - Buceo',
                    'slugEn' => 'sports-diving',
                    'slugEs' => 'deportes-buceo',
                ),
            2145 =>
                array (
                    'en' => 'Sports - Diving - Schools',
                    'es' => 'Deportes - Buceo-Escuelas',
                    'slugEn' => 'sports-diving-schools',
                    'slugEs' => 'deportes-buceo-escuelas',
                ),
            2146 =>
                array (
                    'en' => 'Sports - Ecuestrian',
                    'es' => 'Deportes - Ecuestre',
                    'slugEn' => 'sports-ecuestrian',
                    'slugEs' => 'deportes-ecuestre',
                ),
            2147 =>
                array (
                    'en' => 'Sports - Exercises',
                    'es' => 'Deportes - Ejercicios',
                    'slugEn' => 'sports-exercises',
                    'slugEs' => 'deportes-ejercicios',
                ),
            2148 =>
                array (
                    'en' => 'Sports - Federations',
                    'es' => 'Deportes - Federaciones',
                    'slugEn' => 'sports-federations',
                    'slugEs' => 'deportes-federaciones',
                ),
            2149 =>
                array (
                    'en' => 'Sports - Fishing - Parts And Equipment',
                    'es' => 'Deportes - Pesca-Efectos Y Equipo',
                    'slugEn' => 'sports-fishing-parts-and-equipment',
                    'slugEs' => 'deportes-pesca-efectos-y-equipo',
                ),
            2150 =>
                array (
                    'en' => 'Sports - Go-Karts',
                    'es' => 'Deportes - Go-Karts',
                    'slugEn' => 'sports-go-karts',
                    'slugEs' => 'deportes-go-karts',
                ),
            2151 =>
                array (
                    'en' => 'Sports - Golf',
                    'es' => 'Deportes - Golf-Campos',
                    'slugEn' => 'sports-golf',
                    'slugEs' => 'deportes-golf-campos',
                ),
            2152 =>
                array (
                    'en' => 'Sports - Golf - Equipment & Supplies',
                    'es' => 'Deportes - Golf-Efectos Y Equipo',
                    'slugEn' => 'sports-golf-equipment-supplies',
                    'slugEs' => 'deportes-golf-efectos-y-equipo',
                ),
            2153 =>
                array (
                    'en' => 'Sports - Gymnasiums',
                    'es' => 'Deportes - Gimnasios',
                    'slugEn' => 'sports-gymnasiums',
                    'slugEs' => 'deportes-gimnasios',
                ),
            2154 =>
                array (
                    'en' => 'Sports - Gymnasiums - Equipment & Supplies',
                    'es' => 'Deportes - Gimnasios-Efectos Y Equipo',
                    'slugEn' => 'sports-gymnasiums-equipment-supplies',
                    'slugEs' => 'deportes-gimnasios-efectos-y-equipo',
                ),
            2155 =>
                array (
                    'en' => 'Sports - ITF Schools',
                    'es' => 'Deportes - ITF Escuelas',
                    'slugEn' => 'sports-itf-schools',
                    'slugEs' => 'deportes-itf-escuelas',
                ),
            2156 =>
                array (
                    'en' => 'Sports - Karate-Schools',
                    'es' => 'Deportes - Karate-Escuelas',
                    'slugEn' => 'sports-karate-schools',
                    'slugEs' => 'deportes-karate-escuelas',
                ),
            2157 =>
                array (
                    'en' => 'Sports - Kayaks',
                    'es' => 'Deportes - Kayaks',
                    'slugEn' => 'sports-kayaks',
                    'slugEs' => 'deportes-kayaks',
                ),
            2158 =>
                array (
                    'en' => 'Sports - Marathon',
                    'es' => 'Deportes - Maraton',
                    'slugEn' => 'sports-marathon',
                    'slugEs' => 'deportes-maraton',
                ),
            2159 =>
                array (
                    'en' => 'Sports - Marcial Arts',
                    'es' => 'Deportes - Artes Marciales',
                    'slugEn' => 'sports-marcial-arts',
                    'slugEs' => 'deportes-artes-marciales',
                ),
            2160 =>
                array (
                    'en' => 'Sports - Paintball',
                    'es' => 'Deportes - Paintball',
                    'slugEn' => 'sports-paintball',
                    'slugEs' => 'deportes-paintball',
                ),
            2161 =>
                array (
                    'en' => 'Sports - Parachuting',
                    'es' => 'Deportes - Paracaidismo',
                    'slugEn' => 'sports-parachuting',
                    'slugEs' => 'deportes-paracaidismo',
                ),
            2162 =>
                array (
                    'en' => 'Sports - Parts And Equipment',
                    'es' => 'Deportes - Efectos Y Equipo',
                    'slugEn' => 'sports-parts-and-equipment',
                    'slugEs' => 'deportes-efectos-y-equipo',
                ),
            2163 =>
                array (
                    'en' => 'Sports - Personal Trainer',
                    'es' => 'Deportes - Entrenador Personal',
                    'slugEn' => 'sports-personal-trainer',
                    'slugEs' => 'deportes-entrenador-personal',
                ),
            2164 =>
                array (
                    'en' => 'Sports - Pistols and Rifles',
                    'es' => 'Deportes - Pistolas y Rifles',
                    'slugEn' => 'sports-pistols-and-rifles',
                    'slugEs' => 'deportes-pistolas-y-rifles',
                ),
            2165 =>
                array (
                    'en' => 'Sports - Soccer',
                    'es' => 'Deportes - Balompié',
                    'slugEn' => 'sports-soccer',
                    'slugEs' => 'deportes-balompie',
                ),
            2166 =>
                array (
                    'en' => 'Sports - Sports Fishing',
                    'es' => 'Deportes - Pesca Deportiva',
                    'slugEn' => 'sports-sports-fishing',
                    'slugEs' => 'deportes-pesca-deportiva',
                ),
            2167 =>
                array (
                    'en' => 'Sports - Swimming',
                    'es' => 'Deportes - Natación',
                    'slugEn' => 'sports-swimming',
                    'slugEs' => 'deportes-natacion',
                ),
            2168 =>
                array (
                    'en' => 'Sports - Taekwon-do',
                    'es' => 'Deportes - Taekwon-do',
                    'slugEn' => 'sports-taekwon-do',
                    'slugEs' => 'deportes-taekwon-do',
                ),
            2169 =>
                array (
                    'en' => 'Sports - Track Road - Skating - Ice',
                    'es' => 'Deportes - Pista - Patinaje - Hielo',
                    'slugEn' => 'sports-track-road-skating-ice',
                    'slugEs' => 'deportes-pista-patinaje-hielo',
                ),
            2170 =>
                array (
                    'en' => 'Sports Promoters',
                    'es' => 'Promotores Deportivos',
                    'slugEn' => 'sports-promoters',
                    'slugEs' => 'promotores-deportivos',
                ),
            2171 =>
                array (
                    'en' => 'Sports- Sport\'s Cards',
                    'es' => 'Deportes - Tarjetas Deportivas',
                    'slugEn' => 'sports-sport-s-cards',
                    'slugEs' => 'deportes-tarjetas-deportivas',
                ),
            2172 =>
                array (
                    'en' => 'Sports Uniforms',
                    'es' => 'Uniformes Deportivos',
                    'slugEn' => 'sports-uniforms',
                    'slugEs' => 'uniformes-deportivos',
                ),
            2173 =>
                array (
                    'en' => 'Sports-Productions',
                    'es' => 'Deportivas-Producciones',
                    'slugEn' => 'sports-productions',
                    'slugEs' => 'deportivas-producciones',
                ),
            2174 =>
                array (
                    'en' => 'Sprinklers - Equipment & Supplies',
                    'es' => 'Rociadores-Efectos Y Equipo',
                    'slugEn' => 'sprinklers-equipment-supplies',
                    'slugEs' => 'rociadores-efectos-y-equipo',
                ),
            2175 =>
                array (
                    'en' => 'Stage Lighting - Services',
                    'es' => 'Iluminación-Servicios',
                    'slugEn' => 'stage-lighting-services',
                    'slugEs' => 'iluminacion-servicios',
                ),
            2176 =>
                array (
                    'en' => 'Stages',
                    'es' => 'Tarimas',
                    'slugEn' => 'stages',
                    'slugEs' => 'tarimas',
                ),
            2177 =>
                array (
                    'en' => 'Stainless Steel',
                    'es' => 'Acero Inoxidable',
                    'slugEn' => 'stainless-steel',
                    'slugEs' => 'acero-inoxidable',
                ),
            2178 =>
                array (
                    'en' => 'Stamps - Rubber',
                    'es' => 'Sellos-Goma',
                    'slugEn' => 'stamps-rubber',
                    'slugEs' => 'sellos-goma',
                ),
            2179 =>
                array (
                    'en' => 'State and Federal Taxes',
                    'es' => 'Planillas Estatales y Federales',
                    'slugEn' => 'state-and-federal-taxes',
                    'slugEs' => 'planillas-estatales-y-federales',
                ),
            2180 =>
                array (
                    'en' => 'Steel',
                    'es' => 'Acero',
                    'slugEn' => 'steel',
                    'slugEs' => 'acero',
                ),
            2181 =>
                array (
                    'en' => 'Steel Rods',
                    'es' => 'Varillas-Acero',
                    'slugEn' => 'steel-rods',
                    'slugEs' => 'varillas-acero',
                ),
            2182 =>
                array (
                    'en' => 'Stem Cells',
                    'es' => 'Células Madre',
                    'slugEn' => 'stem-cells',
                    'slugEs' => 'celulas-madre',
                ),
            2183 =>
                array (
                    'en' => 'Stenographers - Records',
                    'es' => 'Taquígrafos de Récords',
                    'slugEn' => 'stenographers-records',
                    'slugEs' => 'taquigrafos-de-records',
                ),
            2184 =>
                array (
                    'en' => 'Stereophonic - Equipment',
                    'es' => 'Estereofónico-Equipo',
                    'slugEn' => 'stereophonic-equipment',
                    'slugEs' => 'estereofonico-equipo',
                ),
            2185 =>
                array (
                    'en' => 'Stereophonic - Equipment - Repair',
                    'es' => 'Estereofónico-Equipo-Reparación',
                    'slugEn' => 'stereophonic-equipment-repair',
                    'slugEs' => 'estereofonico-equipo-reparacion',
                ),
            2186 =>
                array (
                    'en' => 'Stocks & Bonds - Brokers',
                    'es' => 'Valores-Corredores',
                    'slugEn' => 'stocks-bonds-brokers',
                    'slugEs' => 'valores-corredores',
                ),
            2187 =>
                array (
                    'en' => 'Storage',
                    'es' => 'Almacenaje',
                    'slugEn' => 'storage',
                    'slugEs' => 'almacenaje',
                ),
            2188 =>
                array (
                    'en' => 'Storage - Documents',
                    'es' => 'Almacenaje-Documentos',
                    'slugEn' => 'storage-documents',
                    'slugEs' => 'almacenaje-documentos',
                ),
            2189 =>
                array (
                    'en' => 'Storage - Household & Commercial',
                    'es' => 'Almacenes Privados',
                    'slugEn' => 'storage-household-commercial',
                    'slugEs' => 'almacenes-privados',
                ),
            2190 =>
                array (
                    'en' => 'Store Display',
                    'es' => 'Exhibidores para Tiendas',
                    'slugEn' => 'store-display',
                    'slugEs' => 'exhibidores-para-tiendas',
                ),
            2191 =>
                array (
                    'en' => 'Structural Steel',
                    'es' => 'Acero Estructural',
                    'slugEn' => 'structural-steel',
                    'slugEs' => 'acero-estructural',
                ),
            2192 =>
                array (
                    'en' => 'Stucco Ceiling',
                    'es' => 'Estucados',
                    'slugEn' => 'stucco-ceiling',
                    'slugEs' => 'estucados',
                ),
            2193 =>
                array (
                    'en' => 'Study of Sex',
                    'es' => 'Sexology',
                    'slugEn' => 'study-of-sex',
                    'slugEs' => 'sexology',
                ),
            2194 =>
                array (
                    'en' => 'Styling Salons',
                    'es' => 'Salones De Estilismo',
                    'slugEn' => 'styling-salons',
                    'slugEs' => 'salones-de-estilismo',
                ),
            2195 =>
                array (
                    'en' => 'Suitcase Repair',
                    'es' => 'Arreglo de Maletas',
                    'slugEn' => 'suitcase-repair',
                    'slugEs' => 'arreglo-de-maletas',
                ),
            2196 =>
                array (
                    'en' => 'Sunglasses',
                    'es' => 'Gafas De Sol',
                    'slugEn' => 'sunglasses',
                    'slugEs' => 'gafas-de-sol',
                ),
            2197 =>
                array (
                    'en' => 'Supermarkets',
                    'es' => 'Supermercados',
                    'slugEn' => 'supermarkets',
                    'slugEs' => 'supermercados',
                ),
            2198 =>
                array (
                    'en' => 'Supermarkets - Equipment',
                    'es' => 'Supermercados - Equipos',
                    'slugEn' => 'supermarkets-equipment',
                    'slugEs' => 'supermercados-equipos',
                ),
            2199 =>
                array (
                    'en' => 'Supervised Assignments',
                    'es' => 'Asignaciones Supervisadas',
                    'slugEn' => 'supervised-assignments',
                    'slugEs' => 'asignaciones-supervisadas',
                ),
            2200 =>
                array (
                    'en' => 'Surf Shops',
                    'es' => 'Surf Shops',
                    'slugEn' => 'surf-shops',
                    'slugEs' => 'surf-shops',
                ),
            2201 =>
                array (
                    'en' => 'Surgery - Arthroscopic',
                    'es' => 'Cirguía- Artroscópica',
                    'slugEn' => 'surgery-arthroscopic',
                    'slugEs' => 'cirguia-artroscopica',
                ),
            2202 =>
                array (
                    'en' => 'Surveyors - Land',
                    'es' => 'Agrimensores',
                    'slugEn' => 'surveyors-land',
                    'slugEs' => 'agrimensores',
                ),
            2203 =>
                array (
                    'en' => 'Sushi',
                    'es' => 'Sushi',
                    'slugEn' => 'sushi',
                    'slugEs' => 'sushi',
                ),
            2204 =>
                array (
                    'en' => 'Sweepers',
                    'es' => 'Barredoras',
                    'slugEn' => 'sweepers',
                    'slugEs' => 'barredoras',
                ),
            2205 =>
                array (
                    'en' => 'Sweet 15',
                    'es' => 'Quinceañeros',
                    'slugEn' => 'sweet-15',
                    'slugEs' => 'quinceaneros',
                ),
            2206 =>
                array (
                    'en' => 'Swimming Lessons',
                    'es' => 'Clases - Natación',
                    'slugEn' => 'swimming-lessons',
                    'slugEs' => 'clases-natacion',
                ),
            2207 =>
                array (
                    'en' => 'Swimming Pool Maintenance',
                    'es' => 'Piscina Mantenimiento Reparacion Y Servicio',
                    'slugEn' => 'swimming-pool-maintenance',
                    'slugEs' => 'piscina-mantenimiento-reparacion-y-servicio',
                ),
            2208 =>
                array (
                    'en' => 'Swimming Pools',
                    'es' => 'Piscinas',
                    'slugEn' => 'swimming-pools',
                    'slugEs' => 'piscinas',
                ),
            2209 =>
                array (
                    'en' => 'Swimming Pools - Equipment & Supplies',
                    'es' => 'Piscinas-Efectos Y Equipo',
                    'slugEn' => 'swimming-pools-equipment-supplies',
                    'slugEs' => 'piscinas-efectos-y-equipo',
                ),
            2210 =>
                array (
                    'en' => 'Swimming Pools - Services',
                    'es' => 'Piscinas-Servicio',
                    'slugEn' => 'swimming-pools-services',
                    'slugEs' => 'piscinas-servicio',
                ),
            2211 =>
                array (
                    'en' => 'Synagogue',
                    'es' => 'Sinagogas',
                    'slugEn' => 'synagogue',
                    'slugEs' => 'sinagogas',
                ),
            2212 =>
                array (
                    'en' => 'Tables And Chairs -Rental And Sales',
                    'es' => 'Sillas Y Mesas-Alquiler Y Venta',
                    'slugEn' => 'tables-and-chairs-rental-and-sales',
                    'slugEs' => 'sillas-y-mesas-alquiler-y-venta',
                ),
            2213 =>
                array (
                    'en' => 'Tablet Repair Service',
                    'es' => 'Reparación de Tablets',
                    'slugEn' => 'tablet-repair-service',
                    'slugEs' => 'reparacion-de-tablets',
                ),
            2214 =>
                array (
                    'en' => 'Taekwondo',
                    'es' => 'Taekwondo',
                    'slugEn' => 'taekwondo',
                    'slugEs' => 'taekwondo',
                ),
            2215 =>
                array (
                    'en' => 'Tailoring Alterations',
                    'es' => 'Sastrería Alteraciones',
                    'slugEn' => 'tailoring-alterations',
                    'slugEs' => 'sastreria-alteraciones',
                ),
            2216 =>
                array (
                    'en' => 'Tailors',
                    'es' => 'Sastrerías',
                    'slugEn' => 'tailors',
                    'slugEs' => 'sastrerias',
                ),
            2217 =>
                array (
                    'en' => 'Take Deposition',
                    'es' => 'Toma de Deposiciones',
                    'slugEn' => 'take-deposition',
                    'slugEs' => 'toma-de-deposiciones',
                ),
            2218 =>
                array (
                    'en' => 'Talent Agencies',
                    'es' => 'Agencias De Talento',
                    'slugEn' => 'talent-agencies',
                    'slugEs' => 'agencias-de-talento',
                ),
            2219 =>
                array (
                    'en' => 'Tango Lessons',
                    'es' => 'Clases de Tango',
                    'slugEn' => 'tango-lessons',
                    'slugEs' => 'clases-de-tango',
                ),
            2220 =>
                array (
                    'en' => 'Tank Cleaning',
                    'es' => 'Limpieza de Tanques',
                    'slugEn' => 'tank-cleaning',
                    'slugEs' => 'limpieza-de-tanques',
                ),
            2221 =>
                array (
                    'en' => 'Tanks',
                    'es' => 'Tanques',
                    'slugEn' => 'tanks',
                    'slugEs' => 'tanques',
                ),
            2222 =>
                array (
                    'en' => 'Tanks - Water',
                    'es' => 'Tanques-Agua',
                    'slugEn' => 'tanks-water',
                    'slugEs' => 'tanques-agua',
                ),
            2223 =>
                array (
                    'en' => 'Tanning Salons',
                    'es' => 'Bronceado-Salones',
                    'slugEn' => 'tanning-salons',
                    'slugEs' => 'bronceado-salones',
                ),
            2224 =>
                array (
                    'en' => 'Tapas',
                    'es' => 'Tapas',
                    'slugEn' => 'tapas',
                    'slugEs' => 'tapas',
                ),
            2225 =>
                array (
                    'en' => 'Tarot Reading',
                    'es' => 'Lecturas de Tarot',
                    'slugEn' => 'tarot-reading',
                    'slugEs' => 'lecturas-de-tarot',
                ),
            2226 =>
                array (
                    'en' => 'Tattoo Removal',
                    'es' => 'Remoción de Tatuajes',
                    'slugEn' => 'tattoo-removal',
                    'slugEs' => 'remocion-de-tatuajes',
                ),
            2227 =>
                array (
                    'en' => 'Tattoos',
                    'es' => 'Tatuajes',
                    'slugEn' => 'tattoos',
                    'slugEs' => 'tatuajes',
                ),
            2228 =>
                array (
                    'en' => 'Tax Return - Preparation',
                    'es' => 'Planillas-Preparación',
                    'slugEn' => 'tax-return-preparation',
                    'slugEs' => 'planillas-preparacion',
                ),
            2229 =>
                array (
                    'en' => 'Taxes',
                    'es' => 'Contribuciones',
                    'slugEn' => 'taxes',
                    'slugEs' => 'contribuciones',
                ),
            2230 =>
                array (
                    'en' => 'Taxi Cabs - Intercity',
                    'es' => 'Taxis',
                    'slugEn' => 'taxi-cabs-intercity',
                    'slugEs' => 'taxis',
                ),
            2231 =>
                array (
                    'en' => 'Technical Colleges',
                    'es' => 'Colegios Técnicos',
                    'slugEn' => 'technical-colleges',
                    'slugEs' => 'colegios-tecnicos',
                ),
            2232 =>
                array (
                    'en' => 'Technology-IT',
                    'es' => 'Tecnología-IT',
                    'slugEn' => 'technology-it',
                    'slugEs' => 'tecnologia-it',
                ),
            2233 =>
                array (
                    'en' => 'Teeth Whitening',
                    'es' => 'Blanqueado Dental',
                    'slugEn' => 'teeth-whitening',
                    'slugEs' => 'blanqueado-dental',
                ),
            2234 =>
                array (
                    'en' => 'Telecommunication',
                    'es' => 'Telecomunicaciones',
                    'slugEn' => 'telecommunication',
                    'slugEs' => 'telecomunicaciones',
                ),
            2235 =>
                array (
                    'en' => 'Telecommunications - Consultants',
                    'es' => 'Telecomunicaciones-Consultores',
                    'slugEn' => 'telecommunications-consultants',
                    'slugEs' => 'telecomunicaciones-consultores',
                ),
            2236 =>
                array (
                    'en' => 'Telecommunications - Equipment & Supplies',
                    'es' => 'Telecomunicaciones-Efectos Y Equipo',
                    'slugEn' => 'telecommunications-equipment-supplies',
                    'slugEs' => 'telecomunicaciones-efectos-y-equipo',
                ),
            2237 =>
                array (
                    'en' => 'Telephone Answering Service',
                    'es' => 'Telephone Answering Service',
                    'slugEn' => 'telephone-answering-service',
                    'slugEs' => 'telephone-answering-service',
                ),
            2238 =>
                array (
                    'en' => 'Telephone -Equipment',
                    'es' => 'Telefónico-Equipo',
                    'slugEn' => 'telephone-equipment',
                    'slugEs' => 'telefonico-equipo',
                ),
            2239 =>
                array (
                    'en' => 'Telephone-Companies',
                    'es' => 'Telefónicas-Compañías',
                    'slugEn' => 'telephone-companies',
                    'slugEs' => 'telefonicas-companias',
                ),
            2240 =>
                array (
                    'en' => 'Telephones - Cellular',
                    'es' => 'Teléfonos Celulares',
                    'slugEn' => 'telephones-cellular',
                    'slugEs' => 'telefonos-celulares',
                ),
            2241 =>
                array (
                    'en' => 'Telephones Cellular - Accessories',
                    'es' => 'Teléfonos Celulares - Accesorios',
                    'slugEn' => 'telephones-cellular-accessories',
                    'slugEs' => 'telefonos-celulares-accesorios',
                ),
            2242 =>
                array (
                    'en' => 'Telephones Cellular - Repair and Modification',
                    'es' => 'Teléfonos Celulares - Desbloqueo y Reparación',
                    'slugEn' => 'telephones-cellular-repair-and-modification',
                    'slugEs' => 'telefonos-celulares-desbloqueo-y-reparacion',
                ),
            2243 =>
                array (
                    'en' => 'Television - Cable Systems',
                    'es' => 'Televisión - Sistemas Cable',
                    'slugEn' => 'television-cable-systems',
                    'slugEs' => 'television-sistemas-cable',
                ),
            2244 =>
                array (
                    'en' => 'Television - Production Services',
                    'es' => 'Televisión-Servicios Producción',
                    'slugEn' => 'television-production-services',
                    'slugEs' => 'television-servicios-produccion',
                ),
            2245 =>
                array (
                    'en' => 'Television - Stations',
                    'es' => 'Televisión-Estaciones',
                    'slugEn' => 'television-stations',
                    'slugEs' => 'television-estaciones',
                ),
            2246 =>
                array (
                    'en' => 'Television - Studios',
                    'es' => 'Televisión-Estudios',
                    'slugEn' => 'television-studios',
                    'slugEs' => 'television-estudios',
                ),
            2247 =>
                array (
                    'en' => 'Television Sets',
                    'es' => 'Televisores',
                    'slugEn' => 'television-sets',
                    'slugEs' => 'televisores',
                ),
            2248 =>
                array (
                    'en' => 'Television Sets - Repair',
                    'es' => 'Televisores-Reparación',
                    'slugEn' => 'television-sets-repair',
                    'slugEs' => 'televisores-reparacion',
                ),
            2249 =>
                array (
                    'en' => 'Television-Close Circuit Systems',
                    'es' => 'Television-Sistemas Circuito Cerrado',
                    'slugEn' => 'television-close-circuit-systems',
                    'slugEs' => 'television-sistemas-circuito-cerrado',
                ),
            2250 =>
                array (
                    'en' => 'Televisions-Parts and Accessories',
                    'es' => 'Televisores-Accesorios Y Piezas',
                    'slugEn' => 'televisions-parts-and-accessories',
                    'slugEs' => 'televisores-accesorios-y-piezas',
                ),
            2251 =>
                array (
                    'en' => 'Temperature - Controls',
                    'es' => 'Temperatura-Controles',
                    'slugEn' => 'temperature-controls',
                    'slugEs' => 'temperatura-controles',
                ),
            2252 =>
                array (
                    'en' => 'Tents',
                    'es' => 'Carpas',
                    'slugEn' => 'tents',
                    'slugEs' => 'carpas',
                ),
            2253 =>
                array (
                    'en' => 'Tents - Sales and Rental',
                    'es' => 'Carpas - Venta y Alquiler',
                    'slugEn' => 'tents-sales-and-rental',
                    'slugEs' => 'carpas-venta-y-alquiler',
                ),
            2254 =>
                array (
                    'en' => 'Terrace-Wood',
                    'es' => 'Terrazas-Madera',
                    'slugEn' => 'terrace-wood',
                    'slugEs' => 'terrazas-madera',
                ),
            2255 =>
                array (
                    'en' => 'Terrazzo',
                    'es' => 'Terrazo-Integral',
                    'slugEn' => 'terrazzo',
                    'slugEs' => 'terrazo-integral',
                ),
            2256 =>
                array (
                    'en' => 'Testing - Paternity',
                    'es' => 'Pruebas - Paternidad',
                    'slugEn' => 'testing-paternity',
                    'slugEs' => 'pruebas-paternidad',
                ),
            2257 =>
                array (
                    'en' => 'Textiles',
                    'es' => 'Tejidos',
                    'slugEn' => 'textiles',
                    'slugEs' => 'tejidos',
                ),
            2258 =>
                array (
                    'en' => 'Thai Food',
                    'es' => 'Restaurantes Comida Thai',
                    'slugEn' => 'thai-food',
                    'slugEs' => 'restaurantes-comida-thai',
                ),
            2259 =>
                array (
                    'en' => 'Theaters & Movie Theaters',
                    'es' => 'Teatros Y Cines',
                    'slugEn' => 'theaters-movie-theaters',
                    'slugEs' => 'teatros-y-cines',
                ),
            2260 =>
                array (
                    'en' => 'Theatrical Supplies',
                    'es' => 'Teatro-Equipo-Utilería',
                    'slugEn' => 'theatrical-supplies',
                    'slugEs' => 'teatro-equipo-utileria',
                ),
            2261 =>
                array (
                    'en' => 'Theological College',
                    'es' => 'Universidades Teológicas',
                    'slugEn' => 'theological-college',
                    'slugEs' => 'universidades-teologicas',
                ),
            2262 =>
                array (
                    'en' => 'Therapists',
                    'es' => 'Terapeutas',
                    'slugEn' => 'therapists',
                    'slugEs' => 'terapeutas',
                ),
            2263 =>
                array (
                    'en' => 'Therapy - Educational',
                    'es' => 'Terapia Educativa',
                    'slugEn' => 'therapy-educational',
                    'slugEs' => 'terapia-educativa',
                ),
            2264 =>
                array (
                    'en' => 'Therapy - Family',
                    'es' => 'Terapia Familiar',
                    'slugEn' => 'therapy-family',
                    'slugEs' => 'terapia-familiar',
                ),
            2265 =>
                array (
                    'en' => 'Therapy - Motivation',
                    'es' => 'Terapia - Motivacion',
                    'slugEn' => 'therapy-motivation',
                    'slugEs' => 'terapia-motivacion',
                ),
            2266 =>
                array (
                    'en' => 'Therapy - Occupational',
                    'es' => 'Terapia Ocupacional',
                    'slugEn' => 'therapy-occupational',
                    'slugEs' => 'terapia-ocupacional',
                ),
            2267 =>
                array (
                    'en' => 'Therapy - Physical',
                    'es' => 'Terapia Física',
                    'slugEn' => 'therapy-physical',
                    'slugEs' => 'terapia-fisica',
                ),
            2268 =>
                array (
                    'en' => 'Therapy - Respiratory',
                    'es' => 'Terapia Respiratoria',
                    'slugEn' => 'therapy-respiratory',
                    'slugEs' => 'terapia-respiratoria',
                ),
            2269 =>
                array (
                    'en' => 'Therapy - Sexology',
                    'es' => 'Terapia Sexologia',
                    'slugEn' => 'therapy-sexology',
                    'slugEs' => 'terapia-sexologia',
                ),
            2270 =>
                array (
                    'en' => 'Therapy - Speech',
                    'es' => 'Terapia Del Habla',
                    'slugEn' => 'therapy-speech',
                    'slugEs' => 'terapia-del-habla',
                ),
            2271 =>
                array (
                    'en' => 'Therapy-Psychological',
                    'es' => 'Terapia Psicológica',
                    'slugEn' => 'therapy-psychological',
                    'slugEs' => 'terapia-psicologica',
                ),
            2272 =>
                array (
                    'en' => 'Threads',
                    'es' => 'Costura-Materiales',
                    'slugEn' => 'threads',
                    'slugEs' => 'costura-materiales',
                ),
            2273 =>
                array (
                    'en' => 'Thyroid',
                    'es' => 'Tiroides',
                    'slugEn' => 'thyroid',
                    'slugEs' => 'tiroides',
                ),
            2274 =>
                array (
                    'en' => 'Tickets',
                    'es' => 'Boletos-Venta',
                    'slugEn' => 'tickets',
                    'slugEs' => 'boletos-venta',
                ),
            2275 =>
                array (
                    'en' => 'Tie-Factory',
                    'es' => 'Corbatas-Fabricas',
                    'slugEn' => 'tie-factory',
                    'slugEs' => 'corbatas-fabricas',
                ),
            2276 =>
                array (
                    'en' => 'Tiles - Roof',
                    'es' => 'Tejas',
                    'slugEn' => 'tiles-roof',
                    'slugEs' => 'tejas',
                ),
            2277 =>
                array (
                    'en' => 'Tiles and Stones - Instalation',
                    'es' => 'Losas Y Azulejos-Instalación',
                    'slugEn' => 'tiles-and-stones-instalation',
                    'slugEs' => 'losas-y-azulejos-instalacion',
                ),
            2278 =>
                array (
                    'en' => 'Tiles And Stones (Flagstones)',
                    'es' => 'Losas Y Azulejos',
                    'slugEn' => 'tiles-and-stones-flagstones',
                    'slugEs' => 'losas-y-azulejos',
                ),
            2279 =>
                array (
                    'en' => 'Tire Center',
                    'es' => 'Gomera',
                    'slugEn' => 'tire-center',
                    'slugEs' => 'gomera',
                ),
            2280 =>
                array (
                    'en' => 'Tires',
                    'es' => 'Gomas',
                    'slugEn' => 'tires',
                    'slugEs' => 'gomas',
                ),
            2281 =>
                array (
                    'en' => 'Tires - Industrial',
                    'es' => 'Gomas-Industriales',
                    'slugEn' => 'tires-industrial',
                    'slugEs' => 'gomas-industriales',
                ),
            2282 =>
                array (
                    'en' => 'Title Deeds',
                    'es' => 'Estudio de Títulos de Propiedad',
                    'slugEn' => 'title-deeds',
                    'slugEs' => 'estudio-de-titulos-de-propiedad',
                ),
            2283 =>
                array (
                    'en' => 'Titles - Investigations',
                    'es' => 'Títulos-Investigaciones',
                    'slugEn' => 'titles-investigations',
                    'slugEs' => 'titulos-investigaciones',
                ),
            2284 =>
                array (
                    'en' => 'Tobacco - Pipes',
                    'es' => 'Tabaco - Pipas',
                    'slugEn' => 'tobacco-pipes',
                    'slugEs' => 'tabaco-pipas',
                ),
            2285 =>
                array (
                    'en' => 'Togas-Manufacture',
                    'es' => 'Togas-Manufacturas',
                    'slugEn' => 'togas-manufacture',
                    'slugEs' => 'togas-manufacturas',
                ),
            2286 =>
                array (
                    'en' => 'Toilets Portable - Rental',
                    'es' => 'Baños Portátiles-Alquiler',
                    'slugEn' => 'toilets-portable-rental',
                    'slugEs' => 'banos-portatiles-alquiler',
                ),
            2287 =>
                array (
                    'en' => 'Tombstone',
                    'es' => 'Lápidas',
                    'slugEn' => 'tombstone',
                    'slugEs' => 'lapidas',
                ),
            2288 =>
                array (
                    'en' => 'Tomography',
                    'es' => 'Tomografía',
                    'slugEn' => 'tomography',
                    'slugEs' => 'tomografia',
                ),
            2289 =>
                array (
                    'en' => 'Toner Ink',
                    'es' => 'Tintas Toner',
                    'slugEn' => 'toner-ink',
                    'slugEs' => 'tintas-toner',
                ),
            2290 =>
                array (
                    'en' => 'Tools',
                    'es' => 'Herramientas',
                    'slugEn' => 'tools',
                    'slugEs' => 'herramientas',
                ),
            2291 =>
                array (
                    'en' => 'Tools - Electric',
                    'es' => 'Herramientas-Eléctricas',
                    'slugEn' => 'tools-electric',
                    'slugEs' => 'herramientas-electricas',
                ),
            2292 =>
                array (
                    'en' => 'Tools - Grinding',
                    'es' => 'Herramientas-Amolado',
                    'slugEn' => 'tools-grinding',
                    'slugEs' => 'herramientas-amolado',
                ),
            2293 =>
                array (
                    'en' => 'Tools - Pneumatic',
                    'es' => 'Herramientas - Neumáticas',
                    'slugEn' => 'tools-pneumatic',
                    'slugEs' => 'herramientas-neumaticas',
                ),
            2294 =>
                array (
                    'en' => 'Tools - Rental',
                    'es' => 'Herramientas-Alquiler',
                    'slugEn' => 'tools-rental',
                    'slugEs' => 'herramientas-alquiler',
                ),
            2295 =>
                array (
                    'en' => 'Tools - Repair',
                    'es' => 'Herramientas-Reparación',
                    'slugEn' => 'tools-repair',
                    'slugEs' => 'herramientas-reparacion',
                ),
            2296 =>
                array (
                    'en' => 'Tools-Manufacture',
                    'es' => 'Herramientas-Manufactura',
                    'slugEn' => 'tools-manufacture',
                    'slugEs' => 'herramientas-manufactura',
                ),
            2297 =>
                array (
                    'en' => 'Tourism',
                    'es' => 'Turismo',
                    'slugEn' => 'tourism',
                    'slugEs' => 'turismo',
                ),
            2298 =>
                array (
                    'en' => 'Tours - Sightseeing',
                    'es' => 'Excursiones',
                    'slugEn' => 'tours-sightseeing',
                    'slugEs' => 'excursiones',
                ),
            2299 =>
                array (
                    'en' => 'Towboat-Maritime',
                    'es' => 'Remolcadores-Marítimos',
                    'slugEn' => 'towboat-maritime',
                    'slugEs' => 'remolcadores-maritimos',
                ),
            2300 =>
                array (
                    'en' => 'Towing',
                    'es' => 'Grúas',
                    'slugEn' => 'towing',
                    'slugEs' => 'gruas',
                ),
            2301 =>
                array (
                    'en' => 'Toys',
                    'es' => 'Juguetes',
                    'slugEn' => 'toys',
                    'slugEs' => 'juguetes',
                ),
            2302 =>
                array (
                    'en' => 'Trade Schools',
                    'es' => 'Institutos',
                    'slugEn' => 'trade-schools',
                    'slugEs' => 'institutos',
                ),
            2303 =>
                array (
                    'en' => 'Traditional Sweets',
                    'es' => 'Dulces Típicos',
                    'slugEn' => 'traditional-sweets',
                    'slugEs' => 'dulces-tipicos',
                ),
            2304 =>
                array (
                    'en' => 'Trailer Homes',
                    'es' => 'Casas Rodantes',
                    'slugEn' => 'trailer-homes',
                    'slugEs' => 'casas-rodantes',
                ),
            2305 =>
                array (
                    'en' => 'Trailers - Containers',
                    'es' => 'Remolques - Contenedores',
                    'slugEn' => 'trailers-containers',
                    'slugEs' => 'remolques-contenedores',
                ),
            2306 =>
                array (
                    'en' => 'Trailers - Refrigerated',
                    'es' => 'Remolques - Refrigerados',
                    'slugEn' => 'trailers-refrigerated',
                    'slugEs' => 'remolques-refrigerados',
                ),
            2307 =>
                array (
                    'en' => 'Trailers - Refrigerated Containers',
                    'es' => 'Trailers-Contenedores Refrigerados',
                    'slugEn' => 'trailers-refrigerated-containers',
                    'slugEs' => 'trailers-contenedores-refrigerados',
                ),
            2308 =>
                array (
                    'en' => 'Trailers - Repair',
                    'es' => 'Trailers-Remolques-Reparacion',
                    'slugEn' => 'trailers-repair',
                    'slugEs' => 'trailers-remolques-reparacion',
                ),
            2309 =>
                array (
                    'en' => 'Trailers (Towcars)',
                    'es' => 'Trailers (Remolques)',
                    'slugEn' => 'trailers-towcars',
                    'slugEs' => 'trailers-remolques',
                ),
            2310 =>
                array (
                    'en' => 'Training - Business',
                    'es' => 'Capacitación-Gerencia',
                    'slugEn' => 'training-business',
                    'slugEs' => 'capacitacion-gerencia',
                ),
            2311 =>
                array (
                    'en' => 'Transcripts',
                    'es' => 'Transcripciones',
                    'slugEn' => 'transcripts',
                    'slugEs' => 'transcripciones',
                ),
            2312 =>
                array (
                    'en' => 'Transformation Workshops',
                    'es' => 'Talleres de Transformacion',
                    'slugEn' => 'transformation-workshops',
                    'slugEs' => 'talleres-de-transformacion',
                ),
            2313 =>
                array (
                    'en' => 'Transformers',
                    'es' => 'Transformadores',
                    'slugEn' => 'transformers',
                    'slugEs' => 'transformadores',
                ),
            2314 =>
                array (
                    'en' => 'Translators & Interpreters',
                    'es' => 'Traductores E Interpretes',
                    'slugEn' => 'translators-interpreters',
                    'slugEs' => 'traductores-e-interpretes',
                ),
            2315 =>
                array (
                    'en' => 'Transmissions',
                    'es' => 'Transmisiones',
                    'slugEn' => 'transmissions',
                    'slugEs' => 'transmisiones',
                ),
            2316 =>
                array (
                    'en' => 'Transponder Keys',
                    'es' => 'Llaves Transponder',
                    'slugEn' => 'transponder-keys',
                    'slugEs' => 'llaves-transponder',
                ),
            2317 =>
                array (
                    'en' => 'Transportation - Tourist',
                    'es' => 'Transporte Turístico',
                    'slugEn' => 'transportation-tourist',
                    'slugEs' => 'transporte-turistico',
                ),
            2318 =>
                array (
                    'en' => 'Transportation Services',
                    'es' => 'Transportación',
                    'slugEn' => 'transportation-services',
                    'slugEs' => 'transportacion',
                ),
            2319 =>
                array (
                    'en' => 'Transportation Services - Schools',
                    'es' => 'Transportación - Escuelas',
                    'slugEn' => 'transportation-services-schools',
                    'slugEs' => 'transportacion-escuelas',
                ),
            2320 =>
                array (
                    'en' => 'Transportation to Medical Appointments',
                    'es' => 'Transporte a Citas Médica',
                    'slugEn' => 'transportation-to-medical-appointments',
                    'slugEs' => 'transporte-a-citas-medica',
                ),
            2321 =>
                array (
                    'en' => 'Trash',
                    'es' => 'Basura',
                    'slugEn' => 'trash',
                    'slugEs' => 'basura',
                ),
            2322 =>
                array (
                    'en' => 'Travel - Agents',
                    'es' => 'Viajes-Agentes',
                    'slugEn' => 'travel-agents',
                    'slugEs' => 'viajes-agentes',
                ),
            2323 =>
                array (
                    'en' => 'Travel - Services',
                    'es' => 'Viajes-Servicios',
                    'slugEn' => 'travel-services',
                    'slugEs' => 'viajes-servicios',
                ),
            2324 =>
                array (
                    'en' => 'Travel Agencies',
                    'es' => 'Agencias de Viajes',
                    'slugEn' => 'travel-agencies',
                    'slugEs' => 'agencias-de-viajes',
                ),
            2325 =>
                array (
                    'en' => 'Tree Trimming',
                    'es' => 'Poda de Arboles',
                    'slugEn' => 'tree-trimming',
                    'slugEs' => 'poda-de-arboles',
                ),
            2326 =>
                array (
                    'en' => 'Trees',
                    'es' => 'Flores Y Plantas-Artificiales',
                    'slugEn' => 'trees',
                    'slugEs' => 'flores-y-plantas-artificiales',
                ),
            2327 =>
                array (
                    'en' => 'Tripleta',
                    'es' => 'Tripleta',
                    'slugEn' => 'tripleta',
                    'slugEs' => 'tripleta',
                ),
            2328 =>
                array (
                    'en' => 'Trophies',
                    'es' => 'Trofeos',
                    'slugEn' => 'trophies',
                    'slugEs' => 'trofeos',
                ),
            2329 =>
                array (
                    'en' => 'Tropical Music',
                    'es' => 'Música Tropical',
                    'slugEn' => 'tropical-music',
                    'slugEs' => 'musica-tropical',
                ),
            2330 =>
                array (
                    'en' => 'Trousers - Manufacture',
                    'es' => 'Pantalones-Fábricas',
                    'slugEn' => 'trousers-manufacture',
                    'slugEs' => 'pantalones-fabricas',
                ),
            2331 =>
                array (
                    'en' => 'Truck Insurance',
                    'es' => 'Seguros Camiones',
                    'slugEn' => 'truck-insurance',
                    'slugEs' => 'seguros-camiones',
                ),
            2332 =>
                array (
                    'en' => 'Trucks',
                    'es' => 'Camiones',
                    'slugEn' => 'trucks',
                    'slugEs' => 'camiones',
                ),
            2333 =>
                array (
                    'en' => 'Trucks - Bodies',
                    'es' => 'Camiones-Carrocerías',
                    'slugEn' => 'trucks-bodies',
                    'slugEs' => 'camiones-carrocerias',
                ),
            2334 =>
                array (
                    'en' => 'Trucks - Inspection',
                    'es' => 'Camiones - Inspección',
                    'slugEn' => 'trucks-inspection',
                    'slugEs' => 'camiones-inspeccion',
                ),
            2335 =>
                array (
                    'en' => 'Trucks - Parts & Accessories',
                    'es' => 'Camiones-Accesorios Y Piezas',
                    'slugEn' => 'trucks-parts-accessories',
                    'slugEs' => 'camiones-accesorios-y-piezas',
                ),
            2336 =>
                array (
                    'en' => 'Trucks - Rental',
                    'es' => 'Camiones-Alquiler',
                    'slugEn' => 'trucks-rental',
                    'slugEs' => 'camiones-alquiler',
                ),
            2337 =>
                array (
                    'en' => 'Trucks - Repair',
                    'es' => 'Camiones - Reparación',
                    'slugEn' => 'trucks-repair',
                    'slugEs' => 'camiones-reparacion',
                ),
            2338 =>
                array (
                    'en' => 'Trucks - Repair - Maintenance',
                    'es' => 'Camiones-Mantenimiento',
                    'slugEn' => 'trucks-repair-maintenance',
                    'slugEs' => 'camiones-mantenimiento',
                ),
            2339 =>
                array (
                    'en' => 'Trust',
                    'es' => 'Fideicomisos',
                    'slugEn' => 'trust',
                    'slugEs' => 'fideicomisos',
                ),
            2340 =>
                array (
                    'en' => 'Tubs',
                    'es' => 'Baneras-Reparacion Y Renovacion',
                    'slugEn' => 'tubs',
                    'slugEs' => 'baneras-reparacion-y-renovacion',
                ),
            2341 =>
                array (
                    'en' => 'Turnery',
                    'es' => 'Tornería',
                    'slugEn' => 'turnery',
                    'slugEs' => 'torneria',
                ),
            2342 =>
                array (
                    'en' => 'Tutoring',
                    'es' => 'Tutoría',
                    'slugEn' => 'tutoring',
                    'slugEs' => 'tutoria',
                ),
            2343 =>
                array (
                    'en' => 'Tuxedo',
                    'es' => 'Tuxedo',
                    'slugEn' => 'tuxedo',
                    'slugEs' => 'tuxedo',
                ),
            2344 =>
                array (
                    'en' => 'Tuxedos',
                    'es' => 'Etiquetas y Tuxedos',
                    'slugEn' => 'tuxedos',
                    'slugEs' => 'etiquetas-y-tuxedos',
                ),
            2345 =>
                array (
                    'en' => 'Typewriters',
                    'es' => 'Maquinillas',
                    'slugEn' => 'typewriters',
                    'slugEs' => 'maquinillas',
                ),
            2346 =>
                array (
                    'en' => 'Typewriters - Repair',
                    'es' => 'Maquinillas-Reparación',
                    'slugEn' => 'typewriters-repair',
                    'slugEs' => 'maquinillas-reparacion',
                ),
            2347 =>
                array (
                    'en' => 'underground waste containers',
                    'es' => 'Zafacones Soterrados',
                    'slugEn' => 'underground-waste-containers',
                    'slugEs' => 'zafacones-soterrados',
                ),
            2348 =>
                array (
                    'en' => 'Uniforms',
                    'es' => 'Uniformes',
                    'slugEn' => 'uniforms',
                    'slugEs' => 'uniformes',
                ),
            2349 =>
                array (
                    'en' => 'Uniforms - Manufacture',
                    'es' => 'Uniformes-Fábricas',
                    'slugEn' => 'uniforms-manufacture',
                    'slugEs' => 'uniformes-fabricas',
                ),
            2350 =>
                array (
                    'en' => 'Uniforms Emblems',
                    'es' => 'Uniformes emblemas',
                    'slugEn' => 'uniforms-emblems',
                    'slugEs' => 'uniformes-emblemas',
                ),
            2351 =>
                array (
                    'en' => 'Universities & Colleges',
                    'es' => 'Universidades',
                    'slugEn' => 'universities-colleges',
                    'slugEs' => 'universidades',
                ),
            2352 =>
                array (
                    'en' => 'Upholsterers',
                    'es' => 'Tapicerías',
                    'slugEn' => 'upholsterers',
                    'slugEs' => 'tapicerias',
                ),
            2353 =>
                array (
                    'en' => 'Upholstery - Equipment & Supplies',
                    'es' => 'Tapicerías-Efectos Y Equipo',
                    'slugEn' => 'upholstery-equipment-supplies',
                    'slugEs' => 'tapicerias-efectos-y-equipo',
                ),
            2354 =>
                array (
                    'en' => 'Urban Clothing',
                    'es' => 'Ropa Urbana',
                    'slugEn' => 'urban-clothing',
                    'slugEs' => 'ropa-urbana',
                ),
            2355 =>
                array (
                    'en' => 'Used Parts',
                    'es' => 'Piezas Usadas',
                    'slugEn' => 'used-parts',
                    'slugEs' => 'piezas-usadas',
                ),
            2356 =>
                array (
                    'en' => 'Vacational Centers',
                    'es' => 'Centros Vacacionales',
                    'slugEn' => 'vacational-centers',
                    'slugEs' => 'centros-vacacionales',
                ),
            2357 =>
                array (
                    'en' => 'Vaccination Center',
                    'es' => 'Centro de Vacunación',
                    'slugEn' => 'vaccination-center',
                    'slugEs' => 'centro-de-vacunacion',
                ),
            2358 =>
                array (
                    'en' => 'Vaccines',
                    'es' => 'Vacunas',
                    'slugEn' => 'vaccines',
                    'slugEs' => 'vacunas',
                ),
            2359 =>
                array (
                    'en' => 'Vacuum Cleaners',
                    'es' => 'Aspiradoras',
                    'slugEn' => 'vacuum-cleaners',
                    'slugEs' => 'aspiradoras',
                ),
            2360 =>
                array (
                    'en' => 'Valuables - Transfer',
                    'es' => 'Valores-Envíos',
                    'slugEn' => 'valuables-transfer',
                    'slugEs' => 'valores-envios',
                ),
            2361 =>
                array (
                    'en' => 'Valves',
                    'es' => 'Válvulas',
                    'slugEn' => 'valves',
                    'slugEs' => 'valvulas',
                ),
            2362 =>
                array (
                    'en' => 'Valves - Parts & Accessories',
                    'es' => 'Válvulas-Accesorios Y Piezas',
                    'slugEn' => 'valves-parts-accessories',
                    'slugEs' => 'valvulas-accesorios-y-piezas',
                ),
            2363 =>
                array (
                    'en' => 'Vascular Surgery',
                    'es' => 'Cirugía Vascular',
                    'slugEn' => 'vascular-surgery',
                    'slugEs' => 'cirugia-vascular',
                ),
            2364 =>
                array (
                    'en' => 'Vegetarian Restaurant',
                    'es' => 'Restaurante Vegetariano',
                    'slugEn' => 'vegetarian-restaurant',
                    'slugEs' => 'restaurante-vegetariano',
                ),
            2365 =>
                array (
                    'en' => 'Vehicle Monitoring',
                    'es' => 'Monitoreo Vehicular',
                    'slugEn' => 'vehicle-monitoring',
                    'slugEs' => 'monitoreo-vehicular',
                ),
            2366 =>
                array (
                    'en' => 'Vehicle Tracking',
                    'es' => 'Rastreo Vehicular',
                    'slugEn' => 'vehicle-tracking',
                    'slugEs' => 'rastreo-vehicular',
                ),
            2367 =>
                array (
                    'en' => 'Vending Machines',
                    'es' => 'Máquinas Expendedoras',
                    'slugEn' => 'vending-machines',
                    'slugEs' => 'maquinas-expendedoras',
                ),
            2368 =>
                array (
                    'en' => 'Venue',
                    'es' => 'Venue',
                    'slugEn' => 'venue',
                    'slugEs' => 'venue',
                ),
            2369 =>
                array (
                    'en' => 'Vertical Blinds',
                    'es' => 'Vertical Blinds (Cortinas Verticales)',
                    'slugEn' => 'vertical-blinds',
                    'slugEs' => 'vertical-blinds-cortinas-verticales',
                ),
            2370 =>
                array (
                    'en' => 'Vestibular Rehabilitation',
                    'es' => 'Rehabilitación Vestibular',
                    'slugEn' => 'vestibular-rehabilitation',
                    'slugEs' => 'rehabilitacion-vestibular',
                ),
            2371 =>
                array (
                    'en' => 'Veterinarians',
                    'es' => 'Veterinarios',
                    'slugEn' => 'veterinarians',
                    'slugEs' => 'veterinarios',
                ),
            2372 =>
                array (
                    'en' => 'Veterinarians - CT Scan',
                    'es' => 'Veterinarios - Tomografía Computarizada',
                    'slugEn' => 'veterinarians-ct-scan',
                    'slugEs' => 'veterinarios-tomografia-computarizada',
                ),
            2373 =>
                array (
                    'en' => 'Veterinarians - Emergency',
                    'es' => 'Veterinarios - Emergencia',
                    'slugEn' => 'veterinarians-emergency',
                    'slugEs' => 'veterinarios-emergencia',
                ),
            2374 =>
                array (
                    'en' => 'Veterinarians - Laser Therapy - Laser Surgery',
                    'es' => 'Veterinarios - Terapia Láser - Cirugía Láser',
                    'slugEn' => 'veterinarians-laser-therapy-laser-surgery',
                    'slugEs' => 'veterinarios-terapia-laser-cirugia-laser',
                ),
            2375 =>
                array (
                    'en' => 'Veterinarians - Orthopedic Center',
                    'es' => 'Veterinarios - Centro Ortopédico',
                    'slugEn' => 'veterinarians-orthopedic-center',
                    'slugEs' => 'veterinarios-centro-ortopedico',
                ),
            2376 =>
                array (
                    'en' => 'Veterinarians - Products',
                    'es' => 'Veterinarios-Productos',
                    'slugEn' => 'veterinarians-products',
                    'slugEs' => 'veterinarios-productos',
                ),
            2377 =>
                array (
                    'en' => 'Veterinary Medicine - Horses',
                    'es' => 'Medicamentos Veterinarios - Caballos',
                    'slugEn' => 'veterinary-medicine-horses',
                    'slugEs' => 'medicamentos-veterinarios-caballos',
                ),
            2378 =>
                array (
                    'en' => 'Veterinary Vaccines',
                    'es' => 'Veterinario Vacunación',
                    'slugEn' => 'veterinary-vaccines',
                    'slugEs' => 'veterinario-vacunacion',
                ),
            2379 =>
                array (
                    'en' => 'Video - Clubs',
                    'es' => 'Video Clubs',
                    'slugEn' => 'video-clubs',
                    'slugEs' => 'video-clubs',
                ),
            2380 =>
                array (
                    'en' => 'Video - Distributors',
                    'es' => 'Video-Distribuidores',
                    'slugEn' => 'video-distributors',
                    'slugEs' => 'video-distribuidores',
                ),
            2381 =>
                array (
                    'en' => 'Video - Equipment & Supplies',
                    'es' => 'Video-Efectos Y Equipo',
                    'slugEn' => 'video-equipment-supplies',
                    'slugEs' => 'video-efectos-y-equipo',
                ),
            2382 =>
                array (
                    'en' => 'Video - Games',
                    'es' => 'Video Juegos',
                    'slugEn' => 'video-games',
                    'slugEs' => 'video-juegos',
                ),
            2383 =>
                array (
                    'en' => 'Video - Productions',
                    'es' => 'Video-Producciones',
                    'slugEn' => 'video-productions',
                    'slugEs' => 'video-producciones',
                ),
            2384 =>
                array (
                    'en' => 'Video - Repair',
                    'es' => 'Video-Reparaciones',
                    'slugEn' => 'video-repair',
                    'slugEs' => 'video-reparaciones',
                ),
            2385 =>
                array (
                    'en' => 'Video - Services',
                    'es' => 'Video-Servicio',
                    'slugEn' => 'video-services',
                    'slugEs' => 'video-servicio',
                ),
            2386 =>
                array (
                    'en' => 'Video Games - Repair',
                    'es' => 'Video Juegos - Reparaciones',
                    'slugEn' => 'video-games-repair',
                    'slugEs' => 'video-juegos-reparaciones',
                ),
            2387 =>
                array (
                    'en' => 'Video Recording - Services',
                    'es' => 'Video Grabaciones-Servicio',
                    'slugEn' => 'video-recording-services',
                    'slugEs' => 'video-grabaciones-servicio',
                ),
            2388 =>
                array (
                    'en' => 'Vintage',
                    'es' => 'Vintage',
                    'slugEn' => 'vintage',
                    'slugEs' => 'vintage',
                ),
            2389 =>
                array (
                    'en' => 'Virtual Access Control',
                    'es' => 'Control de Acceso Virtual',
                    'slugEn' => 'virtual-access-control',
                    'slugEs' => 'control-de-acceso-virtual',
                ),
            2390 =>
                array (
                    'en' => 'Vision - Therapy',
                    'es' => 'Terapia - Visual',
                    'slugEn' => 'vision-therapy',
                    'slugEs' => 'terapia-visual',
                ),
            2391 =>
                array (
                    'en' => 'Vitamin C Serums',
                    'es' => 'Sueros de Vitamina C',
                    'slugEn' => 'vitamin-c-serums',
                    'slugEs' => 'sueros-de-vitamina-c',
                ),
            2392 =>
                array (
                    'en' => 'Vitamins',
                    'es' => 'Vitaminas',
                    'slugEn' => 'vitamins',
                    'slugEs' => 'vitaminas',
                ),
            2393 =>
                array (
                    'en' => 'Vitamins - Supplements',
                    'es' => 'Vitaminas - Suplementos',
                    'slugEn' => 'vitamins-supplements',
                    'slugEs' => 'vitaminas-suplementos',
                ),
            2394 =>
                array (
                    'en' => 'Wall Bed -Murphy Bed',
                    'es' => 'Cama de Pared (Murphy Bed)',
                    'slugEn' => 'wall-bed-murphy-bed',
                    'slugEs' => 'cama-de-pared-murphy-bed',
                ),
            2395 =>
                array (
                    'en' => 'Warehouses',
                    'es' => 'Almacenes',
                    'slugEn' => 'warehouses',
                    'slugEs' => 'almacenes',
                ),
            2396 =>
                array (
                    'en' => 'Warehouses - Cargo',
                    'es' => 'Almacenes-Carga',
                    'slugEn' => 'warehouses-cargo',
                    'slugEs' => 'almacenes-carga',
                ),
            2397 =>
                array (
                    'en' => 'Washing-Cleaning-Carpet',
                    'es' => 'Lavado-Limpieza-Alfombra',
                    'slugEn' => 'washing-cleaning-carpet',
                    'slugEs' => 'lavado-limpieza-alfombra',
                ),
            2398 =>
                array (
                    'en' => 'Waste - Disposal',
                    'es' => 'Desperdicios-Control',
                    'slugEn' => 'waste-disposal',
                    'slugEs' => 'desperdicios-control',
                ),
            2399 =>
                array (
                    'en' => 'Waste - Disposal - Industrial',
                    'es' => 'Desperdicios Industriales-Control',
                    'slugEn' => 'waste-disposal-industrial',
                    'slugEs' => 'desperdicios-industriales-control',
                ),
            2400 =>
                array (
                    'en' => 'Waste - Vacuum Trucks',
                    'es' => 'Desperdicios - Camiones Vacuum',
                    'slugEn' => 'waste-vacuum-trucks',
                    'slugEs' => 'desperdicios-camiones-vacuum',
                ),
            2401 =>
                array (
                    'en' => 'Waste Containers',
                    'es' => 'Zafacones',
                    'slugEn' => 'waste-containers',
                    'slugEs' => 'zafacones',
                ),
            2402 =>
                array (
                    'en' => 'Waste Oils - Collected and Management',
                    'es' => 'Aceites Usados - Recogido y Manejo',
                    'slugEn' => 'waste-oils-collected-and-management',
                    'slugEs' => 'aceites-usados-recogido-y-manejo',
                ),
            2403 =>
                array (
                    'en' => 'Watches',
                    'es' => 'Relojerías',
                    'slugEn' => 'watches',
                    'slugEs' => 'relojerias',
                ),
            2404 =>
                array (
                    'en' => 'Water - \'Coolers\'',
                    'es' => 'Agua-\'Coolers\'',
                    'slugEn' => 'water-coolers',
                    'slugEs' => 'agua-coolers',
                ),
            2405 =>
                array (
                    'en' => 'Water - Distillers',
                    'es' => 'Destiladores-Agua',
                    'slugEn' => 'water-distillers',
                    'slugEs' => 'destiladores-agua',
                ),
            2406 =>
                array (
                    'en' => 'Water - Equipment & Supplies',
                    'es' => 'Agua - Efectos y Equipos',
                    'slugEn' => 'water-equipment-supplies',
                    'slugEs' => 'agua-efectos-y-equipos',
                ),
            2407 =>
                array (
                    'en' => 'Water - Generators',
                    'es' => 'Agua-Generadores',
                    'slugEn' => 'water-generators',
                    'slugEs' => 'agua-generadores',
                ),
            2408 =>
                array (
                    'en' => 'Water - Samples',
                    'es' => 'Agua - Muestreo',
                    'slugEn' => 'water-samples',
                    'slugEs' => 'agua-muestreo',
                ),
            2409 =>
                array (
                    'en' => 'Water - Treatment',
                    'es' => 'Agua-Tratamiento',
                    'slugEn' => 'water-treatment',
                    'slugEs' => 'agua-tratamiento',
                ),
            2410 =>
                array (
                    'en' => 'Water Delivery Service',
                    'es' => 'Delivery-Servicios - Agua',
                    'slugEn' => 'water-delivery-service',
                    'slugEs' => 'delivery-servicios-agua',
                ),
            2411 =>
                array (
                    'en' => 'Water Distilled',
                    'es' => 'Agua Destilada',
                    'slugEn' => 'water-distilled',
                    'slugEs' => 'agua-destilada',
                ),
            2412 =>
                array (
                    'en' => 'Water Ducts - Parts And Equipment',
                    'es' => 'Acueductos-Efectos Y Equipo',
                    'slugEn' => 'water-ducts-parts-and-equipment',
                    'slugEs' => 'acueductos-efectos-y-equipo',
                ),
            2413 =>
                array (
                    'en' => 'Water Filter - Domestic',
                    'es' => 'Filtros-Agua-Domésticos',
                    'slugEn' => 'water-filter-domestic',
                    'slugEs' => 'filtros-agua-domesticos',
                ),
            2414 =>
                array (
                    'en' => 'Water Filter - Industrial',
                    'es' => 'Filtros-Agua-Industriales',
                    'slugEn' => 'water-filter-industrial',
                    'slugEs' => 'filtros-agua-industriales',
                ),
            2415 =>
                array (
                    'en' => 'Water Heater',
                    'es' => 'Calentadores de Agua',
                    'slugEn' => 'water-heater',
                    'slugEs' => 'calentadores-de-agua',
                ),
            2416 =>
                array (
                    'en' => 'Water Heater - Repair',
                    'es' => 'Calentadores Solares - Reparacion',
                    'slugEn' => 'water-heater-repair',
                    'slugEs' => 'calentadores-solares-reparacion',
                ),
            2417 =>
                array (
                    'en' => 'Water Mineral',
                    'es' => 'Agua Mineral',
                    'slugEn' => 'water-mineral',
                    'slugEs' => 'agua-mineral',
                ),
            2418 =>
                array (
                    'en' => 'Water Pumps',
                    'es' => 'Bombas de Agua',
                    'slugEn' => 'water-pumps',
                    'slugEs' => 'bombas-de-agua',
                ),
            2419 =>
                array (
                    'en' => 'Water Purifiers',
                    'es' => 'Purificadores de Agua',
                    'slugEn' => 'water-purifiers',
                    'slugEs' => 'purificadores-de-agua',
                ),
            2420 =>
                array (
                    'en' => 'Water Slides',
                    'es' => 'Playground Chorreras',
                    'slugEn' => 'water-slides',
                    'slugEs' => 'playground-chorreras',
                ),
            2421 =>
                array (
                    'en' => 'Water Softening - Conditioning Equipment',
                    'es' => 'Agua - Suavizadores',
                    'slugEn' => 'water-softening-conditioning-equipment',
                    'slugEs' => 'agua-suavizadores',
                ),
            2422 =>
                array (
                    'en' => 'Water Spring',
                    'es' => 'Agua Manantial',
                    'slugEn' => 'water-spring',
                    'slugEs' => 'agua-manantial',
                ),
            2423 =>
                array (
                    'en' => 'Water Tank -Cleaning - Repair',
                    'es' => 'Cisternas-Reparación Y Limpieza',
                    'slugEn' => 'water-tank-cleaning-repair',
                    'slugEs' => 'cisternas-reparacion-y-limpieza',
                ),
            2424 =>
                array (
                    'en' => 'Water Tanks',
                    'es' => 'Cisternas de Agua',
                    'slugEn' => 'water-tanks',
                    'slugEs' => 'cisternas-de-agua',
                ),
            2425 =>
                array (
                    'en' => 'Waterblasting',
                    'es' => 'Lavado A Presión',
                    'slugEn' => 'waterblasting',
                    'slugEs' => 'lavado-a-presion',
                ),
            2426 =>
                array (
                    'en' => 'Watersports',
                    'es' => 'Deportes Acuáticos',
                    'slugEn' => 'watersports',
                    'slugEs' => 'deportes-acuaticos',
                ),
            2427 =>
                array (
                    'en' => 'Website Products',
                    'es' => 'Website Products',
                    'slugEn' => 'website-products',
                    'slugEs' => 'website-products',
                ),
            2428 =>
                array (
                    'en' => 'Wedding Coordination',
                    'es' => 'Bodas - Coordinación',
                    'slugEn' => 'wedding-coordination',
                    'slugEs' => 'bodas-coordinacion',
                ),
            2429 =>
                array (
                    'en' => 'Wedding Dresses',
                    'es' => 'Vestidos de Novia',
                    'slugEn' => 'wedding-dresses',
                    'slugEs' => 'vestidos-de-novia',
                ),
            2430 =>
                array (
                    'en' => 'Wedding Photographer',
                    'es' => 'Fotógrafo de bodas',
                    'slugEn' => 'wedding-photographer',
                    'slugEs' => 'fotografo-de-bodas',
                ),
            2431 =>
                array (
                    'en' => 'Weddings',
                    'es' => 'Bodas',
                    'slugEn' => 'weddings',
                    'slugEs' => 'bodas',
                ),
            2432 =>
                array (
                    'en' => 'Weddings & Birthdays',
                    'es' => 'Bodas Y Cumpleaños-Servicios',
                    'slugEn' => 'weddings-birthdays',
                    'slugEs' => 'bodas-y-cumpleanos-servicios',
                ),
            2433 =>
                array (
                    'en' => 'Weight control',
                    'es' => 'Control de Peso',
                    'slugEn' => 'weight-control',
                    'slugEs' => 'control-de-peso',
                ),
            2434 =>
                array (
                    'en' => 'Weight Control - Services',
                    'es' => 'Peso-Control-Servicios',
                    'slugEn' => 'weight-control-services',
                    'slugEs' => 'peso-control-servicios',
                ),
            2435 =>
                array (
                    'en' => 'Weight Reduction',
                    'es' => 'Reducción de Peso',
                    'slugEn' => 'weight-reduction',
                    'slugEs' => 'reduccion-de-peso',
                ),
            2436 =>
                array (
                    'en' => 'Welding',
                    'es' => 'Soldaduras',
                    'slugEn' => 'welding',
                    'slugEs' => 'soldaduras',
                ),
            2437 =>
                array (
                    'en' => 'Welding - Equipment & Supplies',
                    'es' => 'Soldaduras-Efectos Y Equipo',
                    'slugEn' => 'welding-equipment-supplies',
                    'slugEs' => 'soldaduras-efectos-y-equipo',
                ),
            2438 =>
                array (
                    'en' => 'Wesleyan - Church',
                    'es' => 'Iglesias - Wesleyana',
                    'slugEn' => 'wesleyan-church',
                    'slugEs' => 'iglesias-wesleyana',
                ),
            2439 =>
                array (
                    'en' => 'Wheelchair',
                    'es' => 'Silla de Rueda',
                    'slugEn' => 'wheelchair',
                    'slugEs' => 'silla-de-rueda',
                ),
            2440 =>
                array (
                    'en' => 'Whisky Bar',
                    'es' => 'Whisky Bar',
                    'slugEn' => 'whisky-bar',
                    'slugEs' => 'whisky-bar',
                ),
            2441 =>
                array (
                    'en' => 'Wholesalers',
                    'es' => 'Mayoristas',
                    'slugEn' => 'wholesalers',
                    'slugEs' => 'mayoristas',
                ),
            2442 =>
                array (
                    'en' => 'Wigs',
                    'es' => 'Pelucas',
                    'slugEn' => 'wigs',
                    'slugEs' => 'pelucas',
                ),
            2443 =>
                array (
                    'en' => 'Wills',
                    'es' => 'Testamentos',
                    'slugEn' => 'wills',
                    'slugEs' => 'testamentos',
                ),
            2444 =>
                array (
                    'en' => 'Windows',
                    'es' => 'Ventanas',
                    'slugEn' => 'windows',
                    'slugEs' => 'ventanas',
                ),
            2445 =>
                array (
                    'en' => 'Windows - Cleaning',
                    'es' => 'Ventanas-Limpieza',
                    'slugEn' => 'windows-cleaning',
                    'slugEs' => 'ventanas-limpieza',
                ),
            2446 =>
                array (
                    'en' => 'Windows - Cleaning - High Risk',
                    'es' => 'Ventanas - limpieza - Alto Riesgo',
                    'slugEn' => 'windows-cleaning-high-risk',
                    'slugEs' => 'ventanas-limpieza-alto-riesgo',
                ),
            2447 =>
                array (
                    'en' => 'Windows - Glass',
                    'es' => 'Ventanas-Cristal',
                    'slugEn' => 'windows-glass',
                    'slugEs' => 'ventanas-cristal',
                ),
            2448 =>
                array (
                    'en' => 'Windows - Wood',
                    'es' => 'Ventanas-Madera',
                    'slugEn' => 'windows-wood',
                    'slugEs' => 'ventanas-madera',
                ),
            2449 =>
                array (
                    'en' => 'Windows Aluminum',
                    'es' => 'Ventanas Aluminio',
                    'slugEn' => 'windows-aluminum',
                    'slugEs' => 'ventanas-aluminio',
                ),
            2450 =>
                array (
                    'en' => 'Wine Bar',
                    'es' => 'Wine Bar',
                    'slugEn' => 'wine-bar',
                    'slugEs' => 'wine-bar',
                ),
            2451 =>
                array (
                    'en' => 'Wines',
                    'es' => 'Vinos',
                    'slugEn' => 'wines',
                    'slugEs' => 'vinos',
                ),
            2452 =>
                array (
                    'en' => 'Wood-fired ovens',
                    'es' => 'Hornos de Leña',
                    'slugEn' => 'wood-fired-ovens',
                    'slugEs' => 'hornos-de-lena',
                ),
            2453 =>
                array (
                    'en' => 'Woodworking - Equipment & Supplies',
                    'es' => 'Ebanisterías-Efectos Y Equipo',
                    'slugEn' => 'woodworking-equipment-supplies',
                    'slugEs' => 'ebanisterias-efectos-y-equipo',
                ),
            2454 =>
                array (
                    'en' => 'Word Processing',
                    'es' => 'Word Processing (Procesamiento De Palabras)-Servicio',
                    'slugEn' => 'word-processing',
                    'slugEs' => 'word-processing-procesamiento-de-palabras-servicio',
                ),
            2455 =>
                array (
                    'en' => 'Workshop',
                    'es' => 'Taller',
                    'slugEn' => 'workshop',
                    'slugEs' => 'taller',
                ),
            2456 =>
                array (
                    'en' => 'Workshops-Classes',
                    'es' => 'Talleres-Clases',
                    'slugEn' => 'workshops-classes',
                    'slugEs' => 'talleres-clases',
                ),
            2457 =>
                array (
                    'en' => 'Wound Healing',
                    'es' => 'Curación de Heridas',
                    'slugEn' => 'wound-healing',
                    'slugEs' => 'curacion-de-heridas',
                ),
            2458 =>
                array (
                    'en' => 'X-Ray Sales & Service',
                    'es' => 'Rayos X Venta y Servicio',
                    'slugEn' => 'x-ray-sales-service',
                    'slugEs' => 'rayos-x-venta-y-servicio',
                ),
            2459 =>
                array (
                    'en' => 'X-Rays',
                    'es' => 'Rayos X',
                    'slugEn' => 'x-rays',
                    'slugEs' => 'rayos-x',
                ),
            2460 =>
                array (
                    'en' => 'Yellow Pages',
                    'es' => 'Páginas Amarillas',
                    'slugEn' => 'yellow-pages',
                    'slugEs' => 'paginas-amarillas',
                ),
            2461 =>
                array (
                    'en' => 'Yoga Classes',
                    'es' => 'Clases de Yoga',
                    'slugEn' => 'yoga-classes',
                    'slugEs' => 'clases-de-yoga',
                ),
            2462 =>
                array (
                    'en' => 'Yogurt',
                    'es' => 'Yogur',
                    'slugEn' => 'yogurt',
                    'slugEs' => 'yogur',
                ),
            2463 =>
                array (
                    'en' => 'Yogurts',
                    'es' => 'Yogurts',
                    'slugEn' => 'yogurts',
                    'slugEs' => 'yogurts',
                ),
        );
        
        return $categories;
    }

    /**
     * @return array
     */
    public static function getCategoryEnMergeMapping()
    {
        $categories = array (
            'sounders-materials' => 'acustic-materials',
            'elderly-care' => 'elderly-care',
            'aged-people-homes-temporar' => 'aged-people-homes-temporary',
            'agriculture-tools-hardware' => 'tools',
            'aluminium-windows' => 'windows-aluminum',
            'furnished-apartments' => 'apartments-furnished',
            'auto-parts-and-accesories' => 'automobiles-parts-accessories',
            'automobiles-window-tinting' => 'automobiles-tint',
            'carpets-rugs-cleaning' => 'carpets-washing',
            'chiropractic' => 'chiropractors',
            'coffee' => 'coffee-barista',
            'kitchen' => 'kitchens',
            'lab-tests-for-marriage' => 'laboratories-test-for-marriage',
            'opticians-vision-correction' => 'opticians-visual-correction',
            'printing-digital' => 'print-digital',
            'prints' => 'print',
            'roof-aluminum' => 'roofs-aluminum',
            'schools-academy' => 'schools-academies',
            'schools-equipment-supplies' => 'schools-equipments-supplies',
            'technical-college' => 'technical-colleges',
            'tree-pruning' => 'tree-trimming',
            'water-sports' => 'watersports',
            'yoga' => 'yoga-classes',
            'restaurants-asian-food' => 'restaurants-asian',
            'curtains-outdoor' => 'curtains-outdoor',
            'consultants-insurance' => 'consultant-insurance',
            'houses-predesign' => 'house-pre-designed',
            'rehabilitation' => 'physicians-and-surgeons-physiatry-physical-medicine-and-rehabilitation',
            'curtains-canvas' => 'canvas-curtains',
            'canvas-curtains' => 'canvas-curtains',
            'consultants-education' => 'education',
            'natural-products-supermarkets' => 'supermarkets',
            'pools' => 'swimming-pools',
            'stamp-mechanics' => 'mechanical-seals',
            'doctors-centers' => 'medical-centers-services',
            'dentists-specialists-oral-maxillofacial-surgery' => 'dentists-specialists-oral-maxillofacial-surgery',
            'dentists-specialists-ortodoncy-braces' => 'dentists-specialists-orthodontics-braces',
            'medicine-equipment-supplies' => 'medical-equipment-supplies',
            'medical-specialists-hepatology' => 'physicians-and-surgeons-hepatology',
            'medical-specialists-orthopedics-endocrinology-and-diabetes' => 'physicians-and-surgeons-endocrinology-and-diabetes',
            'medical-specialists-surgery-breast' => 'physicians-and-surgeons-breast-surgery',
            'specialists-allergy' => 'physicians-and-surgeons-allergy',
            'medical-specialists-orthopedics' => 'physicians-and-surgeons-orthopedic-medicine',
            'oral-and-maxillofacial-surgery-specialists' => 'dentists-specialists-oral-maxillofacial-surgery',
            'buses-charters-rental' => 'charter-rental',
            'cereals' => 'health-food',
            'corporate-events' => 'events-corporations',
            'baggage' => 'luggage',
            'automobiles-loans' => 'loans',
            'automobiles-batteries' => 'batteries',
            'blasting-explosives' => 'explosives',
            'messenger-service' => 'courier',
            'corrugated-cardboard-pallets' => 'pallets',
            'occupational-therapy' => 'therapy-occupational',
            'water-heaters' => 'solar-heaters',
            'vegetables' => 'fruits-vegetables',
            'buildings-steel' => 'steel',
            'gourmet-coffee' => 'coffee-gourmet',
            'contractors-plumbing' => 'plumbing',
            'christian-music' => 'christian-music',
            'clothing-sports' => 'sports',
            'cold-storage' => 'storage',
            'air-conditioner' => 'air-conditioner',
            'aluminum' => 'aluminum',
            'ambulance' => 'ambulance',
            'auto-parts' => 'auto-parts',
            'bars' => 'bars',
            'beads-stores' => 'beads-stores',
            'clinical-laboratory' => 'clinical-laboratory',
            'condo-hotel' => 'condo-hotel',
            'engineers' => 'engineers',
            'fleet-insurance' => 'fleet-insurance',
            'furniture' => 'furniture',
            'furniture-cleaning' => 'furniture-cleaning',
            'health-food' => 'health-food',
            'immigration-services' => 'immigration-services',
            'interior-design' => 'interior-design',
            'locksmiths' => 'locksmiths',
            'luggage' => 'luggage',
            'mechanics' => 'mechanics',
            'motorcycles' => 'motorcycles',
            'nutritionist' => 'nutritionist',
            'office-modules' => 'office-modules',
            'psychiatry' => 'psychiatry',
            'schools-florists' => 'schools-florists',
            'security-cameras' => 'security-cameras',
            'septic-tanks' => 'septic-tanks',
            'yoga-classes' => 'yoga-classes',
            'beauty' => 'beauty',
            'beauty-salon' => 'beauty',
            'cigarette-store' => 'smoke-shop',
            'no-clasification' => 'unclassified',
            'aged-people-services' => 'elderly-care',
            'homes-aged-people' => 'elderly-care',
        );

        return $categories;
    }

    /**
     * @return array
     */
    public static function getCategoryEsMergeMapping()
    {
        $categories = array (
            'acusticos-materiales' => 'acusticos-materiales',
            'envejecientes-servicios' => 'cuido-de-ancianos',
            'hogares-envejecientes-temporeros' => 'hogares-envejecientes-temporeros',
            'herramientas' => 'herramientas',
            'ventanas-aluminio' => 'ventanas-aluminio',
            'apartamentos-amueblados' => 'apartamentos-amueblados',
            'automoviles-accesorios-y-piezas' => 'automoviles-accesorios-y-piezas',
            'automoviles-tintes' => 'automoviles-tintes',
            'alfombras-lavado' => 'alfombras-lavado',
            'quiropracticos' => 'quiropracticos',
            'cafe-barista' => 'cafe-barista',
            'cocinas' => 'cocinas',
            'laboratorios-pruebas-para-matrimonio' => 'laboratorios-pruebas-para-matrimonio',
            'opticas-correccion-visual' => 'opticas-correccion-visual',
            'imprenta-digital' => 'imprenta-digital',
            'impresion' => 'impresion',
            'techos-aluminio' => 'techos-aluminio',
            'colegios-academias' => 'colegios-academias',
            'escuelas-efectos-y-equipo' => 'escuelas-efectos-y-equipo',
            'colegios-tecnicos' => 'colegios-tecnicos',
            'poda-de-arboles' => 'poda-de-arboles',
            'deportes-acuaticos' => 'deportes-acuaticos',
            'yoga' => 'clases-de-yoga',
            'restaurantes-comida-asiatica' => 'restaurantes-comida-asiatica',
            'cortinas-exterior' => 'cortinas-exterior',
            'consultores-seguros' => 'consultores-seguros',
            'casas-predisenadas' => 'casas-predisenadas',
            'rehabilitacion' => 'rehabilitacion',
            'cortinas-de-lona' => 'cortinas-de-lona',
            'cortinas' => 'cortinas-de-lona',
            'educacion' => 'educacion',
            'supermercados' => 'supermercados',
            'piscinas' => 'piscinas',
            'sellos-mecanicos' => 'sellos-mecanicos',
            'medicos-centros' => 'medicos-centros',
            'dentistas-especialistas-cirugia-oral-y-maxilofacial' => 'dentistas-especialistas-cirugia-oral-y-maxilofacial',
            'dentistas-especialistas-ortodoncia-braces' => 'dentistas-especialistas-ortodoncia-braces',
            'facial' => 'dentistas-especialistas-cirugia-oral-y-maxilofacial',
            'medicos-efectos-y-equipo' => 'medicos-efectos-y-equipo',
            'medicos-especialistas-hepatologia' => 'medicos-especialistas-hepatologia',
            'medicos-especialistas-endocrinologia-y-diabetes' => 'medicos-especialistas-endocrinologia-y-diabetes',
            'medicos-especialistas-cirugia-senos' => 'medicos-especialistas-cirugia-senos',
            'medicos-especialistas-alergia' => 'medicos-especialistas-alergia',
            'medicos-especialistas-ortopedia' => 'medicos-especialistas-ortopedia',
            'hogares-envejecientes' => 'cuido-de-ancianos',
            'guaguas-alquiler' => 'guaguas-alquiler',
            'alimentos-salud' => 'alimentos-salud',
            'eventos-corporativos' => 'eventos-corporativos',
            'maletas' => 'equipaje',
            'prestamos' => 'prestamos',
            'baterias' => 'baterias',
            'explosivos' => 'explosivos',
            'mensajeros' => 'mensajeros',
            'paletas' => 'paletas',
            'terapia-ocupacional' => 'terapia-ocupacional',
            'calentadores-solares' => 'calentadores-solares',
            'vegetales' => 'vegetales',
            'acero' => 'acero',
            'cafe-gourmet' => 'cafe-gourmet',
            'plomeria' => 'plomeria',
            'musica' => 'cristiano-musica',
            'deportes' => 'deportes',
            'almacenaje' => 'almacenaje',
            'aire-acondicionado-accesorios-y-piezas' => 'aire-acondicionado',
            'aluminio-manufactura' => 'aluminio',
            'ambulancia' => 'ambulancias',
            'venta-de-piezas' => 'automoviles-piezas',
            'piezas-de-autos' => 'automoviles-piezas',
            'barras' => 'bares',
            'tiendas-de-beads' => 'beads-tiendas',
            'laboratorio-clinico' => 'laboratorios-clinicos',
            'cuidado-ancianos' => 'cuido-de-ancianos',
            'condo-hotel' => 'condominio-hotel',
            'ingenieros-y-agrimensores' => 'ingenieros',
            'seguros-para-flotas' => 'seguro-para-flotas',
            'muebles' => 'mueblerias',
            'limpieza-de-muebles' => 'muebles-limpieza',
            'comida-saludable' => 'alimentos-salud',
            'servicios-personales-agencias' => 'inmigracion-servicio',
            'diseno-de-interiores' => 'decoracion-interiores',
            'cerrajeros-llaves' => 'cerrajeros',
            'mecanico' => 'mecanica',
            'motocicletas' => 'motoras',
            'nutricionista-dietista' => 'nutricionista',
            'modulos-de-oficina' => 'oficinas-modulos',
            'psiquiatria' => 'siquiatria',
            'escuelas-floristeria' => 'colegios-floristerias',
            'camaras-de-seguridad' => 'seguridad-camaras',
            'pozos-septicos' => 'tanques-acero',
            'musica-cristiana' => 'cristiano-musica',
            'belleza-productos' => 'beauty',
            'beauty' => 'beauty',
            'smoke-shop' => 'tabaqueria-tabaco',
            'sin-classificacion' => 'unclassified',
        );

        return $categories;
    }
}
