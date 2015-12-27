<?php
/**
 * 对象关系模型不能解决联表查询的需求
 * @author huwei
 *
 */
class CityModel extends BaseModel{
    
    protected $_fields_info = array('id', 'status', 'x', 'y', 'create_time', 'update_time', 'create_user', 'update_user', 'image');
    
    protected $_fields_meta = array('id', 'name', 'pinyin', 'pid', 'provinceid', 'cityid','countryid','continentid');
    
    public function __construct(){
        parent::__construct();
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
        $filterInfo = '';
        $filterMeta = '';        
        foreach ($arrInfo as $key => $val){
            if(in_array($key, $this->_fields_meta)){
                $filterMeta .= 'city_meta.`'.$key."` = ".$val." AND ";                
            }else{
                $filterInfo .= 'city.`'.$key."` = ".$val." AND ";
            }
        }
        $filter = $filterInfo . $filterMeta . " city_meta.`cityid` = 0 AND city_meta.`provinceid` != 0";
        $sql  = 'SELECT * FROM  `city` right join `city_meta` ON city_meta.`id` = city.`id` where '.$filter." order by city.`status` desc limit $from,$pageSize";
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
        $filterInfo = '';
        $filterMeta = '';        
        foreach ($arrInfo as $key => $val){
            if(in_array($key, $this->_fields_info)){
                $filterInfo .= 'city.`'.$key."` = ".$val." AND ";
            }else{
                $filterMeta .= 'city_meta.`'.$key."` = ".$val." AND ";
            }
        }
        $filter = $filterInfo . $filterMeta . " city_meta.`cityid` = 0 AND city_meta.`provinceid` != 0";
        $sql  = 'SELECT count(*) FROM `city` right join `city_meta` ON city_meta.`id` = city.`id` where '.$filter;
        if(!empty($str)){
            $sql .= " and city_meta.`name` like '".addslashes($str)."%'";
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
        $filterInfo = '';
        $filterMeta = '';        
        foreach ($arrInfo as $key => $val){
            if(in_array($key, $this->_fields_info)){
                $filterInfo .= 'city.`'.$key."` = ".$val." AND ";
            }else{
                $filterMeta .= 'city_meta.`'.$key."` = ".$val." AND ";
            }
        }
        $filter = $filterInfo . $filterMeta . " city_meta.`cityid` = 0 AND city_meta.`provinceid` != 0 and ";
        $filter .= "city_meta.`cityid` = 0 and city_meta.`provinceid` != 0 and city_meta.`name` like '".addslashes($str)."%'";
        $sql  = 'SELECT * FROM `city` right join `city_meta` ON city.`id` = city_meta.`id` where '.$filter." limit $from,$pageSize";
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
    
    public function getCityTopicNum($cityId){
        $arrTopicIds = array();
        $sql_general = "SELECT distinct(a.id) FROM `topic` a, `topic_tag`  b, `sight_tag` c ,`sight` d WHERE  a.status = ".Topic_Type_Status::PUBLISHED." and a.id = b.topic_id and b.tag_id = c.tag_id and c.sight_id = d.id  and d.status = ".Sight_Type_Status::PUBLISHED." and d.city_id = $cityId";
        $sql_normal  = "SELECT distinct(a.id) FROM `topic`  a, `sight_topic` b ,`sight` c WHERE  a.status = ".Topic_Type_Status::PUBLISHED." and a.id = b.topic_id and b.sight_id = c.id and c.city_id = $cityId and c.status=".Sight_Type_Status::PUBLISHED;
        $data = $this->db->fetchAll($sql_general);
        foreach ($data as $val){
            if(!in_array($val,$arrTopicIds)){
                $arrTopicIds[] = $val['id'];
            }
        }
        $data = $this->db->fetchAll($sql_normal);
        foreach ($data as $val){
            if(!in_array($val,$arrTopicIds)){
                $arrTopicIds[] = $val['id'];
            }
        }
        $arrTopicIds = array_unique($arrTopicIds);
        return count($arrTopicIds);
    }
    
    public function getCityWikiNum($cityId){
        $sql = "SELECT distinct(a.id) FROM `keyword` a, `sight`  b WHERE  a.status = ".Keyword_Type_Status::PUBLISHED." and a.sight_id = b.id and b.city_id  =".$cityId;
        $this->db->query($sql); 
        $num = $this->db->getNumRows();       
        return $num;
    }
    
    public function getCityVidoNum($cityId){
        $sql = "SELECT distinct(a.id) FROM `video` a, `sight_video`  b, `sight` c WHERE  a.status = ".Video_Type_Status::PUBLISHED." and a.id = b.video_id and b.sight_id = c.id and c.city_id  =".$cityId;
        $this->db->query($sql); 
        $num = $this->db->getNumRows();       
        return $num;
    }
    
    public function getCityBookNum($cityId){
        $sql = "SELECT distinct(a.id) FROM `book` a, `sight_book`  b, `sight` c WHERE  a.status = ".Book_Type_Status::PUBLISHED." and a.id = b.book_id and b.sight_id = c.id and c.city_id  =".$cityId;
        $this->db->query($sql); 
        $num = $this->db->getNumRows();       
        return $num;
    }
}
