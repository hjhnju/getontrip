<?php
/**
 * 书籍管理相关操作
 * author:fanyy
 */
class BookapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
    public function listAction(){
         //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page=($start/$pageSize)+1;
         
        $arrInfo = isset($_REQUEST['params'])?$_REQUEST['params']: array(); 
        
        $List = Book_Api::getBooks($page,$pageSize,$arrInfo);;
        
        foreach ($List['list'] as $key => $val){
            $List['list'][$key]['statusName'] = Book_Type_Status::getTypeName($val["status"]); 
        }
        $total = $List['total'];
        $retList['recordsTotal'] = $total;
        $retList['recordsFiltered'] =$total;
        
        //$List =  $tmpList;

        
       
        $retList['data'] =$List['list']; 
        return $this->ajax($retList);
    }

    /**
     * 添加词条
     */
    function addAction(){ 

        //根据skuid或isbn从京东或豆瓣抓取书籍数据
        $bookInfo = Book_Api::getBookSourceFromIsbn($_REQUEST['strIsbn'], $_REQUEST['type']);
        ob_clean(); 

        //判断是否已经存在
        $List = Book_Api::getBooks(0,PHP_INT_MAX,array('isbn'=>$bookInfo['isbn']));
        if (intval($List['total'])>0) { 
            //删除图片
            $oss      = Oss_Adapter::getInstance();
            $filename = $bookInfo['image'];
            $res      = $oss->remove($filename);
             
            return $this->ajaxError('410','该本图书已经存在！'.$filename);
        }
         
        $dbRet=Book_Api::addBook($bookInfo);
         
        if(!empty($bRet)){
            return $this->ajax($bRet);
        }
        return $this->ajaxError();
    }

     /**
     * 编辑保存书籍
     */
    function saveAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
         
        if($id==''){
            return $this->ajaxError();
        }
        $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
        
        //修改 content_desc
        $content_desc = $_REQUEST['content_desc'];  
        if($content_desc != ""){
           $spider = Spider_Factory::getInstance("Filterimg",$content_desc,Spider_Type_Source::STRING);
           $_REQUEST['content_desc'] = trim($spider->getReplacedContent()); 
        }

        //修改  catalog
        $catalog = $_REQUEST['catalog'];  
        if($catalog != ""){
           $spider = Spider_Factory::getInstance("Filterimg",$catalog,Spider_Type_Source::STRING);
           $_REQUEST['catalog'] = trim($spider->getReplacedContent()); 
        } 

        $dbRet=Book_Api::editBook($id,$_REQUEST);

        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

     /**
     * 删除词条
     */
    function delAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            return $this->ajaxError();
        }
        $dbRet=Keyword_Api::delKeyword($id);
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }


    /**
     * 裁剪话题背景图片
     * @return [type] [description]
     */
    public function cropPicAction(){
        $postid=isset($_REQUEST['id'])?intval($_REQUEST['id']):''; 
        $oldhash=$_REQUEST['image'];
        $x=$_REQUEST['x'];
        $y=$_REQUEST['y']; 
        $width=$_REQUEST['width'];
        $height=$_REQUEST['height']; 
        $ret=Base_Image::cropPic($oldhash,$x,$y,$width,$height); 
        if($ret){
          if(!empty($postid)){
            $params = array('image'=>$ret['image']);
            //修改话题的图片hash
            $bRet=Book_Api::editBook($postid,$params);
            if($bRet){
               return $this->ajax($ret); 
            } 
            return $this->ajaxError('400','修改话题的图片hash错误');  
          }
          return $this->ajaxError('402','postid错误'); 
        }
        return $this->ajaxError('401','裁剪图片错误');  
    }
  
    /**
     * 获取保存的状态
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    public function getStatusByActionStr($action){
        switch ($action) {
         case 'NOTPUBLISHED':
           $status = Book_Type_Status::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = Book_Type_Status::PUBLISHED;
           break;
        case 'BLACKLIST':
           $status = Book_Type_Status::BLACKLIST;
           break;
         default:
           $status = Book_Type_Status::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }
   
}