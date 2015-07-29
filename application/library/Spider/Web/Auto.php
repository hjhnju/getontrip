<?php
/**
 * 自动正文数据内容采集器
 * @author huwei
 *
 */
set_time_limit( 60 * 15 );
class Spider_Web_Auto extends Spider_Web_Base{
    
    public function __construct($url,$type){
        parent::__construct($url,$type);
    }
    
    /**
     * 获取正文，果壳正文类型有多种
     * @see Spider_Base::getBody()
     */
    public function getBody(){
        $content    = file_get_contents($this->url);
        $pattern    = '/<meta.*?>/si';
        $obj        = new Base_Extract($content,$this->url);
        $text       = $obj->getPlainText();
        $num = preg_match_all($pattern,$content,$match);
        for( $i = 0; $i < $num; $i++ ){
            if(false !== stristr($match[0][$i],"charset")){
                preg_match('/charset=\"?(.*?)(\"|\s|\/|>)/si',$content,$match);
                $sourceCode = trim($match[1]);
                return mb_convert_encoding($text,"utf8",$sourceCode);
            }
        }
        return $text;     
    }
}