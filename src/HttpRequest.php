<?php

namespace Mrstock\Helper;

use Mrstock\Mjc\Facade\Log;
use Mrstock\Mjc\Facade\Hook;

class HttpRequest
{
    public static function query($url, $data = '', $is_post = 0, $check_ok = false, $username = '', $password = '')
    {
        if ($is_post) {
            $rew = self::execute($url, $data, $username, $password);
        } else {
            $urlStr = is_array($data) ? http_build_query($data) : $data;
            $join = '&';
            strpos($url, '?') !== false ?: $join = '?';
            $rew = $rew = self::execute($url . $join . $urlStr, '', $username, $password);
        }

        $rew = json_decode($rew, true);

        if ($check_ok) {
            if (empty($rew) || !isset($rew['code']) || $rew['code'] != 1) {
                return false;
            }
        }
        return $rew;
    }

    public static function execute($url, $post = '', $username = '', $password = '')
    {
        $startTime = microtime(true);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        if ($username && $password) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        $debug_state = true;

        $data = curl_exec($curl);
        $errorCode = curl_errno($curl);
        if ($errorCode) {
            $debug_state = false;

            $result['code'] = '-' . $errorCode;
            $result['message'] = 'httpError';
            $message = ' url:' . $url . ' post:' . print_r($post, true) . ' result:' . json_encode($result);
            Log::write($message, 'CURLERR');
            $data = json_encode($result);
        }

        curl_close($curl);

        Hook::listen('debug_record', ['type' => 'Http', 'link' => $url, 'state' => $debug_state, 'command' => $post, 'starttime' => $startTime, 'result' => $data]);

        return $data;
    }

    /**
     * Content-Type: application/json请求
     */
    public function httpCurlPostJson($url, $data)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!$data) {
            return 'data is null';
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($data),
            'Cache-Control: no-cache',
            'Pragma: no-cache'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        $errorno = curl_errno($curl);
        if ($errorno) {
            return $errorno;
        }
        curl_close($curl);
        return $res;


    }
}