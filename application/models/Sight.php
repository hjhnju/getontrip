<?php

class SightModel extends PgBaseModel
{

    private $table = 'sight';
    
    private $_fileds = array('id','name','describe','level','city_id','x','y','image','create_time','update_time');

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
            $ret = $sth->fetchObject();
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return false;
        }
        return $ret;
    }
    
    /**
     * 根据城市ID获取景点
     * @param integer $cityId
     * @return boolean|multitype:
     */
    public function getSightByCity($page,$pageSize,$cityId){
        $from = ($page-1)*$pageSize;
        $sql = "SELECT * FROM sight WHERE city_id = $cityId  LIMIT $pageSize OFFSET $from";
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
    public function getSightList($page,$pageSize){
        $from = ($page-1)*$pageSize;
        $sql = "SELECT * FROM sight LIMIT $pageSize OFFSET $from";
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
            if (is_array($_value)) {  
                $_setData .= "$_key=$_value[0],";  
            } else {  
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
    public function search($query,$page,$pageSize){
        $offset = ($page-1)*$pageSize;
        $sql = "SELECT * FROM sight where name like '%".$query."%' limit $pageSize offset $offset";
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
}