<?php

namespace Meniam\Bundle\CoreBundle\Twig\TokenParser;

use Meniam\Bundle\CoreBundle\Twig\Node\PageMetaStorageNode;
use Twig\Token;

class PageMetaJavascriptTokenParser extends PageMetaStorageTokenParser
{
    protected $nodeClass = PageMetaStorageNode::class;
    protected $groupPrefix = 'javascript_';

    public function decideMarkdownEnd(Token $token)
    {
        return $token->test('endjavascript');
    }

    public function getTag()
    {
        return 'javascript';
    }
}