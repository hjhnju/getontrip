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
        $obj      = new Base_Extract($this->url,$element->innertext);
        $content  = $obj->preProcess();
        $content = preg_replace( '/<p.*?>/is', '<p>', $content );
        $content = preg_replace( '/<b\s.*?>/is', '<b>', $content );
        $content = preg_replace( '/<br.*?>/is', '<p>', $content );
         
        //去掉所有标签两旁的空白
	    $arr = array();
        preg_match_all('/(　)*(\xc2\xa0)*\s*(&nbsp;)*<(.*?)>(　)*(\xc2\xa0)*\s*(&nbsp;)*/i', $content, $arr);
        foreach ($arr[0] as $i => $val){
            $val     = str_replace("/", "\/", $val);
            $content = preg_replace("/".$val."/", "<".$arr[4][$i].">", $content, 1);
        }
	    
	    $content = preg_replace( '/(<p>){2,}/i', '<p>', $content );
	    $content = preg_replace( '/(<\/p>){2,}/i', '</p>', $content );
	    $content = preg_replace( '/(<p><\/p>){2,}/i', '<p></p>', $content );	
        
        $num      = preg_match_all('/img.*?real_src\s=\"(.*?)\".*?>/si',$content,$match);
        for($i=0;$i<$num;$i++){
            $url     = explode("&",$match[1][$i]);
            $content = str_replace($match[0][$i],"img src=\"".$url[0]."\">",$content);
        }
        return $content;      
    }    
}