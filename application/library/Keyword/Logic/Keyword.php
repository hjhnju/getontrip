<?php
class Keyword_Logic_Keyword extends Base_Logic{
    
    const WIKI_CATALOG_NUM = 4;
    
    protected $_fields;
    
    public function __construct(){
        $this->_fields = array('id','sight_id','name','url','create_time','update_time','status','x','y');
    }
    
    /**
     * 查询词条列表
     * @param integer $sight_id
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryKeywords($page, $pageSize,$arrInfo){
        $list  = new Keyword_List_Keyword();
        $arr   = array();
        if(isset($arrInfo['status']) && $arrInfo['status'] != Keyword_Type_Status::ALL){
            $arr['status'] = $arrInfo['status'];            
        }
        if(!empty($arrInfo['sight_id'])){
            $arr['sight_id'] = $arrInfo['sight_id'];
            $list->setOrder('weight asc');
            unset($arrInfo['sight_id']);
        }else{
            $list->setOrder('update_time desc');
        }
        if(isset($arrInfo['status'])){
           unset($arrInfo['status']); 
        }
        $arr = array_merge($arr,$arrInfo);
        if(!empty($arr)){
            $list->setFilter($arr);
        }
               
        $list->setPage($page);
        $list->setPagesize($pageSize);
        return $list->toArray();
    }
    
    /**
     * 添加词条信息
     * @param array $arrInfo
     * @return boolean
     */
    public function addKeywords($arrInfo){
        $bCheck = false;
        $obj    = new Keyword_Object_Keyword();
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){  
                $key = $this->getprop($key);              
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if($bCheck){
            $num = $this->getKeywordNumBySight($arrInfo['sight_id']);
            $obj->weight = $num + 1;
            $ret = $obj->save();
        }
        if($ret){
            return $obj->id;
        }
        return '';
    }
    
    /**
     * 词条编辑
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public function editKeyword($id,$arrInfo){
        $bCheck = false;
        $obj    = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $id));
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){  
                $key = $this->getprop($key);              
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if($bCheck){
            $ret =  $obj->save();
        }
        if($ret){
            return $obj->id;
        }
        return '';
    }
    
    /**
     * 删除词条
     * @param integer $id
     * @return boolean
     */
    public function delKeyword($id){
        $redis     = Base_Redis::getInstance();
        $wordInfo  = $this->queryById($id);
        $sightId   = $this->getSightId($id);
        $arrKeys   = $redis->keys(Keyword_Keys::getWikiInfoName($sightId, "*"));
        foreach ($arrKeys as $key){
            $data = $redis->hGetAll($key);
            if($data['title'] == $wordInfo['name']){
                $arrTemp = explode("_",$key);
                $id      = $arrTemp[2];
                $redis->delete($key);
            }
        }
        $arrKeys = $redis->keys(Keyword_Keys::getWikiCatalogName($sightId, $id,"*"));
        foreach ($arrKeys as $key){
            $redis->delete($key);
        }
        $obj    = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $id));
        return $obj->remove();
    }
    
    /**
     * 根据ID查询词条
     * @param integer $id
     * @return array
     */
    public function queryById($id){
        $obj = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $id));
        return $obj->toArray();
    }
    
    /**
     * 根据词条ID获取景点ID
     * @param unknown $keywordId
     * @return number
     */
    public function getSightId($keywordId){
        $obj = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $keywordId));
        return $obj->sightId;
    }
    
    /**
     * 根据词条名称获取ID
     * @param string $name
     * @return integer
     */
    public function getWordIdByName($name){
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('name' => $name));
        return $objKeyword->id;
    }
    
    /**
     * 根据景点ID获取词条数
     * @param integer $sightId
     * @return integer
     */
    public function getKeywordNumBySight($sightId){
        $listKeyword = new Keyword_List_Keyword();
        $listKeyword->setFilter(array('sight_id'=>$sightId));
        $ret = $listKeyword->toArray();
        return $ret['total'];
    }
    
    /**
     * 修改某景点下的词条的权重
     * @param integer $id 词条ID
     * @param integer $to 需要排的位置
     * @return boolean
     */
    public function changeWeight($id,$to){
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $id));
        $from       = $objKeyword->weight;
        $objKeyword->weight = $to;       
        
        $bAsc = ($to > $from)?1:0;
        $min  = min(array($from,$to));
        $max  = max(array($from,$to));
        $listKeyword = new Keyword_List_Keyword();
        $listKeyword->setPagesize(PHP_INT_MAX);
        $listKeyword->setFilter(array('sight_id' => $objKeyword->sightId));
        $listKeyword->setOrder('weight asc');
        $arrKeyword = $listKeyword->toArray();
        $arrKeyword = array_slice($arrKeyword['list'],$min-1+$bAsc,$max-$min); 
        $ret = $objKeyword->save();
        foreach ($arrKeyword as $key => $val){
            $objKeyword->fetch(array('id' => $val['id']));
            if($bAsc){
                $objKeyword->weight = $min + $key ;
            }else{
                $objKeyword->weight = $max - $key;
            }
            $objKeyword->save();
        }
        return $ret;
    }
    
    public function getKeywordByInfo($keywordId){
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $keywordId));
        return $objKeyword->toArray();
    }
    
    public function search($query, $page, $pageSize){
        $arrKeyword  = Base_Search::Search('wiki', $query, $page, $pageSize, array('id'));
        foreach ($arrKeyword as $key => $val){
            $keyword = $this->getKeywordByInfo($val['id']);
            $arrKeyword[$key]['name']  = empty($val['name'])?trim($keyword['name']):$val['name'];
            $arrKeyword[$key]['desc']  = trim($keyword['content']);
            $arrKeyword[$key]['url']   = trim($keyword['url']);
            $arrKeyword[$key]['image'] = isset($keyword['image'])?Base_Image::getUrlByName($keyword['image']):'';
        }
        return $arrKeyword;
    }

    
    /**
     * 获取景点百科信息，并拼接上百科目录,供后端使用
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getKeywords($sightId,$page,$pageSize,$arrParma = array()){
        $listKeyword  = new Keyword_List_Keyword();
        $arrFilter = array_merge(array('sight_id' => $sightId),$arrParma);
        $listKeyword->setFields(array('id','name','url','content','image'));
        $listKeyword->setFilter($arrFilter);
        $listKeyword->setPage($page);
        $listKeyword->setPagesize($pageSize);
        $arrRet = $listKeyword->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $listKeywordCatalog = new Keyword_List_Catalog();
            $listKeywordCatalog->setFields(array('id','name','url'));
            $listKeywordCatalog->setFilter(array('keyword_id' => $val['id']));
            $listKeywordCatalog->setPagesize(self::WIKI_CATALOG_NUM);
            $arrCatalog = $listKeywordCatalog->toArray();
            $arrRet['list'][$key]['catalog'] = $arrCatalog['list'];
            $arrRet['list'][$key]['image']   = Base_Image::getUrlByName($val['image']);
        }
        return $arrRet;
    }
    
    /**
     * 获取景点百科信息，并拼接上百科目录,供前端使用
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getKeywordList($sightId,$page,$pageSize,$arrParma = array()){
        $listKeyword  = new Keyword_List_Keyword();
        $arrFilter = array_merge(array('sight_id' => $sightId),$arrParma);
        $listKeyword->setFields(array('id','name','url','content','image'));
        $listKeyword->setOrder("`weight` asc");
        $listKeyword->setFilter($arrFilter);
        $listKeyword->setPage($page);
        $listKeyword->setPagesize($pageSize);
        $arrRet = $listKeyword->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $arrRet['list'][$key]['id'] = strval($val['id']);
            $listKeywordCatalog = new Keyword_List_Catalog();
            $listKeywordCatalog->setFields(array('name','url'));
            $listKeywordCatalog->setFilter(array('keyword_id' => $val['id']));
            $listKeywordCatalog->setPagesize(self::WIKI_CATALOG_NUM);
            $arrCatalog = $listKeywordCatalog->toArray();
            $arrRet['list'][$key]['catalog'] = $arrCatalog['list'];
            $arrRet['list'][$key]['image']   = Base_Image::getUrlByName($val['image']);
        }
        return $arrRet['list'];
    }
    
    
    /**
     * 获取词条的百科信息,并将数据写入数据库
     * @param string $word
     * @return array
     */
    public function getKeywordSource($sightId,$page,$pageSize,$status = Keyword_Type_Status::PUBLISHED){
        $arrItems  = array();
        $arrRet    = array();
        $arrTemp   = array();
        $hash      = '';
        require_once(APP_PATH."/application/library/Base/HtmlDom.php");
        $arrsight     = Keyword_Api::queryKeywords($page,$pageSize,array('status'=>$status,'sight_id'=>$sightId));
        foreach ($arrsight['list'] as $key  => $sight){
            $arrItems    = array();
            $redis       = Base_Redis::getInstance();
            $index       = ($page-1)*$pageSize+$key+1;
            $word        = urlencode(trim($sight['name']));
    
            $wikiUrl     = "http://baike.baidu.com/search/word?word=".$word;
            $html        = file_get_html($wikiUrl);
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
                foreach ($image as $tempInd => $e){
                    if ($tempInd <= 1) {
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
                $hash  = $this->uploadPic($name,$wikiUrl);
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
                foreach($html->find('li[class="level1"]') as $e){
                    $ret  = $e->find("a",0);
                    $url  = $ret->getAttribute("href")."\t";
                    $name = html_entity_decode($ret->innertext)."\r\n";
                    $arrItems[] = array(
                        'name' => $name,
                        'url'  => $wikiUrl.$url,
                    );
                    if(count($arrItems) >= self::WIKI_CATALOG_NUM){
                        break;
                    }
                }
            }else{
                $content  = strip_tags($content->innertext);
                foreach($html->find('div[class^="catalog-item "]') as $e){
                    $ret  = $e->find("p a",0);
                    $url  = $ret->getAttribute("href")."\t";
                    $name = html_entity_decode($ret->innertext)."\r\n";
                    $arrItems[] = array(
                        'name' => $name,
                        'url'  => $wikiUrl.$url,
                    );
                    if(count($arrItems) >= self::WIKI_CATALOG_NUM){
                        break;
                    }
                }
            }
            $arrTemp['title']       = $sight['name'];
            $arrTemp['content']     = Base_Util_String::trimall($content);
            $arrTemp['image']       = $hash;
            $arrTemp['url']         = $wikiUrl;
            $arrTemp['status']      = Keyword_Type_Status::PUBLISHED;
            
            $objKeyword = new Keyword_Object_Keyword();
            $objKeyword->fetch(array('id' => $sight['id']));
            $objKeyword->content = $arrTemp['content'];
            $objKeyword->image   = $arrTemp['image'];
            $objKeyword->status  = $arrTemp['status'];
            $objKeyword->save();
    
            foreach ($arrItems as $id => $item){
                $objKeywordCatalog            = new Keyword_Object_Catalog();
                $objKeywordCatalog->name      = $item['name'];
                $objKeywordCatalog->url       = $item['url'];
                $objKeywordCatalog->keywordId = $objKeyword->id;
                $objKeywordCatalog->save();
            }
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
            $arr      = $redis->hGetAll(Keyword_Keys::getWikiInfoName($sightId, $keywordId));
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
            $ret1 = $redis->hMset(Keyword_Keys::getWikiInfoName($sightId, $keywordId),$arr);
        }
        //修改百科目录
        if(!empty($arrCatalog)){
            foreach ($arrCatalog as $index => $val){
                $id = $val['id'];
                unset($val['id']);
                $arr      = $redis->hGetAll(Keyword_Keys::getWikiCatalogName($sightId, $keywordId, $id));
                $arrKeys  = array_keys($arr);
                foreach ($val as $key => $data){
                    if(in_array($key,$arrKeys)){
                        $val[$key] = $data;
                    }
                }
                $ret2 = $redis->hMset(Keyword_Keys::getWikiCatalogName($sightId, $keywordId, $id));
            }
        }
        return $ret1&&$ret2;
    }
    
    public function getKeywordNum($sighId, $status = Keyword_Type_Status::PUBLISHED){
        if($status == Keyword_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::LANDSCAPE);
            if(!empty($ret)){
                return $ret;
            }
        }
        $listKeyword = new Keyword_List_Keyword();
        if(!empty($status)){
            $listKeyword->setFilter(array('sight_id' => $sighId, 'status' => $status));
        }else{
            $listKeyword->setFilter(array('sight_id' => $sighId));
        }
        $listKeyword->setPagesize(PHP_INT_MAX);
        $count =  $listKeyword->getTotal();
        if($status == Keyword_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sighId),Sight_Keys::LANDSCAPE, $count);
        }
        return $count;
    }
}