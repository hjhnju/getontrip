<?php
/**
 * 景点相关操作
 * author :fyy
 */
class  SightapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
     
    /**
     * list
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $arrInfo =isset($_REQUEST['params'])?$_REQUEST['params']:array();
        
        $List =Sight_Api::querySights($arrInfo,$page, $pageSize);
    
    
        
        
        $tmpList=$List['list'];

        //添加城市名称
        $cityArray=array(); 
        foreach($tmpList as $key=>$item){   
           $city_id=$item['city_id']; 
            if (!array_key_exists($city_id,$cityArray)) {  
                  //根据ID查找城市名称
                  $cityInfo = City_Api::getCityById($item['city_id']);
                  $tmpList[$key]['city_name'] = isset($cityInfo['name'])?$cityInfo['name']:''; 
                  //添加到数组
                  $cityArray[$city_id]=$tmpList[$key]['city_name'];  
            }
            else{
                 
                 $tmpList[$key]['city_name']  = $cityArray[$item['city_id']];
            }
        } 

          
        for($i=0; $i<count($tmpList); $i++) {
            $sightInfo = $tmpList[$i];
            $sight_id = intval($sightInfo['id']);
            
            //处理状态值 
            $sightInfo["statusName"] = Sight_Type_Status::getTypeName($sightInfo["status"]);  
            
            //处理相关标签
            $tag_id_array =$sightInfo['tags']; 
            $sightInfo['tags'] =$tag_id_array;
            $normalTag = array();
            $generalTag = array();
            $classifyTag = array(); 
            for ($j=0; $j < count($tag_id_array); $j++) { 
                $tag = Tag_Api::getTagInfo($tag_id_array[$j], $sight_id);
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
            $sightInfo['tagList']['normalTag'] = $normalTag; 
            $sightInfo['tagList']['generalTag'] = $generalTag; 
            $sightInfo['tagList']['classifyTag'] = $classifyTag; 


            //处理话题数量
           // $sightInfo['topic_num'] = Topic_Api::getTopicNum(array('sightId'=>$sight_id,'status'=>Topic_Type_Status::PUBLISHED));
            
            $tmpList[$i]=$sightInfo;
        }
        
        

        $List['list']=$tmpList;

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list']; 
		    return $this->ajax($retList);
         
    }

    /**
     * 编辑
     * @return [type] [description]
     */
    public function saveAction()
    {   
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       } 
       if (isset($_REQUEST['status'])) {
           $_REQUEST['status']=intval($_REQUEST['status']);
       }
       if (isset($_REQUEST['action'])) {
          $_REQUEST['status'] = $this->getStatusByActionStr(isset($_REQUEST['action'])?$_REQUEST['action']:'');
       
       }

       $bRet=Sight_Api::editSight($postid,$_REQUEST);   
       if($bRet){
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }
    
   /**
    * 添加景点信息
    */
    public function addAction(){ 
       $_REQUEST['status'] = $this->getStatusByActionStr($_REQUEST['action']);
       //先添加一条景点元数据
       $sightMeta = $_REQUEST;
       $sightMeta['status'] = Sight_Type_Meta::CONFIRMED; 
       $sightMeta['city_id'] = intval($_REQUEST['city_id']);
       $sightMeta['sight_id'] = -1; 

       //根据城市id查询城市信息
       $cityInfo = City_Api::getCityById(intval($_REQUEST['city_id']));
       $sightMeta['continent'] = $cityInfo['continentname'];
       $sightMeta['country'] = $cityInfo['countryname'];
       $sightMeta['province'] = $cityInfo['pidname'];
       $sightMeta['city'] = $cityInfo['name'];
       $sightMeta['weight'] = 3000;

       //判断是否是国内外
       if ($cityInfo['countryid']==10001) {
          if ($cityInfo['pid']==10005||$cityInfo['id']==10003||$cityInfo['id']==10004) {
            $sightMeta['is_china'] = 2;
          }
          $sightMeta['is_china'] = 1;
       }else{
          $sightMeta['is_china'] = 0;
       }
        
       $metaId = Sight_Api::addSightMeta($sightMeta);


       $_REQUEST['id'] = $metaId;
       $bRet = Sight_Api::addSight($_REQUEST); 
        
       if($bRet){ 
            return $this->ajax();
       } 
       return $this->ajaxError();
    }



    public function delAction(){
        //判断是否有ID
        $id=isset($_REQUEST['id'])?$_REQUEST['id']:''; 
        $bRet =Sight_Api::delSight($id);
        if($bRet){
        }
        return $this->ajaxError();
            return $this->ajax();
    }
    

    /**
      * 获取景点信息，模糊查询，用于自动完成下拉框
      * @return [type] [description]
    */
    public function getSightListAction(){ 
        $str=isset($_REQUEST['query'])?$_REQUEST['query']:''; 
        //最大值 PHP_INT_MAX   
        $List = Sight_Api::querySightByPrefix($str,1,PHP_INT_MAX); 
      
        return $this->ajax($List);   
    }


    /**
     * 景点编辑情况汇总
     *  
     */    
    public function situationListAction(){   
        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;
       
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20;

        $page=($start/$pageSize)+1;
         
        $arrInfo =isset($_REQUEST['params'])?$_REQUEST['params']:array();
        if(isset($arrInfo['city_id'])){
            $arrInfo['city_id']=intval($arrInfo['city_id']);
        } 
        $List =Sight_Api::querySights($arrInfo,$page, $pageSize);
    
        if(count($List['list'])>0){
            
            for($i=0;$i<count($List['list']);$i++){  
                $sightInfo=$List['list'][$i]; 
                $sight_id = intval($sightInfo['id']);

                //所属城市
                $cityInfo = City_Api::getCityById($sightInfo['city_id']);
                $sightInfo['city_name'] = isset($cityInfo['name'])?$cityInfo['name']:''; 

                //相关话题个数 
                $sightInfo['topicCount']=Sight_Api::getTopicNum($sight_id);

                //相关词条
                $arrayParams=array(
                  'status'=>Keyword_Type_Status::ALL,
                  'sight_id'=>intval($sightInfo['id'])

                );
                $keywordList = Keyword_Api::queryKeywords(1,PHP_INT_MAX,$arrayParams); 
                $sightInfo['keywordlist']=$keywordList['list'];
                $sightInfo['keywordCount']=count($keywordList['list']);

                //相关标签
                $tag_id_array =$sightInfo['tags']; 
                $sightInfo['tags'] =$tag_id_array;
                $normalTag = array();
                $generalTag = array();
                $classifyTag = array(); 
                for ($j=0; $j < count($tag_id_array); $j++) { 
                    $tag = Tag_Api::getTagInfo($tag_id_array[$j], $sight_id);
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
                $sightInfo['tagList']['normalTag'] = $normalTag; 
                $sightInfo['tagList']['generalTag'] = $generalTag; 
                $sightInfo['tagList']['classifyTag'] = $classifyTag; 
                 
                $List['list'][$i]=$sightInfo;
            }
        }
          

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list']; 
        return $this->ajax($retList);
         
    }

    /*
      发布  
    */
    public function publishAction(){
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       }  
       $bDoPublish = $_REQUEST['action']=='PUBLISHED'?true:false;

       $bRet = Sight_Api::publishSight($postid,$bDoPublish);
       if($bRet){ 
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }

    /*
      取消发布 
    */
    public function cancelpublishAction(){
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if($postid <= 0){
            $this->ajaxError();
       }  
       $bRet=Sight_Api::editSight($postid,array('status'=>Sight_Type_Status::NOTPUBLISHED));
       if($bRet){ 
            return $this->ajax();
       }
       return $this->ajaxError(); 
    }

    /**
     * 检查景点名称是否可用
     * @return [type] [description]
     */
    public function checkSightNameAction(){
       $name = isset($_REQUEST['name'])? $_REQUEST['name'] : '';
       if(empty($name)){
          return $this->ajax();
       }
       $bRet=Sight_Api::checkSightName($name);
       if($bRet){ 
            return $this->ajaxError(); 
       }
        return $this->ajax();
    }


    /**
     * 裁剪背景图片
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
            //修改图片hash
            $bRet=Sight_Api::editSight($postid,$params);
            if($bRet){
               return $this->ajax($ret); 
            } 
            return $this->ajaxError('400','修改图片hash错误');  
          }
          return $this->ajax($ret); 
        }
        return $this->ajaxError('401','裁剪图片错误');  
    }
    
    /**
     * 获取景点元数据 列表
     */
    public function metaListAction(){  

        //第一条数据的起始位置，比如0代表第一条数据
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:0;  
        $pageSize=isset($_REQUEST['length'])?$_REQUEST['length']:20; 
        $page=($start/$pageSize)+1; 
        $arrInfo =isset($_REQUEST['params'])?$_REQUEST['params']:array(); 
        
       /* $arrInfo = array(
            
          );*/
        //return $this->ajax($arrInfo);

        $List = Sight_Api::searchMeta($arrInfo,$page,$pageSize);
         
        for ($i=0; $i < count($List['list']); $i++) { 
           $item = $List['list'][$i];
           //状态
           $item['status_name'] = Sight_Type_Meta::getTypeName($item['status']); 
           //是否是景观
           $item['is_landscape'] = $item['sight_id']==-1?0:1;
           $List['list'][$i] = $item;
        }

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list']; 
        return $this->ajax($retList);
    }

   /**
    * 添加景点信息
    */
    public function addToSightAction(){ 
       $metaId = isset($_REQUEST['id'])?$_REQUEST['id']:''; 
       $action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

       $status = $this->getMetaStatusByActionStr($action);
       if ($status==Sight_Type_Meta::NOTNEED||$status==Sight_Type_Meta::NEEDCONFIRM) {
          //只修改状态
          $ret=Sight_Api::editMeta($metaId,array('status'=>$status));   
          if($ret){
              return $this->ajax($ret);
          } 
          return $this->ajaxError();
       }
       
       $ret=Sight_Api::editMeta($metaId,array('status'=>$status));   
       if(!$ret){
          return $this->ajaxError();
       }
          

       //先判断该景点是否已经添加至景点库 
       $isExistById = Sight_Api::isExistById($metaId);
       if (!empty($isExistById)) {
         return $this->ajaxError(1200,'该景点已经添加到了景点库，无需再添加');
       }

     

       $metaObj = Sight_Api::getSightByMetaId($metaId);
       
       //处理城市，查询当前城市是否存在 
       $cityInfo = City_Api::getCityById($metaObj['city_id']);
       if (!isset($cityInfo['status'])) {
         # 城市尚未保存到city表里面
         $cityInfo['status'] = 1;
         $cityRet = City_Api::addCity($cityInfo); 
       }

       //上传图片
       $logic = new Base_Logic();
       $image = $logic->uploadPic($metaObj['image'],'http://hiphotos.baidu.com');
       $metaObj['image'] = $image;
       $metaObj['status'] = 1;

         

       $bRet=Sight_Api::addSight($metaObj);   
       if($bRet){ 
          return $this->ajax($bRet); 
       } 
       return $this->ajaxError();
    }
    
    /**
     * 修改景点元数据的状态
     * @return [type] [description]
     */
    public function changeMetaStatusAction()
    {
         $metaId = isset($_REQUEST['id'])?$_REQUEST['id']:'';
         $action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
    }


    /**
     * 获取保存的状态
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    public function getStatusByActionStr($action){
        switch ($action) {
         case 'NOTPUBLISHED':
           $status = Sight_Type_Status::NOTPUBLISHED;
           break;
         case 'PUBLISHED':
           $status = Sight_Type_Status::PUBLISHED;
           break;
         default:
           $status = Sight_Type_Status::NOTPUBLISHED;
           break;
       } 
       return   $status;
    }


    
    /**
     * 获取保存的状态
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    public function getMetaStatusByActionStr($action){
        switch ($action) {
         case 'NOTNEED':
           $status = Sight_Type_Meta::NOTNEED;
           break;
         case 'NEEDCONFIRM':
           $status = Sight_Type_Meta::NEEDCONFIRM;
           break;
         case 'CONFIRMED': 
           $status = Sight_Type_Meta::CONFIRMED;
           break;
       } 
       return   $status;
    }


}