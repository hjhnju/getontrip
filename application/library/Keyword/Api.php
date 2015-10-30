<?php
/**
 * 景点所对应的百科词条接口
 * @author huwei
 *
 */
class Keyword_Api{
    
    /**
     * 接口1：Keyword_Api::queryKeywords($page,$pageSize,$arrInfo)
     * 查询景点的词条信息
     * @param integer $page
     * @param integer $pageSize
     * @param array  $arrInfo eg:array('status'=>xxx,'create_user'=>xxx);
     * @return array
     */
    public static function queryKeywords($page,$pageSize,$arrInfo){
        $logic = new Keyword_Logic_Keyword();
        return $logic->queryKeywords($page, $pageSize,$arrInfo);
    }
    
    /**
     * 接口2：Keyword_Api::addKeyword($arrInfo)
     * 添加词条信息
     * @param array $arrInfo
     * @return boolean
     */
    public static function addKeyword($arrInfo){
        $logic = new Keyword_Logic_Keyword();
        return $logic->addKeywords($arrInfo);
    }
    
    /**
     * 接口3:Keyword_Api::editKeyword($id,$arrInfo)
     * 词条编辑
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public static function editKeyword($id,$arrInfo){
        $logic = new Keyword_Logic_Keyword();
        return $logic->editKeyword($id, $arrInfo);
    }
    
    /**
     * 接口4：Keyword_Api::delKeyword($id)
     * 删除词条
     * @param integer $id
     * @return boolean
     */
    public static function delKeyword($id){
        $logic = new Keyword_Logic_Keyword();
        return $logic->delKeyword($id);
    }
    
    /**
     * 接口5：Keyword_Api::queryById($id)
     * 根据ID查询词条
     * @param integer $id
     * @return array
     */
    public static function queryById($id){
        $logic = new Keyword_Logic_Keyword();
        return $logic->queryById($id);
    }
    
    /**
     * 接口6：Keyword_Api::changeWeight($id,$to)
     * 修改某景点下的词条的权重
     * @param integer $id 词条ID
     * @param integer $to 需要排的位置 
     * @return boolean
     */
    public static function changeWeight($id,$to){
        $logic = new Keyword_Logic_Keyword();
        return $logic->changeWeight($id,$to);
    }
    
    /**
     * 接口7：Keyword_Api::getKeywordNum($sighId)
     * @param integer $sighId
     * @param integer $status，默认是已发布的状态，可以不传
     * @return number
     */
    public static function getKeywordNum($sighId, $status = Keyword_Type_Status::PUBLISHED){
        $logic = new Keyword_Logic_Keyword();
        return $logic->getKeywordNum($sighId, $status);
    }
}