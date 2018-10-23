<?php

namespace App\Essential\Services;

class CosService
{
    /*
     * 获取签名
     * @param string $method 请求类型 method
     * @param string $pathname 文件名称
     * @param array $query query参数
     * @param array $headers headers
     * @return string 签名字符串
     */
    public static function sign($method, $pathname, $query = [], $headers = [])
    {
        // 获取个人 API 密钥 https://console.qcloud.com/capi
        $SecretId = config('filesystems.disks.cosv5.credentials.secretId');
        $SecretKey = config('filesystems.disks.cosv5.credentials.secretKey');
        // 整理参数
        $method = strtolower($method ? $method : 'get');
        $pathname = $pathname ? $pathname : '/';
        substr($pathname, 0, 1) != '/' && ($pathname = '/' . $pathname);

        // 工具方法
        function getObjectKeys($obj)
        {
            $list = array_keys($obj);
            sort($list);
            return $list;
        }
        function obj2str($obj)
        {
            $list = [];
            $keyList = getObjectKeys($obj);
            $len = count($keyList);
            for ($i = 0; $i < $len; $i++) {
                $key = $keyList[$i];
                $val = isset($obj[$key]) ? $obj[$key] : '';
                $key = strtolower($key);
                $list[] = rawurlencode($key) . '=' . rawurlencode($val);
            }
            return implode('&', $list);
        }
        // 签名有效起止时间
        $now = time() - 1;
        $expired = $now + 600; // 签名过期时刻，600 秒后

        // 要用到的 Authorization 参数列表
        $qSignAlgorithm = 'sha1';
        $qAk = $SecretId;
        $qSignTime = $now . ';' . $expired;
        $qKeyTime = $now . ';' . $expired;
        $qHeaderList = strtolower(implode(';', getObjectKeys($headers)));
        $qUrlParamList = strtolower(implode(';', getObjectKeys($query)));

        // 签名算法说明文档：https://www.qcloud.com/document/product/436/7778
        // 步骤一：计算 SignKey
        $signKey = hash_hmac('sha1', $qSignTime, $SecretKey);
        // 步骤二：构成 FormatString
        $formatString = implode("\n", [strtolower($method), $pathname, obj2str($query), obj2str($headers), '']);
        // 步骤三：计算 StringToSign
        $stringToSign = implode("\n", ['sha1', $qSignTime, sha1($formatString), '']);
        // 步骤四：计算 Signature
        $qSignature = hash_hmac('sha1', $stringToSign, $signKey);

        // 步骤五：构造 Authorization
        $authorization = implode('&', [
            'q-sign-algorithm=' . $qSignAlgorithm,
            'q-ak=' . $qAk,
            'q-sign-time=' . $qSignTime,
            'q-key-time=' . $qKeyTime,
            'q-header-list=' . $qHeaderList,
            'q-url-param-list=' . $qUrlParamList,
            'q-signature=' . $qSignature
        ]);

        return $authorization;
    }
}
