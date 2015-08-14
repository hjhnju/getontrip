<?php
/**
 * 新浪博客数据采集器
 * @author huwei
 *
 */
class Spider_Web_SinaBlog extends Spider_Web_Base{
    
    public function __construct($url,$type){
        parent::__construct($url,$type);
    }
    
    /**
     * 获取正文
     * @see Spider_Base::getBody()
     */
    public function getBody(){
        $strData  = '';
        $element  = $this->objDom->find('div[id="sina_keyword_ad_area2"]',0);
        $strData  = $this->preProcess($this->url,$element->innertext);
        $obj      = new Base_Extract($this->url,$element->innertext);
        $content  = $obj->preProcess();
        
        $content  = preg_replace( '/<p.*?>/', '<p>', $content );
        $content  = preg_replace( '/<br.*?>/', '<br>', $content );
        $num      = preg_match_all('/img.*?real_src\s=\"(.*?)\".*?>/si',$content,$match);
        for($i=0;$i<$num;$i++){
            $content = str_replace($match[0][$i],"img src=\"".substr($match[1][$i],0,-6)."\">",$content);
        }
        $content = html_entity_decode($content);
        return $content;      
    }    
}