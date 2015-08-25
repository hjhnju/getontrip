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
        $width    = 0;
        $height   = 0;
        $quality  = 100;
        $scaleType = '';
        $hash = $this->_request->get('hash');
        if (empty($hash)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        $arrName  = explode("@",$hash);
        if(isset($arrName[1])){
            $filename = $arrName[0] ;
            $arrData = explode("_",$arrName[1]);
            foreach ($arrData as $val){
                $num  = strlen($val);
                $char = strtolower($val[$num-1]);
                $val  = substr($val,0,$num-1);
                switch ($char) {
                    case 'h':
                        if(!is_numeric($val)){
                            $scaleType = strtolower($val[0]);
                            $val  = substr($val,1,$num);
                        }
                        $height = intval($val);
                        break;
                    case 'w':
                        if(!is_numeric($val)){
                            $scaleType = strtolower($val[0]);
                            $val  = substr($val,1,$num);
                        }
                        $width = intval($val);
                        break;
                    case 'q':
                        $quality = intval(substr($val,0,$num-1));
                        break;
                    default :
                        break;
                }
            }
        }else{
            $filename = $hash ;
        }
        $oss = Oss_Adapter::getInstance();
        $image = $oss->getContent($filename);
        if (empty($image)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        $imagick = new Base_Image_Imagick();
        $imagick->read($image);
        $md5     = md5($image.$filename);
        $mime    = $imagick->get_type();
        ob_clean();
        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && ($md5 == $_SERVER['HTTP_IF_NONE_MATCH'])){
            $this->setBrowserCache($md5, 3600 * 24);
            header("content-type: " . $mime, true, 304);           
        } else {
            $this->setBrowserCache($md5, 3600 * 24);
            if ($width > 0 || $height > 0) {
                if(empty($scaleType) || $scaleType == 'e'){
                    //固定一条边,另一条边按原图比例进行缩放
                    $realHeight = $imagick->get_height();
                    $realWidth  = $imagick->get_width();
                    if(!empty($height) && !empty($width)){
                        $rateHeight = $realHeight/$height;
                        $rateWidth  = $realWidth/$width;
                        if($rateHeight >= $rateWidth){
                            $height = $realHeight/$rateWidth;
                        }else{
                            $width  = $realWidth/$rateHeight;
                        }
                    }                    
                    $imagick->resize_to($width,$height,'force');
                }elseif($scaleType == 'c'){
                    //居中适应的缩放并裁剪
                    $imagick->resize_to($width,$height);
                }elseif($scaleType == 'f'){
                    //强制缩放不裁剪
                    $imagick->resize_to($width,$height,'force');
                }
            }
            echo $imagick->output($quality);
        }
        exit;
    }
}
