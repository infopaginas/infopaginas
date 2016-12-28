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
