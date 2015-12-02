<?php
require_once "config.php";
$total_topic           = 0;
$total_desc            = 0;
if(!is_dir(WORK_PATH)){
    mkdir(WORK_PATH);
}

//创建景点索引文件
$fp = fopen(WORK_PATH.INDEX_SIGHT, "w");
$listSightMeta = new Sight_List_Meta();
$listSightMeta->setPagesize(PHP_INT_MAX);
$listSightMeta->setOrder('`id` asc');
$arrSightMeta  = $listSightMeta->toArray();
foreach ($arrSightMeta['list'] as $index => $val){
    $sightNames = $val['name'];
    $objSight   = new Sight_Object_Sight();
    $objSight->fetch(array('id' => $val['id']));
    if(!empty($objSight->name) && ($sightNames !== $objSight->name)){
        $sightNames .=",".$objSight->name;
    }
    $str = sprintf("%d\t%d\t%s\r\n",$index+1, $val['id'],$sightNames);
    fputs($fp,$str);
}
fclose($fp);

//创建话题索引文件
$fp = fopen(WORK_PATH.INDEX_TOPIC, "w");
$listTopic = new Topic_List_Topic();
$listTopic->setPagesize(PHP_INT_MAX);
$listTopic->setFilter(array('status' => Topic_Type_Status::PUBLISHED));
$listTopic->setOrder('`id` asc');
$arrTopic  = $listTopic->toArray();
foreach ($arrTopic['list'] as $index => $val){
    $str = sprintf("%d:%d\r\n",$index + 1, $val['id']);
    fputs($fp,$str);
}
$total_topic = $listTopic->getTotal();
fclose($fp);

//创建景点描述索引文件
$fp = fopen(WORK_PATH.INDEX_SIGHT_DESC, "w");
$listSightMeta = new Sight_List_Meta();
$listSightMeta->setPagesize(PHP_INT_MAX);
$listSightMeta->setOrder('`id` asc');
$arrSightMeta  = $listSightMeta->toArray();
foreach ($arrSightMeta['list'] as $index => $val){
    if(!empty($val['describe'])){
        $total_desc += 1;
        $str = sprintf("%d:%d\r\n",$total_topic + $index + 1, $val['id']);
        fputs($fp,$str);
    }
}
fclose($fp);

//创建景点百科索引文件
/*$fp = fopen(WORK_PATH.INDEX_SIGHT_WIKI, "w");
$listSightMeta = new Sight_List_Meta();
$listSightMeta->setPagesize(PHP_INT_MAX);
$listSightMeta->setOrder('`id` asc');
$arrSightMeta  = $listSightMeta->toArray();
foreach ($arrSightMeta['list'] as $index => $val){
    $str = sprintf("%d:%d\r\n",$total_topic + $total_desc + $index + 1, $val['id']);
    fputs($fp,$str);
}
fclose($fp);*/
