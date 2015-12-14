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
        if(!empty($arrInfo)){
            $listArticle->setFilter($arrInfo);
        }
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