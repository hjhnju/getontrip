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
        if(strstr($_SERVER['HTTP_USER_AGENT'],"Chrome")){
            $this->chromeownload($hash);
        }else{
            $this->download($hash);
        }
        exit;
    }
    
    
    /** 下载
     * @param String  $file   要下载的文件路径
     * @param String  $name   文件名称,为空则与下载的文件名称一样
     * @param boolean $reload 是否开启断点续传
     */
    public function  download($hash){
        $oss   = Oss_Adapter::getInstance();
        $audio = $oss->getContent($hash);
        if(!empty($audio)){
            $file_size = $oss->getMetaLen($hash);
            $ranges = $this->getRange($file_size);
            ob_clean();
            header('cache-control:public');
            header('content-type:audio/mpeg');
    
            if($ranges!=null){ // 使用续传;
                header('HTTP/1.1 206 Partial Content');
                header('Accept-Ranges:bytes');
    
                // 剩余长度
                header(sprintf('content-length:%u',$ranges['end']-$ranges['start']));
    
                // range信息
                header(sprintf('content-range:bytes %s-%s/%s', $ranges['start'], $ranges['end'], $file_size));
    
                // fp指针跳到断点位置
                //fseek($fp, sprintf('%u', $ranges['start']));
                $audio = substr($audio,$ranges['start']);
            }else{
                header('Content-length:'.$file_size);
            }
            echo $audio; 
        }else{
            header("HTTP/1.1 404 Not Found");
        }
    }
    
    public function chromeownload($hash){
        $oss   = Oss_Adapter::getInstance();
        $audio = $oss->getContent($hash);
        $file_size = $oss->getMetaLen($hash);
        ob_clean();
        header('content-type:audio/mpeg');
        header('Content-length:'.$file_size);
        echo $audio;
    }
    
    
    
    /** 获取header range信息
     * @param  int   $file_size 文件大小
     * @return Array
     */
    private function getRange($file_size){
        if(isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])){
            $range = $_SERVER['HTTP_RANGE'];
            $range = preg_replace('/[\s|,].*/', '', $range);
            $range = explode('-', substr($range, 6));
            if(count($range)<2){
                $range[1] = $file_size;
            }
            $range = array_combine(array('start','end'), $range);
            if(empty($range['start'])){
                $range['start'] = 0;
            }
            if(empty($range['end'])){
                $range['end'] = $file_size;
            }
            return $range;
        }
        return null;
    }
}
