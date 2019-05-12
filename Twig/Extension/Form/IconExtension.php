<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension\Form;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class IconExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $tag;

    public function __construct($iconTag = 'i')
    {
        $this->tag = $iconTag;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('parse_icons', [$this, 'parseIconsFilter'], ['pre_escape' => 'html', 'is_safe' => array('html')])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('icon', [$this, 'getIconFilter'], ['pre_escape' => 'html', 'is_safe' => array('html')])
        ];
    }

    public function parseIconsFilter($text)
    {
        $that = $this;

        $test = substr($text, 0, 5);
        if (in_array($test, ['.fal ', '.fas ', '.far ', '.fab '])) {
            return sprintf('<%1$s class="%2$s"></%1$s>', $this->tag, substr($text, 1));
        }

        return preg_replace_callback(
            '/\.((?:[a-z]+)-(?:[a-z0-9+-]+))/',
            function ($matches) use ($that) {
                return $that->getIconFilter($matches[1]);
            },
            $text
        );
    }

    public function getIconFilter($icon)
    {
        if (strpos($icon, '-')) {
            list($namespace, $iconName) = explode('-', $icon, 2);
        } else {
            $iconName = $icon;
            $namespace = null;
        }

        switch ($namespace) {
            case 'fa':
                $class = 'fal fa-' . $iconName;
                break;
            case 'far':
                $class = 'far fa-' . $iconName;
                break;
            case 'fas':
                $class = 'fas fa-' . $iconName;
                break;
            case 'fal':
                $class = 'fal fa-' . $iconName;
                break;
            case 'fab':
                $class = 'fab fa-' . $iconName;
                break;
            default:
                $class = 'fal fa-' . $icon;
                break;
        }

        return sprintf('<%1$s class="%2$s"></%1$s>', $this->tag, $class);
    }
}
