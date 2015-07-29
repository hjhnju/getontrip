<?php 
set_time_limit( 60 * 15 );
/**
 * 网页自动抓取正文类
 * @author huwei
 *
 */
class Spider_Auto{
    
    /**
     * Spider_Auto::getContent($url)
     * @param string $url
     * @param string ，返回正文内容
     */
    public static function getContent($url){
        $content    = file_get_contents($url); 
        $pattern    = '/<meta.*?>/si';
        $obj        = new Base_Extract($content,$url);
        $text       = $obj->getPlainText();
        preg_match($pattern,$content,$match);
        if((isset($match[0])) &&(false !== stristr($match[0],"charset"))){
            preg_match('/charset=\"?(.*?)(\"|\s|\/|>)/si',$content,$match);
            $sourceCode = trim($match[1]);
            return mb_convert_encoding($text,"utf8",$sourceCode);
        }
        return $text;
    }
}
    
		
