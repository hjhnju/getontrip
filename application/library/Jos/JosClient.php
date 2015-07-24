<?php
use yii\helpers\Json;

class JosClient
{

    public $appkey;

    public $secretKey;

    public $gatewayUrl = "http://gw.api.360buy.com/routerjson";

    protected $apiVersion = '2.0';

    public $checkRequest = false;

    public function getVersion()
    {
        return '20140429';
    }

    public function execute(JosRequest $request, $session = null)
    {
        $result = new stdClass();
        if ($this->checkRequest) {
            try {
                $request->check();
            } catch (Exception $e) {
                $result->code = $e->getCode();
                $result->zh_desc = "api请求参数验证失败";
                $result->en_desc = $e->getMessage();
                return $result;
            }
        }
        // 组装系统参数
        $sysParams['app_key'] = $this->appkey;
        $sysParams['v'] = $this->apiVersion;
        $sysParams['method'] = $request->getApiMethod();
        if ($session !== null) {
            $sysParams['access_token'] = $session;
        }
        $sysParams['timestamp'] = date('Y-m-d H:i:s');
        
        // 获取业务参数
        $apiParams['360buy_param_json'] = $request->getAppJsonParams();
        
        // 签名
        $sysParams['sign'] = $this->generateSign(array_merge($sysParams, $apiParams));
        
        $requestUrl = $this->gatewayUrl . '?' . http_build_query($sysParams);
        
        // 发送http请求
        try {
            $resp = $this->curl($requestUrl, $apiParams);
        } catch (Exception $e) {
            $result->code = $e->getCode();
            $result->zh_desc = "curl发送http请求失败";
            $result->en_desc = $e->getMessage();
            return $result;
        }
        // 解析返回结果
        $respWellFormed = false;
        $respObject = self::jsonDecode($resp);
        
        if (null !== $respObject) {
            $respWellFormed = true;
            foreach ($respObject as $propKey => $propValue) {
                $respObject = $propValue;
            }
        }
        if (false === $respWellFormed) {
            $result->code = 1;
            $result->zh_desc = "api返回数据错误或程序无法解析返回参数";
            $result->en_desc = "HTTP_RESPONSE_NOT_WELL_FORMED";
            $result->resp = $resp;
            return $result;
        }
        return $respObject;
    }

    private static function jsonDecode($str)
    {
        // 特殊字符处理
        $str = str_replace(chr(0),'\\v', '\\t', $str);
        $str = str_replace(chr(4),'\\v', '\\t', $str);
        $str = str_replace(chr(15),'\\v', '\\t', $str);
        $str = str_replace(chr(16),'\\v', '\\t', $str);
        $str = str_replace(chr(19),'\\v', '\\t', $str);
        $str = str_replace(chr(31),'\\v', '\\t', $str);
        
        if (defined('JSON_BIGINT_AS_STRING')) {
            return json_decode($str, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            return PhplutilsJSON::decode($str);
        }
    }

    /**
     * 签名
     *
     * @param $params 业务参数            
     * @return void
     */
    public function generateSign($params)
    {
        if ($params != null) {
            ksort($params);
            $stringToBeSigned = $this->secretKey;
            foreach ($params as $k => $v) {
                $stringToBeSigned .= "$k$v";
            }
            unset($k, $v);
            $stringToBeSigned .= $this->secretKey;
        } else {
            $stringToBeSigned = $this->secretKey;
            $stringToBeSigned .= $this->secretKey;
        }
        return strtoupper(md5($stringToBeSigned));
    }

    public function curl($url, $postFields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // https 请求
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        
        if (is_array($postFields) && 0 < count($postFields)) {
            curl_setopt($ch, CURLOPT_POST, true);
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if ('@' == substr($v, 0, 1)) {
                    $postMultipart = true;
                    break;
                }
            }
            unset($k, $v);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
            }
        }
        $reponse = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new JosSdkException(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new JosSdkException($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }
}