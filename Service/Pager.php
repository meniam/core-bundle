<?php

namespace Meniam\Bundle\CoreBundle\Service;

use Meniam\Bundle\CoreBundle\Traits\PagerTrait;
use Symfony\Component\Routing\Router;

class Pager extends AbstractCoreService
{
    use PagerTrait;

    /**
     * Ключи принимаются и в формате filter[search][query][value], и в формате filter%5Bsearch%5D%5Bquery%5D%5Bvalue%5D
     *
     * Если URL имеет вид <..>/?filter[search][query][value]=iphone&filter[search][rubric][value]=343,
     * то ключи и значения подставляются некодированными, сохраняя общий стиль записи
     *
     * Если URL имеет вид <..>/?filter%5Bsearch%5D%5Bquery%5D%5Bvalue%5D=iphone&filter%5Bsearch%5D%5Brubric%5D%5Bvalue%5D=343,
     * то ключи и значения кодируются посредством urlencode()
     *
     * @param       $replace array    Ассоциативный массив ключей и значений для замены get-параметров
     * @param       $delete  array    Массив ключей для удаления get-параметров
     * @param null  $route
     * @param array $parameters
     * @param int   $referenceType
     *
     * @return mixed|null|string
     * @internal param string $link URL, которая будет преобразована. Если не задана - текущий URL страницы
     * @internal param array $params
     */
    public function pathSaveGet($replace = array(), $delete = array(), $route = null, $parameters = array(), $referenceType = Router::ABSOLUTE_PATH)
    {
        if (!$route) {
            $link = $this->container->get('request_stack')->getCurrentRequest()->getRequestUri();
        } else {
            $link = $this->container->get('router')->generate($route, $parameters, $referenceType);
        }

        $isLinkDecoded = ($link == urldecode($link));

        if (isset($replace['page']) && $replace['page'] == 1) {
            unset($replace['page']);
            $delete['page'] = 'page';
        }

        if ($replace) {
            foreach ($replace as $k => $v) {
                $k = strval($k);
                $k = urldecode($k);
                $v = strval($v);
                $v = urldecode($v);

                if (!$isLinkDecoded) {
                    $k = urlencode($k);
                    $v = urlencode($v);
                }
                if (preg_match('/([&\?])'.preg_quote($k)."=[^&]*/i", $link)) {
                    $link = preg_replace('/([&\?])'.preg_quote($k)."=[^&]*/i", "\\1".$k."=".$v, $link);
                } else {
                    $link .= "&".$k."=".$v;
                }
            }
        }

        if ($delete) {
            foreach ($delete as $k) {
                $k = strval($k);
                $k = urldecode($k);

                if (!$isLinkDecoded) {
                    $k = urlencode($k);
                }
                $link = preg_replace('/([&\?])'.preg_quote($k)."=[^&]*[&]?/i", "\\1", $link);
            }
            if (substr($link, -1, 1) == '&') {
                $link = substr($link, 0, -1);
            }
        }

        if (strpos($link, '?') === false && strpos($link, '&') !== false) {
            $ampPos = strpos($link, '&');
            $link = substr_replace($link, '?', $ampPos, 1);
        }

        $link = rtrim($link, "?");

        if (strpos($link, '?') !== false && (strpos($link, '%2f') !== false || strpos($link, '%2F') !== false)) {
            list($_link, $_query) = explode('?', $link);
            $link = str_replace(array('%2f', '%2F'), '/', $_link).'?'.$_query;
        } elseif (strpos($link, '%2f') !== false || strpos($link, '%2F') !== false) {
            $link = str_replace(array('%2f', '%2F'), '/', $link);
        }

        return $link;
    }
}