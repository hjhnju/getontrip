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
        $obj        = new Base_Extract($this->url);
        $text       = $obj->getPlainText();
        return $text;     
    }
}