<?php
/**
 * 对象关系模型不能解决联表查询的需求
 * @author huwei
 *
 */
class CityModel{
    
    protected $db;
    
    public function __construct(){
        $this->db = Base_Db::getInstance('getontrip');
    }
    
    /**
     * 查询城市信息
     * @param array $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryCity($arrInfo,$page,$pageSize){
        $from   = ($page-1)*$pageSize;
        $filter = '';
        foreach ($arrInfo as $key => $val){
            $filter  = 'a.`'.$key."` = ".$val." AND ";
        }
        $filter .= "b.`cityid` = 0 AND b.`provinceid` != 0 and a.`id` = b.`id`";
        $sql  = 'SELECT a.*, b.* FROM  `city` a, `city_meta` b WHERE '.$filter." limit $from,$pageSize";
        try {                 	
            $data = $this->db->fetchAll($sql);          
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());          
            return array();
        }
        return $data;
    }
    
    /**
     * 获取数量
     * @param array $arrInfo
     * @param string $str
     * @return number
     */
    public function getCityNum($arrInfo = array(), $str = ''){
        $filter = '';
        foreach ($arrInfo as $key => $val){
            $filter  = 'a.`'.$key."` = ".$val." AND ";
        }
        $filter .= "b.`cityid` = 0 AND b.`provinceid` != 0 and a.`id` = b.`id`";
        $sql  = 'SELECT count(*) FROM `city` a, `city_meta` b WHERE '.$filter;
        if(!empty($str)){
            $sql .= " and b.`name` like '".$str."%'";
        }
        try {
            $data = $this->db->fetchOne($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return 0;
        }
        return $data;
    }
    
    /**
     * 根据前缀模糊查询
     * @param unknown $str
     * @param unknown $page
     * @param unknown $pageSize
     * @param unknown $arrParms
     */
    public function queryCityPrefix($str,$page,$pageSize,$arrInfo = array()){
        $from   = ($page-1)*$pageSize;
        $filter = '';
        foreach ($arrInfo as $key => $val){
            $filter  = 'a.`'.$key."` = ".$val." AND ";
        }
        $filter .= "b.`cityid` = 0 and b.`provinceid` != 0 and b.`name` like '".$str."%' and a.`id` = b.`id`";
        $sql  = 'SELECT a.*,b.* FROM `city` a, `city_meta` b WHERE '.$filter." limit $from,$pageSize";
        try {
            $data = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return array();
        }
        foreach ($data as $key => $val){
            $city = new City_Object_City();
            $city->fetch(array('id' => $val['id']));
            $arrCity = $city->toArray();
            $data[$key]['image'] = isset($arrCity['image'])?$arrCity['image']:'';
        }
        return $data;
    }
    
}
