<?php
class Wiki_Logic_Wiki extends Base_Logic{
    
    const WIKI_CATALOG = 4;
    
    public function __construct(){
        
    }
    
    /**
     * 获取景点百科信息，并拼接上百科目录
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getWikis($sightId,$page,$pageSize){
        $listWiki = new Wiki_List_Wiki();
        $listWiki->setFilter(array('sight_id' => $sightId));
        $listWiki->setPage($page);
        $listWiki->setPagesize($pageSize);
        $arrRet = $listWiki->toArray();
        
        foreach ($arrRet['list'] as $key => $val){
            $listWikiCatalog = new Wiki_List_Catalog();
            $listWikiCatalog->setFilter(array('wiki_id' => $val['id']));
            $listWikiCatalog->setPagesize(self::WIKI_CATALOG);
            $ret = $listWikiCatalog->toArray();
            $arrRet['list'][$key]['catalog'] = $ret['list'];
        }
        return $arrRet;
    }
}