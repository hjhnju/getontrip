<?php 
class Base_Util_String {
	
	/**
	 * 邮箱加星加密
	 * @param string $email 需要加密的字符串
	 * @return string
	 */
	public static function starEmail($email) {
		
		if(empty($email)) {			
			return '';
		}
		$email = explode('@',$email);
		$username = $email[0];
		$domain = $email[1];
		$len = strlen($username);
		
		if($len === 1) {
			return $username.'@'.$domain;
		}
		if($len === 2 || $len === 3) {
			$username = substr_replace($username,'***',1,1);
			return $username.'@'.$domain;
		}
		if($len === 4) {
			$username = substr_replace($username,'***',2,1);
			return $username.'@'.$domain;
		}
		if($len > 4) {
			$username = substr_replace($username,'***',2,$len-4);			
			return $username.'@'.$domain;				
		}		
	}
	
	/**
	 * 用户名加密
	 * @param string username 
	 * @return string
	 */
	public static function starUsername($username) {
		if(empty($username)) {
			return '';
		}
		$len = strlen($username);
		if($len === 1) {
			return $username;
		}
		if($len === 2 || $len ===3 ){
			$username = substr_replace($username,'***',1,1);
			return $username;
		}
		if($len === 3) {
			$username = substr_replace($username,'***',2,1);
			return $username;
		}
		if($len === 4) {
			$username = substr_replace($username,'***',2,1);
			return $username;
		}
		if($len > 4) {
			$username = substr_replace($username,'***',2,$len-4);
			return $username;
		}		
	}
	
	/**
	 * 手机号加星 186***043
	 * @param string $phone
	 * @return string
	 */
	public static function starPhone($phone) {
		if(empty($phone)) {
			return '';
		}
		return substr_replace($phone,'***',3,5);
	}
	
	/**
	 * 带中文的字符串截断
	 * @param string $str
	 * @param integer $length
	 * @return string
	 */
	public static function getSubString($str,$length){
	    $str = trim(strip_tags($str));
	    if(mb_strlen($str) > $length){
	        return mb_substr($str,0,$length,'utf-8')."..";
	    }
	    return $str;
	}
	
	/**
	 * 删除字符串中空格
	 * @param string $str
	 * @return string
	 */
	public static function trimall($str){
	    $pattern = '/\s/';
	    $replacement = "";
	    return preg_replace( $pattern, $replacement, $str );
	}
	
	/**
	 * 时间离当前有多久
	 * @param unknown $time
	 */
	public static function getTimeAgoString($time){
	    $ago = time() -  $time;	    
	    if ($ago < 60) {
	        $str = $ago."秒";
	    }elseif ($ago < 3600){
	        $str = floor($ago/60)."分钟";
	    }elseif ($ago < 86400){
	        $str = floor($ago/3600)."小时";
	    }else{
	        $str = floor($ago/86400)."天";
	    }
	}
}