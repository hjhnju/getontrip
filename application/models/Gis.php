<?php
class GisModel
{
    
    private $table = 'sight';
    
    protected $db  = '';
    
    protected $conn = '';

    public function __construct(){
        if(ini_get('yaf.environ') == 'dev'){
            $this->conn = Base_Pg::getInstance("getontripoff");
        }else{
            $this->conn = Base_Pg::getInstance("getontrip");
        }
        
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
    
    public function getNearSight($x,$y,$page,$pageSize){
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
    
    public function getNearLandscape($x,$y,$sight_id, $page,$pageSize){
        $arrRet = array();
        $from   = ($page-1)*$pageSize;
        $sql    = "SELECT id,earth_distance(ll_to_earth(x, y), ll_to_earth($x,$y)) as dis FROM landscape where sight_id=$sight_id ORDER BY dis ASC limit $pageSize offset $from;";
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
            $ret = @pg_exec($this->conn,$sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
        }
        if(!$ret){
            return false;
        }
        return true;        
    }
    
    public function delSight($id){
        $sql = "delete from sight where id=".$id;
        try {
            $ret = @pg_exec($this->conn,$sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
        }
        if(!$ret){
            return false;
        }
        return true;
    }
    
    public function delLandscape($id){
        $sql = "delete from landscape where id=".$id;
        try {
            $ret = @pg_exec($this->conn,$sql);
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
            $ret = @pg_query($this->conn,$sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
        }
        if(!$ret){
            return false;
        }
        return true; 
    }
    
    public function getLocation(){
        $getIp   = Base_Util_Ip::getClientIp();
        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=7IZ6fgGEGohCrRKUE9Rj4TSQ&ip={$getIp}&coor=bd09ll");
        $ret = json_decode($content);
        return array(
            'x' => $ret->content->point->x,
            'y' => $ret->content->point->y,
        );
    }
}