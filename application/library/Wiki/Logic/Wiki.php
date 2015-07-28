<?php
class Wiki_Logic_Wiki extends Base_Logic{
    
    const WIKI_CATALOG_NUM = 4;
    
    public function __construct(){
        
    }
    
    /**
     * 获取景点百科信息，并拼接上百科目录,供线上使用
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getWikis($sightId,$page,$pageSize,$status=Wiki_Type_Status::PUBLISHED){
        $redis  = Base_Redis::getInstance();
        $from   = ($page-1)*$pageSize+1;
        $to     = $page*$pageSize;
        $ret    = array();
        $arrRet = array();
        for($i = $from; $i<=$to; $i++){
            $arrItem = array();
            $ret = $redis->hGetAll(Wiki_Keys::getWikiInfoName($sightId, $i));
            if(($status !== Wiki_Type_Status::ALL)&&($status !== $ret['status'])){
                $i--;
                continue;
            }
            if(empty($ret)){
                break;
            }
            $arrKeys = $redis->keys(Wiki_Keys::getWikiCatalogName($sightId, $i, "*"));
            foreach ($arrKeys as $key){
                $arrItem[] = $redis->hGetAll($key);
            }
            $ret['items']  = $arrItem; 
            $arrRet[]      = $ret;
        }
        return $arrRet;
       
    }
    
    /**
     * 获取词条的百科信息,并将数据写入数据库
     * @param string $word
     * @return array
     */
    public function getWikiSource($sightId,$page,$pageSize){
        $arrItems  = array();
        $arrRet    = array();
        $arrTemp   = array();
        $hash      = '';
        $arrsight     = Keyword_Api::queryKeywords($sightId,$page,$pageSize);
        foreach ($arrsight['list'] as $key  => $sight){
            $redis = Base_Redis::getInstance();
            $index = ($page-1)*$pageSize+$key+1;
            
            $word      = urlencode(trim($sight['name']));
            $ret       = file_get_contents("http://baike.baidu.com/search/word?word=".$word);
            $cotent = $this->getSubstr("<div class=\"card-summary-content\">", "</div></div>", $ret);
            
            $images  = $this->getSubstr("data-src=\"", "\"", $ret);
            foreach ($images as $image){
                $hash = $this->uploadPic(self::TYPE_WIKI,$sight['name'],$image);
                if(!empty($hash)){
                    break;
                }
            }
            
            $items  = $this->getSubstr("<dd class=\"catalog-item.*\">", "</dd", $ret);
            foreach ($items as $id => $item){
                $url  = $this->getSubstr("<a href=\"", "\"", $item);
                $name = $this->getSubstr(";\">", "</a></p>", $item);
                $num  = $id + 1;
                if($num > self::WIKI_CATALOG_NUM){
                    break;
                }
                $strName = strip_tags(isset($name[0])?$name[0]:$name);
                $strUrl  =  isset($url[0])?$url[0]:$url;
                $arrItems[] = array(
                    'name' => $strName,
                    'url'  => $strUrl,              
                );                
                $redis->hset(Wiki_Keys::getWikiCatalogName($sightId, $index, $num),'name',$strName);
                $redis->hset(Wiki_Keys::getWikiCatalogName($sightId, $index, $num),'url',$strUrl);
                $redis->hset(Wiki_Keys::getWikiCatalogName($sightId, $index, $num),'create_time',time());
                $redis->setTimeout(Wiki_Keys::getWikiCatalogName($sightId, $index,$num),self::REDIS_TIME_OUT);
            }
            $arrTemp['word']    = $sight['name'];
            $arrTemp['content'] = strip_tags(isset($cotent[0])?$cotent[0]:$cotent);
            $arrTemp['images']  = $hash;
            $arrTemp['items']   = $arrItems;


            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'title',$arrTemp['word']);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'content',$arrTemp['content']);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'image',$arrTemp['images']);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'status',Wiki_Type_Status::NOTPUBLISHED);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'create_time',time());
            $redis->setTimeout(Wiki_Keys::getWikiInfoName($sightId, $index),self::REDIS_TIME_OUT);
                    
            $arrRet[] = $arrTemp;
        }
        return $arrRet;
    }
    
    public function updateWikiData($sightId){
        $listWiki = new Wiki_List_Wiki();
        if(!empty($sightId)){
            $listWiki->setFilter(array('sight_id' => $sightId));
        }
        $arrWiki = $listWiki->toArray();
        foreach ($arrWiki['list'] as $val){
            $objWiki = new Wiki_Object_Wiki();
            $objWiki->fetch(array('id' => $val['id']));
            $objWiki->remove();
            
            $listWikiCat = new Wiki_List_Catalog();
            $listWikiCat->setFilter(array('wiki_id' => $val['id']));
            $arrWikiCat = $listWikiCat->toArray();
            foreach ($arrWikiCat as $wikiCat){
                $objWikiCat = new Wiki_Object_Catalog();
                $objWikiCat->fetch(array('id' => $wikiCat['id']));
                $objWikiCat->remove();
            }
        }
    }
}