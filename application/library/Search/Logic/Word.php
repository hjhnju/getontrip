<?php
class Search_Logic_Word extends Base_Logic{
    
    const HOT_WORD_SIZE = 10;
    
    protected $fields = array('id', 'word', 'deviceid', 'create_time', 'update_time', 'userid', 'status');
    
    public function addSearchWord($word){
        $objSearchWord = new Search_Object_Word();
        $objSearchWord->word     = $word;
        $objSearchWord->deviceid = isset($_COOKIE['device_id'])?trim($_COOKIE['device_id']):'';
        $objSearchWord->userid   = User_Api::getCurrentUser();
        $objSearchWord->status   = Search_Type_Word::AUDITING;
        return $objSearchWord->save();
    }
    
    public function getSearchHotWord($size = self::HOT_WORD_SIZE){
        $arrRet = array();
        $model  = new BaseModel();
        $sql = "select distinct(`search_word`.`word`) as word,(select count(*) from `search_word` where `word` = word) as count from `search_word` where `status`= ".Search_Type_Word::AUDITPASS." order by count desc limit 0,".$size;
        try {
            $data = $model->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return array();
        }
        foreach ($data as $val){
            $arrRet[] = $val['word'];
        }
        return $arrRet;
    }
    
    public function getQueryWords($page, $pageSize, $arrConf = array()){
        $arrRet   = array();
        $model    = new BaseModel();
        $from     = ($page-1)*$pageSize;
        $strWhere = '1';
        foreach ($arrConf as $key => $val){
            if(in_array($key,$this->fields)){
                $strWhere .= ' and `'.$key."` = $val";
            }
        }
        $sql = "select distinct(`word`), `status` from `search_word` where ".$strWhere." order by `status` desc, create_time desc limit $from, $pageSize ";
        try {
            $data = $model->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return array();
        }
        
        $sql_count = 'select count(distinct(`word`)) from `search_word` where '.$strWhere;
        $num       = $model->db->fetchOne($sql_count);
        return array(
            'page' => $page,
            'pagesize' => $pageSize,
            'pageall' => ceil($num/$pageSize),
            'total' => $num,
            'list' => $data,
        );
    }
    
    
    public function editQueryWordStatus($word, $status){
        $ret = true;
        $listSearchWord = new Search_List_Word();
        $listSearchWord->setFilter(array('word' => $word));
        $listSearchWord->setPagesize(PHP_INT_MAX);
        $arrRet = $listSearchWord->toArray();
        foreach ($arrRet['list'] as $val){
            $objSearchWord = new Search_Object_Word();
            $objSearchWord->fetch(array('id' => $val['id']));
            $objSearchWord->status = $status;
            $ret = $objSearchWord->save();
        }
        return $ret;
    }
}