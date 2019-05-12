<?php

namespace Meniam\Bundle\CoreBundle\Twig\TokenParser;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Meniam\Bundle\CoreBundle\Twig\Node\PageMetaStorageNode;

class PageMetaStorageTokenParser extends AbstractTokenParser
{
    protected $nodeClass = PageMetaStorageNode::class;
    protected $groupPrefix = null;

    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $parser = $this->parser;
        $stream = $this->parser->getStream();

        $params = [];
        while (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test(Token::NAME_TYPE)) {
                // Read the attribute name
                $name = $stream->next()->getValue();

                // Read the "="
                $stream->expect(Token::OPERATOR_TYPE, '=');

                // Read the attribute value
                $token = $stream->expect(Token::STRING_TYPE);
                $params[$name] = $token->getValue();
            }
        }

        if (isset($params['group'])) {
            $params['group'] = $this->groupPrefix . $params['group'];
        } else {
            $params['group'] = $this->groupPrefix . 'default';
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $parser->subparse(array($this, 'decideMarkdownEnd'), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new $this->nodeClass($body, $params, $lineno, $this->getTag());
    }

    public function getTag()
    {
        return 'storage';
    }

    public function decideMarkdownEnd(Token $token)
    {
        return $token->test('endstorage');
    }
}