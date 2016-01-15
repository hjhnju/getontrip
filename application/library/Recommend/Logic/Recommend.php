<?php
class Recommend_Logic_Recommend extends Base_Logic{
    
    const LIMIT_RATE = 0.3;
    
    /**
     * 根据条件获取推荐的文章列表
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrInfo
     */
    public function listArticles($page, $pageSize, $type, $arrInfo = array()){
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
        if(isset($arrInfo['classtag'])){
            $strFilter = "`label_type` =".Recommend_Type_Label::TAG." and label_id=".$arrInfo['classtag'];
            unset($arrInfo['classtag']);
            $bSpecial = true;
        }
        if(empty($strFilter)){
            $strFilter = "1";
        }
        foreach ($arrInfo as $key => $val){            
            $strFilter .= " and `".$key."` =".$val;
        }
        if($type == Recommend_Type_Label::TAG){
            $strFilter .= " and `label_type` = ".$type;
        }else{
            $strFilter .= " and `label_type` != ".Recommend_Type_Label::TAG." and `rate` > ".self::LIMIT_RATE;
        }
        
        $listArticle->setGroup("obj_id");
        $listArticle->setFilterString($strFilter);
        $listArticle->setPage($page);
        $listArticle->setPagesize($pageSize);
        if($bSpecial){
            $listArticle->setOrder("rate desc");
            return $listArticle->toArray();   
        }
        $listArticle->setFields(array('id','obj_id'));
        $arrRet =  $listArticle->toArray();
        foreach($arrRet['list'] as $key => $val){
            $listArticle = new Recommend_List_Result();
            if($type == Recommend_Type_Label::TAG){
                $listArticle->setFilterString('obj_id = '.$val['obj_id']." and `label_type` = ".$type);
            }else{
                $listArticle->setFilterString('obj_id = '.$val['obj_id']." and `label_type` != ".Recommend_Type_Label::TAG." and `rate` > ".self::LIMIT_RATE);
            }
            $listArticle->setFields(array('label_id','label_type','rate','reason','status','create_time','update_time'));
            $listArticle->setPagesize(PHP_INT_MAX);            
            $listArticle->setOrder("`rate` desc");
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
        $arrSightIds = array();
        $arrTags     = array();
        foreach ($arrInfo as $val){
            $objRecommendRet = new Recommend_Object_Result();
            $objRecommendRet->fetch(array('obj_id' => $id,'label_id' => $val['label_id'],'label_type' => $val['label_type']));
            $objRecommendRet->status = $val['status'];
            $ret = $objRecommendRet->save();
            
            //确认后需要添加话题
            if($val['status'] == Recommend_Type_Status::ACCEPT){
                if($val['label_type'] == Recommend_Type_Label::SIGHT){
                    $arrSightIds[] = $val['label_id'];
                }else{
                    $arrTags[]     = $val['label_id'];
                } 
            }
        }
        $article  = $this->getArticleDetail($id);
        if(empty($arrTags) && !empty($article['tagid'])){
            $arrTags[] = $article['tagid'];
        };
        $arrFrom = array();
        if(isset($article['author']) && !empty($article['author'])){
            $arrFrom[] = $article['author'];
        }
        if(isset($article['title']) && !empty($article['title'])){
            $arrFrom[] = $article['title'];
        }
        if(isset($article['source']) && !empty($article['source'])){
            $arrFrom[] = $article['source'];
        }
        $bTest = true;
        foreach ($arrTags as $tag){
            $objTag = Tag_Api::getTagInfo($tag);
            if($tag['type'] == Tag_Type_Tag::GENERAL){
                $bTest = false;
                break;
            }
        }
        if(empty($arrSightIds) && $bTest){
            return false;
        }
        $arrInfo  = array(
             'title'       => isset($article['title'])?trim($article['title']):'',
             'subtitle'    => isset($article['subtitle'])?trim($article['subtitle']):'',
             'tags'        => $arrTags,
             'sights'      => $arrSightIds,
             'content'     => $article['content'],
             'url'         => isset($article['url'])?$article['url']:'',
             'from_detail' => implode(",",$arrFrom).$article['issue'], 
         );
        $ret = Topic_Api::addTopic($arrInfo);
        return $ret;
    }
    
    public function getArticleDetail($id){
        $objArticle = new Recommend_Object_Article();
        $objArticle->fetch(array('id' => $id));
        $ret =  $objArticle->toArray();
        
        $objArticleTag = new Recommend_Object_Result();
        $objArticleTag->fetch(array('obj_id' => $id,'label_type' => Recommend_Type_Label::TAG));
        $ret['tagid']    = empty($objArticleTag->labelId)?'':$objArticleTag->labelId;
        $ret['tagname']  = '';
        if(!empty($ret['tagid'])){
            $tag = Tag_Api::getTagInfo($ret['tagid']);
            $ret['tagname'] = isset($tag['name'])?trim($tag['name']):'';
        }
        return $ret;
    }
}