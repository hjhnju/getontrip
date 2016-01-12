<?php
class GisModel
{
    
    private $table = 'sight';
    
    protected $db  = '';
    
    protected $conn = '';

    public function __construct(){
        $this->conn = Base_Pg::getInstance("getontripoff");
    }

    /**
     * 获取某个点附近的景点
     * @param array $loc,点的经纬度数组          
     * @return array $ret,返回结果数组
     */
    public function getNearSight($loc,$page,$pageSize){
        $x = $loc['x'];
        $y = $loc['y'];
        $from = ($page-1)*$pageSize;
        $sql = "SELECT id, city_id, name, image, describe, earth_distance(ll_to_earth(sight.x, sight.y),ll_to_earth($x,$y))" .
         " AS dis FROM sight  WHERE hastopic = 1  AND status = 2 ORDER BY dis ASC OFFSET $from limit $pageSize";
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
     * 根据给定点，找出其到某景点的距离，单位米
     * @param double $x
     * @param double $y
     * @param integer $sightId
     * @return double
     */
    public function getEarthDistanceToSight($x,$y,$sightId){
        $sql = "SELECT earth_distance(ll_to_earth(sight.x, sight.y),ll_to_earth($x,$y))" .
        " AS dis FROM sight WHERE id = $sightId";
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
     * 根据给定点，找出其到某景点的距离，单位米
     * @param double $x
     * @param double $y
     * @param integer $topicId
     * @return double
     */
    public function getEarthDistanceToTopic($x,$y,$topicId){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $topicId));
        return $this->getEarthDistanceToPoint($x, $y, $objTopic->x, $objTopic->y);
    }
    
    /**
     * 计算两点间的地球面距离，单位米
     * @param double $x1
     * @param double $y1
     * @param double $x2
     * @param double $y2
     * @return double
     */
    public function getEarthDistanceToPoint($x1,$y1,$x2,$y2){
        $sql = "SELECT earth_distance(ll_to_earth($x1, $y1),ll_to_earth($x2,$y2)) AS dis";
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
    
    public function getNear($x,$y,$page,$pageSize){
        $arrRet = array();
        $from   = ($page-1)*$pageSize;
        $sql    = "SELECT id,earth_distance(ll_to_earth(x, y), ll_to_earth($x,$y)) as dis FROM sight  ORDER BY dis ASC limit $pageSize offset $from;";
        try {
            $resultSet = pg_query($this->conn,$sql);
            while ($row = pg_fetch_row($resultSet)){
                $tmp['id']  = $row[0];
                $tmp['dis'] = $row[1];
                $arrRet[] = $tmp;
            }
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
        }
        return $arrRet;
    }
    
    public function insertSight($id){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('id' => $id));
        if(empty($objSight->id) || empty($objSight->x) || empty($objSight->y)){
            return false;
        }
        $sql = "insert into sight values($objSight->id,$objSight->cityId,$objSight->x,$objSight->y)";
        try {
            $ret = pg_exec($this->conn,$sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
        }
        if(!$ret){
            return false;
        }
        return true;        
    }
    
    public function insertLandscape($id){
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $id));
        if(empty($objKeyword->id) || empty($objKeyword->x) || empty($objKeyword->y)){
            return false;
        }
        $sql = "insert into landscape values($objKeyword->id,$objKeyword->sightId,$objKeyword->x,$objKeyword->y)";        
        try {
            $ret = pg_query($this->conn,$sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
        }
        if(!$ret){
            return false;
        }
        return true; 
    }
}