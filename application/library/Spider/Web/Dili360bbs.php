<?php
/**
 * 中国国家地理论坛(http://bbs.dili360.com/)数据采集器
 * @author huwei
 *
 */
class Spider_Web_Dili360bbs extends Spider_Web_Base{
    
    public function __construct($url,$type){
        parent::__construct($url,$type);
    }
    
    /**
     * 获取正文
     * @see Spider_Base::getBody()
     */
    public function getBody(){
        $element  = $this->objDom->find('div[class="t_fsz"]',0);
        $obj      = new Base_Extract($this->url,$element->innertext);
        $content  = $obj->preProcess();
        $content  = preg_replace( '/<p.*?<\/p>/', '', $content );
        $content  = preg_replace( '/<br.*?>/', '<br>', $content );
        
        $num      = preg_match_all('/img.*?file=\"(.*?)\".*?>/si',$content,$match);
        for($i=0;$i<$num;$i++){
            $url     = explode("&",$match[1][$i]);
            $content = str_replace($match[0][$i],"img src=\"".$url[0]."\">",$content);
        }
        return $content;      
    }    
}
