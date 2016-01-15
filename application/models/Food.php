<?php

class FoodModel extends BaseModel
{

    private $table = 'food';
    

    public function __construct(){
        parent::__construct();
    }
    
    /**
     * 根据where条件查询景点数
     * @param unknown $arrWhere
     * @return boolean|multitype:
     */
    public function getDestFood($destId, $destType, $foodStatus){
        $arrRet = array();
        $sql = "SELECT b.id FROM `destination_food` a, `food` b  where a.destination_id = $destId  and a.destination_type = $destType and a.food_id = b.id and b.status=$foodStatus";
        try {
            $ret = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return 0;
        }
        foreach ($ret as $val){
            $arrRet[] = $val['id'];
        }
        return $arrRet;
    }
}