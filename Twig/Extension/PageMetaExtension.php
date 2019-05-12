<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use Meniam\Bundle\CoreBundle\Service\PageMeta;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\PageMetaJavascriptTokenParser;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\PageMetaStorageTokenParser;
use Meniam\Bundle\CoreBundle\Twig\TokenParser\PageMetaStyleTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PageMetaExtension extends AbstractExtension
{
    /**
     * @var PageMeta
     */
    private $pageMeta;

    public function __construct(PageMeta $pageMeta)
    {
        $this->pageMeta = $pageMeta;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('page_meta_asset_add_style', array($this, 'assetAddStyle'), ['is_safe' => ['html']]),
            new TwigFunction('page_meta_asset_style_links', array($this, 'assetStyleLinks'), ['is_safe' => ['html']]),
            new TwigFunction('page_meta_asset_remove_style', array($this, 'assetRemoveStyle'), ['is_safe' => ['html']]),

            new TwigFunction('page_meta_asset_add_javascript', array($this, 'assetAddJavascript'), ['is_safe' => ['html']]),
            new TwigFunction('page_meta_asset_javascript_links', array($this, 'assetJavascriptLinks'), ['is_safe' => ['html']]),
            new TwigFunction('page_meta_asset_remove_javascript', array($this, 'assetRemoveJavascript'), ['is_safe' => ['html']]),

            new TwigFunction('page_meta_javascript_show', array($this, 'javascriptShow'), ['is_safe' => ['html']]),
            new TwigFunction('page_meta_style_show', array($this, 'stylesShow'), ['is_safe' => ['html']]),
            new TwigFunction('page_meta_get_from_storage', array($this, 'getFromStorage'), ['is_safe' => ['html']]),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new PageMetaJavascriptTokenParser(),
            new PageMetaStyleTokenParser(),
            new PageMetaStorageTokenParser(),
        ];
    }

    public function assetAddStyle($link, $packageName = null, $group = 'default', $media = 'screen')
    {
        $this->pageMeta->addStyle($link, $packageName, $group, $media);
    }

    public function assetRemoveStyle($link, $packageName = null, $group = 'default', $media = 'screen')
    {
        $this->pageMeta->removeStyle($link, $packageName, $group, $media);
    }

    public function assetStyleLinks($group = 'default')
    {
        return $this->pageMeta->getStyleLinks($group);
    }

    public function assetAddJavascript($link, $packageName = null, $group = 'default', $type = 'text/javascript')
    {
        $this->pageMeta->addJavascript($link, $packageName, $group, $type);
    }

    public function assetRemoveJavascript($link, $packageName = null, $group = 'default', $type = 'text/javascript')
    {
        $this->pageMeta->removeJavascript($link, $packageName, $group, $type);
    }

    public function assetJavascriptLinks($group = 'default')
    {
        if (!$group) $group = 'default';
        return $this->pageMeta->getJavascriptLinks($group);
    }

    public function addToStorage($text, $params)
    {
        $group = 'default';
        if (isset($params['group'])) {
            $group = $params['group'];
        }
        $this->pageMeta->addToStorage($text, $group);
        return '';
    }

    public function getFromStorage($group = 'default')
    {
        return implode("\n", $this->pageMeta->getFromStorage($group));
    }

    public function javascriptShow($group = 'default')
    {
        if (!$group) $group = 'default';
        $group = 'javascript_' . str_replace('javascript_', '', $group);
        if (!$items = $this->pageMeta->getFromStorage($group)) {
            return '';
        }

        $result = "<script type=\"text/javascript\">\n";
        foreach ($this->pageMeta->getFromStorage($group) as $item) {
            $item = preg_replace('#^\s*<script[^\>]*>#usi', '', $item);
            $item = preg_replace('#</script[^\>]*>\s*$#usi', '', $item);
            $result .= trim($item) . "\n";

        }
        $result .= "</script>";

        return $result;
    }

    public function stylesShow($group)
    {
        $group = 'styles_' . str_replace('styles_', '', $group);
        return implode("\n", $this->pageMeta->getFromStorage($group));
    }
}