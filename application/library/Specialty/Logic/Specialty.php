<?php
class Specialty_Logic_Specialty extends Base_Logic{
    
    const PAGE_SIZE = 20;
    
    const DEFAULT_WEIGHT = 0;
    
    protected $fields = array('sight_id', 'title', 'url', 'image', 'from', 'len', 'type', 'status', 'create_time', 'update_time', 'create_user', 'update_user');
    
    public function __construct(){
        
    }
    
    /**
     * 获取视频信息,供后端使用     
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public function getSpecialtys($page,$pageSize,$arrParam = array()){
        $arrSpecialty['list']   = array();
        $arrRet['list']     = array();
        $title    = '';
        if(isset($arrParam['sight_id'])){
            $sightId    = $arrParam['sight_id'];
            $arrSpecialtyIds = array();            
            $listSightSpecialty = new Sight_List_Specialty();
            $listSightSpecialty->setFilter(array('sight_id' => $sightId));
            if(isset($arrParam['order'])){
                $listSightSpecialty->setOrder($arrParam['order']);
                unset($arrParam['order']);
            }
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $ret = $listSightSpecialty->toArray();
            if(isset($arrParam['title'])){
                $title  = $arrParam['title'];
                unset($arrParam['title']);
            }
            foreach ($ret['list'] as $val){
                $arrParam = array_merge($arrParam,array('id' => $val['Specialty_id']));                
                $objSpecialty = new Specialty_Object_Specialty();
                $objSpecialty->fetch($arrParam);
                $arrTmpSpecialty = $objSpecialty->toArray();
                if(!empty($title) && isset($arrTmpSpecialty['title'])){
                    if( false ==  strstr($arrTmpSpecialty['title'],$title)){
                        continue;
                    }
                }
                if(!empty($arrTmpSpecialty)){
                    $arrSpecialty['list'][] = $arrTmpSpecialty['id'];
                }
            }          
            $arrSpecialty['page'] = $page;
            $arrSpecialty['pagesize'] = $pageSize;
            $arrSpecialty['pageall'] = ceil(count($arrSpecialty['list'])/$pageSize);
            $arrSpecialty['total'] = count($arrSpecialty['list']);
            $arrSpecialty['list'] = array_slice($arrSpecialty['list'], ($page-1)*$pageSize,$pageSize); 
        }else{
            $listSpecialty = new Specialty_List_Specialty();
            if(!empty($arrParam)){
                $filter = "1";
                if(isset($arrParam['title'])){
                    $filter .= " and `title` like '".$arrParam['title']."%'";
                    unset($arrParam['title']);
                }
                foreach ($arrParam as $key => $val){
                    $filter .= " and `".$key."` =".$val;
                }
                $listSpecialty->setFilterString($filter);
            }          
            $listSpecialty->setPage($page);
            $listSpecialty->setPagesize($pageSize);
            $arrSpecialty = $listSpecialty->toArray();  
            foreach ($arrSpecialty['list'] as $key => $val){
                $arrSpecialty['list'][$key] = $val['id'];   
            }
        }
        $arrRet['page'] = $arrSpecialty['page'];
        $arrRet['pagesize'] = $arrSpecialty['pagesize'];
        $arrRet['pageall'] = $arrSpecialty['pageall'];
        $arrRet['total'] = $arrSpecialty['total'];
        foreach($arrSpecialty['list'] as $key => $val){
            $temp = array();
            $arrSpecialty['list'][$key] = Specialty_Api::getSpecialtyInfo($val);
            $arrSpecialty['list'][$key]['sights'] = array();
            $listSightSpecialty = new Sight_List_Specialty();
            $listSightSpecialty->setFilter(array('Specialty_id' => $val));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $data){
                $sight          = Sight_Api::getSightById($data['sight_id']);
                $temp['id']     = $data['sight_id'];
                $temp['name']   = $sight['name'];
                $temp['weight'] = $data['weight'];
            }
            if(!empty($temp)){
                $arrSpecialty['list'][$key]['sights'][] = $temp;
            }
            
        }
        return $arrSpecialty;
    }
    
    /**
     * 获取视频信息,供前端使用
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public function getSpecialtyList($sightId,$page,$pageSize,$arrParam = array()){
        $arrRet         = array();
        $listSightSpecialty = new Sight_List_Specialty();
        $listSightSpecialty->setFilter(array('sight_id' => $sightId));
        $listSightSpecialty->setOrder('`weight` asc');
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $ret = $listSightSpecialty->toArray();
        foreach ($ret['list'] as $val){
            $objSpecialty = new Specialty_Object_Specialty();
            $arrParam = array_merge($arrParam,array('id' => $val['Specialty_id']));
            $objSpecialty->fetch($arrParam);
            $arrSpecialty = $objSpecialty->toArray();
            if(!empty($arrSpecialty)){
                $temp['id']    = strval($arrSpecialty['id']);
                $Specialty         = Specialty_Api::getSpecialtyInfo($arrSpecialty['id']);
                $temp['title'] = Base_Util_String::getHtmlEntity($Specialty['title']);
                $temp['image'] = Base_Image::getUrlByName($Specialty['image']);
                $temp['url']   = Base_Config::getConfig('web')->root.'/Specialty/detail?id='.$temp['id'];
                $temp['type']    = strval($Specialty['type']);
                $logicCollect    = new Collect_Logic_Collect();
                $temp['collected'] = strval($logicCollect->checkCollect(Collect_Type::Specialty, $arrSpecialty['id']));
                $arrRet[] = $temp;
            }
        }
        return array_slice($arrRet,($page-1)*$pageSize,$pageSize);
    }
        
    /**
     * 从爱奇艺源获取数据
     * @param string $query
     * @param integer $page
     * @return array
     */
    public function getAiqiyiSource($sightId,$page){
        
    }    
    
    public function getSpecialtyByInfo($SpecialtyId){
        $objSpecialty = new Specialty_Object_Specialty();
        $objSpecialty->fetch(array('id' => $SpecialtyId));
        $arrSpecialty = $objSpecialty->toArray();
        
        $listSightSpecialty = new Sight_List_Specialty();
        $listSightSpecialty->setFilter(array('Specialty_id' => $SpecialtyId));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $temp['id']   = $val['sight_id'];
            $sight        = Sight_Api::getSightById($val['sight_id']);
            $temp['name'] = $sight['name'];
            $arrSpecialty['sights'][] = $temp;
        }
        return $arrSpecialty;
    }
    
    public function search($query, $page, $pageSize){
        $arrSpecialty  = Base_Search::Search('Specialty', $query, $page, $pageSize, array('id'));
        $num       = $arrSpecialty['num'];
        $arrSpecialty  = $arrSpecialty['data'];
        foreach ($arrSpecialty as $key => $val){
            $Specialty = $this->getSpecialtyByInfo($val['id']);            
            $arrSpecialty[$key]['title']       = empty($val['title'])?trim($Specialty['title']):$val['title'];
            $arrSpecialty[$key]['image']       = isset($Specialty['image'])?Base_Image::getUrlByName($Specialty['image']):'';
            $arrSpecialty[$key]['url']         = isset($Specialty['url'])?trim($Specialty['url']):'';
            $arrSpecialty[$key]['content']     = isset($Specialty['from'])?trim($Specialty['from']):'';
            $arrSpecialty[$key]['search_type'] = 'Specialty';
        }
        return array('data' => $arrSpecialty, 'num' => $num);
    }
    
    public function getAllSpecialtyNum($sightId){
        $maxWeight  = 0;    
        $listSightSpecialty = new Sight_List_Specialty();
        $listSightSpecialty->setFilter(array('sight_id' => $sightId));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $objSpecialty = new Specialty_Object_Specialty();
            $objSpecialty->fetch(array('id' => $val['Specialty_id']));
            if($objSpecialty->status == Specialty_Type_Status::PUBLISHED){
                if($val['weight'] > $maxWeight){
                    $maxWeight = $val['weight'];
                }
            }            
        }
        return $maxWeight + 1;
    }
    
    public function getSpecialtyNum($sighId, $status = Specialty_Type_Status::PUBLISHED){
        if($status == Specialty_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::Specialty);
            if(!empty($ret)){
                return $ret;
            }
        }
        $count          = 0;
        $listSightSpecialty = new Sight_List_Specialty();
        $listSightSpecialty->setFilter(array('sight_id' => $sighId));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $objSpecialty = new Specialty_Object_Specialty();
            $objSpecialty->fetch(array('id' => $val['Specialty_id']));
            if($objSpecialty->status == $status){
                $count += 1;
            }
        }
        if($status == Specialty_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::Specialty,$count);
        }
        return $count;
    }
    
    /**
     * 修改视频信息
     * @param integer $id
     * @param array $arrParam
     */
    public function editSpecialty($id, $arrParam){
        $this->updateRedis($id);
        if(isset($arrParam['status'])){
            $arrSightIds = array();
            $weight      = array();
            $ret         = true;
            $objSpecialty = new Specialty_Object_Specialty();
            $objSpecialty->fetch(array('id' => $id));
            $objSpecialty->status = $arrParam['status'];
            $listSightSpecialty = new Sight_List_Specialty();
            $listSightSpecialty->setFilter(array('Specialty_id' => $id));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $val){
                $objSightSpecialty = new Sight_Object_Specialty();
                $objSightSpecialty->fetch(array('id' => $val['id']));
                
                $redis = Base_Redis::getInstance();
                $redis->hDel(Sight_Keys::getSightTongjiKey($val['sight_id']),Sight_Keys::Specialty);
                
                if($arrParam['status'] == Specialty_Type_Status::BLACKLIST){
                    $ret = $objSightSpecialty->remove();
                }else{
                    $objSightSpecialty->weight = $this->getAllSpecialtyNum($val['sight_id']);
                    $objSightSpecialty->save();
                }
            }
            if($arrParam['status'] == Specialty_Type_Status::BLACKLIST){
                return $objSpecialty->save();
            }
        }
        
        $arrSight = array();
        if(isset($arrParam['sight_id'])){
            $listSightSpecialty = new Sight_List_Specialty();
            $listSightSpecialty->setFilter(array('Specialty_id' => $id));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $val){
                $objSightSpecialty = new Sight_Object_Specialty();
                $objSightSpecialty->fetch(array('id' => $val['id']));
                $objSightSpecialty->remove();
            }
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        $objSpecialty = new Specialty_Object_Specialty();
        $objSpecialty->fetch(array('id' => $id));
        
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                if(($key == 'image') && ($objSpecialty->image !== $val) &&(!empty($objSpecialty->image))){
                    $this->delPic($objSpecialty->image);
                }
                $objSpecialty->$key = $val;
            }
        }
        
        foreach ($arrSight as $sightId){
            $objSightSpecialty = new Sight_Object_Specialty();
            $objSightSpecialty->sightId = $sightId;
            $objSightSpecialty->SpecialtyId = $id;
            $objSightSpecialty->weight  = $this->getAllSpecialtyNum($sightId);
            $objSightSpecialty->save();
        }
        return $objSpecialty->save();
    }
    
    /**
     * 添加视频
     * @param array $arrParam
     */
    public function addSpecialty($arrParam){
        $arrSight = array();
        if(isset($arrParam['sight_id'])){
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        $objSpecialty = new Specialty_Object_Specialty();
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                $objSpecialty->$key = $val;
            }
        }
        $objSpecialty->guid   = md5($arrParam['title'].$arrParam['url']);
        $ret =             $objSpecialty->save();
        
        foreach ($arrSight as $sightId){
            $objSightSpecialty = new Sight_Object_Specialty();
            $objSightSpecialty->sightId = $sightId;
            $objSightSpecialty->SpecialtyId = $objSpecialty->id;
            $objSightSpecialty->weight  = $this->getAllSpecialtyNum($sightId);
            $objSightSpecialty->save();            
        }
        $this->updateRedis($objSpecialty->id);
        return $objSpecialty->id;
    }
    
    /**
     * 删除视频
     * @param integer $id
     */
    public function delSpecialty($id){
        $arrSightIds    = array();
        $weight         = array();
        $this->updateRedis($id);
        $listSightSpecialty = new Sight_List_Specialty();
        $listSightSpecialty->setFilter(array('Specialty_id' => $id));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $objSightSpecialty = new Sight_Object_Specialty();
            $objSightSpecialty->fetch(array('id' => $val['id']));
            $weight[]      = $objSightSpecialty->weight;
            $arrSightIds[] = $objSightSpecialty->sightId;
            $objSightSpecialty->remove();
        }
        
        $objSpecialty = new Specialty_Object_Specialty();
        $objSpecialty->fetch(array('id' => $id));
        if(!empty($objSpecialty->image)){
            $this->delPic($objSpecialty->image);
        }
        $ret = $objSpecialty->remove();
        
        foreach ($arrSightIds as $key => $id){
            $listSightSpecialty = new Sight_List_Specialty();
            $listSightSpecialty->setFilterString("`weight` >".$weight[$key]);
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $val){
                $objSightSpecialty = new Sight_Object_Specialty();
                $objSightSpecialty->fetch(array('id' => $val['id']));
                $objSightSpecialty->weight -= 1;
                $objSightSpecialty->save();
            }
        }
        
        return $ret;
    }
    
    /**
     * 根据GUID获取视频ID
     * @param string $strGuid
     * @return number|''
     */
    public function getSpecialtyByGuid($strGuid){
        $objSpecialty = new Specialty_Object_Specialty();
        $objSpecialty->fetch(array('guid' => $strGuid));
        if($objSpecialty->id){
            return $objSpecialty->id;
        }
        return '';
    }
    
    /**
     * 修改某景点下的视频的权重
     * @param integer $id 视频ID
     * @param integer $to 需要排的位置
     * @return boolean
     */
    public function changeWeight($sightId,$id,$to){
        $strSpecialtyids = '';
        $model = new SpecialtyModel();
        $ret   = $model->getSightSpecialty($sightId, Specialty_Type_Status::PUBLISHED);
        $strSpecialtyids = implode(",",$ret);
        
        $objSightSpecialty = new Sight_Object_Specialty();
        $objSightSpecialty->fetch(array('sight_id' => $sightId,'Specialty_id' => $id));
        $from       = $objSightSpecialty->weight;
        $objSightSpecialty->weight = $to;        
    
        $listSightSpecialty = new Sight_List_Specialty();
        $filter ="`sight_id` =".$sightId." and `Specialty_id` in (".$strSpecialtyids.") and `weight` >= $to and `weight` != $from";
        $listSightSpecialty->setFilterString($filter);       
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $key => $val){
            $objTmpSightSpecialty = new Sight_Object_Specialty();
            $objTmpSightSpecialty->fetch(array('id' => $val['id']));
            $objTmpSightSpecialty->weight += 1;
            $objTmpSightSpecialty->save();
        }
        $ret = $objSightSpecialty->save();
        return $ret;
    }
    
    public function updateRedis($SpecialtyId){
        $redis = Base_Redis::getInstance();
        $listSightSpecialty = new Sight_List_Specialty();
        $listSightSpecialty->setFilter(array('Specialty_id' => $SpecialtyId));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $redis->hDel(Sight_Keys::getSightTongjiKey($val['sight_id']),Sight_Keys::Specialty);
            
            $objSight = new Sight_Object_Sight();
            $objSight->fetch(array('id' => $val['sight_id']));
            $redis->hDel(City_Keys::getCitySpecialtyNumKey(),$objSight->cityId);
        }
    }
}