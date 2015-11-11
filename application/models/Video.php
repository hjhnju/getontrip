<?php

class VideoModel extends BaseModel
{

    private $table = 'video';
    

    public function __construct(){
        parent::__construct();
    }
    
    /**
     * 根据where条件查询景点数
     * @param unknown $arrWhere
     * @return boolean|multitype:
     */
    public function getSightVideo($sightId, $videoStatus){
        $arrRet = array();
        $sql = "SELECT b.id FROM `sight_video` a, `video` b  where a.sight_id = $sightId and a.video_id = b.id and b.status=$videoStatus";
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