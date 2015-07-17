<?php
class User_Logic_Info{
    
    protected $_logicUser;
    
    public function __construct(){
        $this->_logicUser = new User_Logic_User();
    }
    
    /**
     * 获取标签信息列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getUserInfo($deviceId){
      $userId = $this->_logicUser->getUserId($deviceId);
      $objUser = new User_Object_User();
      $objUser->fetch(array('user_id' => $userId));
      return $objUser->toArray();
    }
    
    /**
     * 获取热门标签列表，根据话题所有的标签数量排序作为热度
     * @param integer $size
     * @return array
     */
    public function editUserInfo($deviceId,$arrParam){
        $userId = $this->_logicUser->getUserId($deviceId);
        $objUser = new User_Object_User();
        $objUser->fetch(array('user_id' => $userId));
        foreach ($arrParam as $key => $val){
            $objUser->$key = $val;
        }
        return $objUser->save();
    }
}