<?php

namespace Meniam\Bundle\CoreBundle\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

class PageMetaStorageNode extends Node
{
    protected $params = [];

    public function __construct(Node $body, array $params, $line, $tag = null)
    {
        $this->params = $params;
        parent::__construct(array('body' => $body), $params, $line, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('ob_start();' . PHP_EOL)
            ->subcompile($this->getNode('body'))
            ->write('$body = ob_get_clean();' . PHP_EOL);

        if (!$this->params) {
            $compiler
                ->write('$params = [];' . PHP_EOL);
        } else {
            $paramsJson = json_encode($this->params, JSON_UNESCAPED_UNICODE);
            $compiler
                ->write("\$params = json_decode('{$paramsJson}', true);" . PHP_EOL);
        }

        $compiler
            ->write('echo $this->env->getExtension(\'Meniam\\Bundle\\CoreBundle\\Twig\\Extension\\PageMetaExtension\')->addToStorage($body, $params);' . PHP_EOL)
        ;
    }
}