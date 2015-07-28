<?php
class Book_Keys {

    const REDIS_BOOK_INFO_KEY   = 'book_%s_%s';

    public static function getBookInfoName($sightId,$index){
        return sprintf(self::REDIS_BOOK_INFO_KEY, $sightId,$index);
    }
}
