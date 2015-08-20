<?php

class SightModel extends PgBaseModel
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
        $sql = "SELECT * FROM sight WHERE id = $sightId";
        try {
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        if(isset($ret[0])){
            return $ret[0];
        }
        return array();
    }
    
    /**
     * 根据城市ID获取景点
     * @param integer $cityId
     * @return boolean|multitype:
     */
    public function getSightByCity($page,$pageSize,$cityId){
        $from = ($page-1)*$pageSize;
        $sql = "SELECT id,name,image FROM sight WHERE city_id = $cityId  ORDER BY update_time DESC LIMIT $pageSize OFFSET $from";
        try {
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        return $ret;
    }
    
    /**
     * 获取取景列表
     * @return boolean|mixed
     */
    public function getSightList($page,$pageSize,$status = Sight_Type_Status::PUBLISHED){
        $from = ($page-1)*$pageSize;
        if ($status == Sight_Type_Status::ALL) {
            $sql = "SELECT * FROM sight ORDER BY update_time DESC LIMIT $pageSize OFFSET $from";
        }else{
            $sql = "SELECT * FROM sight WHERE status = $status ORDER BY update_time DESC LIMIT $pageSize OFFSET $from";
        }        
        try {
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        return $ret;
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
        $logicUser = new User_Logic_Login();
        $userId = $logicUser->checkLogin();
        if(!empty($userId)){
            $_addFields[] = 'create_user';
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
            $sth = $this->db->prepare($_sql);
            $ret = $sth->execute();
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
        foreach ($_updateData as $_key=>$_value) { 
            if(in_array($_key,$this->_fileds)){
                $_setData .= "$_key='$_value',"; 
            } 
        }  
        $time = time();
        $_setData .= "update_time=$time";  
        $_sql = "UPDATE sight SET $_setData $_where"; 
        $sth = $this->db->prepare($_sql);
        $ret = $sth->execute();
        return $ret;  
    }
    
    /**
     * 删除景点信息
     * @param integer $id
     * @return boolean
     */
    public function delSight($id){
        $sql = "DELETE FROM sight WHERE id = $id";
        try {
            $sth = $this->db->prepare($sql);
            $ret = $sth->execute();
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        return $ret;
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
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $ret = $sth->fetchColumn();
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
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
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
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
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
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $ret = $sth->fetchColumn();
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return 0;
        }
        return $ret;
    }
}