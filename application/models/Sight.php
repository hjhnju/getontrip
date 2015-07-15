<?php

class SightModel extends PgBaseModel
{

    private $table = 'sight';

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
    public function getSightByCity($cityId){
        $sql = "SELECT * FROM sight WHERE city_id = $cityId";
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
    public function getSightList(){
        $sql = "SELECT * FROM sight";
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
            $_addFields[] = $_key;
            $_addValues[] = $_value;
        }
        $_addFields = implode(',', $_addFields);
        $_addValues = implode("','", $_addValues);
        $_sql = "INSERT INTO sight ($_addFields) VALUES ('$_addValues')";
        try {
            $sth = $this->db->prepare($_sql);
            $ret = $sth->execute()->rowCount();
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
        $_where = "AND id = $sightId"; 
        foreach ($_updateData as $_key=>$_value) {  
            if (is_array($_value)) {  
                $_setData .= "$_key=$_value[0],";  
            } else {  
                $_setData .= "$_key='$_value',";  
            }  
        }  
        $_setData = substr($_setData, 0, -1);  
        $_sql = "UPDATE sight SET $_setData $_where";  
        $sth = $this->db->prepare($_sql);
        $ret = $sth->execute()->rowCount();
        return $ret;  
    }
}