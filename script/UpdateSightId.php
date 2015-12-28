<?php
/*更新sight_meta表里面的id 字段   对应sight表里面的id*/
 
require_once("env.inc.php"); 
$save_path = '/home/work/user/fanyy/getontrip/mytestlog/';  
$filename = $save_path . "sightid_log.txt"; 

$myfile = fopen($filename, "w");
$content = "日志开始:[" . date('y-m-d h:i:s',time()) . "]\n";
fwrite($myfile, $content);
echo $content;

$ret = array('totalCount'=>0,'errorCount'=>0);

$ret = setSightID($myfile);
 
$content = '日志结束[' . date('y-m-d h:i:s',time()) .']';
fwrite($myfile, $content);
echo $content; 
$content = '总数：'.$ret['totalCount'].',错误数：'.$ret['errorCount'];
fwrite($myfile, $content);
echo $content; 
fclose($myfile);


function setSightID($myfile){

   $errorCount = 0;
   $totalCount = 0;
   $arrayNo = array();
   $arrInfo = array();
   $SightList =Sight_Api::querySights(array(),1, PHP_INT_MAX);
   
   $SightList = $SightList['list']; 
   foreach ($SightList as $key => $item) {

       $arrInfo = deailName($item);
       
       $List = Sight_Api::searchMeta($arrInfo,1,1);
      
       
       if ($List['total']==0) {
           array_push($arrayNo, $item['name']);
           $content = '没有找到'.$item['id'].','.$item['name'].','.$item['city_id']."\n";
           fwrite($myfile, $content);
           echo $content; 
       }else{
          $totalCount ++;
          $sight_meta = $List['list'][0]; 
          //更新sight_meta表里面的id值
          //需要更新的id值
          $content = 'id:'.$sight_meta['id'].'=>'.$item['id']."\n";
          fwrite($myfile, $content);
          echo $content;
          var_dump($sight_meta);  
          var_dump($item);
          $ret = Sight_Api::editMeta($sight_meta['id'],array('id'=>$item['id']));
          if (!$ret) { 
            $content = '失败id:'.$sight_meta['id'].'=>'.$item['id']
            fwrite($myfile, $content);
            echo $content;
          } 
          return array('totalCount'=>$totalCount,'errorCount'=>$errorCount);
          
       }
       
   }
   
   return array('totalCount'=>$totalCount,'errorCount'=>$errorCount);
    
}

public  deailName($item){ 
   
    $arrInfo = array(
         'name'=>$item['name'],
         'city_id'=>$item['city_id']
    );
    if ($item['name']=='丽江古城') {
        $arrInfo['name']='丽江';
    }elseif ($item['name']=='扬州何园') {
        $arrInfo['name']='何园';
    }elseif ($item['name']=='天坛公园') {
        $arrInfo['name']='天坛';
    }elseif ($item['name']=='孔庙/国子监') {
        $arrInfo['name']='国子监';
    }elseif ($item['name']=='香山公园') {
        $arrInfo['name']='香山';
    }elseif ($item['name']=='束河古镇') {
        $arrInfo['name']='束河';
    }elseif ($item['name']=='奥林匹克公园') {
        $arrInfo['name']='北京奥林匹克公园';
    }elseif ($item['name']=='巴拉格宗香格里拉大峡谷') {
        $arrInfo['name']='香格里拉大峡谷';
    }elseif ($item['name']=='普达措国家公园') {
        $arrInfo['name']='普达措';
    }elseif ($item['name']=='阆中古城') {
        $arrInfo['name']='阆中';
    }elseif ($item['name']=='绍兴鲁迅故里') {
        $arrInfo['name']='鲁迅故里';
    }elseif ($item['name']=='扬州个园') {
        $arrInfo['name']='个园';
    }elseif ($item['name']=='扬州大明寺') {
        $arrInfo['name']='大明寺';
    }elseif ($item['name']=='广元剑门蜀道') {
        $arrInfo['name']='剑门蜀道';
    }elseif ($item['name']=='成都武侯祠') {
        $arrInfo['name']='武侯祠';
    }elseif ($item['name']=='成都杜甫草堂') {
        $arrInfo['name']='杜甫草堂';
    }elseif ($item['name']=='南浔古镇') {
        $arrInfo['name']='南浔';
    }elseif ($item['name']=='津门故里') {
        $arrInfo['name']='古文化街';
    }elseif ($item['name']=='西递宏村') {
        $arrInfo['name']='西递古村';
    }elseif ($item['name']=='沙溪古镇') {
        $arrInfo['name']='沙溪';
    }elseif ($item['name']=='东方明珠电视塔') {
        $arrInfo['name']='东方明珠';
    }elseif ($item['name']=='南山大小洞天') {
        $arrInfo['name']='大小洞天';
    }elseif ($item['name']=='西溪') {
        $arrInfo['name']='西溪湿地';
    }elseif ($item['name']=='横店影视城') {
        $arrInfo['name']='中国横店影视城';
    }elseif ($item['name']=='呀诺达雨林') {
        $arrInfo['name']='呀诺达';
        unset($arrInfo['city_id']);
    }elseif ($item['name']=='分界洲岛') { 
        unset($arrInfo['city_id']);
    }elseif ($item['name']=='吴中太湖') {
        $arrInfo['name']='太湖';
        unset($arrInfo['city_id']);
    }
    elseif ($item['name']=='卢浮宫'||$item['name']=='埃菲尔铁塔'||$item['name']=='东京塔'||$item['name']=='东京迪士尼乐园'||$item['name']=='秋叶原'||$item['name']=='清水寺'||$item['name']=='浅草寺') { 
       unset($arrInfo['city_id']);
    }
      
    return $arrInfo;
     
} 


//放到test.php去运行
function indexAction(){  
       $arrInfo = array();
       $SightList = Sight_Api::querySights(array(),1, PHP_INT_MAX);
       $SightList = $SightList['list']; 
       $arrayYes = array();
       $arrayNo = array();
        
       foreach ($SightList as $key => $item) {
         $arrInfo = $this->deailName($item);
         //return $this->ajax($arrInfo);

         $List = Sight_Api::searchMeta($arrInfo,1,1);
 
          if ($List['total']==0) {
             array_push($arrayNo, $item['name']);
         }else{
            $sight_meta = $List['list'][0]; 
            //更新sight_meta表里面的id值
            //需要更新的id值
            array_push($arrayYes, $item['name']); 
            
            $ret = Sight_Api::editMeta($sight_meta['id'],array('id'=>$item['id']));
              
            if ($sight_meta['is_china']=='0') {
               $ret = Sight_Api::editSight($item['id'],array('city_id'=>$sight_meta['city_id']));
                
            }
            //删除旧的id
            $logicSight = new Sight_Logic_Meta();
            $delRet = $logicSight->delMeta($sight_meta['id']);
             
         }
       }
       return  $this->ajax(array('yes'=>$arrayYes,'no'=>$arrayNo));  
    }