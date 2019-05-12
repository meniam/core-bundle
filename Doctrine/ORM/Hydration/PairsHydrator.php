<?php

namespace Meniam\Bundle\CoreBundle\Doctrine\ORM\Hydration;

use PDO;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * Class PairsHydrator
 */
class PairsHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
