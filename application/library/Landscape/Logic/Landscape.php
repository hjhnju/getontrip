<?php
class Landscape_Logic_Landscape extends Base_Logic{
    
    protected $_fields;
    
    public function __construct(){
        $this->_fields = array('id','city_id','name','title','image','content','author','x','y','create_time','update_time','status');
    }
    
    /**
     * 添加景观
     * @param array $arrInfo
     * @return number|''
     */
    public function addLandscape($arrInfo){
        $objLandscape = new Landscape_Object_Landscape();
        $bCheck       = false;
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){
                $key                = $this->getprop($key);
                $objLandscape->$key = $val;
                $bCheck             = true;
            }
        }
        if($bCheck){
            $ret = $objLandscape->save();
        }
        if($ret){
            return $objLandscape->id;
        }
        return '';
    }
    
    /**
     * 编辑景观
     * @param integer $id
     * @param array $arrInfo
     * @return number|''
     */
    public function editLandscape($id,$arrInfo){
        $bCheck = false;
        $obj    = new Landscape_Object_Landscape();
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
        return $ret;
    }
    
    /**
     * 删除景观
     * @param integer $id
     * @return boolean
     */
    public function delLandscape($id){
        $obj    = new Landscape_Object_Landscape();
        $obj->fetch(array('id' => $id));        
        $listRelation = new Theme_List_Landscape();
        $listRelation->setFilter(array('landscape_id' => $id));
        $listRelation->setPagesize(PHP_INT_MAX);
        $ret = $listRelation->toArray();
        foreach ($ret['list'] as $val){
            $objRelation = new Theme_Object_Landscape();
            $objRelation->fetch(array('id' => $val['id']));
            $objRelation->remove();
        }
        return $obj->remove();
    }
    
    /**
     * 根据条件查询景观信息
     * @param array $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryLandscape($arrInfo,$page,$pageSize){
        $list = new Landscape_List_Landscape();
        foreach ($arrInfo as $key => $val){
            if(!in_array($key,$this->_fields)){
                unset($arrInfo[$key]);
            }
            if(isset($arrInfo['status']) &&($arrInfo['status'] == Landscape_Type_Status::ALL)){
                unset($arrInfo['status']);
            }
        }
        if(isset($arrInfo['name'])){
            $filter = "`name` like '%".$arrInfo['name']."%' ";
            unset($arrInfo['name']);
            foreach ($arrInfo as $key => $val){
                $filter .=" and `".$key."` = $val";
            }
            $list->setFilterString($filter);
        }elseif(!empty($arrInfo)){
            $list->setFilter($arrInfo);
        }
        $list->setPage($page);
        $list->setPagesize($pageSize);
        return $list->toArray();
    }
    
    /**
     * 获取景观总的访问人数
     * @param integer $landscapeId
     * @return integer
     */
    public function getTotalLandscapeVistUv($landscapeId){
        $redis   = Base_Redis::getInstance();
        $ret = $redis->hGet(Landscape_Keys::getLandscapeVisitKey(),Landscape_Keys::getTotalKey($landscapeId));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Visit_List_Visit();
        $list->setFields(array('device_id'));
        $list->setFilter(array('obj_id' => $landscapeId,'type' => Visit_Type::LANDSCAPE));
        $list->setPagesize(PHP_INT_MAX);
        $arrRet = $list->toArray();
        $arrTotal = array();
        foreach($arrRet['list'] as $val){
            if(!in_array($val,$arrTotal)){
                $arrTotal[] = $val;
            }
        }
        $redis->hSet(Landscape_Keys::getLandscapeVisitKey(),Landscape_Keys::getTotalKey($landscapeId),count($arrTotal));
        return count($arrTotal);
    }
    
    /**
     * 根据ID查询景观
     * @param integer $id
     * @return array
     */
    public function queryLandscapeById($id,$x='',$y=''){
        $obj = new Landscape_Object_Landscape();
        $obj->setFileds(array('id','x','y','image','name','content','city_id'));
        $obj->fetch(array('id' => $id));
        $ret = $obj->toArray();
        if(empty($ret['x']) && empty($ret['y'])){
            return $ret;
        }     
        $modelGis        = new GisModel();
        $logicTopic      = new Topic_Logic_Topic();
        $ret['dis']      = Base_Util_Number::getDis($modelGis->getEarthDistanceToPoint($x, $y, $ret['x'], $ret['y']));
        unset($ret['x']);
        unset($ret['y']);
        $logicCity       = new City_Logic_City();
        $city            = $logicCity->getCityById($ret['city_id']);
        $ret['city']     = str_replace('市', '', $city['name']);
        unset($ret['city_id']);
        $ret['image']    = Base_Image::getUrlByName($ret['image']);
        $ret['visit']    = strval($this->getTotalLandscapeVistUv($id)); 
        $arrTopics       = $logicTopic->searchTopic($ret['name'],1,PHP_INT_MAX);
        $ret['topicNum'] = $arrTopics['total'];
        $ret['topics']   = $arrTopics['list'];
        return $ret;
    }
}