<?php
/**
 * 更新视频、书籍、百科数据脚本,有三个参数:类型 景点ID 条数
 * 类型取值范围：Book Video Wiki All
 * 如果景点ID为空或-1,则取所有景点数据
 * 如果条数为空或-1,则取该景点下的所有数据
 */
require_once("../env.inc.php");
$arrTypes = array("Book","Video","Wiki","All");
if(count($argv) < 2){
    print "参数错误!Usage:Run.php 类型 景点ID 条数\r\n";
    return 0;
}
if(!in_array(trim($argv[1]),$arrTypes)){
    print "类型错误!只有三种类型：Book Video Wiki All\r\n";
    return 0;
}
$type      = isset($argv[1])?trim($argv[1]):'All';
$sightId   = isset($argv[2])?intval($argv[2]):-1;
$num       = isset($argv[3])?intval($argv[3]):-1;

$logic = new Base_Logic();

//删除视频
if($type == 'Video' || $type == 'All'){
     $listVideo = new Video_List_Video();
     $listVideo->setPagesize(PHP_INT_MAX);
     if($sightId != -1){
         $listVideo->setFilter(array('sight_id' => $sightId));
     }
     $arrVideo = $listVideo->toArray();
     foreach ($arrVideo['list'] as $val){
         if(!empty($val['image'])){
             $ret = $logic->delPic($val['image']);
         }
         $objVideo = new Video_Object_Video();
         $objVideo->fetch(array('id' => $val['id']));
         $objVideo->remove();
     }
}

//删除百科
if($type == 'Wiki' || $type == 'All'){
     $listkeyword = new Keyword_List_Keyword();
     $listkeyword->setPagesize(PHP_INT_MAX);
     if($sightId != -1){
         $listkeyword->setFilter(array('sight_id' => $sightId));
     }
     $arrKeyword = $listkeyword->toArray();
     foreach ($arrKeyword['list'] as $val){
         if(!empty($val['image'])){
             $ret = $logic->delPic($val['image']);
         }
         $objKeyword = new Keyword_Object_Keyword();
         $objKeyword->fetch(array('id' => $val['id']));
         $objKeyword->image   = '';
         $objKeyword->content = '';
         $objKeyword->save();
        
         $listKeywordCatalog = new Keyword_List_Catalog();
         $listKeywordCatalog->setFilter(array('keyword_id' => $val['id']));
         $listKeywordCatalog->setPagesize(PHP_INT_MAX);
         $arrKeywordCatalog = $listKeywordCatalog->toArray();
         foreach ($arrKeywordCatalog['list'] as $data){
             $objKeywordCatalog = new Keyword_Object_Catalog();
             $objKeywordCatalog->fetch(array('id' => $data['id']));
             $objKeywordCatalog->remove();
         }
     }
}

$conf    = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
$url  = $conf['web']['root']."/InitData?type=$type";
if(!empty($sightId) && ($sightId !== -1)){
    $url .= "&sightId=$sightId";
}
if(!empty($num) && ($num !== -1)){
    $url .= "&num=$num";
}
$http = Base_Network_Http::instance()->url($url)->exec();