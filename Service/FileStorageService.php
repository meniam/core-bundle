<?php

namespace Meniam\Bundle\CoreBundle\Service;

class FileStorageService
{
    private $storageDir;
    private $memcached;

    public function __construct(MemcacheService $memcached, $storageDir)
    {
        $this->memcached = $memcached;
        $this->storageDir = $storageDir;
    }

    public function set(string $storage, $id, $data = null, $cacheTtl = null)
    {
        if (!$storage) return false;
        if (!$data = is_array($data) ? @json_encode($data, JSON_UNESCAPED_UNICODE) : (string)$data) return false;
        $file = $this->getStorageFile($storage, $id);
        $this->saveFileContents($file, $data);
        $this->memcached->set($this->getMetaCacheId($storage, $id), time(), $cacheTtl);
        return $data;
    }

    public function get(string $storage, $id, $default = null, int $validFrom = null)
    {
        if (!$time = $this->memcached->get($this->getMetaCacheId($storage, $id))) {
            return $default;
        }

        if ($validFrom && ($time <= $validFrom)) {
            return $default;
        }

        $file = $this->getStorageFile($storage, $id);
        return $this->getFileContents($file, $default);
    }

    public function delete($storage, $id)
    {
        $this->invalidate($storage, $id);
        $file = $this->getStorageFile($storage, $id);
        if (is_file($file)) {
            @unlink($file);
        }
        return true;
    }

    public function invalidate($storage, $id)
    {
        return $this->memcached->delete($this->getMetaCacheId($storage, $id));
    }

    private function getStorageFile($prefix, $id)
    {
        $hash = sha1($id);
        $dir = $this->storageDir . '/' . $prefix . '/' . $hash[0] . $hash[1] . '/'  . $hash[2] . $hash[3];
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        return $dir . '/' . $hash . '.gz';
    }

    private function getFileContents($file, $default = null)
    {
        if (!is_file($file)) return $default;

        $content = null;
        if (($fp = gzopen($file, 'r')) === false) return $default;
        while (!gzeof($fp)) {
            $content .= gzgets($fp, 8192);
        }
        gzclose($fp);
        return $content ?: $default;
    }

    private function saveFileContents($file, $content)
    {
        $fp = gzopen($file, 'w9');
        if ($fp !== FALSE) {
            gzwrite($fp, $content);
            gzclose($fp);
            return $content;
        }
        return false;
    }

    private function getMetaCacheId($storage, $id)
    {
        return 'fs__meta_' . sha1($storage) . '_' . sha1($id);
    }
}
