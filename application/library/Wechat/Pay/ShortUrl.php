<?php
/**
 * 短链接转换接口
 */
class Wechat_Pay_ShortUrl extends Wechat_Pay_Client
{
	function __construct() 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/tools/shorturl";
		//设置curl超时时间
		$this->curl_timeout = Wechat_Pay_Config::CURL_TIMEOUT;		
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{		
		try 
		{
			if($this->parameters["long_url"] == null ) 
			{
				throw new Wechat_Pay_Exception("短链接转换接口中，缺少必填参数long_url！"."<br>");
			}
		   	$this->parameters["appid"] = Wechat_Pay_Config::getAppid();//公众账号ID
		   	$this->parameters["mch_id"] = Wechat_Pay_Config::getMchid();//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (Wechat_Pay_Exception $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 获取prepay_id
	 */
	function getShortUrl()
	{
		$this->postXml();
		$prepay_id = $this->result["short_url"];
		return $prepay_id;
	}
	
}