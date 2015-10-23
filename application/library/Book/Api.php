<?php
class Book_Api{
    
    /**
     * 接口1：Book_Api::getJdBooks($sightId,$page,$pageSize)
     * 获取京东商城图书信息
     * @param string $sightId，景点ID
     * @param integer $page
     * @param integer $pageSize
     * @param array   $arrParam，过滤条件
     * @return array
     */
    public static function getJdBooks($sightId,$page,$pageSize,$arrParam = array()){
        $logicBook = new Book_Logic_Book();
        return  $logicBook->getBooks($sightId, $page, $pageSize,$arrParam);
    }
    
    /**
     * 接口2：Book_Api::editBook($id,$arrInfo)
     * 修改书籍数据
     * @param integer $id,书籍ID
     * @param array $arrInfo
     * @return boolean
     */
    public static function editBook($id,$arrInfo){
        $logicBook = new Book_Logic_Book();
        return $logicBook->editBook($id, $arrInfo);
    }
    
    /**
     * 接口3:Book_Api::delBook($id)
     * 删除书籍数据
     * @param integer $id,书籍ID
     * @return boolean
     */
    public static function delBook($id){
        $logicBook = new Book_Logic_Book();
        return $logicBook->delBook($id);
    }
    
    /**
     * 接口4：Book_Api::getBookNum($sightId)
     * 根据景点ID获取数据数量
     * @param integer $sightId
     * @param integer $status
     * @return number
     */
    public static function getBookNum($sightId, $status = Book_Type_Status::PUBLISHED){
        $logicBook = new Book_Logic_Book();
        return $logicBook->getBookNum($sightId, $status);
    }
    

    /**
     * 接口5：Book_Api::addBook($arrInfo)
     * 添加书籍接口
     * @param array $arrInfo,array('title'=>'','sight_id'=>1,...)
     * @return boolean
     */
    public static function addBook($arrInfo){
        $logicBook = new Book_Logic_Book();
        return $logicBook->addBook($arrInfo);
    }
    
    /**
     * 接口6：Book_Api::getBookSourceFromIsbn($strIsbn, $type)
     * 根据skuid或isbn从京东或豆瓣抓取书籍数据
     * @param string $strIsbn
     * @param integer $type, 1:京东，2:豆瓣
     * @return array
     */
    public static function getBookSourceFromIsbn($strIsbn, $type){
        $logicBook = new Book_Logic_Book();
        return $logicBook->getBookSourceFromIsbn($strIsbn, $type);
    }
}