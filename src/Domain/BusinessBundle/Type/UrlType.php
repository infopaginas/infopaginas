<?php


namespace Domain\BusinessBundle\Type;


class UrlType
{
    const URL = 'url';

    public function getName()
    {
        return self::URL;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {

    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {

    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {

    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function convertToPHPValueSQL($sqlExpr, AbstractPlatform $platform)
    {

    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {

    }
}