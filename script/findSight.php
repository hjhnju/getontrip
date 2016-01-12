<?php
require_once("env.inc.php");
ini_set('memory_limit','512M');
$basePath     = "/home/work/publish/data/51data/";
$resultPath   = $basePath."findSight/";

$arrData      = file($basePath."scenics.txt");
//直接对应上的景观，后面要添加进景观（如果不存在的放在），然后补充音频字段
$fpConfirm    = fopen($resultPath."confirm.txt","w");
//不能对应上的景观，提供推荐信息
$fpUnsolved   = fopen($resultPath."unsolved.txt","w");
//没有任何推荐信息的景观
$fpUnsolvable = fopen($resultPath."unsolvable.txt","w");
$logic        = new Base_Logic();

$arrSight   = array();
$arrRawData = array();
$arrFilter  = array('文化旅游区','旅游度假区','历史名胜区','旅游区','风景区','生态景区','景区','旅游区','风景名胜区','名胜区','海上公园','公园','民宅','欢乐谷','寺','庙');
$sight      = '';
$listTmpSight = new Sight_List_Sight();
$listTmpSight->setPagesize(PHP_INT_MAX);
$arrTmpSight  = $listTmpSight->toArray();
foreach ($arrTmpSight['list'] as $key => $val){
     foreach ($arrFilter as $filter){
         $val['name'] = str_replace($filter, "", $val['name']);
     }
     if(!empty($val['name'])){
         $arrRawData[] = array('id'=>$val['id'],'name'=>$val['name']);
     }else{
         print $arrTmpSight['list'][$key]['name']."\r\n";
     }
}

$listTmpSightMeta = new Sight_List_Meta();
$listTmpSightMeta->setFilterString("`sight_id` = -1");
$listTmpSightMeta->setPagesize(PHP_INT_MAX);
$arrTmpSightMeta  = $listTmpSightMeta->toArray();
foreach ($arrTmpSightMeta['list'] as $key => $val){
    foreach ($arrFilter as $filter){
        $val['name'] = str_replace($filter, "", $val['name']);
    }
     if(!empty($val['name'])){
         $arrRawData[] = array('id'=>$val['id'],'name'=>$val['name']);
     }else{
         print $arrTmpSightMeta['list'][$key]['name']."\r\n";
     }
}

foreach ($arrData as $val){
    $arrIds  = array();
    $tmp     = explode("\t",$val);
    $id      = intval($tmp[0]);
    $sight   = $tmp[1];
    $oldSight = $sight;
    $objSightMeta = new Sight_Object_Meta();
    $objSightMeta->fetch(array('name' => $sight));
    if(empty($objSightMeta->id)){
        $objSight = new Sight_Object_Sight();
        $objSight->fetch(array('name' => $sight));
        if(!empty($objSight->id)){
            $str = sprintf("%d\t%d\r\n",$id,$objSight->id);
            fwrite($fpConfirm, $str);
            continue;
        }
    }else{
        $str = sprintf("%d\t%d\r\n",$id,$objSightMeta->id);
        fwrite($fpConfirm, $str);
        continue;
    }
    
    foreach ($arrRawData as $val){
        if(strstr($sight,$val['name']) !== false){
            $arrIds[] = $val['id'];
        }
    }
    if(empty($arrIds)){
        foreach ($arrFilter as $filter){
            $sight = str_replace($filter, "", $sight);
        }
        $listTmpSightMeta = new Sight_List_Meta();
        $listTmpSightMeta->setFilterString("name like '%".$sight."%' and `sight_id` = -1");
        $listTmpSightMeta->setPagesize(PHP_INT_MAX);
        $arrTmpSightMeta  = $listTmpSightMeta->toArray();
        foreach ($arrTmpSightMeta['list'] as $val){
            $arrIds[] = $val['id'];
        }
        
        $listTmpSight = new Sight_List_Sight();
        $listTmpSight->setFilterString("name like '%".$sight."%'");
        $listTmpSight->setPagesize(PHP_INT_MAX);
        $arrTmpSight  = $listTmpSight->toArray();
        foreach ($arrTmpSight['list'] as $val){
            $arrIds[] = $val['id'];
        }
        //在我们库中不能自动分配的景点名称
        if(empty($arrIds) && !in_array($oldSight,$arrSight)){
            $arrSight[] = array('id' => $id,'name' => $oldSight);
        }
    }else{
        $str = $id;
        foreach ($arrIds as $sightId){
            $str  = sprintf("%d\t%d\t%d\t%d\r\n",$id,intval($sightId),0,0);
            fwrite($fpUnsolved, $str);
        }
        
    }
}
foreach ($arrSight as $val){
    $str  = sprintf("%d\t%s\r\n",intval($val['id']),$val['name']);
    fwrite($fpUnsolvable, $str);
}
fclose($fpConfirm);
fclose($fpUnsolved);
fclose($fpUnsolvable);