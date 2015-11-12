<?php
/**
 * 消息逻辑层
 * @author huwei
 *
 */
class Msg_Logic_Msg {
    
    protected $_fields;
    
    public function __construct(){
        $this->_fields = array('mid', 'sender', 'receiver', 'title', 'type', 'content', 'attach', 'image', 'status', 'create_time', 'update_time');
    }
    
    /**
     * 获取消息未读数
     * @param string $uid
     * @return number|0
     */
    public function getUnread($uid){
        $objsMsg = new Msg_List_Msg();
        $num = 0;
        $arrObj = array();
        $objsMsg->setFilter(array('receiver'=>$uid));
        $objsMsg->setPagesize(PHP_INT_MAX);
        $arrObj = $objsMsg->toArray();
        $arrObj = $arrObj['list'];
        if(empty($arrObj)){
            return $num;
        }
        foreach ($arrObj as $obj){
            if(Msg_Type_Status::UNREAD == $obj['status']){
                $num += 1;
            }
        }
        return $num;
    }
    
    /**
     * 将消息标记为已读
     * @param  $mid
     */
    public function setRead($mid){
        $objMsg = new Msg_Object_Msg();
        $objMsg->fetch(array('mid'=>$mid));
        $objMsg->status = Msg_Type_Status::READ;
        $ret = $objMsg->save();
        return $ret;
    }
    
    /**
     * 将所有消息标记为已读
     * @param unknown $uid
     */
    public function setReadAll($uid){
        $objsMsg = new Msg_List_Msg();
        $objsMsg->setFilter(array('receiver'=>$uid));
        $objsMsg->setPagesize(PHP_INT_MAX);
        $arrObj = $objsMsg->getObjects();
        foreach ($arrObj as $obj){
            if(Msg_Type_Status::DEL !== $obj->status){
                $obj->status = Msg_Type_Status::READ;
            }
            $ret = $obj->save();
            if(!$ret){
                return $ret;
            }
        }
        return true;
    }
    
    /**
     * 获取消息详情
     * @param unknown $mid
     */
    public function getDetail($mid){
        $objMsg = new Msg_Object_Msg();
        $objMsg->fetch(array('mid'=>$mid));
        if(empty($objMsg)){
            return array();
        }
        $arrRet = array(
            'title'   => $objMsg->title,
        	'content' => $objMsg->content,
        );
        return $arrRet;
    }
    
    /**
     * 获取消息列表
     * @param string $uid
     * @param integer $intType
     */
    public function getList($toId, $intPage,$intPageSize,$intType = Msg_Type_Status::ALL){
        $objsMsg   = new Msg_List_Msg();
        if (Msg_Type_Status::ALL == $intType) {
            $objsMsg->setFilterString("`receiver` = $toId and `status` !=".Msg_Type_Status::DEL);
        }else{
            $objsMsg->setFilter(array('status'=>$intType,'receiver' => $toId));
        }
        $objsMsg->setFields(array('mid','title','content','image','attach','create_time','type'));
        $objsMsg->setPage($intPage);
        $objsMsg->setPagesize($intPageSize);
        $arrObjs = $objsMsg->toArray();
        foreach ($arrObjs['list'] as $key => $val){
            if(!empty($val['attach'])){
                $tempData = json_decode($val['attach'],true);
                $arrObjs['list'][$key]['topicId'] = strval($tempData['topicId']);
                unset($arrObjs['list'][$key]['attach']);
            }else{
                unset($arrObjs['list'][$key]['attach']);
            }           
            $arrObjs['list'][$key]['mid']   = strval($val['mid']);
            $arrObjs['list'][$key]['type']  = strval($val['type']);
            $arrObjs['list'][$key]['image'] = Base_Image::getUrlByName($val['image']);
            $arrObjs['list'][$key]['create_time'] = Base_Util_String::getTimeAgoString($val['create_time']);
            if($val['type'] == Msg_Type_Type::REPLY){
                sscanf($val['content'],Msg_Type_Type::$_arrMsgMap[Msg_Type_Type::REPLY]['content'],$name);
                $objUser = new User_Object_User();
                $objUser->fetch(array('nick_name' => $name));
                $arrObjs['list'][$key]['avatar'] = Base_Image::getUrlByName($objUser->image);
            }else{
                $arrObjs['list'][$key]['avatar'] = '';
            }
        }
        return $arrObjs['list'];
    }
    
    /**
     * 查询消息
     * @param integer $intPage
     * @param integer $intPageSize
     * @param array $arrParams
     */
    public function queryMsg($intPage, $intPageSize,$arrParams){
        $listMsg = new Msg_List_Msg();
        $filter  = '';
        foreach ($arrParams as $key => $val){
            if(!in_array($key,$this->_fields)){
                unset($arrInfo[$key]);
            }elseif($key != 'status' || (($key == 'status') && ($val != Msg_Type_Status::ALL))){
                $filter .= "`".$key."`= $val and ";
            }
        }
        if(isset($arrParams['status']) && ($arrParams['status'] == Msg_Type_Status::ALL)){
            $filter .= '`status` !='.Msg_Type_Status::DEL;
        }elseif(!empty($filter)){
            $filter = substr($filter,0,-4);
        }
        if(!empty($filter)){
            $listMsg->setFilterString($filter);
        }
        $listMsg->setGroup("content");
        $listMsg->setPage($intPage);
        $listMsg->setPagesize($intPageSize);
        return $listMsg->toArray();
    }
    
    /**
     * 删除消息
     * @param unknown $mid
     */
    public function del($mid){
        $objMsg = new Msg_Object_Msg();
        $objMsg->fetch(array('mid'=>$mid));
        $objMsg->status = Msg_Type_Status::DEL;
        return $objMsg->save();
    }
    
    /**
     * 删除所有消息
     * @param unknown $uid
     */
    public function delAll($uid){
        $objsMsg = new Msg_List_Msg();
        $objsMsg->setFilter(array('receiver'=>$uid));
        $arrObj = $objsMsg->getObjects();
        foreach ($arrObj as $obj){
            $obj->status = Msg_Type_Status::DEL;
            $ret = $obj->save();
            if(!$ret){
                return $ret;
            }
        }
        return true;
    }
    
    /**
     * 发送消息
     * @param unknown $toid
     * @param unknown $intType
     * @param unknown $arrParam
     * @param number $fromid
     * @return boolean
     */
    public function sendmsg($intType, $image = '',$toid = '', $arrParam = array(), $fromid = 0) {
        $count  = 0;
        if($intType == Msg_Type_Type::SYSTEM){//系统消息
            $listUser = new User_List_User();
            $listUser->setFields(array('id'));
            $listUser->setPagesize(PHP_INT_MAX);
            $arrUser = $listUser->toArray();
            foreach ($arrUser['list'] as $user){
                $objMsg = new Msg_Object_Msg();
                $objMsg->sender    = $fromid;
                $objMsg->receiver  = $toid;
                $objMsg->type      = $intType;
                $objMsg->image     = $image;
                $objMsg->title     = vsprintf(Msg_Type_Type::$_arrMsgMap[$intType]['title'],$arrParam['title']);
                $strContent        = vsprintf(Msg_Type_Type::$_arrMsgMap[$intType]['content'],$arrParam['content']);
                $objMsg->content   = $strContent;               
                $objMsg->receiver  = $user['id'];
                $ret = $objMsg->save();
                if($ret){
                    $count += 1;
                }
            }
        }else{ //回复消息
            $objMsg = new Msg_Object_Msg();
            $objMsg->sender    = $fromid;
            $objMsg->receiver  = $toid;
            $objMsg->type      = $intType;
            $objMsg->image     = $image;
            $logicUser         = new User_Logic_User();
            $userName          = $logicUser->getUserName($arrParam['user_id']);
            $strContent        = vsprintf(Msg_Type_Type::$_arrMsgMap[$intType]['content'],$userName);
            //unset($arrParam['user_id']);
            $objMsg->attach    = json_encode($arrParam);
            $objMsg->title     = '';
            $objMsg->content   = $strContent;
            $objMsg->title     = Msg_Type_Type::$_arrMsgMap[$intType]['title'];
            $objMsg->receiver = $toid;
            $ret = $objMsg->save();
            if($ret){
                $count += 1;
            }
        }       
        return $count;
    }
}
