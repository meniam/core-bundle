<?php

namespace Meniam\Bundle\CoreBundle\Traits;

use Meniam\Bundle\CoreBundle\Service\MemcacheService;
use Meniam\Bundle\CoreBundle\Service\PageCache;
use Psr\Container\ContainerInterface;

/**
 * @property ContainerInterface $container
 */
trait CacheTrait
{
    use CacheTtlTrait;
    use ServiceSystemTrait;

    public static $prefetchCacheData = [];
    public static $addToPrefetchOnSet = false;

    /**
     * @return PageCache|mixed
     */
    protected function getPageCache()
    {
        return $this->getService(PageCache::class);
    }

    protected function saveNginxPageCache($content)
    {
        $this->getPageCache()->saveNginxPageCache($this->getRequest(), $content);
        return $content;
    }

    /**
     * @return MemcacheService
     */
    public function getMemcache()
    {
        return $this->getService(MemcacheService::class);
    }

    public function prefetchDecodedCache($keys, $default = null, $cached = true)
    {
        $result = $this->getMemcache()->getMultiple($keys, $default, $cached);

        if ($result && is_array($result)) {
            foreach ($result as $k => $v) {
                if ($v) {
                    CacheTrait::$prefetchCacheData[$k] = @json_decode($v, true);
                }
            }
        }
    }

    public function getCacheItem($cacheId, $default = null, $cached = true)
    {
        $result = null;
        if ($this->isCacheAllowed($cached)) {
            if (isset(CacheTrait::$prefetchCacheData[$cacheId]) && CacheTrait::$prefetchCacheData[$cacheId]) {
                $result = CacheTrait::$prefetchCacheData[$cacheId];
            } else {
                $result = $this->getMemcache()->get($cacheId, $default);
            }
        }
        return $result;
    }

    public function getCacheDecodedItem($cacheId, $default = null, $cached = true)
    {
        $mc = $this->getMemcache();
        $result = null;

        if ($this->isCacheAllowed($cached) && isset(CacheTrait::$prefetchCacheData[$cacheId]) && CacheTrait::$prefetchCacheData[$cacheId]) {
            $result = CacheTrait::$prefetchCacheData[$cacheId];
        } elseif (($result = $mc->get($cacheId, $default, $cached)) && ($result != $default)) {
            $result = @json_decode($result, true);
        }

        return $result;
    }

    public static function addToPrefetchOnSet($flag = true)
    {
        CacheTrait::$addToPrefetchOnSet = $flag;
    }

    public static function clearPrefetchedData()
    {
        CacheTrait::$prefetchCacheData = [];
    }

    public function setCacheItem($cacheId, $data, $ttl = 'cache_ttl_middle')
    {
        if ($ttl == 'cache_ttl_middle') $ttl = $this->getCacheTtlMiddle();
        $this->getMemcache()->set($cacheId, $data, $ttl);
    }

    public function setCacheEncodedItem($cacheId, $data, $ttl = 'cache_ttl_middle')
    {
        if ($ttl == 'cache_ttl_middle') $ttl = $this->getCacheTtlMiddle();
        $encodedData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->getMemcache()->set($cacheId, $encodedData, $ttl);

        if (CacheTrait::$addToPrefetchOnSet) {
            CacheTrait::$prefetchCacheData[$cacheId] = $data;
        }

        return $data;
    }
}
