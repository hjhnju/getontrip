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
	    return $str;
	}
	
	/**
	 * 中文分词
	 * @param string $str
	 */
	public static function ChineseAnalyzer($str){
        //词性数组，只取数组中给出的词性的词，词性含义详见：http://bbs.xunsearch.com/showthread.php?tid=1235
	    $arrAttri = array('n','nr','ns','nz','nt');
	    $arrRet   = array();
	    $so       = scws_new();
	    $so->send_text($str);
	    $so->set_multi(true);
	    $tmp = $so->get_result();
	    foreach ($tmp as $val){
	        if(in_array($val['attr'],$arrAttri)){
	            $arrRet[] = $val['word'];
	        }
	    }
	    $so->close();
	    return $arrRet;
	}
	
	protected function getfirstchar($s0){
	    $fchar = ord($s0{0});
	    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
	    $s1 = mb_convert_encoding($s0,"gb2312","UTF-8");
	    $s2 = mb_convert_encoding($s1,"UTF-8","gb2312");
	    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
	    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	    if($asc >= -20319 and $asc <= -20284) return "A";
	    if($asc >= -20283 and $asc <= -19776) return "B";
	    if($asc >= -19775 and $asc <= -19219) return "C";
	    if($asc >= -19218 and $asc <= -18711) return "D";
	    if($asc >= -18710 and $asc <= -18527) return "E";
	    if($asc >= -18526 and $asc <= -18240) return "F";
	    if($asc >= -18239 and $asc <= -17923) return "G";
	    if($asc >= -17922 and $asc <= -17418) return "H";
	    if($asc >= -17417 and $asc <= -16475) return "J";
	    if($asc >= -16474 and $asc <= -16213) return "K";
	    if($asc >= -16212 and $asc <= -15641) return "L";
	    if($asc >= -15640 and $asc <= -15166) return "M";
	    if($asc >= -15165 and $asc <= -14923) return "N";
	    if($asc >= -14922 and $asc <= -14915) return "O";
	    if($asc >= -14914 and $asc <= -14631) return "P";
	    if($asc >= -14630 and $asc <= -14150) return "Q";
	    if($asc >= -14149 and $asc <= -14091) return "R";
	    if($asc >= -14090 and $asc <= -13319) return "S";
	    if($asc >= -13318 and $asc <= -12839) return "T";
	    if($asc >= -12838 and $asc <= -12557) return "W";
	    if($asc >= -12556 and $asc <= -11848) return "X";
	    if($asc >= -11847 and $asc <= -11056) return "Y";
	    if($asc >= -11055 and $asc <= -10247) return "Z";
	    return null;
	}
	
	
	public static function pinyin_first($zh){
	    $ret = "";
	    $s1 = iconv("UTF-8","gb2312", $zh);
	    $s2 = iconv("gb2312","UTF-8", $s1);
	    if($s2 == $zh){$zh = $s1;}
	    for($i = 0; $i < strlen($zh); $i++){
	        $s1 = substr($zh,$i,1);
	        $p = ord($s1);
	        if($p > 160){
	            $s2 = substr($zh,$i++,2);
	            $ret .= self::getfirstchar($s2);
	        }else{
	            $ret .= $s1;
	        }
	    }
	    return $ret;
	}
	
	/**
	 * 英文标点符号转中文标点符号,半角转全角
	 * @param string $str
	 */
	public static function symbol_change($str){	    
	    $dbc = array( //全角
            '０' , '１' , '２' , '３' , '４' ,  
            '５' , '６' , '７' , '８' , '９' , 
            'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,  
            'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' , 
            'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,  
            'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' , 
            'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,  
            'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' , 
            'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,  
            'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' , 
            'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,  
            'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' , 
            'ｙ' , 'ｚ' , '－' , '　'  , '：' ,
            '．' , '，' , '／' , '％' , '＃' ,
            '！' , '＠' , '＆' , '（' , '）' ,
            '＜' , '＞' , '＂' , '＇' , '？' ,
            '［' , '］' , '｛' , '｝' , '＼' ,
            '｜' , '＋' , '＝' , '＿' , '＾' ,
            '￥' , '￣' , '｀'
        );

        $sbc = array( //半角
            '0', '1', '2', '3', '4',  
            '5', '6', '7', '8', '9', 
            'A', 'B', 'C', 'D', 'E',  
            'F', 'G', 'H', 'I', 'J', 
            'K', 'L', 'M', 'N', 'O',  
            'P', 'Q', 'R', 'S', 'T', 
            'U', 'V', 'W', 'X', 'Y',  
            'Z', 'a', 'b', 'c', 'd', 
            'e', 'f', 'g', 'h', 'i',  
            'j', 'k', 'l', 'm', 'n', 
            'o', 'p', 'q', 'r', 's',  
            't', 'u', 'v', 'w', 'x', 
            'y', 'z', '-', ' ', ':',
            '.', ',', '/', '%', ' #',
            '!', '@', '&', '(', ')',
            '<', '>', '"', '\'','?',
            '[', ']', '{', '}', '\\',
            '|', '+', '=', '_', '^',
            '￥','~', '`'
       );
        
       $symbol_en = array( //英文标点符号
           '.', ',',  '"', '`'
       );
       
       $symbol_ch = array( //中文标点符号
           '。' , '，','“','·'
       );
       
       //英文转中文
       $str = str_replace($symbol_en, $symbol_ch, $str);
       
       //半角转全角
       $str = str_replace($sbc, $dbc, $str);
              
       return $str;
	}
}