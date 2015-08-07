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
     * 上传图片
     */
    public function picAction() {
        if (empty($_FILES['file'])) {
            return $this->ajaxError(Base_RetCode::PARAM_ERROR);
        }

        //特殊处理剪贴板的图片 改为$_FILES['file']['type']
        $ext = substr($_FILES['file']['type'], -3);
        if (!in_array($ext, array('jpg', 'gif', 'png'))) {
             return $this->ajaxError(Base_RetCode::PARAM_ERROR);
        }
         
            
        $hash = md5(microtime(true));
        $hash = substr($hash, 8, 16);
        $filename = $hash . '.jpg';
        
        $oss = Oss_Adapter::getInstance();
        $res = $oss->writeFile($filename, $_FILES['file']['tmp_name']);
        if ($res) {
            @unlink($_FILES['file']['tmp_name']);
            $data = array(
                'hash' => $hash,
                'url'  => Base_Util_Image::getUrl($hash),
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
     * 删除图片
     * @return [type] [description]
     */
    public function delPicAction(){ 
       $oss = Oss_Adapter::getInstance();
       $filename = $_REQUEST['hash'].'.jpg';  
       //$filename='2a0a53495cd589fd.jpg';
       $res = $oss->remove($filename);
       if ($res) {
          return  $this->ajax();
       }
       return  $this->ajaxError();
    }
}
