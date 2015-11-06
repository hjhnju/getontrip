<?php
/**
 * Adapter for Solr
 * usage:
 * $solr = Base_Solr::getInstance();
 */
class Base_Solr {
    
    public static function  getInstance(){
        $objSolr = Base_Config::getConfig('solr');
        $arrSolr = $objSolr->toArray();
        foreach ($arrSolr as $val){
            $arrRet[] = $val;
        }
        $key  = array_rand($arrRet);
        return $arrRet[$key];
    }
}