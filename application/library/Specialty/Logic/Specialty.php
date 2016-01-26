<?php
class Specialty_Logic_Specialty extends Base_Logic{
    
    const PAGE_SIZE = 20;
    
    const DEFAULT_WEIGHT = 0;
    
    const REDIS_RECOMMEND_TIME = 86400;
    
    protected $fields = array('destination_id', 'title', 'image', 'content', 'status', 'create_time', 'update_time', 'create_user', 'update_user','type');
    
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
        $arrRet['list']         = array();
        $title                  = '';
        $destId                 = '';
        $type                   = '';
        if(isset($arrParam['destination_id'])){
            $destId     = $arrParam['destination_id'];
            $type       = $arrParam['destination_type'];
            $arrSpecialtyIds = array();            
            $listSightSpecialty = new Destination_List_Specialty();
            $listSightSpecialty->setFilter(array('destination_id' => $destId,'destination_type' => $type));
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
                $arrParam = array_merge($arrParam,array('id' => $val['specialty_id']));                
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
        foreach($arrSpecialty['list'] as $key => $val){
            $temp = array();
            $arrSpecialty['list'][$key] = Specialty_Api::getSpecialtyInfo($val);
            $arrSpecialty['list'][$key]['content'] = Base_Util_String::getSubString($arrSpecialty['list'][$key]['content'],20);
            $arrSpecialty['list'][$key]['dest'] = array();
            $listSightSpecialty = new Destination_List_Specialty();
            $listSightSpecialty->setFilter(array('specialty_id' => $val));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $data){
                if($data['destination_type'] == Destination_Type_Type::SIGHT){
                    $dest  = Sight_Api::getSightById($data['destination_id']);
                }else{
                    $dest  = City_Api::getCityById($data['destination_id']);
                }
                $temp['id']     = $data['destination_id'];
                $temp['type']   = $data['destination_type'];
                $temp['name']   = $dest['name'];
                $temp['weight'] = $data['weight'];
                if(!empty($temp)){
                    $arrSpecialty['list'][$key]['dest'][] = $temp;
                }
                if(($destId ==$temp['id']) && ($type == $temp['type'])){
                    $arrSpecialty['list'][$key]['weight'] = $temp['weight'];
                }
            }
            
            $temp  = array();
            $arrSpecialty['list'][$key]['product'] = array();
            $arrSpecialtyProduct = Specialty_Api::getProductList($page, $pageSize, array('specialty_id' =>$val));
            foreach ($arrSpecialtyProduct['list'] as $data){
                $temp['name']   = $data['title'];
                if(!empty($temp)){
                    $arrSpecialty['list'][$key]['product'][] = $temp;
                }
            }                       
        }
        return $arrSpecialty;
    }
    
    /**
     * 获取特产信息,供前端使用
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public function getSpecialtyList($destId,$type,$page,$pageSize,$arrParam = array()){
        $arrRet         = array();
        $listDestinationSpecialty = new Destination_List_Specialty();
        $logicProduct  = new Specialty_Logic_Product();
        if($type == Destination_Type_Type::SIGHT){
            $sight = Sight_Api::getSightById($destId);
            $filter = "`destination_id` = $destId and `destination_type` = $type or `destination_type` = ".Destination_Type_Type::CITY." and `destination_id` =".$sight['city_id'];
            $listDestinationSpecialty->setFilterString($filter);
        }else{
            $listDestinationSpecialty->setFilter(array('destination_id' => $destId,'destination_type' => $type));
        }
        $listDestinationSpecialty->setOrder('`weight` asc');
        //$listDestinationSpecialty->setGroup("id");
        $listDestinationSpecialty->setPagesize(PHP_INT_MAX);
        $ret = $listDestinationSpecialty->toArray();
        foreach ($ret['list'] as $val){
            $objSpecialty = new Specialty_Object_Specialty();
            $arrParam = array_merge($arrParam,array('id' => $val['specialty_id']));
            $objSpecialty->fetch($arrParam);
            $arrSpecialty = $objSpecialty->toArray();
            if(!empty($arrSpecialty)){
                $temp['id']       = strval($arrSpecialty['id']);
                $Specialty        = Specialty_Api::getSpecialtyInfo($arrSpecialty['id']);
                $temp['topicNum'] = strval($this->getRecommendTopicNum($arrSpecialty['id']));
                $temp['productNum']   = strval($logicProduct->getProductNum(array('specialty_id' => $arrSpecialty['id'],'status' => Specialty_Type_Product::PUBLISHED)));
                $temp['title']    = trim($Specialty['title']);
                $temp['content']  = trim($Specialty['content']);
                $temp['desc']     = intval($Specialty['type'])==1?'必买':'';
                $temp['image']    = isset($Specialty['image'])?Base_Image::getUrlByName($Specialty['image']):'';
                $temp['url']      = Base_Config::getConfig('web')->root.'/specialty/detail?id='.$temp['id'];
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
            $arrSpecialty[$key]['search_type'] = 'specialty';
        }
        return array('data' => $arrSpecialty, 'num' => $num);
    }
    
    public function getAllSpecialtyNum($sightId,$type){
        $maxWeight  = 0;    
        $listSightSpecialty = new Destination_List_Specialty();
        $listSightSpecialty->setFilter(array('destination_id' => $sightId,'destination_type'=>$type));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $objSpecialty = new Specialty_Object_Specialty();
            $objSpecialty->fetch(array('id' => $val['specialty_id']));
            if($objSpecialty->status == Specialty_Type_Status::PUBLISHED){
                if($val['weight'] > $maxWeight){
                    $maxWeight = $val['weight'];
                }
            }            
        }
        return $maxWeight + 1;
    }
    
    public function getSpecialtyNum($sighId, $type = Destination_Type_Type::SIGHT, $status = Specialty_Type_Status::PUBLISHED){
        /*if($status == Specialty_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::SPECIALTY);
            if(!empty($ret)){
                return $ret;
            }
        }*/
        $count          = 0;
        $listSightSpecialty = new Destination_List_Specialty();
        $listSightSpecialty->setFilter(array('destination_id' => $sighId,'destination_type' => $type));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $objSpecialty = new Specialty_Object_Specialty();
            $objSpecialty->fetch(array('id' => $val['specialty_id']));
            if($objSpecialty->status == $status){
                $count += 1;
            }
        }
        
        if($type == Destination_Type_Type::SIGHT){
            $sight = Sight_Api::getSightById($sighId);
            $listSightSpecialty = new Destination_List_Specialty();
            $listSightSpecialty->setFilter(array('destination_id' => $sight['city_id'],'destination_type' => Destination_Type_Type::CITY));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $val){
                $objSpecialty = new Specialty_Object_Specialty();
                $objSpecialty->fetch(array('id' => $val['specialty_id']));
                if($objSpecialty->status == $status){
                    $count += 1;
                }
            }
        }
        /*if($status == Specialty_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::SPECIALTY,$count);
        }*/
        return $count;
    }
    
    /**
     * 修改视频信息
     * @param integer $id
     * @param array $arrParam
     */
    public function editSpecialty($id, $arrParam){
        $arrSight   = array();
        $arrCity    = array();
        $arrProduct = array();
        if(isset($arrParam['sight_id'])){
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        if(isset($arrParam['city_id'])){
            $arrCity = $arrParam['city_id'];
            unset($arrParam['city_id']);
        }
        if(isset($arrParam['product'])){
            if(is_array($arrParam['product'])){
                $arrProduct = $arrParam['product'];
            }else{
                $arrProduct = array($arrParam['product']);
            }
            unset($arrParam['product']);
        }
        $this->updateRedis($id);
        if(isset($arrParam['status'])){
            $arrSightIds = array();
            $weight      = array();
            $ret         = true;
            $objSpecialty = new Specialty_Object_Specialty();
            $objSpecialty->fetch(array('id' => $id));
            $objSpecialty->status = $arrParam['status'];
            $listSightSpecialty = new Destination_List_Specialty();
            $listSightSpecialty->setFilter(array('specialty_id' => $id));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $val){
                $objSightSpecialty = new Destination_Object_Specialty();
                $objSightSpecialty->fetch(array('id' => $val['id']));
                
                $redis = Base_Redis::getInstance();
                $redis->hDel(Sight_Keys::getSightTongjiKey($val['destination_id']),Sight_Keys::SPECIALTY);
                
                if($arrParam['status'] == Specialty_Type_Status::BLACKLIST){
                    $ret = $objSightSpecialty->remove();
                }else{
                    $objSightSpecialty->weight = $this->getAllSpecialtyNum($val['destination_id'],$objSightSpecialty->destinationType);
                    $objSightSpecialty->save();
                }
            }
            if($arrParam['status'] == Specialty_Type_Status::BLACKLIST){
                return $objSpecialty->save();
            }
        }
        
        if(!empty($arrCity) || !empty($arrSight)){
            $listSightSpecialty = new Destination_List_Specialty();
            $listSightSpecialty->setFilter(array('specialty_id' => $id));
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $val){
                $objSightSpecialty = new Destination_Object_Specialty();
                $objSightSpecialty->fetch(array('id' => $val['id']));
                $objSightSpecialty->remove();
            }
        }
        if(!empty($arrProduct)){
            $listSpecialtyProduct = new Specialty_List_Product();
            $listSpecialtyProduct->setFilter(array('specialty_id' => $id));
            $listSpecialtyProduct->setPagesize(PHP_INT_MAX);
            $arrSpecialtyProduct  = $listSpecialtyProduct->toArray();
            foreach ($arrSpecialtyProduct['list'] as $val){
                $objSpecialtyProduct = new Specialty_Object_Product();
                $objSpecialtyProduct->fetch(array('id' => $val['id']));
                $objSpecialtyProduct->specialtyId = '';
                $objSpecialtyProduct->save();
            }
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
            $objSightSpecialty = new Destination_Object_Specialty();
            $objSightSpecialty->destinationId = $sightId;
            $objSightSpecialty->destinationType = Destination_Type_Type::SIGHT;
            $objSightSpecialty->specialtyId    = $id;
            $objSightSpecialty->weight  = $this->getAllSpecialtyNum($sightId,Destination_Type_Type::SIGHT);
            $objSightSpecialty->save();
        }
        
        foreach ($arrCity as $cityId){
            $objCitySpecialty = new Destination_Object_Specialty();
            $objCitySpecialty->destinationId = $cityId;
            $objCitySpecialty->destinationType = Destination_Type_Type::CITY;
            $objCitySpecialty->specialtyId    = $id;
            $objCitySpecialty->weight  = $this->getAllSpecialtyNum($cityId,Destination_Type_Type::CITY);
            $objCitySpecialty->save();
        }
        
        foreach ($arrProduct as $product){
            $objSpecialtyProduct = new Specialty_Object_Product();            
            $objSpecialtyProduct->fetch(array('id' => intval($product),'specialty_id' => $id));
            if(empty($objSpecialtyProduct->id)){
                $objSpecialtyProduct->specialtyId = $objSpecialty->id;
                $objSpecialtyProduct->id          = intval($product);
                $objSpecialtyProduct->save();
            }
        }
        return $objSpecialty->save();
    }
    
    /**
     * 添加视频
     * @param array $arrParam
     */
    public function addSpecialty($arrParam){
        $arrSight   = array();
        $arrCity    = array();
        $arrProduct = array();
        if(isset($arrParam['sight_id'])){
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        if(isset($arrParam['city_id'])){
            $arrCity = $arrParam['city_id'];
            unset($arrParam['city_id']);
        }
        if(isset($arrParam['product'])){
            if(is_array($arrParam['product'])){
                $arrProduct = $arrParam['product'];
            }else{
                $arrProduct = array($arrParam['product']);
            }
            unset($arrParam['product']);
        }
        $objSpecialty = new Specialty_Object_Specialty();
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                $objSpecialty->$key = $val;
            }
        }
        $objSpecialty->save();
        
        foreach ($arrSight as $sightId){
            $objDestinationSpecialty = new Destination_Object_Specialty();
            $objDestinationSpecialty->destinationId   = $sightId;
            $objDestinationSpecialty->destinationType = Destination_Type_Type::SIGHT;
            $objDestinationSpecialty->specialtyId = $objSpecialty->id;
            $objDestinationSpecialty->weight  = $this->getAllSpecialtyNum($sightId,Destination_Type_Type::SIGHT);
            $objDestinationSpecialty->save();            
        }
        foreach ($arrCity as $cityId){
            $objDestinationSpecialty = new Destination_Object_Specialty();
            $objDestinationSpecialty->destinationId   = $cityId;
            $objDestinationSpecialty->destinationType = Destination_Type_Type::CITY;
            $objDestinationSpecialty->specialtyId = $objSpecialty->id;
            $objDestinationSpecialty->weight  = $this->getAllSpecialtyNum($sightId,Destination_Type_Type::CITY);
            $objDestinationSpecialty->save();
        }
        foreach ($arrProduct as $product){
            $objSpecialtyProduct = new Specialty_Object_Product();
            $objSpecialtyProduct->fetch(array('id' => intval($product)));
            $objSpecialtyProduct->specialtyId = $objSpecialty->id;
            $objSpecialtyProduct->save();
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
        $listSightSpecialty = new Destination_List_Specialty();
        $listSightSpecialty->setFilter(array('specialty_id' => $id));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $objSightSpecialty = new Destination_Object_Specialty();
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
            $listSightSpecialty = new Destination_List_Specialty();
            $listSightSpecialty->setFilterString("`weight` >".$weight[$key]);
            $listSightSpecialty->setPagesize(PHP_INT_MAX);
            $arrSightSpecialty  = $listSightSpecialty->toArray();
            foreach ($arrSightSpecialty['list'] as $val){
                $objSightSpecialty = new Destination_Object_Specialty();
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
    public function changeWeight($destId,$type,$id,$to){
        $strSpecialtyids = '';
        $model = new SpecialtyModel();
        $ret   = $model->getDestSpecialty($destId, $type, Specialty_Type_Status::PUBLISHED);
        $strSpecialtyids = implode(",",$ret);
        
        $objSightSpecialty = new Destination_Object_Specialty();
        $objSightSpecialty->fetch(array('destination_id' => $destId,'specialty_id' => $id,'destination_type' => $type));
        $from       = $objSightSpecialty->weight;
        $objSightSpecialty->weight = $to;  
            
        $listSightSpecialty = new Destination_List_Specialty();
        $filter ="`destination_id` =".$destId." and `destination_type` =".$type." and `specialty_id` in (".$strSpecialtyids.") and `weight` >= $to and `weight` != $from";
        $listSightSpecialty->setFilterString($filter); 
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $key => $val){
            $objTmpSightSpecialty = new Destination_Object_Specialty();
            $objTmpSightSpecialty->fetch(array('id' => $val['id']));
            $objTmpSightSpecialty->weight += 1;
            $objTmpSightSpecialty->save();
        }
        $ret = $objSightSpecialty->save();
        return $ret;
    }
    
    public function updateRedis($specialtyId){
        $redis = Base_Redis::getInstance();
        $listSightSpecialty = new Destination_List_Specialty();
        $listSightSpecialty->setFilter(array('specialty_id' => $specialtyId));
        $listSightSpecialty->setPagesize(PHP_INT_MAX);
        $arrSightSpecialty  = $listSightSpecialty->toArray();
        foreach ($arrSightSpecialty['list'] as $val){
            $redis->hDel(Sight_Keys::getSightTongjiKey($val['destination_id']),Sight_Keys::SPECIALTY);
            
            //$objSight = new Sight_Object_Sight();
            //$objSight->fetch(array('id' => $val['destination_id']));
            //$redis->hDel(City_Keys::getCitySightNumKey(),$objSight->cityId);
        }
    }
    
    public function getRecommendTopicNum($specialtyId){
        $specialty = $this->getSpecialtyByInfo($specialtyId);
        return Base_Search::getRecommendNum($specialty['title'].$specialty['content']);
    }
    
    public function getRecommendTopicIds($specialtyId,$page,$pageSize){
        $arrResult = array();
        $specialty =  $this->getSpecialtyByInfo($specialtyId);
        $count     = $this->getRecommendTopicNum($specialtyId);
        $count    -= ($page-1)*$pageSize;
        if($count < 0){
            $count = 0;
        }elseif($count > $pageSize){
            $count = $pageSize;
        }
        $arrRet = Base_Search::RecommendTopic($specialty['title'].$specialty['content'],$page,$count);
        foreach ($arrRet as $val){
            $arrResult[] = $val['id'];
        }
        return $arrResult;
    }
    
    /**
     * 前端使用的食品详情接口
     * @param integer $specialtyId
     */
    public function getSpecialtyInfo($specialtyId, $page, $pageSize){
        $logicPraise   = new Praise_Logic_Praise();
        $logicTopic    = new Topic_Logic_Topic();
        $logicProduct  = new Specialty_Logic_Product();
        $objSpecialty       = new Specialty_Object_Specialty();
        $objSpecialty->setFileds(array('id','title','content','image'));
        $objSpecialty->fetch(array('id' => $specialtyId));
        $arrSpecialty = $objSpecialty->toArray();
        $arrSpecialty['id']        = strval($arrSpecialty['id']);
        $arrSpecialty['image']     = isset($arrSpecialty['image'])?Base_Image::getUrlByName($arrSpecialty['image']):'';
        
        $arrSpecialty['productNum']   = strval($logicProduct->getProductNum(array('specialty_id' => $specialtyId,'status' => Specialty_Type_Product::PUBLISHED)));
        $arrSpecialty['topicNum']     = strval($this->getRecommendTopicNum($specialtyId));
        
        $arrSpecialty['products']  = array();
        $arrSpecialty['topics']    = array();
    
        $listProduct = new Specialty_List_Product();
        $listProduct->setFilter(array('specialty_id' => $specialtyId,'status' =>Specialty_Type_Product::PUBLISHED));
        $listProduct->setPage($page);
        $listProduct->setPagesize($pageSize);
        $arrProduct  = $listProduct->toArray();
        foreach ($arrProduct['list'] as $key => $val){
            $arrSpecialty['products'][$key]['id']    = strval($val['id']);
            $arrSpecialty['products'][$key]['title'] = trim($val['title']);
            $arrSpecialty['products'][$key]['url']   = strval($val['url']);
            $arrSpecialty['products'][$key]['price'] = strval($val['price']);
            $arrSpecialty['products'][$key]['image'] = isset($val['image'])?Base_Image::getUrlByName($val['image']):'';
        }
    
        $arrTopoicIds = $this->getRecommendTopicIds($specialtyId,$page,$pageSize);
        foreach ($arrTopoicIds as $key => $val){
            $topic = Topic_Api::getTopicById($val);
            $arrSpecialty['topics'][$key]['id']      = strval($topic['id']);
            $arrSpecialty['topics'][$key]['title']   = trim($topic['title']);
            $arrSpecialty['topics'][$key]['desc']    = trim($topic['subtitle']);
            //$arrSpecialty['topics'][$key]['url']     = Base_Config::getConfig('web')->root.'/topic/detail/'.Base_Util_Secure::encryptForUuap(Base_Util_Secure::PASSWD_KEY,$topic['id']);
            $arrSpecialty['topics'][$key]['image']   = isset($topic['image'])?Base_Image::getUrlByName($topic['image']):'';
            $arrSpecialty['topics'][$key]['visit']   = $logicTopic->getTotalTopicVistPv($val);
            $arrSpecialty['topics'][$key]['praise']  = strval($logicPraise->getPraiseNum($val));
        }
        return $arrSpecialty;
    }
    
    public function getSpecialtyTopics($id, $page, $pageSize){
        $logicPraise   = new Praise_Logic_Praise();
        $logicTopic    = new Topic_Logic_Topic();
        $arrRet        = array();
        $arrTopoicIds = $this->getRecommendTopicIds($id,$page,$pageSize);
        foreach ($arrTopoicIds as $key => $val){
            $topic = Topic_Api::getTopicById($val);
            $arrRet[$key]['id']      = strval($topic['id']);
            $arrRet[$key]['title']   = trim($topic['title']);
            $arrRet[$key]['desc']    = trim($topic['subtitle']);
            //$arrRet[$key]['url']     = Base_Config::getConfig('web')->root.'/topic/detail/'.Base_Util_Secure::encryptForUuap(Base_Util_Secure::PASSWD_KEY,$topic['id']);
            $arrRet[$key]['image']   = isset($topic['image'])?Base_Image::getUrlByName($topic['image']):'';
            $arrRet[$key]['visit']   = $logicTopic->getTotalTopicVistPv($val);
            $arrRet[$key]['praise']  = strval($logicPraise->getPraiseNum($val));
        }
        return $arrRet;
    }
    
    public function getSpecialtyProducts($id, $page, $pageSize){
        $arrRet   = array();
        $listProduct = new Specialty_List_Product();
        $listProduct->setFilter(array('specialty_id' => $id,'status' =>Specialty_Type_Product::PUBLISHED));
        $listProduct->setPage($page);
        $listProduct->setPagesize($pageSize);
        $arrProduct  = $listProduct->toArray();
        foreach ($arrProduct['list'] as $key => $val){
            $arrRet[$key]['id']    = strval($val['id']);
            $arrRet[$key]['title'] = trim($val['title']);
            $arrRet[$key]['url']   = strval($val['url']);
            $arrRet[$key]['price'] = strval($val['price']);
            $arrRet[$key]['image'] = isset($val['image'])?Base_Image::getUrlByName($val['image']):'';
        }
        return $arrRet;
    }
}