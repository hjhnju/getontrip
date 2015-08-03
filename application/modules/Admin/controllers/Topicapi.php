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
     * 标签list
     *  
     */    
    public function listAction(){  

        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
        //$query =isset($_REQUEST['params']['content'])?$_REQUEST['params']['content']:'';
        
         
        
        $List = Topic_Api::search($arrParam,$page,$pageSize);

        //处理状态值 
        $tmpList = $List['list'];
        for($i=0; $i<count($tmpList); $i++) { 
            $tmpList[$i]["statusName"] = Topic_Type_Status::getTypeName($tmpList[$i]["status"]);  
         }

        //处理景点名称
        $sightArray=array(); 
        for($i=0; $i<count($tmpList); $i++){
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
        } 

        //处理来源名称
        $fromArray=array(); 
        for($i=0; $i<count($tmpList); $i++) { 
            $fromInfo = Source_Api::getSourceInfo($tmpList[$i]["from"]);
            $tmpList[$i]["fromName"] = isset($fromInfo['name'])?$fromInfo['name']:'';  
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
       if($postid <= 0){
            $this->ajaxError();
       }
       //1.批量上传图片 //2.修改content
       $content = $_REQUEST['content'];  
       if($content != ""){
          $obj = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
          $_REQUEST['content'] = $obj->getReplacedContent();
       }

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
          $obj = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
          $_REQUEST['content'] = $obj->getReplacedContent();
       }
      
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
       $obj = Spider_Factory::getInstance("Auto",$_REQUEST['url'],Spider_Type_Source::URL);
       //2、获取title 解析title 去掉-后面的内容
       $_REQUEST['title'] = $obj->getTitle();
       $_REQUEST['title'] = trim(preg_replace('/-(.)*/i','',$_REQUEST['title'])); 
       //3、获取content
       $content = $obj->getBody();
       //4、content中剔除多余的图片属性
       $content = preg_replace('/data-(.)*\"/', '', $content);
       $content = preg_replace('/style=(.)*\"/', '', $content);
       //5、批量上传图片，得到最终的content 
       if($content != ""){
          $obj = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
          $content = $obj->getReplacedContent();  
       } 
       $_REQUEST['content'] =$content; 
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
           $imgHashArray=$this->getimgHashArray($content);  
           $oss = Oss_Adapter::getInstance();  
           //循环删除图片
           foreach ($imgHashArray as $key => $value) {
              $filename = $value.'.jpg';   
              $res = $oss->remove($filename);
           }   
        }
        

        $bRet =Topic_Api::delTopic($postid);
        if($bRet){
            return $this->ajax($postid);
        }
        return $this->ajaxError();
    }
     
   public function getimgHashArray($content){ 
       $pat='/data-hash=\"(.){16,16}\"/';
       //将匹配成功的参数写入数组中 
       preg_match_all($pat, $content, $matches); 
       preg_match_all('/data-hash=\"?(.){16,16}\"?/i',$content,$matches);
       for($i=0;$i<count($matches[0]);$i++) {  
            $matches[0][$i]=preg_replace('/data-hash=\"/i','',$matches[0][$i]);
            $matches[0][$i]=preg_replace('/\"/i','',$matches[0][$i]); 
          } 
      return $matches[0];
   }





   
}