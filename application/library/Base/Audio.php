<?php
require_once dirname(__FILE__) . '/Audio/getid3.php';

/**
 * Base_Audio::getInstance()->getLen($fileName);
 */ 
class Base_Audio {

    private static $instance = null;

    //getID3类
    private $audio = null;

    /**
     * 获取单例
     */
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new Base_Audio();
        }
        return self::$instance;
    }

    private function __construct(){
        $this->audio = new getID3();
    }

    /**
     * 分析音频文件
     * @param string $fileName
     * @return boolean|array
     */
    public function getInfo($fileName){
        if(!$this->audio){
            return false;
        }
        return  $this->audio->analyze($fileName);
    }
    
    /**
     * 获取音频文件的播放时长
     * @param string $fileName
     * @return string
     */
    public function getLen($fileName){
        if(!$this->audio){
            return '';
        }
        $info  = $this->audio->analyze($fileName);
        return isset($info['playtime_string'])?strval($info['playtime_string']):'';
    }
}
