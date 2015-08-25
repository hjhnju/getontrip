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
        if($status == Wiki_Type_Status::ALL){
            for($i = $from; $i<=$to; $i++){
                $arrItem = array();
                $ret = $redis->hGetAll(Wiki_Keys::getWikiInfoName($sightId, $i));
                if(empty($ret)){
                    continue;
                }
                $arrKeys = $redis->keys(Wiki_Keys::getWikiCatalogName($sightId, $i, "*"));
                foreach ($arrKeys as $key){
                    $arrItem[] = $redis->hGetAll($key);
                }
                $ret['items']  = $arrItem;                
                $arrRet[]      = $ret;
            }
        }else{
            $arrWikiKeys = $redis->keys(Wiki_Keys::getWikiInfoName($sightId, "*"));
            sort($arrWikiKeys);
            foreach ($arrWikiKeys as $index => $wikiKey){
                $ret     = $redis->hGetAll($wikiKey);
                $num     = $index + 1;
                $arrItem = array();
                if(($ret['status'] == $status)&&($num >= $from)&&($num <= $to)){
                    $arrKeys = $redis->keys(Wiki_Keys::getWikiCatalogName($sightId, $num, "*"));
                    foreach ($arrKeys as $key){
                        $arrItem[] = $redis->hGetAll($key);
                    }
                    $ret['items']  = $arrItem;
                    $arrRet[]      = $ret;
                }
            }
        }        
        return $arrRet;       
    }
    
    
    /**
     * 获取词条的百科信息,并将数据写入数据库
     * @param string $word
     * @return array
     */
    public function getWikiSource($sightId,$page,$pageSize,$type){
        $arrItems  = array();
        $arrRet    = array();
        $arrTemp   = array();
        $hash      = '';
        require_once(APP_PATH."/application/library/Base/HtmlDom.php");
        $arrsight     = Keyword_Api::queryKeywords($page,$pageSize,array('status'=>Keyword_Type_Status::PUBLISHED,'sight_id'=>$sightId));
        foreach ($arrsight['list'] as $key  => $sight){
            $arrItems    = array();
            $redis       = Base_Redis::getInstance();
            $index       = ($page-1)*$pageSize+$key+1;    
            $word        = urlencode(trim($sight['name']));
            
            $url         = "http://baike.baidu.com/search/word?word=".$word;
            $html        = file_get_html($url);
            $image       = $html->find('img');
            $name        = '';
            foreach ($image as $e){
                $val = $e->getAttribute('data-src');
                if(!empty($val)){
                    $name  = $val;
                    break;
                }
            }
            if(empty($name)){
                foreach ($image as $index => $e){
                    if ($index <= 1) {
                        continue;
                    }
                    $val = $e->getAttribute('src');
                    if(!empty($val)){
                        $name  = $val;
                        break;
                    }
                }
            }
            if(!empty($name)){
                $hash  = $this->uploadPic($name);
            }else{
                $hash  = $name;
            }
            
            $content   = $html->find('div.card-summary-content div.para',0);
            if(empty($content)){
                $content = $html->find('div[class="lemmaWgt-lemmaSummary lemmaWgt-lemmaSummary-light"]',0);
                if(empty($content)){
                    $content = $html->find('div.lemma-summary div.para',0);
                    $content     = strip_tags($content->innertext);
                }
                foreach($html->find('li[class^="title level1 column-"]') as $e){
                    $ret  = $e->find("a",0);
                    $url  = $ret->getAttribute("href")."\t";
                    $name = html_entity_decode($ret->innertext)."\r\n";
                    $arrItems[] = array(
                        'name' => $name,
                        'url'  => $url,
                    );  
                    if(count($arrItems) >= self::WIKI_CATALOG_NUM){
                        break;
                    }              
                }
            }else{
                $content     = strip_tags($content->innertext);
                foreach($html->find('dd[class^="catalog-item "]') as $e){
                    $ret  = $e->find("p a",0);
                    $url  = $ret->getAttribute("href")."\t";
                    $name = html_entity_decode($ret->innertext)."\r\n";
                    $arrItems[] = array(
                        'name' => $name,
                        'url'  => $url,
                    );
                    
                    if(count($arrItems) >= self::WIKI_CATALOG_NUM){
                        break;
                    }
                }
            }         
            $arrTemp['title']       = $sight['name'];
            $arrTemp['content']     = Base_Util_String::trimall($content);
            $arrTemp['image']       = Base_Image::getUrlByName($hash);
            $arrTemp['items']       = $arrItems;
            $arrTemp['status']      = Wiki_Type_Status::PUBLISHED;
            $arrTemp['create_time'] = time();
            foreach ($arrItems as $id => $item){
                $num  = $id + 1;
                $redis->delete(Wiki_Keys::getWikiCatalogName($sightId, $index, $num));
                $redis->hset(Wiki_Keys::getWikiCatalogName($sightId, $index, $num),'id',$num);
                $redis->hset(Wiki_Keys::getWikiCatalogName($sightId, $index, $num),'name',$item['name']);
                $redis->hset(Wiki_Keys::getWikiCatalogName($sightId, $index, $num),'url',$item['url']);
                $redis->hset(Wiki_Keys::getWikiCatalogName($sightId, $index, $num),'create_time',time());
                
                $arrItems[$id]['id'] = $num;
            }
            
            $picName = $redis->hget(Wiki_Keys::getWikiInfoName($sightId, $index),'image');
            if(!empty($picName)){
                $this->delPic($picName);
            }
            $redis->delete(Wiki_Keys::getWikiInfoName($sightId, $index));
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'title',$arrTemp['title']);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'content',$arrTemp['content']);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'image',$arrTemp['image']);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'status',$arrTemp['status']);
            $redis->hset(Wiki_Keys::getWikiInfoName($sightId, $index),'create_time',$arrTemp['create_time']);
            
            $arrRet[] = $arrTemp;
            
            $html->clear();
        }
        return $arrRet;
    }
    
    /**
     * 修改百科数据
     * @param integer $keywordId
     * @param array $arrInfo
     * @return boolean
     */
    public function editWiki($keywordId,$arrInfo){
        $redis        = Base_Redis::getInstance();
        $ret          = false;
        $arrCatalog   = array();
        $logicKeyword = new Keyword_Logic_Keyword();
        $sightId      = $logicKeyword->getSightId($keywordId);
        if(!empty($sightId)){
            $arr      = $redis->hGetAll(Wiki_Keys::getWikiInfoName($sightId, $keywordId));
            if(isset($arrInfo['catalog'])){
               $arrCatalog = $arrInfo['catalog']; 
               unset($arrInfo['catalog']);
            }
            $arrKeys  = array_keys($arr);
            foreach ($arrInfo as $key => $val){
                if(in_array($key,$arrKeys)){
                    $arr[$key] = $val;
                }
            }
            $ret1 = $redis->hMset(Wiki_Keys::getWikiInfoName($sightId, $keywordId),$arr);
        }
        //修改百科目录
        if(!empty($arrCatalog)){
            foreach ($arrCatalog as $index => $val){
                $id = $val['id'];
                unset($val['id']);
                $arr      = $redis->hGetAll(Wiki_Keys::getWikiCatalogName($sightId, $keywordId, $id));                
                $arrKeys  = array_keys($arr);
                foreach ($val as $key => $data){
                    if(in_array($key,$arrKeys)){
                        $val[$key] = $data;
                    }
                }
                $ret2 = $redis->hMset(Wiki_Keys::getWikiCatalogName($sightId, $keywordId, $id));
            }
        }
        return $ret1&&$ret2;       
    }
    
    /**
     * 删除百科数据
     * @param integer $keywordId
     * @return boolean
     */
    public function delWiki($keywordId){
        $redis        = Base_Redis::getInstance();
        $ret          = false;
        $logicKeyword = new Keyword_Logic_Keyword();
        $sightId      = $logicKeyword->getSightId($keywordId);
        if(!empty($sightId)){
            $ret      = $redis->delete(Wiki_Keys::getWikiInfoName($sightId, $keywordId));
            
            $arrKeys = $redis->keys(Wiki_Keys::getWikiCatalogName($sightId, $keywordId, "*"));
            foreach ($arrKeys as $key){
                $redis->delete($key);
            }          
        }
        return $ret;
    }
}