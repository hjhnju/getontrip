<?php
/**
* 	配置账号信息
*/

class Wechat_Pay_Config
{
	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;

	public static function getConfig(){
		return Wechat_Xjd::getConfig();
	}

	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	public static function getAppid(){
		$config = self::getConfig();
		return $config['basic']['appid'];
	}

	//受理商ID，身份标识
	public static function getMchid(){
		$config = self::getConfig();
		return $config['pay']['mchid'];
	}

	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	public static function getKey(){
		$config = self::getConfig();
		return $config['pay']['key'];
	}

	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	public static function getAppsecret(){
		$config = self::getConfig();
		return $config['basic']['appsecret'];
	}
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	public static function getJsApiCallUrl(){
		$config = self::getConfig();
		return $config['pay']['js_api_call_url'];
	}
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	public static function getSslcertPath(){
		$config = self::getConfig();
		return $config['pay']['sslcert_path'];
	}
	public static function getSslkeyPath(){
		$config = self::getConfig();
		return $config['pay']['sslkey_path'];
	}
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	public static function getNotifyUrl(){
		$config = self::getConfig();
		return $config['pay']['notify_url'];
	}
}
	
?>