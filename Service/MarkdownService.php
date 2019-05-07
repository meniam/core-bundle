<?php

namespace Meniam\Bundle\CoreBundle\Service;

use League\CommonMark\CommonMarkConverter;

class MarkdownService
{
    /** @var CommonMarkConverter */
    private $markdownParser;

    public function markdown($text)
    {
        $text = $this->getMarkdownParser()->convertToHtml($text);
        return $text;
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