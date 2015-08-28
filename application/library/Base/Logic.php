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
    public function uploadPic($url){
        $oss      = Oss_Adapter::getInstance();
        $content  = file_get_contents($url);  
        if(empty($url) || empty($content)){
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