<?php

namespace Meniam\Bundle\CoreBundle\Service\Pager\Simple;

class Template extends \Pagerfanta\View\Template\Template
{
    static protected $defaultOptions = array(
        'prev_message'        => '&larr; Назад',
        'next_message'        => 'Дальше &rarr;',
        'dots_message'        => '&hellip;',
        'active_suffix'       => '',
        'css_container_class' => 'ectlg-pagination',
        'css_prev_class'      => 'ectlg-pagination-prev',
        'css_next_class'      => 'ectlg-pagination-next',
        'css_disabled_class'  => 'ectlg-pagination-disabled',
        'css_dots_class'      => 'ectlg-pagination-dots',
        'css_active_class'    => 'ectlg-pagination-active',
    );

    public function __construct()
    {
        parent::__construct();
        $this->setOptions(array('proximity' => 4, 'active_suffix' => '<span class="sr-only">(текущая)</span>'));
    }

    public function container()
    {
        return sprintf('<div class="%s">%%pages%%</div>',
            $this->option('css_container_class')
        );
    }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        $class = null;

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    private function pageWithTextAndClass($page, $text, $class)
    {
        if ($page == 1) {
            $page = null;
        }
        $href = $this->generateRoute($page);

        return $this->linkLi($class, $href, $text);
    }

    public function previousDisabled()
    {
        $class = $this->previousDisabledClass();
        $text = $this->option('prev_message');

        return $this->spanLi($class, $text);
    }

    private function previousDisabledClass()
    {
        return $this->option('css_prev_class').' '.$this->option('css_disabled_class');
    }

    public function previousEnabled($page)
    {
        $text = $this->option('prev_message');
        $class = $this->option('css_prev_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function nextDisabled()
    {
        $class = $this->nextDisabledClass();
        $text = $this->option('next_message');

        return $this->spanLi($class, $text);
    }

    private function nextDisabledClass()
    {
        return $this->option('css_next_class').' '.$this->option('css_disabled_class');
    }

    public function nextEnabled($page)
    {
        $text = $this->option('next_message');
        $class = $this->option('css_next_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function first()
    {
        return $this->page(1);
    }

    public function last($page)
    {
        return $this->page($page);
    }

    public function current($page)
    {
        $text = trim($page.' '.$this->option('active_suffix'));
        $class = $this->option('css_active_class');

        return $this->spanLi($class, $text);
    }

    public function separator()
    {
        $class = $this->option('css_dots_class');
        $text = $this->option('dots_message');

        return $this->spanLi($class, $text);
    }

    private function linkLi($class, $href, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<a%s href="%s">%s</a>', $liClass, $href, $text);
    }

    private function spanLi($class, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<span%s>%s</span>', $liClass, $text);
    }

}
