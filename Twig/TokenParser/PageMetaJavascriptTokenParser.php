<?php

namespace Meniam\Bundle\CoreBundle\Twig\TokenParser;

use App\Twig\Shared\PageMetaStorage\PageMetaStorageNode;
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