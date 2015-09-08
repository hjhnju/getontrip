<?php
/**
 * 收藏接口层
 * @author huwei
 */
class Collect_Logic_Collect{
    
    protected $logicUser;
    
    public function __construct(){
        $this->logicUser = new User_Logic_User();
    }
    
    /**
     * 添加收藏逻辑层,同时注意更新redis中的统计数据
     * @param integer $type
     * @param integer $device_id
     * @param integer $obj_id
     * @return boolean 
     */
    public function addCollect($type, $device_id, $obj_id){
        $obj         = new Collect_Object_Collect();
        $obj->type   = $type;
        $obj->objId  = $obj_id;
        $obj->userId = $this->logicUser->getUserId($device_id);
        $ret         = $obj->save();
        $redis       = Base_Redis::getInstance();
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($obj_id,'*'));
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($obj_id));    

        //更新下热度信息
        return $ret;
    }
    
    /**
     * 删除收藏逻辑层,同时注意更新redis中的统计数据
     * @param integer $type
     * @param integer $device_id
     * @param integer $obj_id
     * @return boolean
     */
    public function delCollect($type, $device_id, $obj_id){
        $obj         = new Collect_Object_Collect();
        $userId      = $this->logicUser->getUserId($device_id);
        $obj->fetch(array('type' => $type,'obj_id' => $obj_id,'user_id'=> $userId));
        $ret         = $obj->remove();
        $redis       = Base_Redis::getInstance();
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($obj_id,'*'));
        $redis->hDel(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($obj_id));    
        //更新下热度信息
        return $ret;
    }
    
    /**
     * 检测用户是否收藏过
     * @param integer $type
     * @param integer $device_id
     * @param integer $obj_id
     * @return boolean
     */
    public function checkCollect($type, $device_id, $obj_id){
        $obj = new Collect_Object_Collect();
        $userId = $this->logicUser->getUserId($device_id);
        $obj->fetch(array(
            'type'    => $type,
            'user_id' => $userId,
            'obj_id'  => $obj_id,
        ));
        if(empty($obj->id)){
            return true;
        }
        return false;
    }
    
    /**
     * 获取收藏信息
     * @param integer $type
     * @param integer $device_id
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCollect($type, $device_id, $page, $pageSize){
        $arrRet      = array();
        $listCollect = new Collect_List_Collect();
        $listCollect->setFilter(array(
            'type'   => $type,
            'user_id' => $this->logicUser->getUserId($device_id),
        ));
        $listCollect->setPage($page);
        $listCollect->setPagesize($pageSize);
        $arrCollect = $listCollect->toArray();
        if(empty($arrCollect['list'])){
            return array();
        }
        switch ($type){
            case Collect_Type::SIGHT:
                $logicSight = new Sight_Logic_Sight();
                foreach ($arrCollect['list'] as $val){
                    $temp['id']       = $val['obj_id'];
                    $sight            = $logicSight->getSightById($val['obj_id']);
                    $temp['name']     = $sight['name'];
                    $temp['image']    = Base_Image::getUrlByName($sight['image']);
                    $temp['topicNum'] = Base_Util_String::getTopicNumStr($logicSight->getTopicNum($val['obj_id']));
                    $arrRet[]         = $temp;
                }
                break;
            case Collect_Type::THEME:
                $logicTheme = new Theme_Logic_Theme();
                foreach ($arrCollect['list'] as $val){
                    $temp['id']      = $val['obj_id'];
                    $theme           = $logicTheme->queryThemeById($val['obj_id']);
                    $temp['image']   = $theme['image'];
                    $temp['name']    = $theme['name'];
                    $temp['period']  = $theme['period'];
                    $temp['collect'] = strval($this->getTotalCollectNum(Collect_Type::THEME, $val['obj_id']));
                    $arrRet[]        = $temp;
                }
                break;
            case Collect_Type::TOPIC:
                $logicTopic = new Topic_Logic_Topic();
                foreach ($arrCollect['list'] as $val){
                    $temp['id']         = $val['obj_id'];
                    $topic              = $logicTopic->getTopicById($val['obj_id']);
                    $temp['image']      = Base_Image::getUrlByName($topic['image']);
                    $temp['subtitle']   = trim($topic['subtitle']);
                    $temp['title']      = trim($topic['title']);
                    //话题收藏数
                    $temp['collect']    = strval($this->getTotalCollectNum(Collect_Type::TOPIC, $val['obj_id']));
                    $arrRet[]           = $temp;
                }
                break;
            default:
                break;                
        }
        return $arrRet;
    }
    
    /**
     * 根据type获取景点或话题或答案或主题收藏的人数
     * @param integer $type
     * @param integer $objId
     * @return integer
     */
    public function getTotalCollectNum($type,$objId){
        $redis = Base_Redis::getInstance();
        $ret = $redis->hGet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($objId));
        if(!empty($ret)){
            return $ret;
        }
        $list = new Collect_List_Collect();
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilter(array('type' => $type,'obj_id' => $objId));
        $arrRet = $list->toArray();
        $redis->hSet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getTotalKeyName($objId),$arrRet['total']);
        return $arrRet['total'];
    }
    
    /**
     * 获取最近一个月的收藏量
     * @param unknown $type
     * @param unknown $objId
     */
    public function getLateCollectNum($type,$objId,$periods=''){
        $redis = Base_Redis::getInstance();
        $count = 0;
        $end = time();
        if(empty($periods)){
            $start = 0;
        }else{
            $start = strtotime($periods.' days ago');
        }
        $ret = $redis->hGet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($objId,$periods));
        if(!empty($ret)){
            $count = $ret;
        }else{
            $list = new Collect_List_Collect();
            $filter = "'type' = $type and 'obj_id' = $objId and 'create_time' >= $start";
            $list->setPagesize(PHP_INT_MAX);
            $list->setFilterString($filter);
            $arrRet = $list->toArray();
            $count  = $arrRet['total'];
            $redis->hSet(Collect_Keys::getHashKeyByType($type),Collect_Keys::getLateKeyName($objId,$periods),$count);
        }        
        return $count;
    }
}