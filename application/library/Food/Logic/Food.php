<?php
class Food_Logic_Food extends Base_Logic{
    
    const PAGE_SIZE = 20;
    
    const DEFAULT_WEIGHT = 0;
    
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
    public function getFoods($page,$pageSize,$arrParam = array()){
        $arrFood['list']   = array();
        $arrRet['list']         = array();
        $title                  = '';
        $destId                 = '';
        $type                   = '';
        if(isset($arrParam['destination_id'])){
            $destId     = $arrParam['destination_id'];
            $type       = $arrParam['destination_type'];
            $arrFoodIds = array();            
            $listSightFood = new Destination_List_Food();
            $listSightFood->setFilter(array('destination_id' => $destId,'destination_type' => $type));
            if(isset($arrParam['order'])){
                $listSightFood->setOrder($arrParam['order']);
                unset($arrParam['order']);
            }
            $listSightFood->setPagesize(PHP_INT_MAX);
            $ret = $listSightFood->toArray();
            if(isset($arrParam['title'])){
                $title  = $arrParam['title'];
                unset($arrParam['title']);
            }
            foreach ($ret['list'] as $val){
                $arrParam = array_merge($arrParam,array('id' => $val['food_id']));                
                $objFood = new Food_Object_Food();
                $objFood->fetch($arrParam);
                $arrTmpFood = $objFood->toArray();
                if(!empty($title) && isset($arrTmpFood['title'])){
                    if( false ==  strstr($arrTmpFood['title'],$title)){
                        continue;
                    }
                }
                if(!empty($arrTmpFood)){
                    $arrFood['list'][] = $arrTmpFood['id'];
                }
            }          
            $arrFood['page'] = $page;
            $arrFood['pagesize'] = $pageSize;
            $arrFood['pageall'] = ceil(count($arrFood['list'])/$pageSize);
            $arrFood['total'] = count($arrFood['list']);
            $arrFood['list'] = array_slice($arrFood['list'], ($page-1)*$pageSize,$pageSize); 
        }else{
            $listFood = new Food_List_Food();
            if(!empty($arrParam)){
                $filter = "1";
                if(isset($arrParam['title'])){
                    $filter .= " and `title` like '".$arrParam['title']."%'";
                    unset($arrParam['title']);
                }
                foreach ($arrParam as $key => $val){
                    $filter .= " and `".$key."` =".$val;
                }
                $listFood->setFilterString($filter);
            }          
            $listFood->setPage($page);
            $listFood->setPagesize($pageSize);
            $arrFood = $listFood->toArray();  
            foreach ($arrFood['list'] as $key => $val){
                $arrFood['list'][$key] = $val['id'];   
            }
        }
        foreach($arrFood['list'] as $key => $val){
            $temp = array();
            $arrFood['list'][$key] = Food_Api::getFoodInfo($val);
            $arrFood['list'][$key]['dest'] = array();
            $listSightFood = new Destination_List_Food();
            $listSightFood->setFilter(array('food_id' => $val));
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $data){
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
                    $arrFood['list'][$key]['dest'][] = $temp;
                }
                if(($destId ==$temp['id']) && ($type == $temp['type'])){
                    $arrFood['list'][$key]['weight'] = $temp['weight'];
                }
            }

            $temp  = array();
            $arrFood['list'][$key]['shop'] = array();
            $arrFoodShop = Food_Api::getShopList($page, $pageSize, array('food_id' =>$val));
            foreach ($arrFoodShop['list'] as $data){
                $temp['name']   = $data['title'];
                if(!empty($temp)){
                    $arrFood['list'][$key]['shop'][] = $temp;
                }
            }
        }
        return $arrFood;
    }
    
    /**
     * 获取特产信息,供前端使用
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public function getFoodList($destId,$type,$page,$pageSize,$arrParam = array()){
        $arrRet         = array();
        $logicShop      = new Food_Logic_Shop();
        $listDestinationFood = new Destination_List_Food();
        $listDestinationFood->setFilter(array('destination_id' => $destId,'destination_type' => $type));
        $listDestinationFood->setOrder('`weight` asc');
        $listDestinationFood->setPagesize(PHP_INT_MAX);
        $ret = $listDestinationFood->toArray();
        foreach ($ret['list'] as $val){
            $objFood = new Food_Object_Food();
            $arrParam = array_merge($arrParam,array('id' => $val['food_id']));
            $objFood->fetch($arrParam);
            $arrFood = $objFood->toArray();
            if(!empty($arrFood)){
                $temp['id']       = strval($arrFood['id']);
                $Food             = Food_Api::getFoodInfo($arrFood['id']);
                $temp['shopNum']  = strval($logicShop->getShopNum(array('food_id' => $arrFood['id'],'status' => Food_Type_Shop::PUBLISHED)));
                $temp['topicNum'] = strval('10');
                $temp['title']    = trim($Food['title']);
                $temp['desc']     = trim($Food['content']);
                $temp['image']    = isset($Food['image'])?Base_Image::getUrlByName($Food['image']):'';
                $temp['url']      = Base_Config::getConfig('web')->root.'/food/detail?id='.$temp['id'];
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
    
    public function getFoodByInfo($FoodId){
        $objFood = new Food_Object_Food();
        $objFood->fetch(array('id' => $FoodId));
        $arrFood = $objFood->toArray();
        return $arrFood;
    }
    
    public function search($query, $page, $pageSize){
        $arrFood  = Base_Search::Search('Food', $query, $page, $pageSize, array('id'));
        $num       = $arrFood['num'];
        $arrFood  = $arrFood['data'];
        foreach ($arrFood as $key => $val){
            $Food = $this->getFoodByInfo($val['id']);            
            $arrFood[$key]['title']       = empty($val['title'])?trim($Food['title']):$val['title'];
            $arrFood[$key]['image']       = isset($Food['image'])?Base_Image::getUrlByName($Food['image']):'';
            $arrFood[$key]['url']         = isset($Food['url'])?trim($Food['url']):'';
            $arrFood[$key]['content']     = isset($Food['from'])?trim($Food['from']):'';
            $arrFood[$key]['search_type'] = 'food';
        }
        return array('data' => $arrFood, 'num' => $num);
    }
    
    public function getAllFoodNum($sightId,$type){
        $maxWeight  = 0;    
        $listSightFood = new Destination_List_Food();
        $listSightFood->setFilter(array('destination_id' => $sightId,'destination_type'=>$type));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $objFood = new Food_Object_Food();
            $objFood->fetch(array('id' => $val['food_id']));
            if($objFood->status == Food_Type_Status::PUBLISHED){
                if($val['weight'] > $maxWeight){
                    $maxWeight = $val['weight'];
                }
            }            
        }
        return $maxWeight + 1;
    }
    
    public function getFoodNum($sighId, $status = Food_Type_Status::PUBLISHED){
        if($status == Food_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::FOOD);
            if(!empty($ret)){
                return $ret;
            }
        }
        $count          = 0;
        $listSightFood = new Destination_List_Food();
        $listSightFood->setFilter(array('destination_id' => $sighId));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $objFood = new Food_Object_Food();
            $objFood->fetch(array('id' => $val['food_id']));
            if($objFood->status == $status){
                $count += 1;
            }
        }
        if($status == Food_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::FOOD,$count);
        }
        return $count;
    }
    
    /**
     * 修改视频信息
     * @param integer $id
     * @param array $arrParam
     */
    public function editFood($id, $arrParam){
        $arrSight   = array();
        $arrCity    = array();
        $arrShop = array();
        if(isset($arrParam['sight_id'])){
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        if(isset($arrParam['city_id'])){
            $arrCity = $arrParam['city_id'];
            unset($arrParam['city_id']);
        }
        if(isset($arrParam['shop'])){
            if(is_array($arrParam['shop'])){
                $arrShop = $arrParam['shop'];
            }else{
                $arrShop = array($arrParam['shop']);
            }
            unset($arrParam['shop']);
        }
        $this->updateRedis($id);
        if(isset($arrParam['status'])){
            $arrSightIds = array();
            $weight      = array();
            $ret         = true;
            $objFood = new Food_Object_Food();
            $objFood->fetch(array('id' => $id));
            $objFood->status = $arrParam['status'];
            $listSightFood = new Destination_List_Food();
            $listSightFood->setFilter(array('food_id' => $id));
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $val){
                $objSightFood = new Destination_Object_Food();
                $objSightFood->fetch(array('id' => $val['id']));
                
                $redis = Base_Redis::getInstance();
                $redis->hDel(Sight_Keys::getSightTongjiKey($val['destination_id']),Sight_Keys::FOOD);
                
                if($arrParam['status'] == Food_Type_Status::BLACKLIST){
                    $ret = $objSightFood->remove();
                }else{
                    $objSightFood->weight = $this->getAllFoodNum($val['destination_id'],$objSightFood->destinationType);
                    $objSightFood->save();
                }
            }
            if($arrParam['status'] == Food_Type_Status::BLACKLIST){
                return $objFood->save();
            }
        }
        
        if(!empty($arrCity) || !empty($arrSight)){
            $listSightFood = new Destination_List_Food();
            $listSightFood->setFilter(array('food_id' => $id));
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $val){
                $objSightFood = new Destination_Object_Food();
                $objSightFood->fetch(array('id' => $val['id']));
                $objSightFood->remove();
            }
        }
        if(!empty($arrShop)){
            $listFoodShop = new Food_List_Shop();
            $listFoodShop->setFilter(array('food_id' => $id));
            $listFoodShop->setPagesize(PHP_INT_MAX);
            $arrFoodShop  = $listFoodShop->toArray();
            foreach ($arrFoodShop['list'] as $val){
                $objFoodShop = new Food_Object_Shop();
                $objFoodShop->fetch(array('id' => $val['id']));
                $objFoodShop->remove();
            }
        }
        $objFood = new Food_Object_Food();
        $objFood->fetch(array('id' => $id));
        
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                if(($key == 'image') && ($objFood->image !== $val) &&(!empty($objFood->image))){
                    $this->delPic($objFood->image);
                }
                $objFood->$key = $val;
            }
        }
        
        foreach ($arrSight as $sightId){
            $objSightFood = new Destination_Object_Food();
            $objSightFood->destinationId = $sightId;
            $objSightFood->destinationType = Destination_Type_Type::SIGHT;
            $objSightFood->foodId    = $id;
            $objSightFood->weight  = $this->getAllFoodNum($sightId,Destination_Type_Type::SIGHT);
            $objSightFood->save();
        }
        
        foreach ($arrCity as $cityId){
            $objCityFood = new Destination_Object_Food();
            $objCityFood->destinationId = $cityId;
            $objCityFood->destinationType = Destination_Type_Type::CITY;
            $objCityFood->foodId    = $id;
            $objCityFood->weight  = $this->getAllFoodNum($cityId,Destination_Type_Type::CITY);
            $objCityFood->save();
        }
        
        foreach ($arrShop as $shop){
            $objFoodShop = new Food_Object_Shop();
            $objFoodShop->fetch(array('id' => intval($shop)));
            $objFoodShop->foodId = $objFood->id;
            $objFoodShop->save();
        }
        return $objFood->save();
    }
    
    /**
     * 添加视频
     * @param array $arrParam
     */
    public function addFood($arrParam){
        $arrSight   = array();
        $arrCity    = array();
        $arrShop = array();
        if(isset($arrParam['sight_id'])){
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        if(isset($arrParam['city_id'])){
            $arrCity = $arrParam['city_id'];
            unset($arrParam['city_id']);
        }
        if(isset($arrParam['shop'])){
            if(is_array($arrParam['shop'])){
                $arrShop = $arrParam['shop'];
            }else{
                $arrShop = array($arrParam['shop']);
            }
            unset($arrParam['shop']);
        }
        $objFood = new Food_Object_Food();
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                $objFood->$key = $val;
            }
        }
        $objFood->save();
        
        foreach ($arrSight as $sightId){
            $objDestinationFood = new Destination_Object_Food();
            $objDestinationFood->destinationId   = $sightId;
            $objDestinationFood->destinationType = Destination_Type_Type::SIGHT;
            $objDestinationFood->foodId = $objFood->id;
            $objDestinationFood->weight  = $this->getAllFoodNum($sightId,Destination_Type_Type::SIGHT);
            $objDestinationFood->save();            
        }
        foreach ($arrCity as $cityId){
            $objDestinationFood = new Destination_Object_Food();
            $objDestinationFood->destinationId   = $cityId;
            $objDestinationFood->destinationType = Destination_Type_Type::CITY;
            $objDestinationFood->foodId = $objFood->id;
            $objDestinationFood->weight  = $this->getAllFoodNum($sightId,Destination_Type_Type::CITY);
            $objDestinationFood->save();
        }
        foreach ($arrShop as $shop){
            $objFoodShop = new Food_Object_Shop();
            $objFoodShop->fetch(array('id' => intval($shop)));
            $objFoodShop->foodId = $objFood->id;
            $objFoodShop->save();
        }
        $this->updateRedis($objFood->id);
        return $objFood->id;
    }
    
    /**
     * 删除视频
     * @param integer $id
     */
    public function delFood($id){
        $arrSightIds    = array();
        $weight         = array();
        $this->updateRedis($id);
        $listSightFood = new Destination_List_Food();
        $listSightFood->setFilter(array('food_id' => $id));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $objSightFood = new Destination_Object_Food();
            $objSightFood->fetch(array('id' => $val['id']));
            $weight[]      = $objSightFood->weight;
            $arrSightIds[] = $objSightFood->sightId;
            $objSightFood->remove();
        }
        
        $objFood = new Food_Object_Food();
        $objFood->fetch(array('id' => $id));
        if(!empty($objFood->image)){
            $this->delPic($objFood->image);
        }
        $ret = $objFood->remove();
        
        foreach ($arrSightIds as $key => $id){
            $listSightFood = new Destination_List_Food();
            $listSightFood->setFilterString("`weight` >".$weight[$key]);
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $val){
                $objSightFood = new Destination_Object_Food();
                $objSightFood->fetch(array('id' => $val['id']));
                $objSightFood->weight -= 1;
                $objSightFood->save();
            }
        }
        
        return $ret;
    }
    
    /**
     * 根据GUID获取视频ID
     * @param string $strGuid
     * @return number|''
     */
    public function getFoodByGuid($strGuid){
        $objFood = new Food_Object_Food();
        $objFood->fetch(array('guid' => $strGuid));
        if($objFood->id){
            return $objFood->id;
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
        $strFoodids = '';
        $model = new FoodModel();
        $ret   = $model->getDestFood($destId, $type, Food_Type_Status::PUBLISHED);
        $strFoodids = implode(",",$ret);
        
        $objSightFood = new Destination_Object_Food();
        $objSightFood->fetch(array('destination_id' => $destId,'food_id' => $id,'destination_type' => $type));
        $from       = $objSightFood->weight;
        $objSightFood->weight = $to;  
            
        $listSightFood = new Destination_List_Food();
        $filter ="`destination_id` =".$destId." and `destination_type` =".$type." and `food_id` in (".$strFoodids.") and `weight` >= $to and `weight` != $from";
        $listSightFood->setFilterString($filter); 
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $key => $val){
            $objTmpSightFood = new Destination_Object_Food();
            $objTmpSightFood->fetch(array('id' => $val['id']));
            $objTmpSightFood->weight += 1;
            $objTmpSightFood->save();
        }
        $ret = $objSightFood->save();
        return $ret;
    }
    
    public function updateRedis($FoodId){
        $redis = Base_Redis::getInstance();
        $listSightFood = new Destination_List_Food();
        $listSightFood->setFilter(array('food_id' => $FoodId));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $redis->hDel(Sight_Keys::getSightTongjiKey($val['destination_id']),Sight_Keys::FOOD);
            
            //$objSight = new Sight_Object_Sight();
            //$objSight->fetch(array('id' => $val['destination_id']));
            //$redis->hDel(City_Keys::getCitySightNumKey(),$objSight->cityId);
        }
    }
}