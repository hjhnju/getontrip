<?php
class Food_Logic_Food extends Base_Logic{
    
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
    public function getFoods($page,$pageSize,$arrParam = array()){
        $arrFood['list']   = array();
        $arrRet['list']     = array();
        $title    = '';
        if(isset($arrParam['sight_id'])){
            $sightId    = $arrParam['sight_id'];
            $arrFoodIds = array();            
            $listSightFood = new Sight_List_Food();
            $listSightFood->setFilter(array('sight_id' => $sightId));
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
                $arrParam = array_merge($arrParam,array('id' => $val['Food_id']));                
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
        $arrRet['page'] = $arrFood['page'];
        $arrRet['pagesize'] = $arrFood['pagesize'];
        $arrRet['pageall'] = $arrFood['pageall'];
        $arrRet['total'] = $arrFood['total'];
        foreach($arrFood['list'] as $key => $val){
            $temp = array();
            $arrFood['list'][$key] = Food_Api::getFoodInfo($val);
            $arrFood['list'][$key]['sights'] = array();
            $listSightFood = new Sight_List_Food();
            $listSightFood->setFilter(array('Food_id' => $val));
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $data){
                $sight          = Sight_Api::getSightById($data['sight_id']);
                $temp['id']     = $data['sight_id'];
                $temp['name']   = $sight['name'];
                $temp['weight'] = $data['weight'];
            }
            if(!empty($temp)){
                $arrFood['list'][$key]['sights'][] = $temp;
            }
            
        }
        return $arrFood;
    }
    
    /**
     * 获取视频信息,供前端使用
     * @param integer $sightId，景点ID
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public function getFoodList($sightId,$page,$pageSize,$arrParam = array()){
        $arrRet         = array();
        $listSightFood = new Sight_List_Food();
        $listSightFood->setFilter(array('sight_id' => $sightId));
        $listSightFood->setOrder('`weight` asc');
        $listSightFood->setPagesize(PHP_INT_MAX);
        $ret = $listSightFood->toArray();
        foreach ($ret['list'] as $val){
            $objFood = new Food_Object_Food();
            $arrParam = array_merge($arrParam,array('id' => $val['Food_id']));
            $objFood->fetch($arrParam);
            $arrFood = $objFood->toArray();
            if(!empty($arrFood)){
                $temp['id']    = strval($arrFood['id']);
                $Food         = Food_Api::getFoodInfo($arrFood['id']);
                $temp['title'] = Base_Util_String::getHtmlEntity($Food['title']);
                $temp['image'] = Base_Image::getUrlByName($Food['image']);
                $temp['url']   = Base_Config::getConfig('web')->root.'/Food/detail?id='.$temp['id'];
                $temp['type']    = strval($Food['type']);
                $logicCollect    = new Collect_Logic_Collect();
                $temp['collected'] = strval($logicCollect->checkCollect(Collect_Type::Food, $arrFood['id']));
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
        
        $listSightFood = new Sight_List_Food();
        $listSightFood->setFilter(array('Food_id' => $FoodId));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $temp['id']   = $val['sight_id'];
            $sight        = Sight_Api::getSightById($val['sight_id']);
            $temp['name'] = $sight['name'];
            $arrFood['sights'][] = $temp;
        }
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
            $arrFood[$key]['search_type'] = 'Food';
        }
        return array('data' => $arrFood, 'num' => $num);
    }
    
    public function getAllFoodNum($sightId){
        $maxWeight  = 0;    
        $listSightFood = new Sight_List_Food();
        $listSightFood->setFilter(array('sight_id' => $sightId));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $objFood = new Food_Object_Food();
            $objFood->fetch(array('id' => $val['Food_id']));
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
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::Food);
            if(!empty($ret)){
                return $ret;
            }
        }
        $count          = 0;
        $listSightFood = new Sight_List_Food();
        $listSightFood->setFilter(array('sight_id' => $sighId));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $objFood = new Food_Object_Food();
            $objFood->fetch(array('id' => $val['Food_id']));
            if($objFood->status == $status){
                $count += 1;
            }
        }
        if($status == Food_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::Food,$count);
        }
        return $count;
    }
    
    /**
     * 修改视频信息
     * @param integer $id
     * @param array $arrParam
     */
    public function editFood($id, $arrParam){
        $this->updateRedis($id);
        if(isset($arrParam['status'])){
            $arrSightIds = array();
            $weight      = array();
            $ret         = true;
            $objFood = new Food_Object_Food();
            $objFood->fetch(array('id' => $id));
            $objFood->status = $arrParam['status'];
            $listSightFood = new Sight_List_Food();
            $listSightFood->setFilter(array('Food_id' => $id));
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $val){
                $objSightFood = new Sight_Object_Food();
                $objSightFood->fetch(array('id' => $val['id']));
                
                $redis = Base_Redis::getInstance();
                $redis->hDel(Sight_Keys::getSightTongjiKey($val['sight_id']),Sight_Keys::Food);
                
                if($arrParam['status'] == Food_Type_Status::BLACKLIST){
                    $ret = $objSightFood->remove();
                }else{
                    $objSightFood->weight = $this->getAllFoodNum($val['sight_id']);
                    $objSightFood->save();
                }
            }
            if($arrParam['status'] == Food_Type_Status::BLACKLIST){
                return $objFood->save();
            }
        }
        
        $arrSight = array();
        if(isset($arrParam['sight_id'])){
            $listSightFood = new Sight_List_Food();
            $listSightFood->setFilter(array('Food_id' => $id));
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $val){
                $objSightFood = new Sight_Object_Food();
                $objSightFood->fetch(array('id' => $val['id']));
                $objSightFood->remove();
            }
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
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
            $objSightFood = new Sight_Object_Food();
            $objSightFood->sightId = $sightId;
            $objSightFood->FoodId = $id;
            $objSightFood->weight  = $this->getAllFoodNum($sightId);
            $objSightFood->save();
        }
        return $objFood->save();
    }
    
    /**
     * 添加视频
     * @param array $arrParam
     */
    public function addFood($arrParam){
        $arrSight = array();
        if(isset($arrParam['sight_id'])){
            $arrSight = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        $objFood = new Food_Object_Food();
        foreach ($arrParam as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                $objFood->$key = $val;
            }
        }
        $objFood->guid   = md5($arrParam['title'].$arrParam['url']);
        $ret =             $objFood->save();
        
        foreach ($arrSight as $sightId){
            $objSightFood = new Sight_Object_Food();
            $objSightFood->sightId = $sightId;
            $objSightFood->FoodId = $objFood->id;
            $objSightFood->weight  = $this->getAllFoodNum($sightId);
            $objSightFood->save();            
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
        $listSightFood = new Sight_List_Food();
        $listSightFood->setFilter(array('Food_id' => $id));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $objSightFood = new Sight_Object_Food();
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
            $listSightFood = new Sight_List_Food();
            $listSightFood->setFilterString("`weight` >".$weight[$key]);
            $listSightFood->setPagesize(PHP_INT_MAX);
            $arrSightFood  = $listSightFood->toArray();
            foreach ($arrSightFood['list'] as $val){
                $objSightFood = new Sight_Object_Food();
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
    public function changeWeight($sightId,$id,$to){
        $strFoodids = '';
        $model = new FoodModel();
        $ret   = $model->getSightFood($sightId, Food_Type_Status::PUBLISHED);
        $strFoodids = implode(",",$ret);
        
        $objSightFood = new Sight_Object_Food();
        $objSightFood->fetch(array('sight_id' => $sightId,'Food_id' => $id));
        $from       = $objSightFood->weight;
        $objSightFood->weight = $to;        
    
        $listSightFood = new Sight_List_Food();
        $filter ="`sight_id` =".$sightId." and `Food_id` in (".$strFoodids.") and `weight` >= $to and `weight` != $from";
        $listSightFood->setFilterString($filter);       
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $key => $val){
            $objTmpSightFood = new Sight_Object_Food();
            $objTmpSightFood->fetch(array('id' => $val['id']));
            $objTmpSightFood->weight += 1;
            $objTmpSightFood->save();
        }
        $ret = $objSightFood->save();
        return $ret;
    }
    
    public function updateRedis($FoodId){
        $redis = Base_Redis::getInstance();
        $listSightFood = new Sight_List_Food();
        $listSightFood->setFilter(array('Food_id' => $FoodId));
        $listSightFood->setPagesize(PHP_INT_MAX);
        $arrSightFood  = $listSightFood->toArray();
        foreach ($arrSightFood['list'] as $val){
            $redis->hDel(Sight_Keys::getSightTongjiKey($val['sight_id']),Sight_Keys::Food);
            
            $objSight = new Sight_Object_Sight();
            $objSight->fetch(array('id' => $val['sight_id']));
            $redis->hDel(City_Keys::getCityFoodNumKey(),$objSight->cityId);
        }
    }
}