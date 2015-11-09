<?php
/**
 * 话题相关操作
 * author :fyy
 */
class  TopicapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
     
    /**
     * 话题list
     * @return [type] [description]
     */
    public function listAction(){  

        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        //$query =isset($_REQUEST['params']['content'])?$_REQUEST['params']['content']:'';
        
        
        $List = Topic_Api::search($arrParam,$page,$pageSize);
        $sightArray=array();
        
        //处理数据 
        $tmpList = $List['list'];
        for($i=0; $i<count($tmpList); $i++) {  
            $tmpList[$i]["statusName"] = Topic_Type_Status::getTypeName($tmpList[$i]["status"]);
            $topic = Topic_Api::getTopicById($tmpList[$i]['id']);
            $tmpList[$i]["symbols"]    = Base_Util_String::checkEnglishSymbol($topic['content']);
            $tmpList[$i]["subtitle"] =isset($topic["subtitle"])?$topic["subtitle"]:'';
            $tmpList[$i]["isContent"] =empty($topic["content"])?false:true;


            //处理景点名称
            $sightlist = $tmpList[$i]['sights'];
            for($j=0; $j<count($sightlist); $j++){
                $item = $sightlist[$j];
                $sight_id = $item['sight_id'];
                if (!array_key_exists($sight_id,$sightArray)) {
                    //根据ID查找景点名称
                    $sightInfo =(array) Sight_Api::getSightById($sight_id);
                     
                    $item['sight_name'] = isset($sightInfo['name'])?$sightInfo['name']:'';
                    //添加到数组
                    $sightArray[$sight_id]=isset($sightInfo['name'])?$sightInfo['name']:'';
                }
                else{
                    $item['sight_name']  = $sightArray[$sight_id];
                }
                $sightlist[$j] = $item;
            }
            $tmpList[$i]['sights'] = $sightlist;
            
            //处理来源名称
            if(empty($tmpList[$i]["from_detail"])) {
                $fromInfo = Source_Api::getSourceInfo($tmpList[$i]["from"]);
                $tmpList[$i]["fromName"] = isset($fromInfo['name'])?$fromInfo['name']:'';
            }else{
                $tmpList[$i]["fromName"] =$tmpList[$i]["from_detail"];
            }
            
            //处理图片名称 分割为 img_hash 和 img_type
            if(!empty($tmpList[$i]["image"])){
                $img=Base_Image::getImgParams($tmpList[$i]["image"]);
                $tmpList[$i]["img_hash"] = $img['img_hash'];
                $tmpList[$i]["img_type"] = $img['img_type'];
            }

            //若存在标签处理相关标签
            if (isset($tmpList[$i]['tags'])) {
                $tag_id_array = $tmpList[$i]['tags'];  
                $normalTag = array();
                $generalTag = array();
                $classifyTag = array(); 
                for ($j=0; $j < count($tag_id_array); $j++) { 
                    $tag = $tag_id_array[$j];
                    switch ($tag['type']) {
                      case Tag_Type_Tag::NORMAL:
                        array_push($normalTag,$tag); 
                        break; 
                      case Tag_Type_Tag::GENERAL:
                        array_push($generalTag,$tag); 
                        break;
                      case Tag_Type_Tag::CLASSIFY:
                        array_push($classifyTag,$tag); 
                        break;
                    } 
                }
                $tmpList[$i]['tagList']['normalTag'] = $normalTag; 
                $tmpList[$i]['tagList']['generalTag'] = $generalTag; 
                $tmpList[$i]['tagList']['classifyTag'] = $classifyTag; 
            }
            
         }
       
        $List['list']=$tmpList;
        
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];  
		    return $this->ajax($retList);
         
    }

    /**
     * 编辑话题
     * @return [type] [description]
     */
    public function saveAction()
    {   
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       $_REQUEST['sights'] = isset($_REQUEST['sights'])? $_REQUEST['sights'] : array(); 
       if($postid <= 0){
            $this->ajaxError();
       }
       //1.批量上传图片 //2.修改content
       $content = $_REQUEST['content'];  
       if($content != ""){
          $spider = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
          $_REQUEST['content'] = trim($spider->getReplacedContent());
       }
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);
       $bRet=Topic_Api::editTopic($postid,$_REQUEST);
       if($bRet){  
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }
    
   /**
    * 添加话题
    */
    public function addAction(){  
       //1.批量上传图片 //2.修改content
       $content = $_REQUEST['content'];  
       if($content != ""){
          $spider = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
          $_REQUEST['content'] = trim($spider->getReplacedContent());
       }
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);
       //添加到数据库
       $bRet=Topic_Api::addTopic($_REQUEST);   
       if(!empty($bRet)){
            return $this->ajax($bRet);
       } 
       return $this->ajaxError();
    }

   /**
   * 过滤器 添加话题
   */
    public function addByFilterAction(){
        
       //1、调用相应的采集器
       $spiderType = $this->getSpider($_REQUEST['url']);
       $spider = Spider_Factory::getInstance($spiderType,$_REQUEST['url'],Spider_Type_Source::URL);

       //2、获取title 解析title 去掉-后面的内容
       $title = $spider->getTitle(); 
       $_REQUEST['title'] = $title;
         
       
       //3、获取content
       $content = $spider->getBody();
      
     /*  //4、content中剔除多余的图片属性  
       $content = preg_replace('/data-(.)*\"/', '', $content);
       $content = preg_replace('/style=(.)*\"/', '', $content);*/
      
       //5、批量上传图片，得到最终的content 
       if($content != ""){
          $spider = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
          $content = $spider->getReplacedContent($_REQUEST['url']);  
       } 
       $_REQUEST['content'] =trim($content); 
       $_REQUEST['status'] = $this->getStatusByActionStr('NOTPUBLISHED');

       //保存到数据库
       $bRet=Topic_Api::addTopic($_REQUEST);
       ob_end_clean();   
       if(!empty($bRet)){
            return $this->ajax($bRet);
       } 
       return $this->ajaxError();
    }


    /**
    * 删除话题
    */
    public function delAction(){
        //判断是否有ID
        $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
        //根据ID查询出话题信息 
        $topicList = Topic_Api::search(array('id'=>$postid),1,1);
        if (count($topicList['list'])==0) {
            return $this->ajaxError();
        }
        $topicInfo=$topicList['list'][0];
        //正则提取出hash
        $content=$topicInfo['content'];
        if(!empty($content)&&$content!=''){
           $imgNameArray=Base_Image::getimgNameArray($content);  
           $oss = Oss_Adapter::getInstance();  
           //循环删除图片
           foreach ($imgNameArray as $key => $filename) { 
              $res = $oss->remove($filename);
           }   
        }
         
        $bRet =Topic_Api::delTopic($postid);
        if($bRet){
            return $this->ajax($postid);
        }
        return $this->ajaxError();
    }
     
    /*
      修改话题状态
     */
    public function changeStatusAction(){
       $idArray = isset($_REQUEST['idArray'])? $_REQUEST['idArray'] : array(); 
 
       $status = $this->getStatusByActionStr($_REQUEST['action']);
       for ($i=0; $i < count($idArray); $i++) { 
           $postid = intval($idArray[$i]);
           $bRet=Topic_Api::editTopic($postid,array('status'=>$status,'id'=>$postid));
          
           if(!$bRet){  
              return $this->ajaxError('501',$postid.'话题修改状态失败');
           }
       }
       return $this->ajax();
      /* $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       }  
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);
       $bRet=Topic_Api::editTopic($postid,$_REQUEST);
       if($bRet){ 
            return $this->ajax();
       }
       return $this->ajaxError(); */
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
            $bRet=Topic_Api::editTopic($postid,$params);
            if($bRet){
               return $this->ajax($ret); 
            } 
            return $this->ajaxError('400','修改话题的图片hash错误');  
          }
          return $this->ajax($ret); 
        }
        return $this->ajaxError('401','裁剪图片错误');  
    }
 

     /**
     * 选择数据采集器
     * @param  [string] $url [原文链接]
     * @return [string]      [description]
     */
    public function getSpider($url){ 
        $url=parse_url($url);
        $host=strtolower($url['host']);
        switch ($host) {
            case 'www.zhihu.com':
              return 'Zhihu';
              break;
            case 'blog.sina.com.cn':
              return 'SinaBlog';
              break;
            case 'bbs.dili360.com':
              return 'Dili360bbs';
              break;
            case 'thepaper.cn':
              return 'Thepaper';
              break; 
            default:
              return 'Auto';
              break;
        }
    }

    /**
     * 获取保存的状态
     * @param  [type] $action [description]
     * @return [type]         [description]
    */
    public function getStatusByActionStr($action){
        switch ($action) {
         case 'NOTPUBLISHED':
           $status = Topic_Type_Status::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = Topic_Type_Status::PUBLISHED;
           break;
         default:
           $status = Topic_Type_Status::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }

   
}