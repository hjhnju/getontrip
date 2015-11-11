<?php

class BookModel extends BaseModel
{

    private $table = 'book';
    

    public function __construct(){
        parent::__construct();
    }
    
    /**
     * 根据where条件查询景点数
     * @param unknown $arrWhere
     * @return boolean|multitype:
     */
    public function getSightBook($sightId, $bookStatus){
        $arrRet = array();
        $sql = "SELECT b.id FROM `sight_book` a, `book` b  where a.sight_id = $sightId and a.book_id = b.id and b.status=$bookStatus";
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