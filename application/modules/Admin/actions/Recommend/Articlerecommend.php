<?php
/**
 * 文章推荐列表
 * @author fyy
 *
 */
class ArticlerecommendAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $statusArray=Recommend_Type_Status::$names;
        $statusArray=array_reverse($statusArray,true);
        $this->getView()->assign('statusArray', $statusArray); 
    }
}