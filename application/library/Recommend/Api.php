<?php
class Recommend_Api{
    
    /**
     * 接口1：Recommend_Api::listArticles($page, $pageSize, $arrInfo = array())
     * 根据条件获取推荐的文章列表
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrInfo
     */
    public static function listArticles($page, $pageSize, $arrInfo = array()){
        $logic = new Recommend_Logic_Recommend();
        return $logic->listArticles($page, $pageSize, $arrInfo);
    }
    
    /**
     * 接口2：Recommend_Api::dealArticle($id, $arrInfo)
     * 对文章进行处理
     * @param integer $id
     * @param array $arrInfo,eg:array(array('label_id'=>1,'label_type'=>1,'status'=>1),array(...)...); 
     * @return boolean
     */
    public static function dealArticle($id, $arrInfo){
        $logic = new Recommend_Logic_Recommend();
        return $logic->dealArticle($id, $arrInfo);
    }
    
    /**
     * 接口3：Recommend_Api::getArticleDetail($id)
     * 获取文章详情
     * @param integer $id
     * @return array
     */
    public static function getArticleDetail($id){
        $logic = new Recommend_Logic_Recommend();
        return $logic->getArticleDetail($id);
    }
    
} 