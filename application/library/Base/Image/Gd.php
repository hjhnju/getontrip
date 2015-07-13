<?php
class Base_Image_Gd{
	//水印类型：0为文字水印、1为图片水印
	public $waterType=1;

	//-1随机,1上左,2上中,3上右,4中左,5中中,6中右,7下左,8下中,9下右,默认9
	public $position=9;

	//水印透明度
	public $transparent=100;

	//水印图片
	public $waterImg='';

	//水印文字
	public $waterStr='';

	//文字字体大小，默认为12
	public $fontSize=12;

	//水印文字颜色(RGB),默认为黑色:array(0,0,0)
	public $fontColor=array(0,0,0);

	//字体文件,建议使用绝对路径,默认可以为空
	public $fontFile='';

	//需要添加水印的图片
	public $srcImg='';

	//图片句柄
	private $im='';

	//水印图片句柄
	private $water_im='';

	//源图片信息
	private $srcImg_info='';

	//水印图片信息
	private $waterImg_info='';

	//水印文字宽度
	private $str_w='';

	//水印文字高度
	private $str_h='';

	//水印X坐标
	private $x='';

	//水印y坐标
	private $y='';

	/**
	 * 构造函数 do nothing
	 */
	function __construct(){
	}

	/**
	 * 获取需要添加水印的图片的信息，并载入图片。
	 * @return [type] [description]
	 */
	private function imginfo(){
		$this->srcImg_info=getimagesize($this->srcImg);
		switch($this->srcImg_info[2]){
			case 3:
				$this->im=imagecreatefrompng($this->srcImg);
				break;
			case 2:
				$this->im=imagecreatefromjpeg($this->srcImg);
				break;
			case 1:
				$this->im=imagecreatefromgif($this->srcImg);
				break;
			default:
				die('源图片格式不对,只支持png/jpeg/gif/jpg.');
				break;
		}
	}

	/**
	 * 获取水印图片的信息，并载入图片。
	 * @return [type] [description]
	 */
	private function waterimginfo(){
		$this->waterImg_info=getimagesize($this->waterImg);
			switch($this->waterImg_info[2]){
				case 3:
					$this->water_im=imagecreatefrompng($this->waterImg);
					break;
				case 2:
					$this->water_im=imagecreatefromjpeg($this->waterImg);
					break;
				case 1:
					$this->water_im=imagecreatefromgif($this->waterImg);
					break;
				default:
					die('水印图片格式不对,只支持png/jpeg/gif/jpg.');
					break;
		}
	}

	/**
	 * 字符串出现的位置
	 * -1随机,1上左,2上中,3上右,4中左,5中中,6中右,7下左,8下中,9下右
	 * @return [type] []
	 */
	private function waterpos(){
		switch ($this->position){
			case -1:
				$this->x=rand(0,$this->srcImg_info[0]-$this->waterImg_info[0]);
				$this->y=rand(0,$this->srcImg_info[1]-$this->waterImg_info[1]);
				break;
			case 1:
				$this->x=0;$this->y=0;
				break;
			case 2:
				$this->x=($this->srcImg_info[0]-$this->waterImg_info[0])/2;$this->y=0;
				break;
			case 3:
				$this->x=$this->srcImg_info[0]-$this->waterImg_info[0];$this->y=0;
				break;
			case 4:
				$this->x=0;$this->y=($this->srcImg_info[1]-$this->waterImg_info[1])/2;
				break;
			case 5:
				$this->x=($this->srcImg_info[0]-$this->waterImg_info[0])/2;
				$this->y=($this->srcImg_info[1]-$this->waterImg_info[1])/2;
				break;
			case 6:
				$this->x=$this->srcImg_info[0]-$this->waterImg_info[0];
				$this->y=($this->srcImg_info[1]-$this->waterImg_info[1])/2;
				break;
			case 7:
				$this->x=0;$this->y=$this->srcImg_info[1]-$this->waterImg_info[1];
				break;
			case 8:
				$this->x=($this->srcImg_info[0]-$this->waterImg_info[0])/2;
				$this->y=$this->srcImg_info[1]-$this->waterImg_info[1];
				break;
			default:
				$this->x=$this->srcImg_info[0]-$this->waterImg_info[0];
				$this->y=$this->srcImg_info[1]-$this->waterImg_info[1];
				break;
		}
	}

	/**
	 * 水印图片
	 * @return [type] [description]
	 */
	private function waterimg(){
		if($this->srcImg_info[0]<=$this->waterImg_info[0] || 
				$this->srcImg_info[1]<=$this->waterImg_info[1]){
			return false;
		}
		$this->waterpos();
		$cut=imagecreatetruecolor($this->waterImg_info[0],$this->waterImg_info[1]);
		imagecopy($cut,$this->im,0,0,$this->x,$this->y,$this->waterImg_info[0],$this->waterImg_info[1]);
		imagecopy($cut,$this->water_im,0,0,0,0,$this->waterImg_info[0],$this->waterImg_info[1]);
		imagecopymerge($this->im,$cut,$this->x,$this->y,0,0,$this->waterImg_info[0],$this->waterImg_info[1],$this->transparent);
	}

	/**
	 * 图片中天加文字
	 * @return [type] [description]
	 */
	private function waterstr(){
		$rect=imagettfbbox($this->fontSize,0,$this->fontFile,$this->waterStr);
		$w=abs($rect[2]-$rect[6]);
		$h=abs($rect[3]-$rect[7]);
		$fontHeight=$this->fontSize;
		$this->water_im=imagecreatetruecolor($w,$h);
		imagealphablending($this->water_im,false);
		imagesavealpha($this->water_im,true);
		$white_alpha=imagecolorallocatealpha($this->water_im,255,255,255,127);
		imagefill($this->water_im,0,0,$white_alpha);
		$color=imagecolorallocate($this->water_im,$this->fontColor[0],$this->fontColor[1],$this->fontColor[2]);
		imagettftext($this->water_im,$this->fontSize,0,0,$this->fontSize,$color,$this->fontFile,$this->waterStr);
		$this->waterImg_info=array(0=>$w,1=>$h);
		$this->waterimg();
	}

	/**
	 * 水印图片入口函数
	 * @param  [type] $c_srcimg      [目标图片]
	 * @param  [type] $c_waterimg    [水印图片]
	 * @param  [type] $c_position    [水印的位置]
	 * @param  [type] $c_transparent [透明度]
	 * @return [type]                [description]
	 */
	function initImg($c_srcimg, $c_waterimg, $c_position, $c_transparent){
		$this->srcImg=file_exists($c_srcimg)?$c_srcimg:die('源文件不存在!');
		if($c_waterimg) {
			$this->waterImg=$c_waterimg;
		}else {
			die('请指定水印文件!');
		}
		$this->waterType=1;
		if($c_position){
			$this->position=$c_position;
		} 
		if($c_transparent) {
			$this->transparent=$c_transparent;
		}
		$this->outputWater();
	}

	/**
	 * 水印文字入口函数
	 * @param  [type] $c_srcimg      [目标图片]
	 * @param  [type] $c_fontText    [文字]
	 * @param  $style = array(
	 *                  'font' => '',字体文件
	 *                  'color' => '',字体颜色
	 *                  'size' => '',字体大小
	 *                  )
	 * @param  [type] $c_positioin   [文字出现的位置]
	 * @param  [type] $c_transparent [透明度]
	 * @return [type]                [description]
	 */
	function initText($c_srcimg, $c_fontText, $style, $c_positioin, $c_transparent){
		$c_fontFamilyPath 	= isset($style['font'])? $style['font'] : '';
		$c_fontColor		= isset($style['color'])? $style['color'] : array(0, 0, 0);
		$c_fontSize			= isset($style['size'])? $style['size'] : '16';
		$this->srcImg=file_exists($c_srcimg)?$c_srcimg:die('源文件不存在!');
		if($c_fontText) {
			$this->waterStr=$c_fontText;
		}else {
			die('请指定水印文字内容!');
		}
		if($c_fontSize) {
			$this->fontSize=$c_fontSize;
		}
		if($c_fontColor) {
			$this->fontColor=$c_fontColor;
		}
		if($c_fontFamilyPath) {
			$this->fontFile=$c_fontFamilyPath;
		}
		if($c_positioin) {
			$this->position=$c_positioin;
		}
		if($c_transparent) {
			$this->transparent=$c_transparent;
		}
		$this->waterType=0;
		$this->outputWater();
	}

	/**
	 * 输出函数
	 * @return [type] [description]
	 */
	function outputWater(){
		$this->imginfo();
		if($this->waterType==0){
			$this->waterstr();
		}else{
			$this->waterimginfo();
			$this->waterimg();
		}
		switch($this->srcImg_info[2]){
			case 3:
				!@imagepng($this->im,$this->srcImg);
				break;
			case 2:
				!@imagejpeg($this->im,$this->srcImg);
				break;
			case 1:
				!@imagegif($this->im,$this->srcImg);
				break;
			default:
				die('添加水印失败!');
				break;
		}
		imagedestroy($this->im);
		imagedestroy($this->water_im);
	}
}