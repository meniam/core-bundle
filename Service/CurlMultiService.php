<?php

namespace Meniam\Bundle\CoreBundle\Service;

class CurlMultiService
{
    protected $_threads = array();

    /**
     * Добавить поток
     *
     * @param string $url
     * @param string $callback
     * @param array  $curlOpts
     */
    public function addTread($url, $callback, $curlOpts = array())
    {
        $thread = array('url' => $url,
            'callback' => $callback,
            'curl_opts' => $curlOpts);

        $this->_threads[] = $thread;
    }

    public function request()
    {
        //create the multiple cURL handle
        $mh = curl_multi_init();

        $running = null;

        # Setup all curl handles
        # Loop through each created curlNode object.
        foreach($this->_threads as $id => $thread){
            $url = $thread['url'];

            $current = new CurlService();
            $current->setAutodetectEncoding(false);
            $current->setOpt(CURLOPT_CONNECTTIMEOUT, 10);
            $current->setOpt(CURLOPT_TIMEOUT, 10);
            $current->setUrl($url);

            # Set defined options, set through curlNode->setOpt();
            if (isset($thread['curl_opts'])){
                foreach($thread['curl_opts'] as $key => $value){
                    $current->setOpt($key, $value);
                }
            }

            curl_multi_add_handle($mh, $current->getHandler());

            $this->_threads[$id]['curl'] = $current;
            $this->_threads[$id]['handler'] = $current->getHandler();
            $this->_threads[$id]['start'] = microtime(1);
        }

        unset($thread);

        do {
            $mrc = curl_multi_exec($mh, $running);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        $sleepTimes = 0;

        while ($running && ($mrc == CURLM_OK)){
            if (curl_multi_select($mh, 1) == -1) {
                usleep(100);
                if ($sleepTimes++ > 10) {
                    break;
                }
            }

            do {
                // stucked here
                $mrc = curl_multi_exec($mh, $running);

                while (false !== ($done = curl_multi_info_read($mh))) {
                    foreach($this->_threads as $id => $thread){
                        # Strict compare handles.
                        if ($thread['handler'] === $done['handle']) {
                            # Get content

                            /** @var CurlService $curl */
                            $curl = $thread['curl'];
                            $curl->parseResult(curl_multi_getcontent($done['handle']));

                            # Call the callback.
                            call_user_func($thread['callback'], $curl, $thread['url']);

                            # Remove unnecesary handle (optional, script works without it).
                            curl_multi_remove_handle($mh, $done['handle']);
                        }
                    }
                }

            } while ($mrc === CURLM_CALL_MULTI_PERFORM);
        }
    }
}