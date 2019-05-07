<?php

namespace Meniam\Bundle\CoreBundle\Service\Pager\Simple;

use Pagerfanta\View\TwitterBootstrapView;

class View extends TwitterBootstrapView
{
    protected function createDefaultTemplate()
    {
        return new Template();
    }

    protected function getDefaultProximity()
    {
        return 4;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'catalog';
    }
}
