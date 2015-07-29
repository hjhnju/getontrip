<?php 
set_time_limit( 60 * 15 );
class Spider_Auto{
    
    /**
     * Spider_Auto::getContent($url)
     * @param string $url
     * @param string ，返回正文内容
     */
    public static function getContent($url){
        $content    = file_get_contents($url); 
        $pattern    = '/charset=(.*?)\"/si';
        preg_match($pattern,$content,$match);
        $sourceCode = $match[1];
        $obj     = new Base_Extract($content);
        $text    = $obj->getPlainText();
        return  iconv($sourceCode,"utf8",$text);
    }
}
    
		
