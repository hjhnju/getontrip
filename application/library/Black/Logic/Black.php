<?php
class Black_Logic_Black extends Base_Logic{
    
    public function __construct(){
        
    }
    
    /**
     * 获取黑名单列表
     * @param integer $type
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getList($type,$page='',$pageSize=''){
        if (empty($page) && empty($pageSize)) {
            $redis = Base_Redis::getInstance();
            $ret   = $redis->sMembers(Black_Keys::getBlackKey($type));
            if(!empty($ret)){
                return $ret;
            }
            $arrRet    = array();
            $listBlack = new Black_List_Black();
            $listBlack->setPagesize(PHP_INT_MAX);
            $listBlack->setFields(array('obj_id'));
            $listBlack->setFilter(array('type' => $type));
            $ret = $listBlack->toArray();
            foreach($ret['list'] as $val){
                $arrRet[] = $val['obj_id'];
                $redis->sAdd(Black_Keys::getBlackKey($type),$val['obj_id']);
            }
            return $arrRet;
        }else{
            $arrRet    = array();
            $listBlack = new Black_List_Black();
            $listBlack->setPage($page);
            $listBlack->setPagesize($pageSize);
            $listBlack->setFilter(array('type' => $type));
            $ret = $listBlack->toArray();
            return $ret;
        }
    }
    
    /**
     * 添加黑名单
     * @param integer $id
     * @param integer $type
     * @return boolean
     */
    public function addBlack($id,$type){
        $objBlack = new Black_Object_Black();
        $objBlack->objId = $id;
        $objBlack->type  = $type;
        $ret = $objBlack->save();
        
        $redis = Base_Redis::getInstance();
        $redis->delete(Black_Keys::getBlackKey($type));
        return $ret;
    }
    
    /**
     * 删除黑名单
     * @param integer $id
     * @param integer $type
     * @return boolean
     */
    public function cancelBlack($id,$type){
        $objBlack = new Black_Object_Black();
        $objBlack->fetch(array('obj_id' => $id,'type' => $type));
        $ret = $objBlack->remove();
        
        $redis = Base_Redis::getInstance();
        $redis->delete(Black_Keys::getBlackKey($type));
        return $ret;
    }
}