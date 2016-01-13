<?php
class Search_Logic_Label extends Base_Logic{
    
    /**
     * 获取搜索标记的景点数
     * @param integer $labelId
     * @return integer
     */
    public function getLabeledNum($labelId){
        $listSearchLabel = new Search_List_Label();
        $listSearchLabel->setFilter(array('label_id' => $labelId));
        $listSearchLabel->setPagesize(PHP_INT_MAX);
        $arrRet = $listSearchLabel->toArray();
        return $arrRet['total'];
    }
    
    /**
     * 为城市或景点添加上搜索标签
     * @param array $arrObjs
     * @param integer $type
     * @param integer $labelId
     * @return boolean
     */
    public function addLabel($arrObjs, $arrType, $labelId){
        $listSearchLabel = new Search_List_Label();
        $listSearchLabel->setFilter(array('label_id' => $labelId));
        $listSearchLabel->setPagesize(PHP_INT_MAX);
        $arrSearchLabel  = $listSearchLabel->toArray();
        foreach ($arrSearchLabel['list'] as $val){
            $objSearchLabel = new Search_Object_Label();
            $objSearchLabel->fetch(array('id' => $val['id']));
            $objSearchLabel->remove();
        }
               
        foreach ($arrObjs as $key => $objId){
            $objSearchLabel = new Search_Object_Label();
            $objSearchLabel->objId   = $objId;
            $objSearchLabel->type    = $arrType[$key];
            $objSearchLabel->labelId = $labelId;
            $ret =  $objSearchLabel->save();
        }
        return $ret;
    }
    
    /**
     * 删除搜索标签
     * @param integer $objId
     * @param integer $type
     * @param integer $labelId
     * @return boolean
     */
    public function delLabel($labelId, $objId){
        $objSearchLabel = new Search_Object_Label();
        $objSearchLabel->fetch(array('obj_id' => $objId,'label_id'=>$labelId));
        return $objSearchLabel->remove();
    }
    
    /**
     * 查询搜索标签列表
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrInfo
     * @return array
     */
    public function listLabel($page, $pageSize, $arrInfo = array()){  
        $logic   = new Search_Logic_Search();  
        $arrInfo = array_merge($arrInfo,array('type' => Tag_Type_Tag::SEARCH));
        $List    = Tag_Api::getTagList($page, $pageSize, $arrInfo);
        foreach ($List['list'] as $index => $val){
            $arrObjInfo      = array();
            $listSearchLabel = new Search_List_Label();
            $listSearchLabel->setFilter(array('label_id' => $val['id']));
            $listSearchLabel->setPagesize(PHP_INT_MAX);
            $arrSearchLabel = $listSearchLabel->toArray();
            $data           = array();
            foreach ($arrSearchLabel['list'] as $key => $data){
                $arrObjInfo[$key]['id'] = $data['obj_id'];
                if(Search_Type_Label::CITY == $data['type']){
                    $city = City_Api::getCityById($data['obj_id']);
                    $arrObjInfo[$key]['name'] = $city['name'];
                }else{
                    $sight = Sight_Api::getSightById($data['obj_id']);
                    $arrObjInfo[$key]['name'] = isset($sight['name'])?$sight['name']:'';
                }
            }
            if(isset($data['type'])){
                $List['list'][$index]['typename'] = Search_Type_Label::getTypeName($data['type']);
            }else{
                $List['list'][$index]['typename'] = '';
            }
            $List['list'][$index]['type'] = isset($data['type'])?$data['type']:'';
            if($val['id'] == $logic->arrIds[ini_get('yaf.environ')]['hot_topic']){
                $logicTopic = new Topic_Logic_Topic();
                $List['list'][$index]['obj_num'] = $logicTopic->getHotTopicNum();
            }elseif($val['id'] == $logic->arrIds[ini_get('yaf.environ')]['near_sight']){
                $List['list'][$index]['obj_num'] = Sight_Api::getSightNum(array('status' => Sight_Type_Status::PUBLISHED));
            }else{
                $List['list'][$index]['obj_num'] = count($arrObjInfo);
            }
            $List['list'][$index]['objs']    = $arrObjInfo; 
        }
        return $List;
    }
    
    /**
     * 获取某个搜索标签信息
     * @param integer $labelId
     * @param integer $page,标签数据的页码
     * @param integer $pageSize,标签对应对象数据的页面大小
     */
    public function getLabel($labelId, $page, $pageSize){
        $logic   = new Search_Logic_Search();
        $tag = Tag_Api::getTagInfo($labelId);
        if($tag['id'] == $logic->arrIds[ini_get('yaf.environ')]['hot_topic']){
            $arrData = Topic_Api::getHotTopic($page, $pageSize);
            foreach ($arrData['list'] as $index => $val){
                $temp['id']   = $labelId;
                $temp['typename'] = '话题';
                $temp['type']     = Search_Type_Label::TOPIC;
                $temp['name']     = $tag['name'];
                $temp['label_id']   = $labelId;
                $temp['obj']   = $val['title'];
                $temp['obj_id']   = $val['id'];
                $temp['weight']   = $val['hot1'];
                $temp['create_time'] = $val['create_time'];
                $temp['update_time'] = $val['update_time'];
                $temp['create_user'] = $val['create_user'];
                $temp['update_user'] = $val['update_user'];
                $arrData['list'][$index] = $temp;
            }
            return $arrData;
        }elseif($tag['id'] == $logic->arrIds[ini_get('yaf.environ')]['near_sight']){
            $model      = new GisModel();
            $logicSight = new Sight_Logic_Sight();
            $location   = $model->getLocation();
            $arrSight = $model->getNearSight($location['x'], $location['y'], $page, $pageSize);
            foreach ($arrSight as $index => $val){
                $sight         = Sight_Api::getSightById($val['id']);
                $sightId       = $val['id'];
                $arrSight      = $logicSight->getSightById($sightId);
                $temp['id']    = $labelId;
                $temp['typename'] = '景点';
                $temp['type']  = strval(Search_Type_Label::SIGHT);
                $temp['name']  = $tag['name'];
                $temp['label_id']   = $labelId;
                $temp['obj']      = $sight['name'];
                $temp['obj_id']   = $sight['id'];
                $temp['weight']   = $sight['hot1'];
                $temp['create_time'] = $sight['create_time'];
                $temp['update_time'] = $sight['update_time'];
                $temp['create_user'] = $sight['create_user'];
                $temp['update_user'] = $sight['update_user'];

                $arrData['list'][$index] = $temp;
            }
            return $arrData;
        }
        
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $labelId));  
        $arrTag = $objTag->toArray(); 
        $listSearchLabel  = new Search_List_Label();
        $listSearchLabel->setFilter(array('label_id' => $labelId));
        $listSearchLabel->setPage($page);
        $listSearchLabel->setPagesize($pageSize);
        $arrSearchLabel = $listSearchLabel->toArray();
        foreach ($arrSearchLabel['list'] as $key => $val){
            $arrSearchLabel['list'][$key]         = array_merge($arrSearchLabel['list'][$key],$arrTag);
            $arrSearchLabel['list'][$key]['typename'] = Search_Type_Label::getTypeName($val['type']);
            $arrSearchLabel['list'][$key]['type']     = $val['type'];
            if(Search_Type_Label::CITY == $val['type']){
                $city = City_Api::getCityById($val['obj_id']);
                $arrSearchLabel['list'][$key]['obj'] = $city['name'];
            }else{
                $sight = Sight_Api::getSightById($val['obj_id']);
                $arrSearchLabel['list'][$key]['obj'] = isset($sight['name'])?$sight['name']:'';
            }
        }
        return $arrSearchLabel;
    }
    
    /**
     * 添加搜索标签
     * @param string $name
     * @param string $type
     * @param array $arrObjIds
     * @return boolean
     */
    public function addNewTag($name,$type = '', $arrObjIds = array()){
        $objTag = new Tag_Object_Tag();
        $objTag->name = $name;
        $objTag->type = Tag_Type_Tag::SEARCH;
        $logicTag     = new Tag_Logic_Tag();
        $num = $logicTag->getSearchTagNum();
        $objTag->weight = $num + 1;
        $ret = $objTag->save();
        if(!empty($arrObjIds)){
            foreach ($arrObjIds as $val){
                $objSearchLabel = new Search_Object_Label();
                $objSearchLabel->labelId = $objTag->id;
                $objSearchLabel->objId   = intval($val);
                $objSearchLabel->type    = $type;
                $objSearchLabel->save();
            }
        }
        return $ret;
    }
}