<?php

class SpecialtyModel extends BaseModel
{

    private $table = 'specialty';
    

    public function __construct(){
        parent::__construct();
    }
    
    /**
     * 根据where条件查询景点数
     * @param unknown $arrWhere
     * @return boolean|multitype:
     */
    public function getDestSpecialty($destId, $destType, $specialtyStatus){
        $arrRet = array();
        $sql = "SELECT b.id FROM `destination_specialty` a, `specialty` b  where a.destination_id = $destId  and a.destination_type = $destType and a.specialty_id = b.id and b.status=$specialtyStatus";
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