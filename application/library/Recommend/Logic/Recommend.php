<?php
class Recommend_Logic_Recommend extends Base_Logic{
    
    /**
     * 根据条件获取推荐的文章列表
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrInfo
     */
    public function listArticles($page, $pageSize, $arrInfo = array()){
        $listArticle = new Recommend_List_Result();
        $strFilter   = "";
        if(isset($arrInfo['sight'])){
            $strFilter = "`label_type` =".Recommend_Type_Label::SIGHT." and label_id=".$arrInfo['sight'];
            unset($arrInfo['sight']);
        }
        if(isset($arrInfo['tag'])){
            $strFilter = "`label_type` =".Recommend_Type_Label::GENERAL." and label_id=".$arrInfo['tag'];
            unset($arrInfo['tag']);
        }
        if(empty($strFilter)){
            $strFilter = "1";
        }
        foreach ($arrInfo as $key => $val){            
            $strFilter .= " and `".$key."` =".$val;
        }
        $listArticle->setFilterString($strFilter);
        $listArticle->setPage($page);
        $listArticle->setPagesize($pageSize);
        return $listArticle->toArray();
    }
    
    /**
     * 对文章进行处理
     * @param integer $id
     * @param array $arrInfo
     */
    public function dealArticle($id, $arrInfo){
        foreach ($arrInfo as $val){
            $objRecommendRet = new Recommend_Object_Result();
            $objRecommendRet->fetch(array('id' => $id,'label_id' => $val['label_id'],'label_type' => $val['label_type']));
            $objRecommendRet->status = $val['status'];
            $objRecommendRet->save();
        }
    }
}