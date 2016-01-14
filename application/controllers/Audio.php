<?php
/**
 * OSS音频下载
 */
class AudioController extends Base_Controller_Page {

    public function init(){
        $this->setNeedLogin(false);
        parent::init();
    }

    /**
     * /audio/xxx.mp3
     * 音频数据下载接口
     * 
     */
    public function indexAction() {
        $hash = $this->_request->get('hash');
        $ext = explode(".",$hash);
        if (empty($hash) || !isset($ext[1])) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        $oss   = Oss_Adapter::getInstance();
        $audio = $oss->getContent($hash);
        if (empty($audio)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        ob_clean();
        if($ext[1] =='mp3'){
            header('Content-type: audio/mpeg') ;
        }elseif($ext[1] =='amr'){
            header('Content-type: audio/amr') ;
        }
        echo $audio;
        exit;
    }
}
