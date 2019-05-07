<?php

namespace Meniam\Bundle\CoreBundle\Service\Pager\Simple;

class SimpleTemplate extends \Pagerfanta\View\Template\Template
{
    static protected $defaultOptions = array(
        'dots_message'        => '&hellip;',
        'active_suffix'       => '<span class="sr-only">(текущая)</span>',
    );

    public function __construct()
    {
        parent::__construct();
        $this->setOptions(array('proximity' => 4, 'max_index_pages' => 100, 'hide_next_page_button' => false, 'next_page_button_prepend' => ''));
    }

    public function beginPagesUl()
    {
        return '<ul class="pages">';
    }

    public function endPagesUl()
    {
        return '</ul>';
    }

    public function container()
    {
        return '<div class="pager">%pages%</div>';
    }

    public function page($page, $isHidden = false)
    {
        $hiddenClass = $isHidden ? ' hidden-xs' : '';
        $text = '<span class="sr-only">Страница №</span>' . $page;
        $href = $this->generateRoute($page==1?null:$page);

        $maxIndexPages = $this->option('max_index_pages');

        $rel = ($page > $maxIndexPages) ? ' rel="noindex,nofollow"' : '';

        return '<li class="page'.$hiddenClass.'"><a'.$rel.' href="'.$href.'">'.$text.'</a></li>';
    }

    public function nextEnabled($page)
    {
        $showNextPageButton = $this->option('hide_next_page_button');
        if ($showNextPageButton) return '';

        $href = $this->generateRoute($page == 1?null:$page);
        $maxIndexPages = $this->option('max_index_pages');
        $rel = ($page > $maxIndexPages) ? ' rel="noindex,nofollow"' : '';

        $nextPageButtonPrepend = $this->option('next_page_button_prepend');

        return "<div class=\"next-page\">{$nextPageButtonPrepend}<a$rel href=\"{$href}\" class=\"pager-next-link btn btn-lg btn-main\">Следующая страница &rarr;</a></div>";
    }

    public function first()
    {
        return $this->page(1, true);
    }

    public function last($page)
    {
        return $this->page($page, true);
    }

    public function current($page)
    {
        $text = '<span class="sr-only">Страница №</span>' . trim($page.' '.$this->option('active_suffix'));
        return '<li class="page page-active"><span>'.$text.'</span></li>';
    }

    public function separator()
    {
        return '<li class="pager-dots hidden-xs">&#133;</li>';
    }

    /**
     * Renders a given page with a specified text.
     *
     * @param int    $page
     * @param string $text
     *
     * @return string
     */
    public function pageWithText($page, $text)
    {
        return '';
    }

    public function previousDisabled()
    {
        return '';
    }

    public function previousEnabled($page)
    {
        return '';
    }

    public function nextDisabled()
    {
        return '';
    }

}
