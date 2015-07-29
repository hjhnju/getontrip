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
    
    const TYPE_WEB    = 1;
    
    const TYPE_STRING = 2;
    
    /**
     * 获取正文的抽象方法，子类必须实现
     */
    abstract public function getBody();
    
    /**
     * 构造函数，通过传URL参数来构造对象
     * @param string $url
     */
    public function __construct( $source ,$type=self::TYPE_WEB){
        switch($type){
            case self::TYPE_WEB:
                $this->url     = $source;
                $this->objDom  = file_get_html($source);
                break;
            case self::TYPE_STRING:
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
        $title = $this->objDom->find('title',0)->innertext;
        $this->objDom->clear();
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
        $filename = $hash . '.jpg';
        $res = $oss->writeFileContent($filename, $content);
        if($res){
            return $hash;
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
}