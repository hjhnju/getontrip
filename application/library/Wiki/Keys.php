<?php
class Wiki_Keys {

    const REDIS_WIKI_INFO_KEY     = 'wiki_%s_%s';
    
    const REDIS_WIKI_CATALOG_KEY  = 'catalog_%s_%s_%s';

    public static function getWikiInfoName($sightId,$keywordId){
        return sprintf(self::REDIS_WIKI_INFO_KEY, $sightId,$keywordId);
    }
    
    public static function getWikiCatalogName($sightId,$keywordId,$index){
        return sprintf(self::REDIS_WIKI_CATALOG_KEY, $sightId,$keywordId,$index);
    }
}
