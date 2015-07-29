<?php
/**
 * 果壳网数据采集器
 * @author huwei
 *
 */
class Spider_Web_Guoke extends Spider_Web_Base{
    
    public function __construct($url,$type){
        parent::__construct($url,$type);
    }
    
    /**
     * 获取正文，果壳正文类型有多种
     * @see Spider_Base::getBody()
     */
    public function getBody(){
        $strData = '';
        $arrTemp = explode("/",$this->url);
        if(!isset($arrTemp[3])){
            return $strData;
        }
        switch($arrTemp[3]){
            case 'question':
                $element = $this->objDom->find('div[class^="answer-txt answerTxt"]',0);
                foreach ($element->find("p") as $para){
                    $strData .= "<p>".strip_tags($para->innertext,"<img>")."</p>";
                }
                break;
            case 'post':
                $element = $this->objDom->find('div[class^="post-detail"]',0);
                foreach ($element->find("p") as $para){
                    $strData .= "<p>".strip_tags($para->innertext,"<img>")."</p>";
                }
                break;
            case 'article':
                $element = $this->objDom->find('div[class="content-txt"]',0);
                foreach ($element->find("p") as $para){
                    $strData .= "<p>".strip_tags($para->innertext,"<img>")."</p>";
                }
                break;
            default :
                break;
       }
       return $strData;        
    }
}