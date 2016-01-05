<?php
/**
 * 文章推荐功能
 * fyy
 */
class RecommendapiController extends Base_Controller_Api{
     
    public function init() {
        parent::init();
    }

  

     /**
     * 文章元数据列表
     * @return [type] [description]
     */
    public function articlelistAction () {

        //第一条数据的起始位置，比如0代表第一条数据 
        $start =isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page = ($start/$pageSize)+1;
        $arrInfo = isset($_REQUEST['params'])?$_REQUEST['params']:array(); 

         
        $List = Recommend_Api::listArticles($page, $pageSize,$arrInfo);
        $tempList = $List['list'];
        for ($i=0; $i < $List['total']; $i++) { 
            $item = $tempList[$i]; 
            //处理文章标题
            $article = Recommend_Api::getArticleDetail($item['obj_id']);
            $item['title']='aa';
            $tempList[$i] = $item; 
        }  
        $List['list'] = $tempList;

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
        return $this->ajax($retList);
    }


    
    /**
     * 文章推荐列表
     * @return [type] [description]
     */
    public function articlerecommendlistAction () {

        //第一条数据的起始位置，比如0代表第一条数据 
        $start =isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:10; 
        $page = ($start/$pageSize)+1;
        $arrInfo = isset($_REQUEST['params'])?$_REQUEST['params']:array(); 

         
        $List = Recommend_Api::listArticles($page, $pageSize,$arrInfo);
        $tempList = $List['list'];
        for ($i=0; $i < count($tempList); $i++) { 
            $item = $tempList[$i];
            //处理推荐结果
            $group = isset($item['group'])?$item['group']:array($item); 
            for ($j=0; $j < count($group); $j++) { 
                $groupItem = $group[$j];
                $labelType = $groupItem['label_type'];
                switch ($labelType) {
                    case Recommend_Type_Label::SIGHT:
                        $obj = Sight_Api::getSightById($groupItem['label_id']);
                        if (!empty($obj)) {
                            $groupItem['name'] = $obj['name'];
                            $citye = City_Api::getCityById($obj['city_id']);
                            $groupItem['cityname'] = $obj['name'];
                        }
                        break;
                    case Recommend_Type_Label::GENERAL:
                        $obj = Tag_Api::getTagById($groupItem['label_id']);
                        if (!empty($obj)) {
                            $groupItem['name'] = $obj['name'];
                        }
                        break;
                    default:
                        # code...
                        break;
                }
                
                $groupItem['status_name'] = Recommend_Type_Status::getTypeName($groupItem['status']);
                $group[$j] = $groupItem;
            }
            $item['group'] = $group;

            //处理文章标题
            $article = Recommend_Api::getArticleDetail($item['obj_id']);
            if (empty($article)) {
                continue;
            }
            $item['title'] = $article['title'];
            $item['subtitle'] = '';  
            $item['tagid']    = $article['tagid'];
            $item['tagname']    = $article['tagname'];
            $item['source'] = $article['source'];
            $item['issue'] = $article['issue'];  
            $item['subcontent'] = Base_Util_String::getSubString($article['content'],150);
             
            $tempList[$i] = $item; 
        }  
        $List['list'] = $tempList;

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
        return $this->ajax($retList);
    }

    /**
     * 处理推荐文章
     */
    public function dealArticleAction()
    {
        $id =isset($_REQUEST['id'])?intval($_REQUEST['id']):0; 
        $arrInfo = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        if (empty($arrInfo)) {
            return $this->ajaxError(-1,'请选择一个状态');
        }
        for ($i=0; $i < count($arrInfo); $i++) { 
            $arrInfo[$i]['status'] = $this->getStatusByActionStr($arrInfo[$i]['action']);
            if ($arrInfo[$i]['status']==-1) {
               array_splice($arrInfo, $i, 1); 
            }
        }
 
        $ret = Recommend_Api::dealArticle($id, $arrInfo);

        if (!$ret) {
            return $this->ajaxError($ret);
        }
        return $this->ajax($ret);
    }


        /**
     * 获取保存的状态
     * @param  [type] $action [description]
     * @return [type]         [description]
    */
    public function getStatusByActionStr($action){
        switch ($action) {
         case 'NOT_DEAL':
           $status = Recommend_Type_Status::NOT_DEAL;
           break;
         case 'ACCEPT':
           $status = Recommend_Type_Status::ACCEPT;
           break;
         case 'REJECT':
           $status = Recommend_Type_Status::REJECT;
           break;
         default:
           $status = -1;
           break;
       } 
       return   $status;
    }
}