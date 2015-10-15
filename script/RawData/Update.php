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
$num       = isset($argv[3])?intval($argv[3]):intval(Base_Config::getConfig('thirddata')->initnum);

$logic     = new Base_Logic();
$redis     = Base_Redis::getInstance();

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

//删除书籍
if($type == 'Book' || $type == 'All'){
     $listBook = new Book_List_Book();
     $listBook->setPagesize(PHP_INT_MAX);
     $arrBook = $listBook->toArray();
     foreach ($arrBook['list'] as $val){
         if(!empty($val['image'])){
             $ret = $logic->delPic($val['image']);
         }
         $objBook = new Book_Object_Book();
         $objBook->fetch(array('id' => $val['id']));
         $objBook->remove();
        
         $listSightBook = new Sight_List_Book();
         $listSightBook->setFilter(array('book_id' => $val['id']));
         $listSightBook->setPagesize(PHP_INT_MAX);
         $arrSightBook  = $listSightBook->toArray();
         foreach ($arrSightBook['list'] as $data){
             $objSightBook = new Sight_Object_Book();
             $objSightBook->fetch(array('id' => $data['id']));
             $objSightBook->remove();
         }
     }
}

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
            $redis->hDel(Sight_Keys::getSightTopicKey($id),Sight_Keys::BOOK);
            $logicBook = new Book_Logic_Book();
            $page      = ceil($num/self::PAGE_SIZE);
            for( $i = 1;$i <= $page; $i++ ){
                $logicBook->getJdBooks($id, $i,self::PAGE_SIZE);
            }
            break;
        case 'Video':
            $redis->hDel(Sight_Keys::getSightTopicKey($id),Sight_Keys::VIDEO);
            $logicVideo = new Video_Logic_Video();
            $page = ceil($num/self::PAGE_SIZE);
            for( $i = 1;$i <= $page; $i++ ){
                $logicVideo->getAiqiyiSource($id, $i);
            }
            break;
        case 'Wiki':
            $redis->hDel(Sight_Keys::getSightTopicKey($id),Sight_Keys::LANDSCAPE);
            $logicWiki = new Keyword_Logic_Keyword();
            $logicWiki->getKeywordSource($id,1,$num,Keyword_Type_Status::PUBLISHED);
            break;
        case 'All':
            $redis->hDel(Sight_Keys::getSightTopicKey($id),Sight_Keys::BOOK);
            $redis->hDel(Sight_Keys::getSightTopicKey($id),Sight_Keys::VIDEO);
            $redis->hDel(Sight_Keys::getSightTopicKey($id),Sight_Keys::LANDSCAPE);
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