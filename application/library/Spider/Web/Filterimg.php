<?php
/**
 * 过滤出img 并修改content
 * @author fanyy
 *
 */
class Spider_Web_Filterimg extends Spider_Web_Base{
    
    public function __construct($url,$type){
        parent::__construct($url,$type);
    }
    
    /**
     *  
     * @see Spider_Base::getBody()
     */
    public function getBody(){
         $strData = '';
        
       return $strData;      
    }
    
    /**
     * 获取图片的src url 数组 
     * @return [array] [url 字符数组]
     */
    public function getImgUrlArray(){ 

         $imgUrlArray=array(); 
         $imgDomArray=array();   
         foreach ($this->objDom->find('img') as $img){
            $oldSrc = $img->src;
            if($this->isUrl($oldSrc)){ 
                //去掉url 后面的参数
                $src= preg_replace("/\?(.)*/", '', $oldSrc);
                $img->src = $src;
                array_push($imgUrlArray, $src);
                array_push($imgDomArray, $img);

            }
         } 
        $this->imgUrlArray = $imgUrlArray;
        $this->imgDomArray = $imgDomArray;
        return $imgUrlArray;
    }

    /**
     * 上传图片
     * @return [type] [description]
     */
    public function uploadImgs(){
        $imgHashArray=array(); 
        foreach ($this->imgUrlArray as $picUrl){
            $hash = $this->uploadPic($picUrl);
            array_push($imgHashArray, $hash);
        }
        $this->imgHashArray = $imgHashArray; 
        return $imgHashArray;
    }
   
   /**
    * 更新img标签
    * @return [type] [description]
    */
    public function replaceImg(){
        $strData = '';
        $imgHashArray=$this->imgHashArray; 
        $imgDomArray = $this->imgDomArray;
        for($i=0;$i<count($imgHashArray);$i++){ 
            if($this->isUrl($imgDomArray[$i]->src)){  
              $imgDomArray[$i] ->setAttribute('data-hash',$imgHashArray[$i]);
              $imgDomArray[$i]->src = '/pic/'.$imgHashArray[$i].'.jpg';
            } 
        }  
        return $this->objDom->__toString();
    }

    /**
    * 综合上述操作  
    * @return [type] [description]
    */
    public function getReplacedContent(){ 

        $this->getImgUrlArray(); 
        $this->uploadImgs(); 
        return $this->replaceImg();
    }
    
    /**
     * 验证是否是url
     * @param  [type]  $str [description]
     * @return boolean    [description]
     */
    public function isUrl($str)  
    {  
        return preg_match('/^http[s]?:\/\/'.  
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'. // IP形式的URL- 199.194.52.184  
            '|'. // 允许IP和DOMAIN（域名）  
            '([0-9a-z_!~*\'()-]+\.)*'. // 域名- www.  
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. // 二级域名  
            '[a-z]{2,6})'.  // first level domain- .com or .museum  
            '(:[0-9]{1,4})?'.  // 端口- :80  
            '((\/\?)|'.  // a slash isn't required if there is no file name  
            '(\/[0-9a-zA-Z_!~\'
        \.;\?:@&=\+\$,%#-\/^\*\|]*)?)$/',  
            $str) == 1;  
    }
}