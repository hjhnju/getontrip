<?php

class SightModel extends BaseModel
{

    private $table = 'sight';
    
    private $_fileds = array('id','name','describe','level','city_id','x','y','image','hastopic','create_user','create_time','update_user','update_time','status');

    public function __construct(){
        parent::__construct();
    }

    
    /**
     * 根据景点ID获取景点信息
     * @param integer $sightId
     * @return array
     */
    public function getSightById($sightId){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' => $sightId));
        return $objSight->toArray();
    }
    
    /**
     * 根据城市ID获取景点列表
     * @param integer $cityId
     * @return boolean|multitype:
     */
    public function getSightByCity($page,$pageSize,$cityId){
        $listSight = new Sight_List_Sight();
        $listSight->setFields(array('id','name','image'));
        $listSight->setFilter(array('city_id' => $cityId));
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $arrSight = $listSight->toArray();
        return $arrSight['list'];
    }
    
    /**
     * 获取取景列表
     * @return boolean|mixed
     */
    public function getSightList($page,$pageSize,$status = Sight_Type_Status::PUBLISHED){
        $listSight = new Sight_List_Sight();
        if ($status != Sight_Type_Status::ALL) {
            $listSight->setFilter(array('status' => $status));
        }
        $listSight->setPage($page);
        $listSight->setPagesize($pageSize);
        $arrSight = $listSight->toArray();
        return $arrSight['list'];
    }
    
    /**
     * 向数据库中添加新的数据
     * @param array $_addData
     */
    public function addNewSight( $_addData ){
        $_addFields = array();
        $_addValues = array();
        foreach ($_addData as $_key => $_value) {
            if(in_array($_key,$this->_fileds)){
                $_addFields[] = $_key;
                $_addValues[] = $_value;
            }
        }
        $logicUser = new User_Logic_Third();
        $userId = $logicUser->checkLogin();
        if(!empty($userId)){
            $_addFields[] = 'create_user';
            $_addValues[] = $userId;
            
            $_addFields[] = 'update_user';
            $_addValues[] = $userId;
        }
        if(!in_array('create_time',$_addFields)){
            $_addFields[] = 'create_time';
            $_addValues[] = time();
        }
        if(!in_array('update_time',$_addFields)){
            $_addFields[] = 'update_time';
            $_addValues[] = time();
        }
        $_addFields = implode(',', $_addFields);
        $_addValues = implode("','", $_addValues);
        $_sql = "INSERT INTO sight ($_addFields) VALUES ('$_addValues')";
        try {
            $ret = $this->db->query($_sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        return $ret;
    }
    
    /**
     * 修改景点信息
     * @param integer $sightId
     * @param array $_updateData
     * @return boolean
     */
    public function eddSight($sightId, $_updateData){
        $_setData = '';  
        $_where = "WHERE id = $sightId"; 
        $logicUser = new User_Logic_Third();
        $userId = $logicUser->checkLogin();
        if(!empty($userId)){
            $_updateData['update_user'] = $userId;
        }
        foreach ($_updateData as $_key=>$_value) { 
            if(in_array($_key,$this->_fileds)){
                $_setData .= "$_key='$_value',"; 
            } 
        }  
        $time = time();
        $_setData .= "update_time=$time";  
        $_sql = "UPDATE sight SET $_setData $_where"; 
        $ret = $this->db->query($_sql);
        return $ret;  
    }
    
    /**
     * 删除景点信息
     * @param integer $id
     * @return boolean
     */
    public function delSight($id){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' => $id));
        return $objSight->remove();
    }
    
    /**
     * 获取所有景点数
     * @return integer
     */
    public function getSightNum($cityId=''){
        if(empty($cityId)){
            $sql = "SELECT count(*) FROM sight";
        }else{
            $sql = "SELECT count(*) FROM sight WHERE city_id = $cityId";
        }
        try {
            $ret = $this->db->fetchOne($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return 0;
        }
        return $ret;
    }
    
    /**
     * 根据关键词模糊查询景点名
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function search($query,$page,$pageSize,$x,$y){
        $offset = ($page-1)*$pageSize;
        if(empty($x)){
            $sql = "SELECT * FROM sight where name like '%".$query."%' limit $pageSize offset $offset";
        }else{
            $sql = "SELECT id, city_id, name, image, describe, earth_distance(ll_to_earth(sight.x, sight.y),ll_to_earth($x,$y)) AS dis FROM sight where name like '%".$query."%'  ORDER BY dis ASC limit $pageSize offset $offset";
        }                
        try {
            $ret = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        return $ret;
    }
    
    /**
     * 根据给定条件查询
     * @param array $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function query($arrInfo,$page,$pageSize){
        $offset = ($page-1)*$pageSize;
        $where = "where ";
        if(isset($arrInfo['status']) && ($arrInfo['status'] == Sight_Type_Status::ALL)){
            unset($arrInfo['status']);
        }
        foreach ($arrInfo as $key => $val){
            $where .= "$key = '".$val."' and";
        }
        $where = substr($where, 0,-4);
        $sql = "SELECT * FROM sight $where ORDER BY update_time DESC limit $pageSize offset $offset";
        try {
            $ret = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        return $ret;
    }
    
    /**
     * 根据where条件查询景点数
     * @param unknown $arrWhere
     * @return boolean|multitype:
     */
    public function getSightNumByWhere($arrWhere){
        $where = "where ";
        foreach ($arrWhere as $key => $val){
            $where .= "$key = '".$val."' and";
        }
        $where = substr($where, 0,-4);
        $sql = "SELECT count(*) FROM sight $where";
        try {
            $ret = $this->db->fetchOne($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return 0;
        }
        return $ret;
    }
}