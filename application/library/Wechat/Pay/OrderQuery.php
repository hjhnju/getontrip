<?php
/**
 * 订单查询接口
 */
class Wechat_Pay_OrderQuery extends Wechat_Pay_Client
{
	function __construct() 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/orderquery";
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
			//检测必填参数
			if($this->parameters["out_trade_no"] == null && 
				$this->parameters["transaction_id"] == null) 
			{
				throw new Wechat_Pay_Exception("订单查询接口中，out_trade_no、transaction_id至少填一个！"."<br>");
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

}