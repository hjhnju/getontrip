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
}