<?php
/**
 * 全文检索接口
 */
class Base_Search {
    
    const PAGE_SIZE = 8;
    
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
        'theme',  
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
        $query  = '';
        $arrRet = array();
        if(!in_array(trim($type),self::$arrTypes)){
            return $arrRet;
        }
        foreach (self::$arrPrams as $val){
            $query .= $val.":".$word." or ";
        }
        $query = substr($query,0,-3);
        $query = urlencode($query);
        
        $arrParams = array(
            'id',
            'name',
        );
        if(empty($arrNeedKey)){
            $param = implode(",",self::$arrPrams);
        }else{
            $param = implode(",",$arrNeedKey);
        }
        $param  = urlencode($param);
        $from   = ($page-1)*$pageSize;
        $url    = Base_Config::getConfig('solr')->url.'/solr/'.$type.'/select?q='.$query.'&wt=json&fl='.$param."&start=".$from."&rows=".$pageSize;
        $ret    = file_get_contents($url);
        $arrRet = json_decode($ret,true);
        return $arrRet['response']['docs'];
    }
}
