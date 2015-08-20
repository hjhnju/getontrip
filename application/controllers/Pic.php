<?php
/**
 * OSS图片浏览
 */
class PicController extends Base_Controller_Page {

    public function init(){
        $this->setNeedLogin(false);
        parent::init();
    }

    /**
     * 图片浏览
     */
    public function indexAction() {
        $width  = 0;
        $height = 0;
        $hash = $this->_request->get('hash');
        if (empty($hash)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        $arrName = explode(".",$hash);
        $ary = explode('_', $arrName[0]);
        $hash = $ary[0];
        $cnt = count($ary);
        if ($cnt > 1) {
            $width = intval($ary[1]);
        }
        if ($cnt > 2) {
            $height = intval($ary[2]);
        }

        if (!empty($width) && empty($height)) {
            $height = $width;
        }
        $filename = $hash ."." .$arrName[1];
        $oss = Oss_Adapter::getInstance();
        $image = $oss->getContent($filename);
        if (empty($image)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        //TODO: 使用Base类
        // $imagick = new Base_Image_Imagick();
        // $imagick->read($blob);

        $imagick = new Imagick();
        $imagick->readimageblob($image);
        $md5 = md5($image);

        $mime = $imagick->getimagemimetype();
        //TODO:获取header头
        if($md5 == "8d4db6fe3f6b647784bc76c510b91cd4"){
            header("content-type: " . $mime, true, 304);
            $this->setBrowserCache($md5, 3600 * 24);
        } else {
            header("content-type: " . $mime);
            $this->setBrowserCache($md5, 3600 * 24);
            // @TODO 需要对图片跟缩略图做本地cache
            if ($width > 0 && $height > 0) {
                //$imagick->adaptiveResizeImage($width, $height);
                $imagick->cropthumbnailimage($width, $height);
            }
            $imagick->setimagecompressionquality(100);
            ob_clean();
            echo $imagick->getimageblob();
        }
        exit;
    }
}
