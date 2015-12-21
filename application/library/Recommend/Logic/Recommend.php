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
        $bSpecial    = false;
        if(isset($arrInfo['sight'])){
            $strFilter = "`label_type` =".Recommend_Type_Label::SIGHT." and label_id=".$arrInfo['sight'];
            unset($arrInfo['sight']);
            $bSpecial = true;
        }
        if(isset($arrInfo['tag'])){
            $strFilter = "`label_type` =".Recommend_Type_Label::GENERAL." and label_id=".$arrInfo['tag'];
            unset($arrInfo['tag']);
            $bSpecial = true;
        }
        if(empty($strFilter)){
            $strFilter = "1";
        }
        foreach ($arrInfo as $key => $val){            
            $strFilter .= " and `".$key."` =".$val;
        }
        $listArticle->setGroup("obj_id");
        $listArticle->setFilterString($strFilter);
        $listArticle->setPage($page);
        $listArticle->setPagesize($pageSize);
        if($bSpecial){
            return $listArticle->toArray();   
        }
        $listArticle->setFields(array('id','obj_id'));
        $arrRet =  $listArticle->toArray();
        foreach($arrRet['list'] as $key => $val){
            $listArticle = new Recommend_List_Result();
            $listArticle->setFilter(array('obj_id' => $val['obj_id']));
            $listArticle->setFields(array('label_id','label_type','rate','reason','status','create_time','update_time'));
            $listArticle->setPagesize(PHP_INT_MAX);
            $arrTemp = $listArticle->toArray();
            $arrRet['list'][$key]['group'] = $arrTemp['list'];
        }
        return $arrRet;
    }
    
    /**
     * 对文章进行处理
     * @param integer $id
     * @param array $arrInfo
     */
    public function dealArticle($id, $arrInfo){
        foreach ($arrInfo as $val){
            $objRecommendRet = new Recommend_Object_Result();
            $objRecommendRet->fetch(array('obj_id' => $id,'label_id' => $val['label_id'],'label_type' => $val['label_type']));
            $objRecommendRet->status = $val['status'];
            $ret = $objRecommendRet->save();
            if ($val['status']==Recommend_Type_Status::ACCEPT) {
                //如果状态为确认，则保存当前文章到话题
                //添加到数据库
               $article = Recommend_Api::getArticleDetail($id);
               $sourceInfo = Source_Api::getSourceByName($article['source']); 
               if (empty($sourceInfo)) {
                   //不存在来源，则添加此来源
                   $sourceInfo = array(
                                        'name'=>$article['source'],
                                        'type'=>Source_Type_Type::MAGZINE,
                                        'group'=>0
                                        );
                   $addret = Source_Api::addSource($sourceInfo);
                   if($addret){
                       $sourceInfo=Source_Api::getSourceByName($article['source']); 
                   }
                }
                
                $topic['from'] = $sourceInfo['id'];
                   
                $topic['title'] = $article['title'];
                $topic['subtitle'] = $article['subtitle'];
                $topic['url'] = $article['url'];
                $topic['status'] = Topic_Type_Status::NOTPUBLISHED;
                $spider = Spider_Factory::getInstance("Filterimg",$article['content'],Spider_Type_Source::STRING);
                $topic['content'] =  trim($spider->getReplacedContent());
                $topic['from_detail'] = $article['author'].','.$article['title'].','.$article['source'].$article['issue'];
                //特殊处理景点和通用标签
                if ($val['label_type']==Recommend_Type_Label::SIGHT) {
                    $topic['sights'] = array($val['label_id']);
                }elseif ($val['label_type']==Recommend_Type_Label::GENERAL) {
                    $topic['tags'] = array($val['label_id']);
                }
                $bRet=Topic_Api::addTopic($topic);   
             }
        }
        return $ret;
    }
    
    public function getArticleDetail($id){
        $objArticle = new Recommend_Object_Article();
        $objArticle->fetch(array('id' => $id));
        return $objArticle->toArray();
    }
}