<?php
class Praise_Logic_Praise extends Base_Logic{

    /**
     * 添加点赞逻辑层,同时注意更新redis中的统计数据
     * @param integer $type
     * @param integer $user_id
     * @param integer $obj_id
     * @return boolean
     */
    public function addPraise($type, $user_id, $obj_id){
        $obj         = new Praise_Object_Praise();
        $obj->fetch(array('type' => $type,'user_id' => $user_id,'obj_id' => $obj_id));
        if(!empty($obj->id)){
            return Praise_RetCode::HAS_PRAISED;
        }
        $obj->type   = $type;
        $obj->objId  = $obj_id;
        $obj->userId = $user_id;
        $ret         = $obj->save();
        if($ret){
            //记一条业务日志
            $arrLog = array(
                'type'     => 'priase-add',
                'uid'      => $user_id,
                'obj_id'   => $obj_id,
                'obj_type' => $type,
            );
            Base_Log::NOTICE($arrLog);
            return Praise_RetCode::SUCCESS;
        }
        return Praise_RetCode::UNKNOWN_ERROR;
    }
    
    /**
     * 删除点赞逻辑层,同时注意更新redis中的统计数据
     * @param integer $type
     * @param integer $user_id
     * @param integer $obj_id
     * @return boolean
     */
    public function delPraise($type, $user_id, $obj_id){
        $obj         = new Praise_Object_Praise();
        $obj->fetch(array('type' => $type,'obj_id' => $obj_id,'user_id'=> $user_id));
        $ret         = $obj->remove();
        //记一条业务日志
        $arrLog = array(
            'type'     => 'priase-del',
            'uid'      => $user_id,
            'obj_id'   => $obj_id,
            'obj_type' => $type,
        );
        Base_Log::NOTICE($arrLog);
        return $ret;
    }
    
    /**
     * 获取点赞数量
     * @param integer $obj_id
     * @param integer $type
     * @return integer
     */
    public function getPraiseNum($obj_id, $type = Praise_Type_Type::TOPIC){
        $listPraise = new Praise_List_Praise();
        $listPraise->setFilter(array('type' => $type,'obj_id' => $obj_id));
        return $listPraise->getTotal();
    }
    
    /**
     * 获取最近的收藏量
     * @param unknown $type
     * @param unknown $objId
     */
    public function getLatePraiseNum($type,$objId,$periods='',$dateType= 'DAY'){
        $redis = Base_Redis::getInstance();
        $count = 0;
        $end = time();
        if(empty($periods)){
            $start = 0;
        }else{
            if($dateType == 'DAY'){
                $start = strtotime($periods.' days ago');
            }else{
                $start = time() - 60*$periods;
            }
        }
        if($dateType == 'DAY'){
            $ret = $redis->hGet(Praise_Keys::getTopicInfoKey(),Praise_Keys::getLateKeyName($objId,$periods));
        }else{
            $ret = $redis->hGet(Praise_Keys::getTopicInfoKey(),Praise_Keys::getLateMinuteKeyName($objId,$periods));
        }
        if(!empty($ret)){
            $count = $ret;
        }else{
            $list = new Praise_List_Praise();
            $filter = "`type` = $type and `obj_id` = $objId and `create_time` >= ".$start;
            $list->setPagesize(PHP_INT_MAX);
            $list->setFilterString($filter);
            $arrRet = $list->toArray();
            $count  = $arrRet['total'];
            if($dateType == 'DAY'){
                $redis->hSet(Praise_Keys::getTopicInfoKey(),Praise_Keys::getLateKeyName($objId,$periods),$count);
            }else{
                $redis->hSet(Praise_Keys::getTopicInfoKey(),Praise_Keys::getLateMinuteKeyName($objId,$periods),$count);
            }
    
        }
        return $count;
    }
    
    /**
     * 检测用户是否收藏过
     * @param integer $type
     * @param integer $obj_id
     * @return boolean
     */
    public function checkPraise($type, $obj_id){
        $user_id     = User_Api::getCurrentUser();
        if(empty($user_id)){
            return false;
        }
        $obj = new Praise_Object_Praise();
        $obj->fetch(array(
            'type'    => $type,
            'user_id' => $user_id,
            'obj_id'  => $obj_id,
        ));
        if(!empty($obj->id)){
            return true;
        }
        return false;
    }
    
    public function listPraise($page, $pageSize){
        $arrRet      = array();
        $logicPraise = new Praise_Logic_Praise();
        $logicVisit  = new Tongji_Logic_Visit();
        $user_id     = User_Api::getCurrentUser();
        if(empty($user_id)){
            return $arrRet;
        }
        $listPraise = new Praise_List_Praise();
        $listPraise->setFilter(array('user_id' => $user_id));
        $listPraise->setPage($page);
        $listPraise->setPagesize($pageSize);
        $arrPraise  = $listPraise->toArray();
        foreach ($arrPraise['list'] as $val){
            $temp = array();
            switch ($val['type']){
                case Praise_Type_Type::TOPIC:
                    $objTopic = new Topic_Object_Topic();
                    $objTopic->fetch(array('id' => $val['obj_id']));
                    $temp['id']        = strval($objTopic->id);
                    $temp['type']      = strval($val['type']);
                    $temp['title']     = $objTopic->title;
                    $temp['subtitle']  = $objTopic->subtitle;
                    $temp['image']     = Base_Image::getUrlByName($objTopic->image);
                    $temp['praisenum'] = strval($logicPraise->getPraiseNum($objTopic->id));
                    $temp['visitnum']  = strval($logicVisit->getVisitCount(Tongji_Type_Visit::TOPIC, $objTopic->id));
                    break;
                case Praise_Type_Type::BOOK:
                    $objBook = new Book_Object_Book();
                    $objBook->fetch(array('id' => $val['obj_id']));
                    $temp['id']     = strval($objBook->id);
                    $temp['type']   = strval($val['type']);
                    $temp['title']  = $objBook->title;
                    $temp['author'] = $objBook->author;
                    $temp['image']  = Base_Image::getUrlByName($objBook->image);
                    $temp['praisenum'] = strval($logicPraise->getPraiseNum($objTopic->id,Praise_Type_Type::BOOK));
                    $temp['visitnum']  = strval($logicVisit->getVisitCount(Tongji_Type_Visit::BOOK, $objTopic->id));
                    break;
                default:
                    break;
            }
            if(!empty($temp)){
                $arrRet[] = $temp;
            }
        }
        return $arrRet;
    }
}