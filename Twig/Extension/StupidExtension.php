<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Meniam\Bundle\CoreBundle\Filter\Rule\Typographics;
use Meniam\Bundle\CoreBundle\Traits\StringTrait;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\NoindexTokenParser;
use Symfony\Component\HttpFoundation\File\File;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class StupidExtension extends AbstractExtension
{
    use StringTrait;

    /**
     * @var array
     */
    private $storage = [];


    public function getFilters()
    {
        return [
            # Strings
            new TwigFilter('lcfirst', array($this, 'lcfirst')),
            new TwigFilter('ucfirst', array($this, 'ucfirst')),
            new TwigFilter('ucwords', array($this, 'ucwords')),

            new TwigFilter('ltrim', [$this, 'ltrimFilter']),
            new TwigFilter('rtrim', [$this, 'rtrimFilter']),

            new TwigFilter('contains', [$this, 'containsFilter']),
            new TwigFilter('ends_with', [$this, 'endsWithFilter']),
            new TwigFilter('starts_with', [$this, 'startsWithFilter']),

            new TwigFilter('typo', [$this, 'getTypoFilter']),

            # Arrays
            new TwigFilter('fieldName', [$this, 'fieldNameFilter']),

            # Hash
            new TwigFilter('sha1', array($this, 'sha1Filter')),
            new TwigFilter('md5', array($this, 'md5Filter')),
            new TwigFilter('base64_encode', [$this, 'base64EncodeFilter']),
            new TwigFilter('base64_decode', [$this, 'base64DecodeFilter']),

            # Pretty
            new TwigFilter('file_pretty_size', array($this, 'filePrettySizeFilter')),
            new TwigFilter('file_pretty_image_size', array($this, 'filePrettyImageSizeFilter')),

            // Casts
            new TwigFilter('string', [$this, 'stringFilter']),
            new TwigFilter('int', [$this, 'intFilter'], ['is_safe' => ['all']]),
            new TwigFilter('bool', [$this, 'boolFilter']),
            new TwigFilter('float', [$this, 'floatFilter'], ['is_safe' => ['all']]),
            new TwigFilter('array', [$this, 'arrayFilter']),

        ];
    }

    public function getFunctions()
    {
        return array(
            # storage
            new TwigFunction('put_to_storage', array($this, 'putToStorage'), ['is_safe' => ['all']]),
            new TwigFunction('get_from_storage', array($this, 'getFromStorage'), ['is_safe' => ['all']]),

            # path, url
            new TwigFunction('pathStarts', array($this, 'pathStarts'), ['is_safe' => ['all']]),
            new TwigFunction('pathEquals', array($this, 'pathEquals'), ['is_safe' => ['all']]),
            new TwigFunction('path_save_get', array($this, 'pathSaveGet'), ['is_safe' => ['all']]),

            new TwigFunction('spacer', array($this, 'spacerFilter'), ['is_safe' => ['all']]),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new NoindexTokenParser()
        ];
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

    public function containsFilter($haystack, $needle, $caseSensitive = true)
    {
        if (empty($needle)) return $haystack;
        return $this->contains($haystack, $needle, $caseSensitive);
    }

    public function endsWithFilter($haystack, $needle, $caseSensitive = true)
    {
        return $this->endsWith($haystack, $needle, $caseSensitive);
    }

    public function startsWithFilter($haystack, $needle, $caseSensitive = true)
    {
        return $this->startsWith($haystack, $needle, $caseSensitive);
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

    public function ltrimFilter($value, $chars = null)
    {
        return ltrim($value, $chars);
    }

    public function rtrimFilter($value, $chars = null)
    {
        return rtrim($value, $chars);
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