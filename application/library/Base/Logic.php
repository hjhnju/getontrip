<?php
class Base_Logic{
     
    const TYPE_WIKI  = 'wiki';
    
    const TYPE_VIDEO = 'video';
    
    const TYPE_BOOK  = 'book';
        
    
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
    public function uploadPic($type,$word,$url){
        $oss = Oss_Adapter::getInstance();
        $content  = file_get_contents($url);
        
        $hash     = $type.$word;
        $filename = $hash . '.jpg'; 
        $oss->remove($filename);
        $res = $oss->writeFileContent($filename, $content);
        if($res){
           return $hash; 
        }
        return '';
    }
}