<?php

namespace Meniam\Bundle\CoreBundle\Util;

use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml as YamlParser;

class YamlUtil
{
    /**
     * {@inheritdoc}
     * @see FileFormatterInterface::encode()
     */
    public static function encode($data, $inline = 2, $indent = 4)
    {
        try {
            return YamlParser::dump(
                $data,
                $inline,
                $indent,
                YamlParser::DUMP_EXCEPTION_ON_INVALID_TYPE
            );
        } catch (DumpException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     * @see FileFormatterInterface::decode()
     */
    public static function decode($data)
    {
        try {
            return (array) YamlParser::parse($data);
        } catch (ParseException $e) {
            return null;
        }
    }
}
