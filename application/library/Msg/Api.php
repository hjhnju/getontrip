<?php 
class Msg_Api {
    
    /**
     * 消息映射
     * 1 系统消息
     */
    public static $_arrMsgMap = array(
    	Msg_Type::SYSTEM => array(
    	    'type'     => '系统消息',
    	    'content'  => '欢迎您使用途知产品！',
    	    'linkname' => '首页',
    	    'link'     => '/home',
    	),        
    );
    
    /**
     * 根据消息类型返回消息link
     * @param string $strType
     * @return array
     */
    public static function getLink($strType){
        foreach (self::$_arrMsgMap as $val){
            if($val['type'] === $strType){
                return array(
                    'link'     =>$val['link'],
                    'linkname' => $val['linkname'],
                );
            }
        }
    }
    
    /**
     * 发送系统消息
     * @param integer $fromid
     * @param integer $toid
     * @param int     $intType：消息类型，定义见 Msg_Type
     * @param array   $arrParam
     * @return true|false 成功true, 失败 false
     */
    public static function sendmsg($toid, $intType, $arrParam = array(), $fromid = 0) {
        $objMsg = new Msg_Object_Msg();
        $objMsg->sender    = $fromid;
        $objMsg->receiver  = $toid;
        $objMsg->type      = self::$_arrMsgMap[$intType]['type'];
        $objMsg->title     = Msg_Type::getTypeName($intType);
        if(!empty($arrParam)){
            $strContent = vsprintf(self::$_arrMsgMap[$intType]['content'],$arrParam);            
        }else{
            $strContent = self::$_arrMsgMap[$intType]['content'];
        }
        $objMsg->content   = $strContent;
        $ret = $objMsg->save();
        Base_Log::notice(array(
        	'msg'    => 'msg send',
        	'fromid' => $fromid,
        	'toid'   => $toid,
        ));
        return $ret;
    }
    
    /**
     * 获取未读消息数
     * @param int $uid
     * @return int 
     */
    public static function getUnreadMsgNum($uid){
        $msg = new Msg_Logic_Msg();
        $ret = $msg->getUnread($uid);
        return $ret;
    }
    
    public static function getMsgList(){
        
    }
}