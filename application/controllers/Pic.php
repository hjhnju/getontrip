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
     * /pic/xxx.jpg             后面可以拼接尺寸参数如下几种情况：
     * @200h_100w、@e200h_e100w  固定一条边,另一条边按原图比例进行缩放 
     * @c200h_c100w              居中适应的缩放并裁剪
     * @f200h_f100w              强制缩放不裁剪
     * @参数后面还可以加r,s设置模糊效果,如@e200h_e100w_9r_3s
     */
    public function indexAction() {
        $width     = 0;
        $height    = 0;
        $quality   = 100;
        $scaleType = '';
        $radius    = '';
        $sigma     = '';
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
                    case 'r':
                        $radius = intval(substr($val,0,$num-1));
                        break;
                    case 's':
                        $sigma  = intval(substr($val,0,$num-1));
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
            echo $imagick->output($quality,$radius,$sigma);
        }
        exit;
    }
}
