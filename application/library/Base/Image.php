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
     * 改变突变的大小
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
}