<?php

namespace Meniam\Bundle\CoreBundle\Twig\TokenParser;

use Twig\Token;
use Meniam\Bundle\CoreBundle\Twig\Node\PageMetaStorageNode;

class PageMetaStyleTokenParser extends PageMetaStorageTokenParser
{
    protected $nodeClass = PageMetaStorageNode::class;
    protected $groupPrefix = 'style_';

    public function decideMarkdownEnd(Token $token)
    {
        return $token->test('endstyle');
    }

    public function getTag()
    {
        return 'style';
    }
}