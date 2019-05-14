<?php

namespace Meniam\Bundle\CoreBundle\Twig\TokenParser;

use Meniam\Bundle\CoreBundle\Twig\Node\TypoNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * {% typo %}
 * Content
 * {% endtypo %}
 */
class TypoTokenParser extends AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideTypoEnd'), true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        return new TypoNode($body, $lineno, $this->getTag());
    }
    /**
     * Decide if current token marks end of Markdown block.
     *
     * @param Token $token
     * @return bool
     */
    public function decideTypoEnd(Token $token)
    {
        return $token->test('endtypo');
    }
    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'typo';
    }
}
