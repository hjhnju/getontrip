<?php
class Book_Logic_Book extends Base_Logic{
    
    public function __construct(){
        
    }
    
    /**
     * 获取书籍信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getBooks($sightId,$page,$pageSize){
        $listBook = new Book_List_Book();
        $listBook->setFilter(array('sight_id' => $sightId));
        $listBook->setPage($page);
        $listBook->setPagesize($pageSize);
        $arrRet = $listBook->toArray();
        return $arrRet;
    }
}