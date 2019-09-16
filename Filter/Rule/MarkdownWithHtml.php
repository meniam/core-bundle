<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use League\CommonMark\CommonMarkConverter;

class MarkdownWithHtml extends HtmlSanitize
{
    /** @var CommonMarkConverter */
    private $markdownParser;

    public function filter($value)
    {
        if (!$value) return $value;
        return parent::filter($this->getMarkdownParser()->convertToHtml((string)$value));
    }

    /**
     * @return CommonMarkConverter
     */
    private function getMarkdownParser()
    {
        if (!$this->markdownParser) {
            $this->markdownParser = new CommonMarkConverter([
                'renderer' => [
                    'block_separator' => "\n",
                    'inner_separator' => "\n",
                    'soft_break'      => "\n",
                ],
                'enable_em' => true,
                'enable_emphasis' => true,
                'enable_strong' => true,
                'use_asterisk' => true,
                'use_underscore' => true,
                'html_input' => 'allow',
                'allow_unsafe_links' => true,
            ]);
        }

        return $this->markdownParser;
    }
}
