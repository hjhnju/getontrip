<?php
/**
 * 上传功能
 */
class UploadController extends Base_Controller_Page {

    public function init(){
        $this->setNeedLogin(false);
        parent::init();
    }
    
    /**
     * 接口1：/upload/urlpic
     * 上传图像，根据一个URL上传图像
     * @param string url，头像的链接
     * @return json
     * 
     */
    public function urlPicAction(){
        $url = isset($_REQUEST['url'])?trim($_REQUEST['url']):'';
        if(empty($url)){
            return $this->ajaxError();
        }
        $hash = md5(microtime(true));
        $hash = substr($hash, 8, 16);
        $arrName = explode(".",$url);
        $count   = count($arrName);
        if(trim($arrName[$count-1]) == 'gif'){
            $filename = $hash . '.gif';
        }else{
            $filename = $hash . '.jpg';
        }
        $logic = new Base_Logic();
        $ret = $logic->uploadPic($url);
        return $this->ajax($ret);
    }
    
    /**
     * 上传图片
     */
    public function picAction() {
        if (empty($_FILES['file'])) {
            return $this->ajaxError(Base_RetCode::PARAM_ERROR);
        }

        //特殊处理剪贴板的图片 改为$_FILES['file']['type']
        $ext = explode("/",$_FILES['file']['type']);
        $filename = isset($_REQUEST['filename'])?trim($_REQUEST['filename']):'';
        if (!isset($ext[1])||!in_array($ext[1], array('jpg', 'gif', 'jpeg','png'))) {
             return $this->ajaxError(Base_RetCode::PARAM_ERROR);
        }
        $hash = md5(microtime(true));
        $hash = substr($hash, 8, 16);
        if(empty($filename)){
            if(trim($ext[1]) == 'gif'){
                $filename = $hash . '.gif';
            }else{
                $filename = $hash . '.jpg';
            }
        }              
        
        $oss = Oss_Adapter::getInstance();
        $res = $oss->writeFile($filename, $_FILES['file']['tmp_name']);
        if ($res) {
            @unlink($_FILES['file']['tmp_name']);
            $data = array(
                'hash' => $hash,
                'image' => $filename,
                'url'  => Base_Image::getUrlByName($filename),
            );
            $res = array(
                'status' => 0,
                'statusInfo' => '',
                'data' => $data,
            );
            echo json_encode($res);
            return false;
        }

        $msg = array(
            'hash' => $hash,
            'file' => $_FILES['file']['name'],
        );
        Base_Log::warn($msg);
        $this->ajaxError();
    }
    
    /**
     * 上传音频
     */
    public function audioAction() {
        if (empty($_FILES['file'])) {
            return $this->ajaxError(Base_RetCode::PARAM_ERROR);
        }
    
        //特殊处理剪贴板的图片 改为$_FILES['file']['type']
        $ext = explode("/",$_FILES['file']['type']);
        if (!isset($ext[1])||!in_array($ext[1], array('mp3','amr'))) {
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $hash = md5(microtime(true));
        $hash = substr($hash, 8, 16);
        $filename = $hash.".".$ext[1];
    
        $oss = Oss_Adapter::getInstance();
        $res = $oss->writeFile($filename, $_FILES['file']['tmp_name']);
        if ($res) {
            $data = array(
                'name' => $filename,
                'url'  => "/audio/".$filename,
                'len'  => Base_Audio::getInstance()->getLen($_FILES['file']['tmp_name']),
            );
            @unlink($_FILES['file']['tmp_name']);
            $res = array(
                'status' => 0,
                'statusInfo' => '',
                'data' => $data,
            );
            echo json_encode($res);
            return false;
        }
    
        $msg = array(
            'name' => $filename,
            'file' => $_FILES['file']['name'],
        );
        Base_Log::warn($msg);
        $this->ajaxError();
    }
    


    /**
     * 删除图片
     * @return [type] [description]
     */
    public function delPicAction(){ 
       $oss      = Oss_Adapter::getInstance();
       $filename = $_REQUEST['image'];
       $res      = $oss->remove($filename);
       if ($res) {
          return  $this->ajax();
       }
       return  $this->ajaxError();
    }
}
