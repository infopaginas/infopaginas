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
            self::CODE_PLUMBERS,
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
        return [
            self::CODE_SOLICITORS => [
                'en' => 'Solicitors',
                'es' => 'Solicitors',
            ],
            self::CODE_BUILDERS => [
                'en' => 'Builders',
                'es' => 'Builders',
            ],
            self::CODE_PHARMACIES => [
                'en' => 'Pharmacies',
                'es' => 'Pharmacies',
            ],
            self::CODE_ELECTRICIANS => [
                'en' => 'Electricians',
                'es' => 'Electricians',
            ],
            self::CODE_PLUMBERS => [
                'en' => 'Plumbers',
                'es' => 'Plumbers',
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
                'es' => 'Florists',
            ],
            self::CODE_BEAUTY_SALONS => [
                'en' => 'Beauty salons',
                'es' => 'Beauty salons',
            ],
            self::CODE_HAIRDRESSERS => [
                'en' => 'Hairdressers',
                'es' => 'Hairdressers',
            ],
            self::CODE_DOCTORS => [
                'en' => 'Doctors',
                'es' => 'Doctors',
            ],
        ];
    }

    /**
     * Used to load default fixtures
     *
     * @return array
     */
    public static function getOtherCategoriesNames()
    {
        return [
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
                'en' => 'Medical',
                'es' => 'Salud',
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
