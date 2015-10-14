<?php
/**
 * 抓取视频、书籍、百科数据脚本,有三个参数:类型 景点ID 条数
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
$num       = isset($argv[3])?intval($argv[3]):intval(Base_Config::getConfig('thirddata')->initnum);

//获取景点ID数组
if(-1 == $sightId){
    $logic = new Sight_Logic_Sight();
    $arr   = $logic->getSightList(1, PHP_INT_MAX, Sight_Type_Status::PUBLISHED);
    foreach ($arr['list'] as $val){
        $arrSight[] = $val['id'];
    }
}else{
    $arrSight[] = $sightId;
}
foreach ($arrSight as $id){
    switch($type){
        case 'Book':
            $logicBook = new Book_Logic_Book();
            $page      = ceil($num/self::PAGE_SIZE);
            for( $i = 1;$i <= $page; $i++ ){
                $logicBook->getJdBooks($id, $i,self::PAGE_SIZE);
            }
            break;
        case 'Video':
            $logicVideo = new Video_Logic_Video();
            $page = ceil($num/self::PAGE_SIZE);
            for( $i = 1;$i <= $page; $i++ ){
                $logicVideo->getAiqiyiSource($id, $i);
            }
            break;
        case 'Wiki':
            $logicWiki = new Keyword_Logic_Keyword();
            $logicWiki->getKeywordSource($id,1,$num,Keyword_Type_Status::PUBLISHED);
            break;
        case 'All':
            $logicBook  = new Book_Logic_Book();
            $logicVideo = new Video_Logic_Video();
            $logicWiki  = new Keyword_Logic_Keyword();
            $page       = ceil($num/self::PAGE_SIZE);
            for( $i = 1; $i <= $page; $i++ ){
                $logicBook->getJdBooks($id, $i,self::PAGE_SIZE);
                $logicVideo->getAiqiyiSource($id, $i);
            }
            $logicWiki->getKeywordSource($id,1,$num,Keyword_Type_Status::PUBLISHED);
            break;
        default:
            break;
    }
}