<?php
require_once(APP_PATH."/application/library/Base/HtmlDom.php");
/**
 * 话题来源网页解析基类
 * @author huwei
 *
 */
abstract class Spider_Web_Base{
    
    /**
     * 文档对象
     * @var object
     */
    public $objDom;
    
    public $url;
    
    public $strDom;
    
    /**
     * 获取正文的抽象方法，子类必须实现
     */
    abstract public function getBody();
    
    /**
     * 构造函数，source可能是网址，也可以是页面内容
     * @param string $url
     */
    public function __construct( $source ,$type=Spider_Type_Source::URL){
        switch($type){
            case Spider_Type_Source::URL:
                $this->url     = $source;
                $this->objDom  = file_get_html($source);
                break;
            case Spider_Type_Source::STRING:
                $this->strDom  = $source;
                $this->objDom  =  str_get_html($source);
                break;
            default:
                break;
        }      
    }
    
    /**
     * 获取文章标题方法
     * @return string
     */
    public function getTitle(){
        $title = trim(html_entity_decode($this->objDom->find('title',0)->innertext));
        return $title;
    }
    
    /**
     * 将picUrl中给出的图片上传到阿里云
     * @param string $picUrl
     * @return string,如果上传上成功返回hash，否则返回空
     */
    public function uploadPic($picUrl){
        $oss      = Oss_Adapter::getInstance();
        $content  = file_get_contents($picUrl);
        $hash     = md5(microtime(true));
        $hash     = substr($hash, 8, 16);
        
        $arrName  = explode(".",$picUrl);
        $size     = count($arrName);
        if(strtolower($arrName[$size-1]) == "gif"){
            $filename = $hash . '.jpg';
        }else{
            $filename = $hash . '.jpg';
        }
        $res = $oss->writeFileContent($filename, $content);
        if($res){
            return array('hash'=>$hash,'name'=>$filename,'url'=>Base_Image::getUrlByName($filename));
        }
        return '';
    }
    
    /**
     * 加载页面消耗不少内存，用些方法释放
     * @return boolean
     */
    public function release(){
        return $this->objDom->clear();
    }
    
    /**
     * 网页内容预处理,只保留URL及换行
     * @param string $content
     * @return string
     */
    protected function preProcess($url,$content,$imageName='src') {
        $obj   = new Base_Extract($url,$content);
        $data  = $obj->preProcess();  
        return $obj->dataClean($data,$imageName); 
    }
}