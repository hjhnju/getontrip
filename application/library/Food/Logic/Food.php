<?php
class Food_Logic_Food extends Base_Logic{
    
    /**
     * 获取特产信息
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public  function getFoods($page,$pageSize,$arrParam = array()){
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
     * 根据景点ID获取特产数量
     * @param integer $sighId
     * @param integer $status
     * @return number
     */
    public function getFoodNum($sighId, $status = Food_Type_Status::PUBLISHED){
    }
    
    /**
     * 修改特产信息
     * @param integer $id
     * @param array $arrParam
     */
    public function editFood($id, $arrParam){
        
    }
    
    /**
     * 删除特产
     * @param integer $id
     */
    public function delFood($id){
    }
    
    /**
     * 添加特产
     * @param array $arrParam,array('title'=>'xxx','sight_id'=>1,...)
     */
    public function addFood($arrParam){
    }
    
    /**
     * 根据ID获取特产信息
     * @param string $id
     * @return array
     */
    public function getFoodInfo($id){
    }
    
    /**
     * 修改某景点下的特产的权重
     * @param integer $id ID
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
}