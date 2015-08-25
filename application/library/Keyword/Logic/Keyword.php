<?php
class Keyword_Logic_Keyword extends Base_Logic{
    
    protected $_fields;
    
    public function __construct(){
        $this->_fields = array('id','sight_id','name','url','create_time','update_time','status');
    }
    
    /**
     * 查询词条列表
     * @param integer $sight_id
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryKeywords($page, $pageSize,$arrInfo){
        $list  = new Keyword_List_Keyword();
        $arr   = array();
        if(isset($arrInfo['status']) && $arrInfo['status'] != Keyword_Type_Status::ALL){
            $arr['status'] = $arrInfo['status'];            
        }
        if(!empty($arrInfo['sight_id'])){
            $arr['sight_id'] = $arrInfo['sight_id'];
            $list->setOrder('weight asc');
            unset($arrInfo['sight_id']);
        }else{
            $list->setOrder('update_time desc');
        }
        if(isset($arrInfo['status'])){
           unset($arrInfo['status']); 
        }
        $arr = array_merge($arr,$arrInfo);
        if(!empty($arr)){
            $list->setFilter($arr);
        }
               
        $list->setPage($page);
        $list->setPagesize($pageSize);
        return $list->toArray();
    }
    
    /**
     * 添加词条信息
     * @param array $arrInfo
     * @return boolean
     */
    public function addKeywords($arrInfo){
        $bCheck = false;
        $obj    = new Keyword_Object_Keyword();
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){  
                $key = $this->getprop($key);              
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if($bCheck){
            $num = $this->getKeywordNumBySight($arrInfo['sight_id']);
            $obj->weight = $num + 1;
            $ret = $obj->save();
        }
        if($ret){
            if($obj->status == Keyword_Type_Status::PUBLISHED){
                $conf = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
                $url  = $_SERVER["HTTP_HOST"]."/InitData?sightId=".$obj->sightId."&type=Wiki&num=".$conf['thirddata'] ['initnum'];
                $http = Base_Network_Http::instance()->url($url);
                $http->timeout(1);
                $http->exec();
            }
            return $obj->id;
        }
        return '';
    }
    
    /**
     * 词条编辑
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public function editKeyword($id,$arrInfo){
        $bCheck = false;
        $obj    = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $id));
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){  
                $key = $this->getprop($key);              
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if($bCheck){
            $ret =  $obj->save();
        }
        if($ret){
            if($obj->status == Keyword_Type_Status::PUBLISHED){
                $conf = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
                $url  = $_SERVER["HTTP_HOST"]."/InitData?sightId=".$obj->sightId."&type=Wiki&num=".$conf['thirddata'] ['initnum'];
                $http = Base_Network_Http::instance()->url($url);
                $http->timeout(1);
                $http->exec();
            }
            return $obj->id;
        }
        return '';
    }
    
    /**
     * 删除词条
     * @param integer $id
     * @return boolean
     */
    public function delKeyword($id){
        $redis     = Base_Redis::getInstance();
        $wordInfo  = $this->queryById($id);
        $sightId   = $this->getSightId($id);
        $arrKeys   = $redis->keys(Wiki_Keys::getWikiInfoName($sightId, "*"));
        foreach ($arrKeys as $key){
            $data = $redis->hGetAll($key);
            if($data['title'] == $wordInfo['name']){
                $arrTemp = explode("_",$key);
                $id      = $arrTemp[2];
                $redis->delete($key);
            }
        }
        $arrKeys = $redis->keys(Wiki_Keys::getWikiCatalogName($sightId, $id,"*"));
        foreach ($arrKeys as $key){
            $redis->delete($key);
        }
        $obj    = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $id));
        return $obj->remove();
    }
    
    /**
     * 根据ID查询词条
     * @param integer $id
     * @return array
     */
    public function queryById($id){
        $obj = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $id));
        return $obj->toArray();
    }
    
    /**
     * 根据词条ID获取景点ID
     * @param unknown $keywordId
     * @return number
     */
    public function getSightId($keywordId){
        $obj = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $keywordId));
        return $obj->sightId;
    }
    
    /**
     * 根据词条名称获取ID
     * @param string $name
     * @return integer
     */
    public function getWordIdByName($name){
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('name' => $name));
        return $objKeyword->id;
    }
    
    /**
     * 根据景点ID获取词条数
     * @param integer $sightId
     * @return integer
     */
    public function getKeywordNumBySight($sightId){
        $listKeyword = new Keyword_List_Keyword();
        $listKeyword->setFilter(array('sight_id'=>$sightId));
        $ret = $listKeyword->toArray();
        return $ret['total'];
    }
}