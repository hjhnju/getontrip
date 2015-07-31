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
    public static function getJdBooks($sightId,$page,$pageSize,$status=Book_Type_Status::ALL){
        $logicBook = new Book_Logic_Book();
        $arrBooks  = $logicBook->getBooks($sightId, $page, $pageSize,$status);
        if(!empty($arrBooks)){
            return $arrBooks;
        }
        return $logicBook->getJdBooks($sightId, $page, $pageSize);
    }
    
    /**
     * 接口2：Book_Api::editBook($sightId,$id,$arrInfo)
     * 修改书籍数据
     * @param integer $sightId,景点ID
     * @param integer $id,书籍ID
     * @param array $arrInfo
     * @return boolean
     */
    public function editBook($sightId,$id,$arrInfo){
        $logicBook = new Book_Logic_Book();
        return $logicBook->editBook($sightId, $id, $arrInfo);
    }
    
    /**
     * 接口3:Book_Api::delBook($sightId,$id)
     * 删除书籍数据
     * @param integer $sightId,景点ID
     * @param integer $id,视频ID
     * @return boolean
     */
    public function delBook($sightId,$id){
        $logicBook = new Book_Logic_Book();
        return $logicBook->delBook($sightId, $id);
    }
}