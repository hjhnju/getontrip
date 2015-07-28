<?php
class Book_Api{
    
    /**
     * 接口1：Book_Api::getJdBooks($sightId,$page,$pageSize)
     * 获取京东商城图书信息
     * @param string $sightId，景点ID
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getJdBooks($sightId,$page,$pageSize,$status=Book_Type_Type::ALL){
        $logicBook = new Book_Logic_Book();
        $arrBooks  = $logicBook->getBooks($sightId, $page, $pageSize,$status);
        if(!empty($arrBooks)){
            return $arrBooks;
        }
        return $logicBook->getJdBooks($sightId, $page, $pageSize);
    }
}