<?php

namespace Meniam\Bundle\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PageCache
{
    private $storageDir;
    private $env;
    private $tokenStorage;

    public function __construct($storageDir, $env, TokenStorageInterface $tokenStorage)
    {
        $this->storageDir = $storageDir;
        $this->env = $env;
        $this->tokenStorage = $tokenStorage;
    }

    public function saveNginxPageCache(Request $request, $content)
    {
        if (($this->env != 'prod') && !$this->isUser()) return true;

        if (!$filename = $this->getNginxFilename($request)) return false;

        if ($this->saveZipped($filename, $content)) {
            $filename = preg_replace('#\.gz$#usi', '', $filename);
            @touch($filename);
            return true;
        }

        return false;
    }

    private function getNginxFilename(Request $request)
    {
        $dir = $this->storageDir.'/nginx_page_cache';

        $parts = explode('?', $request->getRequestUri());
        $path = $parts[0];
        $params = '';
        if (count($parts) == 2) {
            $params = $parts[1];
        }

        $path = trim($path, '/');
        if ($params) $path .= $params;
        $path = trim($path, '/');

        $filename = $dir . '/' . $path . '.html.gz';

        $dir = dirname($filename);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        return $filename;
    }

    private function saveZipped($file, $content)
    {
        $fp = gzopen($file, 'w9');

        if ($fp !== FALSE) {
            gzwrite($fp, $content);
            gzclose($fp);
            return $content;
        } else {
            return false;
        }
    }

    private function isUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return false;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return false;
        }

        return true;

    }
}
