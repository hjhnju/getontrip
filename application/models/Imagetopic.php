<?php
class ImagetopicModel extends BaseModel{    
    
    const ORDER_NEW = 1;
    
    const ORDER_HOT = 2;
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * 获取图文列表
     * @param string $strTopicId
     * @return array
     */
    public function getHotImageTopicIds($sightId,$page,$pageSize,$order){              
        $from = ($page-1)*$pageSize;        
        if($order == self::ORDER_NEW){
            $sql = "SELECT a.id FROM `imagetopic`  a,`sight_imagetopic` b WHERE a.status = ".Imagetopic_Type_Status::PUBLISHED." and a.id=b.imagetopic_id and b.sight_id = $sightId  ORDER by a.update_time desc limit $from,$pageSize";
        }else{
            $sql = "SELECT a.id FROM `imagetopic`  a,`sight_imagetopic` b WHERE a.status = ".Imagetopic_Type_Status::PUBLISHED." and a.id=b.imagetopic_id and b.sight_id = $sightId  ORDER by a.hot desc limit $from,$pageSize";
        }                   
        try {                  	
            $data = $this->db->fetchAll($sql);          
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());          
            return array();
        }        
        return $data;
    }
}
