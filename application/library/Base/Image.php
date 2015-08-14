<?php
class Base_Image {
    /**
     * 为图片添加文字水印
     * @param [type] $src         [需要添加水印的图片]
     * @param [type] $text        [文本]
     * @param [type] $style       [array(
     *                              'font'  => '字体文件', 
     *                              'color' => array(0, 0, 0), 字体颜色
     *                              'size'  => '字体大小',
     *                            )]
     * @param [type] $position    [水印的位置,默认是5；-1随机,1上左,2上中,3上右,4中左,5中中,6中右,7下左,8下中,9下右]
     * @param [type] $transparent [透明度，默认是100标示不透明]
     */
    public static function addText($src, $text, $style, $position=5, $transparent=100) {
      $image = new Base_Image_Gd();
      $image->initText($src, $text, $style, $position, $transparent);
    }

    /**
     * 为图片添加水印功能
     * @param [type]  $src         [需要添加水印的图片]
     * @param [type]  $waterImg    [水印图片]
     * @param integer $position    [水印的位置,默认是5；-1随机,1上左,2上中,3上右,4中左,5中中,6中右,7下左,8下中,9下右]
     * @param integer $transparent [透明度，默认是100标示不透明]
     */
    public static function addWaterMark($src, $waterImg, $position=5, $transparent=100) {
        $image = new Base_Image_Gd();
        $image->initImg($src, $waterImg, $position, $transparent);
    }

    /**
     * 改变图片的大小
     * @param  $src 图片路径
     * @param  $width 期望图片宽度
     * @param  $height 期望图片高度
     * @param  $fit 适应大小方式，有如下几种方式
     *         force => 把图片强制变形成 $width X $height 大小
     *         scale => 按比例在安全框 $width X $height 内缩放图片, 输出缩放后图像大小 不完全等于 $width X $height
     *         scale_fill => 按比例在安全框 $width X $height 内缩放图片，安全框内没有像素的地方填充色, 使用此参数时可设置背景填充色 $fill_color = array(255,255,255)(红,绿,蓝, 透明度) 透明度(0不透明-127完全透明))
     *         其它: 智能模能 缩放图像并载取图像的中间部分 $width X $height 像素大小
     *         $fit = 'force','scale','scale_fill' 时： 输出完整图像
     *         $fit = 图像方位值时, 输出指定位置部分图像 ,字母与图像的对应关系如下:
     *         north_west   north   north_east
     *         west         center        east
     *         south_west   south   south_east
     * @param  $dst 图片裁剪后存放的位置
     */
    public static function resizeTo($src, $dst, $width=100, $height=100, $fit='center', $fill_color = array(255,255,255,0)) {
        $image = new Base_Image_Imagick();
        $image->open($src);
        $image->resize_to($width, $height, $fit, $fill_color);
        $image->save_to($dst);
    }
    
    /**
     * 获取图片的URL地址 Base_Image::getUrlByName($name, $width = 0, $height = 0)
     * @param string $name
     * @param number $width
     * @param number $height
     * @return string
     */
    public static function getUrlByName($name, $width = 0, $height = 0) {
        $arrName = explode(".",$name);
        $url     = "/pic/".$arrName[0];
        if ($width > 0) {
            $url .= "_{$width}";
            if ($height > 0) {
                $url .= "_{$height}";
            }
        }
        $url .= ".".$arrName[1];
        return $url;
    }


     /**
     * 获取图片的完整URL地址 Base_Image::getWholeUrlByName($name, $width = 0, $height = 0)
     * @param string $name
     * @param number $width
     * @param number $height
     * @return string
     */
    public static function getWholeUrlByName($name, $width = 0, $height = 0) {
        $url = Base_Config::getConfig('web')->root . Base_Image::getUrlByName($name, $width, $height);
        return $url;
    }


    /**
     * 裁剪图片并替换原有图片
     * @return [type] [description]
     */
    public static function cropPic($oldname,$x=0, $y=0, $width=100, $height=100){  
        if(empty($oldname)){
          return false;
        }
        $imgParams = Base_Image::getImgParams($oldname);
        
        $wholeUrl=Base_Image::getWholeUrlByName($oldname);
         
        $imagick = new Base_Image_Imagick();
        $image = $imagick->open($wholeUrl);
        //裁剪 
        $imagick->crop($x, $y, $width, $height); 
        //获取图片的二进制信息
        $imageBlob = $image->getImagesBlob();  


        $hash = md5(microtime(true));
        $hash = substr($hash, 8, 16);
        $filename = $hash . '.' .  $imgParams['img_type'];

        
        $oss = Oss_Adapter::getInstance();
        $res = $oss->writeFileContent($filename, $imageBlob);
        if ($res) {
            //删除原来的文件
            $oss = Oss_Adapter::getInstance(); 
            $oss->remove($oldname);

            $data = array(
                'hash' => $hash,
                'image' => $filename,
                'url'  => Base_Image::getUrlByName($filename),
            );  
            return $data;
        } 
        return false; 
    }

   /**
    * 获取图片的参数 
    * @param  string $name [description]
    * @return array      [图片的名称和类型]
    */
    public static function getImgParams($name){
        $arrName = explode(".",$name);
        return array(
             'img_hash'=>$arrName[0],
             'img_type'=>$arrName[1]
            );
    }

     /**
    * 获取图片的hash 
    * @param  string $name [description]
    * @return string      [图片的名称]
    */
    public static function getImgHash($name){
        $arrName = explode(".",$name);
        return $arrName[0];
    }

    /**
    * 获取图片的类型
    * @param  string $name [description]
    * @return string      [图片的类型]
    */
    public static function getImgType($name){
        $arrName = explode(".",$name);
        return $arrName[1];
    }
    
    /**
    * 根据src获取图片名字获取图片的类型
    * @param  string $name [description]
    * @return string      [图片的类型]
    */
    public static function getImgNameBySrc($src){
        $pat='/\/pic\/[a-za-z0-9]{16,16}.[jpg|gif]{3,3}/i'; 
        preg_match($pat, $src, $name);
        $name=preg_replace('/\/pic\//i','',$name); 
        if(count($name)==0){
            return '';
        }
        return $name[0];
    }

     /**
     * 根据content 获取图片name数组
     * @param  [string] $content [正文]
     * @return [array]          [图片name数组]
     */
    public static function getimgNameArray($content){ 
        $pat='/src=\"\/pic\/[a-za-z0-9]{16,16}.[jpg|gif]{3,3}/i';

        //将匹配成功的参数写入数组中 
        preg_match_all($pat, $content, $matches);  
        for($i=0;$i<count($matches[0]);$i++) {  
            $matches[0][$i]=preg_replace('/src=\"\/pic\//i','',$matches[0][$i]); 
          } 
        return $matches[0];
    }

}