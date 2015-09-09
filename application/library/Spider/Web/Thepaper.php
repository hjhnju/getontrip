<?php
/**
 * 澎湃新闻数据采集器
 * @author huwei
 *
 */
class Spider_Web_Thepaper extends Spider_Web_Base{
    
    public function __construct($url,$type){
        parent::__construct($url,$type);
    }
    
    /**
     * 获取正文
     * @see Spider_Base::getBody()
     */
    public function getBody(){
        $strData  = '';
        $element  = $this->objDom->find('div.news_txt',0);
        $obj      = new Base_Extract($this->url,$element->innertext);
        $content  = $obj->preProcess();
        $content  = $obj->dataClean($content);
        return $content;
    }    
}