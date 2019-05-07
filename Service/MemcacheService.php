<?php

namespace Meniam\Bundle\CoreBundle\Service;

use \Exception;
use \Memcached;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class MemcacheService extends AbstractCoreService
{
    const MC_DEFAULT = '-1~1982~06~01';

    /**
     * Transaction started flag
     * @var bool
     */
    private $transaction = false;

    /**
     * @var array
     */
    private $transactionCachedIds = [];

//    /** @var MemcachedCache */
//    private $client;

    /**
     * @var \Memcached
     */
    private $memcached;

//    /**
//     * @return MemcachedCache
//     * @throws \ErrorException
//     */
//    private function getClient()
//    {
//        if ($this->client) return $this->client;
//        $this->client = new MemcachedCache($this->getMemcachedAdapter());
//        return $this->client;
//    }

    private function getMemcachedAdapter()
    {
        if ($this->memcached) return $this->memcached;
        $this->memcached = MemcachedAdapter::createConnection(
            'memcached://memcached-server'
        );
        return $this->memcached;
    }

    public function beginTransaction()
    {
        $this->transaction = true;
        return $this;
    }

    public function commit()
    {
        $this->transaction = false;
        $this->transactionCachedIds = [];
        return $this;
    }

    public function rollBack()
    {
        if ($this->transaction) {
            $this->transaction = false;
            if ($this->transactionCachedIds) {
                $this->deleteMultiple($this->transactionCachedIds);
            }
            $this->transactionCachedIds = [];
        }

        return $this;
    }

    /**
     * @param       $key
     * @param null  $value
     * @param int   $ttl
     * @return bool
     */
    public function set($key, $value = null, $ttl = null)
    {
        if ($this->transaction) $this->transactionCachedIds[$key] = $key;

        try {
            return $this->getMemcachedAdapter()->set($key, $value, time() + $ttl);
        } catch (Exception $e) {
            $this->getLogger()->error("Memcached set failed", ['key' => $key, 'value' => $value, 'e' => $e]);
            return false;
        }
    }

    /**
     * @param      $values
     * @param null $ttl
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        try {
            return $this->getMemcachedAdapter()->setMulti($values, time() + $ttl);
        } catch (Exception $e) {
            $this->getLogger()->error("Memcached setMultiple failed", ['values' => $values, 'e' => $e]);
            return false;
        }
    }

    /**
     * @param      $key
     * @param      $default
     * @param bool $cached
     * @return mixed|null
     */
    public function get($key, $default = false, $cached = true)
    {
        if (!$this->isCacheAllowed($cached)) return $default;

        try {
            $result = $this->getMemcachedAdapter()->get($key);
            if ($this->getMemcachedAdapter()->getResultCode() == Memcached::RES_NOTFOUND) {
                return $default;
            }
            return $result;
        } catch (Exception $e) {
            $this->getLogger()->error("Memcached get failed", ['key' => $key, 'e' => $e]);
            return $default;
        }
    }

    /**
     * @param      $keys
     * @param      $default
     * @param bool $cached
     * @return bool|iterable|null
     */
    public function getMultiple($keys, $default = false, $cached = true)
    {
        if (!$this->isCacheAllowed($cached)) return $default;

        try {
            return $this->getMemcachedAdapter()->getMulti($keys);
        } catch (Exception $e) {
            $this->getLogger()->error("Memcached getMultiple failed", ['keys' => $keys, 'default' => $default, 'e' => $e]);
            return $default;
        }
    }

    /**
     * @param      $key
     * @param      $default
     * @param bool $cached
     *
     * @return mixed|null
     */
    public function getDecoded($key, $default = false, $cached = true)
    {
        if (!$this->isCacheAllowed($cached)) return $default;

        $result = $this->get($key, self::MC_DEFAULT);

        if ($result != self::MC_DEFAULT && $result) {
            $result = @json_decode($result, true);
        } else {
            $result = $default;
        }

        return $result;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function delete($key)
    {
        try {
            return $this->getMemcachedAdapter()->delete($key);
        } catch (Exception $e) {
            $this->getLogger()->error("Memcached deleteMultiple failed", ['key' => $key, 'e' => $e]);
            return false;
        }
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function deleteMultiple(array $keys)
    {
        try {
            return $this->getMemcachedAdapter()->deleteMulti($keys);
        } catch (Exception $e) {
            $this->getLogger()->error("Memcached deleteMultiple failed", ['keys' => $keys, 'e' => $e]);
            return false;
        }
    }
}