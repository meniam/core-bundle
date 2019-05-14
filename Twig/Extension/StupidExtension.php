<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\MarkdownTokenParser;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Meniam\Bundle\CoreBundle\Filter\Rule\MarkdownWithHtml;
use Meniam\Bundle\CoreBundle\Filter\Rule\Typographics;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\NoindexTokenParser;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\SwitchTokenParser;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\TypoTokenParser;
use Meniam\Bundle\CoreBundle\Util\StringUtil;
use Meniam\Bundle\CoreBundle\Util\YamlUtil;
use Symfony\Component\HttpFoundation\File\File;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class StupidExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $storage = [];

    public function getFilters()
    {
        return [
            # Strings
            new TwigFilter('lcfirst', [StringUtil::class, 'lcfirst']),
            new TwigFilter('ucfirst', [StringUtil::class, 'ucfirst']),
            new TwigFilter('ucwords', [StringUtil::class, 'ucwords']),

            new TwigFilter('ltrim', ['ltrim']),
            new TwigFilter('rtrim', ['rtrim']),

            new TwigFilter('contains', [StringUtil::class, 'contains']),
            new TwigFilter('ends_with', [StringUtil::class, 'endsWith']),
            new TwigFilter('starts_with', [StringUtil::class, 'startsWith']),

            new TwigFilter('typo',        [$this, 'typo']),

            new TwigFilter('truncate', [StringUtil::class, 'safeTruncate']),
            new TwigFilter('truncate_html', [StringUtil::class, 'safeTruncateHtml']),

            new TwigFilter('markdown', [StringUtil::class, 'safeTruncate'], ['is_safe' => ['all']]),

            # Arrays
            new TwigFilter('fieldName', [$this, 'fieldNameFilter']),
            new TwigFilter('array_unique', 'array_unique'),

            # Hash and decodes
            new TwigFilter('sha1',          [$this, 'sha1']),
            new TwigFilter('md5',           [$this, 'md5']),
            new TwigFilter('base64_encode', [$this, 'base64Encode']),
            new TwigFilter('base64_decode', [$this, 'base64Decode']),
            new TwigFilter('yaml_encode',   [$this, 'yamlEncode']),
            new TwigFilter('yaml_decode',   [$this, 'yamlDecode']),
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
            new TwigFilter('json_encode', [$this, 'jsonEncode']),

            # Pretty
            new TwigFilter('file_pretty_size', array($this, 'filePrettySizeFilter')),
            new TwigFilter('file_pretty_image_size', array($this, 'filePrettyImageSizeFilter')),

            // Casts
            new TwigFilter('string', [$this, 'stringFilter']),
            new TwigFilter('int',    [$this, 'intFilter']),
            new TwigFilter('bool',   [$this, 'boolFilter']),
            new TwigFilter('float',  [$this, 'floatFilter']),
            new TwigFilter('array',  [$this, 'arrayFilter']),

            // System
            new TwigFilter('basename', 'basename'),
            new TwigFilter('dirname', 'dirname'),
            new TwigFilter('print_r', 'print_r'),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('markdown', [$this, 'markdown'], ['is_safe' => ['all']]),
            new TwigFunction('typo',     [$this, 'typo']),

            # storage
            new TwigFunction('put_to_storage',   [$this, 'putToStorage'], ['is_safe' => ['all']]),
            new TwigFunction('get_from_storage', [$this, 'getFromStorage'], ['is_safe' => ['all']]),

            new TwigFunction('spacer',           [$this, 'spacer'], ['is_safe' => ['all']]),

            # System
            new TwigFunction('array_key_exists', 'array_key_exists'),
            new TwigFunction('array_unique',     'array_unique'),
            new TwigFunction('print_r',          'print_r'),
            new TwigFunction('range',            'range'),
            new TwigFunction('pathinfo',         'pathinfo'),

            new TwigFunction('is_ajax_request', [$this, 'isAjaxRequest']),
            new TwigFunction('array_intersect', [$this, 'arrayIntersect']),

            new TwigFunction('get_cookie', [$this, 'getCookie']),

            # Hash and decodes
            new TwigFunction('sha1',          [$this, 'sha1']),
            new TwigFunction('md5',           [$this, 'md5']),
            new TwigFunction('base64_encode', [$this, 'base64Encode']),
            new TwigFunction('base64_decode', [$this, 'base64Decode']),
            new TwigFunction('yaml_encode',   [$this, 'yamlEncode']),
            new TwigFunction('yaml_decode',   [$this, 'yamlDecode']),
            new TwigFunction('json_decode',   [$this, 'jsonDecode']),
            new TwigFunction('json_encode',   [$this, 'jsonEncode']),

            // Casts
            new TwigFunction('string', [$this, 'stringFilter']),
            new TwigFunction('int',    [$this, 'intFilter']),
            new TwigFunction('bool',   [$this, 'boolFilter']),
            new TwigFunction('float',  [$this, 'floatFilter']),
            new TwigFunction('array',  [$this, 'arrayFilter']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new NoindexTokenParser(),
            //new MarkdownTokenParser(),
            //new TypoTokenParser(),
            //new SwitchTokenParser()
        ];
    }

    public function yamlEncode($data, $inline = 10)
    {
        return YamlUtil::encode($data, $inline);
    }

    public function yamlDecode($data)
    {
        return YamlUtil::decode($data);
    }

    public function markdown($text)
    {
        return FilterStatic::filterValue($text, MarkdownWithHtml::class);
    }

    public function noindex($text)
    {
        $lines = [];
        $lines[] = '<!--googleoff: all-->';
        $lines[] = '<!--noindex-->';
        $lines[] = $text;
        $lines[] = '<!--/noindex-->';
        $lines[] = '<!--googleon: all-->';
        return implode("\n", $lines);
    }

    public function putToStorage($var, $value)
    {
        $this->storage[$var] = $value;
    }

    public function getFromStorage($var, $default = null)
    {
        return isset($this->storage[$var]) ? $this->storage[$var] : $default;
    }

    public function stringFilter($input)
    {
        return (string) $input;
    }

    public function intFilter($input)
    {
        return (int) $input;
    }

    public function boolFilter($input)
    {
        return (bool) $input;
    }

    public function floatFilter($input)
    {
        return (float) $input;
    }

    public function arrayFilter($input)
    {
        return (array) $input;
    }

    public function sha1($str)
    {
        return sha1((string)$str);
    }

    public function md5($str)
    {
        return md5((string)$str);
    }

    public function base64Encode($str)
    {
        return base64_encode((string)$str);
    }

    public function base64Decode($str)
    {
        return base64_decode((string)$str);
    }

    public function filePrettySizeFilter(File $file)
    {
        $bytes = $file->getSize();

        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2).' '.$unit[(int)$i];
    }

    public function filePrettyImageSizeFilter(File $file)
    {
        if (in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'ico']) && is_file($file->getRealPath())) {
            $imageInfo = getimagesize($file->getRealPath());
            $type = null;
            if (isset($imageInfo[0])) {
                if (isset($imageInfo['mime']) && ($mime = $imageInfo['mime']) && preg_match('#^image/#', $mime)) {
                    $type = strtoupper(str_replace('image/', '', $mime));
                }

                if ($type) {
                    return sprintf('%s@%sx%spx', $type, $imageInfo[0], $imageInfo[1]);
                } else {
                    return sprintf('%sx%spx', $imageInfo[0], $imageInfo[1]);
                }
            }
        }

        return '';
    }

    public function spacer($width, $tag = 'div')
    {
        $width = (preg_match('#^\d+$#', $width)) ? $width . 'px' : $width;
        return sprintf('<%s style="width:%s"></%s>', $tag, $width, $tag);
    }

    public function typo($str)
    {
        return FilterStatic::filterValue($str, Typographics::class);
    }

    public function isAjaxRequest()
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    public function fieldNameFilter($str)
    {
        $path = explode('.', rtrim($str, '.'));
        return array_shift($path) . ($path ? '[' . implode('][', $path) . ']' : '');
    }

    public function getCookie($key)
    {
        return filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_STRING);
    }

    public function jsonDecode($str, $assoc = false, $depth = 512, $options = 0)
    {
        return json_decode(html_entity_decode($str), $assoc, $depth, $options);
    }

    public function jsonEncode($str)
    {
        return json_encode($str, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Wrapper for array_intersect() method
     *
     * @param array $array1
     * @param array $array2
     * @return array|ArrayCollection
     */
    public function arrayIntersect($array1, $array2)
    {
        if ($array1 instanceof ArrayCollection && $array2 instanceof ArrayCollection) {
            return new ArrayCollection(
                array_merge($array1->toArray(), $array1->toArray())
            );
        }

        return array_intersect($array1, $array2);
    }
}