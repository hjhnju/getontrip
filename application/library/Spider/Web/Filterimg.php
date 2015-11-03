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
     * 获取图片的src url 数组 imgUrlArray
     * imgDomArray 图片dom 对象数组
     * @return [array] [url 字符数组]
     */
    public function getImgUrlArray(){ 

         $imgUrlArray=array(); 
         $imgDomArray=array();   
         foreach ($this->objDom->find('img') as $img){
            $oldSrc = $img->src; 
            //是否已经上传过
            $isUploaded=$this->isUploaded($oldSrc);
            if(!$isUploaded){
                //去掉url 后面的参数
                $src= preg_replace("/\?(.)*/", '', $oldSrc);
                $img->src = $src;
                array_push($imgUrlArray, $src);
                array_push($imgDomArray, $img);  
               
            }else{ 
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
    public function uploadImgs($refer){
        $imgNameArray=array(); 
        foreach ($this->imgUrlArray as $picUrl){
            //已经上传过的图片，自己网站的图片,则不用上传
            //在这里处理是为了  如果从页面复制本站图片，没有data-image属性 
            //而且 src默认添加上域名了， 在下一步 需要过滤掉
            if($this->isOurUrl($picUrl)){  
                preg_match('/[a-za-z0-9]{16,16}.[jpg|gif]{3,3}/i', $picUrl, $hashs);
                //$hash=preg_replace('/.[jpg|gif]{3,3}/i','',$hashs[0]); 
                $name = $hashs[0]; 
            }else{
               //其他站图片则根据url上传到云 
                $name = $this->uploadPic($picUrl,$refer); 

            }
            array_push($imgNameArray, $name);
        }
        $this->imgNameArray = $imgNameArray; 
        return $imgNameArray;
    }
   
   /**
    * 更新img标签
    * @return [type] [description]
    */
    public function replaceImg(){
        $strData = '';
        $imgNameArray=$this->imgNameArray; 
        $imgDomArray = $this->imgDomArray;
        for($i=0;$i<count($imgNameArray);$i++){   
              //$imgDomArray[$i] ->setAttribute('data-image',$imgNameArray[$i]);
              $imgDomArray[$i]->src = Base_Image::getUrlByName($imgNameArray[$i]);
        }   
    } 

    /**
     * 替换掉多余的回车等
     * @param  [string] $content [description]
     * @return [type] [description]
     */
    public function replaceBrs($content){ 
       //去掉空的段落 
       $content = preg_replace('/<p>[\s|\n|\r]*<\/p>/','',$content); 
       //去掉多余的回车
       $content = $this->replaceByPattern('/<br>[\s|\n|\r]*<br>/','<p><br></p>',$content);
       $content = $this->replaceByPattern('/<p>[\s|\n|\r]*<br>[\s|\n|\r]*<\/p>[\s|\n|\r]*<br>/','<p><br></p>',$content);
       $content = $this->replaceByPattern('/<br>[\s|\n|\r]*<p>[\s|\n|\r]*<br>[\s|\n|\r]*<\/p>/','<p><br></p>',$content); 
       $content = $this->replaceByPattern('/<p>[\s|\n|\r]*<br>[\s|\n|\r]*<\/p>[\s|\n|\r]*<p>[\s|\n|\r]*<br>[\s|\n|\r]*<\/p>/','<p><br></p>',$content);
       
       return $content;
    }

    /**
    * 综合上述操作 [用于编辑话题，上传图片] 
    * @return [type] [description]
    */
    public function getReplacedContent($refer=''){

        $this->getImgUrlArray(); 
        $this->uploadImgs($refer); 
        $this->replaceImg();
        $content=$this->objDom->outertext;
        $obj   = new Base_Extract('',$content);
        $data  = $obj->preProcess();
        $content   = $obj->dataUpdate($data);
        ob_clean(); 
        //$content=$this->dataClean($content,false); 
        return $content;
    }



    /**
     * 过滤掉多余的回车
     * @param  [string] $pattern    [正则表达式]
     * @param  [string] $replaceStr [替换成]
     * @param  [string] $subject    [转换的对象]
     * @return [string]             [description]
     */
    public function replaceByPattern($pattern,$replaceStr,$subject){
       preg_match_all($pattern, $subject, $matches);  
       for ($i=0; $i < count($matches[0]); $i++) {  
           $subject = preg_replace($pattern, $replaceStr, $subject);
           preg_match_all($pattern, $subject, $matches_tmp);  
           if(count($matches_tmp[0])==0){
               break;
           }
       } 
       return $subject;
    }



    /**
    * 处理正文中的图片 [用于客户端详情显示] 
    * @return [type] [description]
    */
    public function getContentToDis(){ 
       foreach ($this->objDom->find('img') as $img){
            $oldSrc = $img->src; 
            $web =Base_Config::getConfig('web');

            //定义默认占位图片
            $img->src = $web->stroot . '/v1/' . $web->version . '/asset/common/img/imgloading.png'; 
            $img->setAttribute('data-actualsrc',$web->root . $oldSrc);  
            
          } 
        $content = $this->objDom->__toString();
        return $content;
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
    
    /*
     是否是本站图片
     */
    public function isOurUrl($str){
        $ourUrl=parse_url(Base_Config::getConfig('web')->root);
        $url=parse_url($str);
        return $ourUrl['host']==$url['host'];
    }

     /**
     * 只保留img br p
     * @param  [string] $content [description]
     * @return [string]          [description]
     */
    public function dataClean($content,$bSourceOther){ 
        //只保留img br p  
       $obj   = new Base_Extract('',$content);
       $content  =  $obj->preProcess(); 
       $content  =  $obj->dataClean($content,$bSourceOther); 
       ob_clean();  
       return $content;
    }
}