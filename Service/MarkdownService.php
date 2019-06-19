<?php

namespace Meniam\Bundle\CoreBundle\Service;

use Evirma\CommonMark\Extension\AttributesExtension;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\Strikethrough\StrikethroughExtension;

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
            $environment = Environment::createCommonMarkEnvironment();
            $environment->addExtension(new AttributesExtension());
            $environment->addExtension(new StrikethroughExtension());

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
            ], $environment);
        }

        return $this->markdownParser;
    }
}