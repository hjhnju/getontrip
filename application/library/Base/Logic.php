<?php
class Base_Logic{
     
    const TYPE_WIKI      = 'wiki';
    
    const TYPE_VIDEO     = 'video';
    
    const TYPE_BOOK      = 'book';
        
    
    /**
     * 将字段中带有'_'的key进行处理
     * @param unknown $colname
     * @return string
     */  
    public function getprop($colname) {
        $tmp = explode('_', $colname);
        for($i = 1; $i < count($tmp); $i++) {
            $tmp[$i] = ucfirst($tmp[$i]);
        }
        $colname = implode($tmp);
        return $colname;
    }
    
    /**
     * 取出string 中从from 到to的字符串
     * @param string $strFrom
     * @param string $strTo
     * @param string $string
     * @return array
     */
    public function getSubstr($strFrom, $strTo, $string){
        $pattern = "@$strFrom(.*)$strTo@Uis";
        preg_match_all($pattern, $string, $out);
        if(isset($out[1])){
            return $out[1];
        }
        return $out;
    }
    
    /**
     * 将URL中给出的图片上传一到阿里云
     * @param string $url
     * @return string $hash
     */
    public function uploadPic($url,$refer=''){
        $oss      = Oss_Adapter::getInstance();        
        $ch       = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_REFERER, $refer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        $content = curl_exec($ch);
        curl_close($ch);
        $im = @imagecreatefromstring($content);        
        if(empty($url) || empty($content) || empty($im)){
            return '';
        }   
        $hash     = md5(microtime(true));
        $hash     = substr($hash, 8, 16);
        $ext      = explode(".",$url);
        $count    = count($ext);
        $name     = trim($ext[$count-1]);
        if($name == 'gif'){
            $filename = $hash . '.gif';
        }else{
            $filename = $hash . '.jpg';
        }
        $res      = $oss->writeFileContent($filename, $content);
        if($res){
           return $filename; 
        }
        return '';
    }
    
    public function upPicData($file){
        $ext = explode("/",$file['type']);
        if (!isset($ext[1])||!in_array($ext[1], array('jpg', 'gif', 'jpeg','png'))) {
            return false;
        }
        
        $hash = md5(microtime(true));
        $hash = substr($hash, 8, 16);
        if(trim($ext[1]) == 'gif'){
            $filename = $hash . '.gif';
        }else{
            $filename = $hash . '.jpg';
        }
        
        $oss = Oss_Adapter::getInstance();
        $res = $oss->writeFile($filename, $file['tmp_name']);
        if($res){
            return $filename;
        }else{
            return false;
        }
    }
    
    /**
     * 上传音频数据
     * @param string $filename,文件路径及名称
     * @param string $data,文件二进制文件
     * @return string|boolean
     */
    public function upAudioData($filename){
        $oss           = Oss_Adapter::getInstance();
        $arrTemp       = explode("/",$filename);
        $name          = md5(microtime(true));
        $name          = substr($name, 8, 16);
        
        $ext      = explode(".",$filename);
        $count    = count($ext);
        $name    .= ".".trim($ext[$count-1]);
        
        $res = $oss->writeFile($name, $filename);
        if($res){
            return $name;
        }else{
            return false;
        }
    }
    
    /**
     * 删除图片
     * @param string $name
     * @return boolean
     */
    public function delPic($name){
        $oss  = Oss_Adapter::getInstance();
        return $oss->remove($name);
    }
    
    /**
     * redis key的排序
     * @param array $arrKeys
     * @param integer $order
     * @return array
     */
    public function  keySort($arrKeys,$order = SORT_ASC){
        $arrWeight = array();
        foreach ($arrKeys as $key){
            $temp  = explode("_",$key);
            $count = count($temp);            
            $arrWeight[] = $temp[$count-1];
        }
        array_multisort($arrWeight, $order , $arrKeys);
        return $arrKeys;
    }
}