<?php
class Admin_Logic_Admin extends Base_Logic{
    
    public function __construct(){
        
    }
    

    /**
     * 登录接口
     * @param string $name,用户名
     * @param string $passwd,密码
     * @return boolean
     */
    public function login($name,$password){
        $objAdmin = new Admin_Object_Admin();
        $password = Base_Util_Secure::encrypt($password);
        $objAdmin->fetch(array('name' => $name,'passwd' => $password));
        if(!empty($objAdmin->id)){
            $objAdmin->loginTime = time();
            Yaf_Session::getInstance()->set(Admin_Keys::getLoginAdminKey(), $objAdmin->id);
            return $objAdmin->save();
        }
        return false;
    }
    
    /**
     * 根据条件查询管理员信息
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrParams
     * @return array
     */
    public function listAdmin($page, $pageSize, $arrParams = array()){
        $listAdmin = new Admin_List_Admin();
        $listAdmin->setPage($page);
        $listAdmin->setPageSize($pageSize);
        if(!empty($arrParams)){
            $listAdmin->setFilter($arrParams);
        }
        return $listAdmin->toArray();
    }
    
    /**
     * 根据ID查询管理员信息详情
     * @param integer $adminId
     * @return array
     */
    public function getAdminById($adminId){
        $objAdmin = new Admin_Object_Admin();
        $objAdmin->fetch(array('id' => $adminId));
        return $objAdmin->toArray();
    }
    
    
    /**
     * 添加管理员
     * @param array $arrParams
     * @return boolean
     */
    public function addAdmin($arrParams){
        $objAdmin = new Admin_Object_Admin();
        foreach($arrParams as $key => $val){
            $key = $this->getprop($key);
            $objAdmin->$key = $val;
        }
        return $objAdmin->save();
    }
    
    /**
     * 删除管理员
     * @param integer $adminId
     * @return boolean
     */
    public function delAdmin($adminId){
        $objAdmin = new Admin_Object_Admin();
        $objAdmin->fetch(array('id' => $adminId));
        return $objAdmin->remove();
    }
    
    /**
     * 编辑管理员信息
     * @param integer $adminId
     * @param array $arrParams
     * @return boolean
     */
    public function editAdmin($adminId,$arrParams){
        $objAdmin = new Admin_Object_Admin();
        $objAdmin->fetch(array('id' => $adminId));
        foreach($arrParams as $key => $val){
            $key = $this->getprop($key);
            $objAdmin->$key = $val;
        }
        return $objAdmin->save();
    }
    
    public function getUserName($userId){
        $objAdmin = new Admin_Object_Admin();
        $objAdmin->fetch(array('id' => $userId));
        return $objAdmin->name;
    }
}