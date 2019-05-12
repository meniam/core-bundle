<?php

namespace Meniam\Bundle\CoreBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TsvectorType extends Type
{
    public function getName()
    {
        return 'tsvector';
    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "TSVECTOR";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('to_tsvector(%s)', $sqlExpr);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_array($value)) {
            $value = implode(" ", $value);
        }
        return $value;
    }
}