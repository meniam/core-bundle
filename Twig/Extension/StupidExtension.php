<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use Meniam\Bundle\CoreBundle\Twig\TokenParser\MarkdownTokenParser;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Meniam\Bundle\CoreBundle\Filter\Rule\MarkdownWithHtml;
use Meniam\Bundle\CoreBundle\Filter\Rule\Typographics;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\NoindexTokenParser;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\SwitchTokenParser;
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

            new TwigFilter('typo', [$this, 'getTypoFilter']),

            new TwigFilter('truncate', [StringUtil::class, 'safeTruncate']),
            new TwigFilter('truncate_html', [StringUtil::class, 'safeTruncateHtml']),

            new TwigFilter('markdown', [StringUtil::class, 'safeTruncate'], ['is_safe' => ['all']]),

            # Arrays
            new TwigFilter('fieldName', [$this, 'fieldNameFilter']),
            new TwigFilter('array_unique', 'array_unique'),

            # Hash and decodes
            new TwigFilter('sha1',          [$this, 'sha1Filter']),
            new TwigFilter('md5',           [$this, 'md5Filter']),
            new TwigFilter('base64_encode', [$this, 'base64EncodeFilter']),
            new TwigFilter('base64_decode', [$this, 'base64DecodeFilter']),
            new TwigFilter('yaml_encode',   [$this, 'yamlEncodeFilter']),
            new TwigFilter('yaml_decode',   [$this, 'yamlDecodeFilter']),

            # Pretty
            new TwigFilter('file_pretty_size', array($this, 'filePrettySizeFilter')),
            new TwigFilter('file_pretty_image_size', array($this, 'filePrettyImageSizeFilter')),

            // Casts
            new TwigFilter('string', [$this, 'stringFilter']),
            new TwigFilter('int', [$this, 'intFilter'], ['is_safe' => ['all']]),
            new TwigFilter('bool', [$this, 'boolFilter']),
            new TwigFilter('float', [$this, 'floatFilter'], ['is_safe' => ['all']]),
            new TwigFilter('array', [$this, 'arrayFilter']),

            // System
            new TwigFilter('basename', 'basename'),
            new TwigFilter('dirname', 'dirname'),
            new TwigFilter('print_r', 'print_r'),
        ];
    }

    public function getFunctions()
    {
        return array(
            # markdown
            new TwigFunction('markdown', array($this, 'markdown'), ['is_safe' => ['all']]),

            new TwigFilter('typo', [$this, 'getTypoFilter']),


            # storage
            new TwigFunction('put_to_storage', array($this, 'putToStorage'), ['is_safe' => ['all']]),
            new TwigFunction('get_from_storage', array($this, 'getFromStorage'), ['is_safe' => ['all']]),

            # path, url
            new TwigFunction('pathStarts', array($this, 'pathStarts'), ['is_safe' => ['all']]),
            new TwigFunction('pathEquals', array($this, 'pathEquals'), ['is_safe' => ['all']]),

            new TwigFunction('spacer', array($this, 'spacerFilter'), ['is_safe' => ['all']]),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new NoindexTokenParser(),
            new MarkdownTokenParser(),
            new SwitchTokenParser()
        ];
    }

    public function yamlEncodeFilter($data, $inline = 10)
    {
        return YamlUtil::encode($data, $inline);
    }

    public function yamlDecodeFilter($data)
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

    public function sha1Filter($str)
    {
        return sha1((string)$str);
    }

    public function md5Filter($str)
    {
        return md5((string)$str);
    }

    public function base64EncodeFilter($str)
    {
        return base64_encode((string)$str);
    }

    public function base64DecodeFilter($str)
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

    public function spacerAction($width, $tag = 'div')
    {
        $width = (preg_match('#^\d+$#', $width)) ? $width . 'px' : $width;
        return sprintf('<%s style="width:%s"></%s>', $tag, $width, $tag);
    }

    public function getTypoFilter($str)
    {
        return FilterStatic::filterValue($str, Typographics::class);
    }

    /**
     * Filters field name by changing dot notation into array notation.
     * @param  string $str
     * @return string
     */
    public function fieldNameFilter($str)
    {
        $path = explode('.', rtrim($str, '.'));
        return array_shift($path) . ($path ? '[' . implode('][', $path) . ']' : '');
    }

    public function getName()
    {
        return 'stupid_extension';
    }
}