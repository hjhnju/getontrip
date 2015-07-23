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
        $obj = new Collect_Object_Collect();
        $obj->type   = $type;
        $obj->objId  = $obj_id;
        $obj->userId = $this->logicUser->getUserId($device_id);
        $ret1 = $obj->save();
        $redis = Base_Redis::getInstance();
        $ret2 = $redis->zAdd(Collect_Keys::getHashKeyByType($type),$obj_id,time());        
        return $ret1&&$ret2;
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
        $listCollect = new Collect_List_Collect();
        $listCollect->setFilter(array(
            'type'   => $type,
            'userid' => $this->logicUser->getUserId($device_id),
        ));
        if(empty($listCollect->list)){
            return array();
        }
        switch ($type){
            case Collect_Type::SIGHT:
                foreach ($listCollect as $key => $val){
                    
                }
                break;
            case Collect_Type::THEME:
                break;
            case Collect_Type::TOPIC:
                break;
            case Collect_Type::ANSWER:
                break;
            default:
                break;                
        }
    }
    
    /**
     * 根据type获取景点或话题或答案或主题收藏的人数
     * @param integer $type
     * @param integer $objId
     * @return integer
     */
    public function getCollectNum($type,$objId){
        $redis = Base_Redis::getInstance();
        $ret = $redis->zRangeByScore(Collect_Keys::getHashKeyByType($type),$objId,$objId);
        if(empty($ret)){
            return 0;
        }
        return count($ret);
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
            $start = strtotime($periods);
        }
        $ret = $redis->zRangeByScore(Collect_Keys::getHashKeyByType($type),$objId,$objId);
        if(empty($ret)){
            return 0;
        }
        foreach ($ret as $key => $val){
            if(($val >= $start) && ($val <= $end)){
                $count += 1;
            }
        }
        return $count;
    }
}