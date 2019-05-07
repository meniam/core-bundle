<?php

namespace Meniam\Bundle\CoreBundle\Traits;

trait ScriptCacheTrait
{
    private $scriptCache = [];

    public function getScriptCache($id)
    {
        $id = (string) $id;
        return isset($this->scriptCache[$id]) ? $this->scriptCache[$id] : null;
    }

    public function setScriptCache($id, $data)
    {
        $id = (string) $id;
        $this->scriptCache[$id] = $data;
    }

    public function deleteScriptCache($id = null)
    {
        if (is_null($id)) {
            $this->scriptCache = [];
        } else {
            unset($this->scriptCache[$id]);
        }
    }
}
