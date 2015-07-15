<?php

class GisModel extends PgBaseModel
{

    private $table = 'sight';

    public function __construct(){
        parent::__construct();
    }

    /**
     * 获取某个点附近的景点
     * @param array $loc,点的经纬度数组          
     * @return array $ret,返回结果数组
     */
    public function getNearSight($loc){
        $x = $loc['x'];
        $y = $loc['y'];
        $sql = "SELECT id, name, image, describe, earth_distance(ll_to_earth(sight.x, sight.y),ll_to_earth($x,$y))" . " AS dis FROM sight ORDER BY dis ASC";
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