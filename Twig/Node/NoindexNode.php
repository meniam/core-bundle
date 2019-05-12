<?php

namespace Meniam\Bundle\CoreBundle\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

class NoindexNode extends Node
{
    public function __construct(Node $body, $line, $tag = null)
    {
        parent::__construct(array('body' => $body), array(), $line, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('ob_start();' . PHP_EOL)
            ->subcompile($this->getNode('body'))
            ->write('$body = ob_get_clean();' . PHP_EOL)
            ->write('echo $this->env->getExtension(\'Meniam\\Bundle\\CoreBundle\\Twig\Extension\\StupidExtension\')->noindex($body);' . PHP_EOL)
        ;
    }
}