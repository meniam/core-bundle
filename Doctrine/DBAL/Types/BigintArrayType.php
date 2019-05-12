<?php

namespace Meniam\Bundle\CoreBundle\Doctrine\DBAL\Types;

use InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class BigintArrayType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'bigint[]';
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return array();
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;
        return explode('","', trim($value, '{""}') );
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        settype($value, 'array'); // can be called with a scalar or array
        foreach ($value as $t) {
            if (!is_numeric($t)) {
                throw new InvalidArgumentException(sprintf('%s is not a properly numeric.', $t));
            }
        }

        return '{' . implode(",", $value) . '}';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bigint[]';
    }
}