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
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        list($longitude, $latitude) = sscanf($value, 'POINT(%f %f)');

        return new Point($latitude, $longitude);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Point) {
            $value = sprintf('POINT(%F %F)', $value->getLongitude(), $value->getLatitude());
        }

        return $value;
    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function convertToPHPValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('AsText(%s)', $sqlExpr);
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('PointFromText(%s)', $sqlExpr);
    }
}