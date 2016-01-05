<?php
require_once("env.inc.php");
ini_set('memory_limit','512M');
$basePath = "/home/work/publish/data/";
$path     = $basePath."mp3/";
$arrData  = file($basePath."jingqubao.txt");
$fp       = fopen($basePath."unsolved.txt","a+");
$logic    = new Base_Logic();

$arrSight = array();
$arrIds   = array();
$arrFilter = array('黎苗文化旅游区','文化旅游区','旅游度假区','风景区','生态景区','景区','旅游区','风景名胜区','公园','民宅','欢乐谷');
$arrCityNames = array();
$sight    = '';
$audio    = '';
$sight    = '';

$listCityMeta = new City_List_Meta();
$listCityMeta->setPagesize(PHP_INT_MAX);
$arrCityMeta  = $listCityMeta->toArray();
foreach ($arrCityMeta['list'] as $val){
    if(!in_array($val['name'],$arrCityNames)){
        $arrCityNames[] = $val['name'];
    }
    $name = str_replace("市", "", $val['name']);
    $name = str_replace("县", "", $name);
    if(!in_array($name,$arrCityNames) && strlen($name)!==3){
        $arrCityNames[] = $name;
    }
    $objCity = new City_Object_City();
    $objCity->fetch(array('id' => $val['id']));
    if(!in_array($objCity->name,$arrCityNames)){
        $arrCityNames[] = $objCity->name;
    }
    $name = str_replace("市", "", $objCity->name);
    $name = str_replace("县", "", $name);
    if(!in_array($name,$arrCityNames) && strlen($name)!==3){
        $arrCityNames[] = $name;
    }
}

foreach ($arrData as $val){
    sscanf($val,"%s\t%s\t%s",$keyword,$audio,$sight);
    $oldSight = $sight;
    foreach ($arrFilter as $filter){
        $sight = str_replace($filter, "", $sight);
    }
    foreach ($arrCityNames as $filter){
        $sight = str_replace($filter, "", $sight);
    }
    $listSightMeta = new Sight_List_Meta();
    $listSightMeta->setFilterString("name like '%".$sight."%'");
    $count1 = $listSightMeta->getTotal();
    
    $listSight = new Sight_List_Sight();
    $listSight->setFilterString("name like '%".$sight."%'");
    $count2 = $listSight->getTotal(); 
    
    if(empty($count1) && empty($count2)){
        //在我们库中不能自动分配的景点名称
        if(!in_array($oldSight,$arrSight)){
            $arrSight[] = $oldSight;
        }
    }else{
        if(!empty($count1)){
            $arrTmpSightMeta = $listSightMeta->toArray();
            $id              = $arrTmpSightMeta['list'][0]['id'];
        }else{
            $arrTmpSight     = $listSight->toArray();
            $id              = $arrTmpSight['list'][0]['id'];
        }
        //获得景点ID后,上传景观音频并保存
        $audioUrl   = $logic->upAudioData($path.$audio);
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('name' => $keyword));
        if(!empty($objKeyword->id)){
            $objKeyword->audio = $audioUrl;
            $objKeyword->save();
        }else{
            $logicKeyword = new Keyword_Logic_Keyword();
            $objKeyword->sightId = $id;
            $objKeyword->name    = $keyword;
            $objKeyword->url     = 'http://baike.baidu.com/item/'.$keyword;
            $objKeyword->audio   = $audioUrl;
            $objKeyword->weight  = $logicKeyword->getKeywordWeight($id);
            $objKeyword->save();
            $logicKeyword->getKeywordSource($objKeyword->id,Keyword_Type_Status::NOTPUBLISHED);
        }
    }
}

foreach ($arrSight as $sight){
    fwrite($fp, $sight."\r\n");
}
fclose($fp);
