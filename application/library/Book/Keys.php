<?php
class Book_Keys {
    
    //书籍详情
    const REDIS_BOOK_INFO_KEY   = 'book_%s_%s';
    
    //书籍黑名单
    const REDIS_BLACK_BOOK_KEY  = 'black_book_%s';

    public static function getBookInfoName($sightId,$index){
        return sprintf(self::REDIS_BOOK_INFO_KEY, $sightId,$index);
    }
    
    public static function getBlackBookName($id){
        return sprintf(self::REDIS_BLACK_BOOK_KEY, $id);
    }
}
