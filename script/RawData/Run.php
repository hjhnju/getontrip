<?php
/**
 * 抓取视频、书籍、百科数据脚本,有三个参数:类型 景点ID 条数
 * 类型取值范围：Book Video Wiki
 * 如果景点ID为-1,则取所有景点数据
 * 如果条数为-1,则取该景点下的所有数据
 */
require_once("../env.inc.php");
$arrTypes = array("Book","Video","Wiki","All");
if(count($argv) < 4){
    print "参数错误!Usage:Run.php 类型 景点ID 条数\r\n";
    return 0;
}
if(!in_array(trim($argv[1]),$arrTypes)){
    print "类型错误!只有三种类型：Book Video Wiki All\r\n";
    return 0;
}
$type      = trim($argv[1]);
$sightId   = intval($argv[2]);
$num       = intval($argv[3]);

$conf    = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
$url  = $conf['web']['root']."/InitData?sightId=$sightId&type=$type&num=$num";
$http = Base_Network_Http::instance()->url($url)->exec();