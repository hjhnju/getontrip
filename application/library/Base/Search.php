<?php
/**
 * 全文检索接口
 */
class Base_Search {
    
    const PAGE_SIZE = 8;
    
    const HIGHT_LIGHT  = true;
    
    //所有检索字段
    protected static $arrPrams = array(
        'name',
        'content',
        'title',
        'subtitle',
        'desc',
    );
    
    //支持的检索表
    protected static $arrTypes = array(
        'topic',
        'sight',
        'book',
        'video',  
        'wiki',
        'city',
    );
    
    /**
     * 对theme、topic两表提供全文检索,eg:Base_Search::Search('topic','测试',1,10,array('id'))
     * @param string  $type，类型，eg:'topic','theme'
     * @param string  $word,检索关键词
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrNeedKey,需要返回的key
     */
    public static function Search($type,$word,$page = 1,$pageSize = self::PAGE_SIZE, $arrNeedKey = array()){
        $query         = '';
        $searchSizeMax = 10000;
        if($pageSize >= $searchSizeMax){
            $pageSize = $searchSizeMax;
        }
        $arrRet = array();
        if(!in_array(trim($type),self::$arrTypes)){
            return $arrRet;
        }
        foreach (self::$arrPrams as $val){
            //$query .= $val.":*".$word."* or ";
            $query .= $val.":".$word." or ";
        }
        $query = substr($query,0,-3);
        $query = urlencode($query);
        
        $arrParams = array(
            'id',
            'name',
            'title',
        );
        if(empty($arrNeedKey)){
            $param = implode(",",self::$arrPrams);
        }else{
            $param = implode(",",$arrNeedKey);
        }
        $param  = urlencode($param);
        $from   = ($page-1)*$pageSize;
        $url    = Base_Config::getConfig('solr')->url.'/solr/'.$type.'/select?q='.$query.'&wt=json&fl='.$param."&start=".$from."&rows=".$pageSize;
        if(self::HIGHT_LIGHT){
            if($type == 'book' || $type == 'video' || $type == 'topic'){
                $url   .= '&hl=true&hl.fl=title&hl.simple.pre='.urlencode('<b>').'&hl.simple.post='.urlencode('</b>')."&hl.requireFieldMatch=true";
            }else{
                $url   .= '&hl=true&hl.fl=name&hl.simple.pre='.urlencode('<b>').'&hl.simple.post='.urlencode('</b>')."&hl.requireFieldMatch=true";
            }  
        }        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $ret = curl_exec($ch);
        curl_close($ch);
        
        $arrRet = json_decode($ret,true);
        foreach ($arrRet['response']['docs'] as $key => $val){
            if($type == 'book'|| $type == 'video' || $type == 'topic'){
                $arrRet['response']['docs'][$key]['title'] = isset($arrRet['highlighting'][$val['id']]['title'][0])?$arrRet['highlighting'][$val['id']]['title'][0]:'';                
            }else{
                $arrRet['response']['docs'][$key]['name']  = isset($arrRet['highlighting'][$val['id']]['name'][0])?$arrRet['highlighting'][$val['id']]['name'][0]:'';
            }          
        }
        return $arrRet['response']['docs'];
    }
}
