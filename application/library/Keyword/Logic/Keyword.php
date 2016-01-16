<?php
class Keyword_Logic_Keyword extends Base_Logic{
    
    const WIKI_CATALOG_NUM = 4;
    
    protected $_fields;
    
    public function __construct(){
        $this->_fields = array('id','sight_id','name','url','content','image','audio','audio_len','create_time','update_time','status','x','y','level');
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
            if(isset($arr['query'])){
                $filter = "name like '%".$arr['query']."%'";
                unset($arr['query']);
                foreach ($arr as $key => $val){
                    $filter .= " and $key='".$val."'";
                }
                $list->setFilterString($filter);
            }else{
                $list->setFilter($arr);
            }
        }
               
        $list->setPage($page);
        $list->setPagesize($pageSize);
        return $list->toArray();
    }
    
    /**
     * 添加词条信息,有的话更新
     * @param array $arrInfo
     * @return boolean
     */
    public function addKeywords($arrInfo){
        $bCheck = false;
        $obj    = new Keyword_Object_Keyword();
        if(isset($arrInfo['sight_id']) && isset($arrInfo['name'])){
            $obj->fetch(array('sight_id' => $arrInfo['sight_id'],'name' => $arrInfo['name']));
            if(!empty($obj->id)){
                unset($arrInfo['sight_id']);
                unset($arrInfo['name']);
            }
        }
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->_fields)){  
                $key = $this->getprop($key); 
                if('url' == $key){
                    $val = urldecode($val);
                }             
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if(isset($arrInfo['sight_id'])){
            $redis = Base_Redis::getInstance();
            $redis->hDel(City_Keys::getCityWikiNumKey(),$arrInfo['sight_id']);
        }
        if($bCheck){
            $obj->weight = $this->getKeywordWeight($arrInfo['sight_id']);
            $ret = $obj->save();            
            $logicWiki = new Keyword_Logic_Keyword();
            $logicWiki->getKeywordSource($obj->id,$obj->status);
        }
        
        if(isset($arrInfo['status']) && (intval($arrInfo['status']) == Keyword_Type_Status::PUBLISHED)){
            $model = new GisModel();
            $model->insertLandscape($obj->id);
        }elseif(isset($arrInfo['status']) && (intval($arrInfo['status']) == Keyword_Type_Status::NOTPUBLISHED)){
            $model = new GisModel();
            $model->delLandscape($obj->id);
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
                if('url' == $key){
                    $val = urldecode($val);
                }            
                $obj->$key = $val;
                $bCheck    = true;
            }
        }
        if(isset($arrInfo['sight_id'])){
            $redis = Base_Redis::getInstance();
            $redis->hDel(City_Keys::getCityWikiNumKey(),$arrInfo['sight_id']);
        }
        if($bCheck){
            $ret =  $obj->save();
            if(isset($arrInfo['status']) && (intval($arrInfo['status']) == Keyword_Type_Status::PUBLISHED)){
                $model = new GisModel();
                $model->insertLandscape($id);
                
                $logicWiki = new Keyword_Logic_Keyword();
                $logicWiki->getKeywordSource($id,Keyword_Type_Status::PUBLISHED);
            }elseif(isset($arrInfo['status']) && (intval($arrInfo['status']) == Keyword_Type_Status::NOTPUBLISHED)){
                $model = new GisModel();
                $model->delLandscape($id);
            }
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
        $model = new GisModel();
        $model->delLandscape($id);
        
        $listCatalog = new Keyword_List_Catalog();
        $listCatalog->setFilter(array('keyword_id' => $id));
        $listCatalog->setPagesize(PHP_INT_MAX);
        $arrCatalog  = $listCatalog->toArray();
        foreach ($arrCatalog['list'] as $val){
            $objCatalog = new Keyword_Object_Catalog();
            $objCatalog->fetch(array('id' => $val['id']));
            $objCatalog->remove();
        }
        $obj    = new Keyword_Object_Keyword();
        $obj->fetch(array('id' => $id));
        if(!empty($obj->sight_id)){
            $redis = Base_Redis::getInstance();
            $redis->hDel(City_Keys::getCityWikiNumKey(),$obj->sight_id);
        }
        
        if(!empty($obj->image)){
            $this->delPic($obj->image);   
        }
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
    
    public function getKeywordWeight($sightId){
        $maxWeight  = 0;
        $listKeyword = new Keyword_List_Keyword();
        $listKeyword->setFilter(array('sight_id' => $sightId));
        $listKeyword->setPagesize(PHP_INT_MAX);
        $arrKeyword  = $listKeyword->toArray();
        foreach ($arrKeyword['list'] as $val){
            if($val['weight'] > $maxWeight){
                $maxWeight = $val['weight'];
            }
        }
        return $maxWeight + 1;
    }
    
    public function changeWeight($sightId,$id,$to){
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('sight_id' => $sightId,'id' => $id));
        $from       = $objKeyword->weight;
        $objKeyword->weight = $to;
    
        $listKeyword = new Keyword_List_Keyword();
        $filter ="`sight_id` =".$sightId." and `weight` >= $to and `weight` != $from";
        $listKeyword->setFilterString($filter);
        $listKeyword->setPagesize(PHP_INT_MAX);
        $arrKeyword = $listKeyword->toArray();
        foreach ($arrKeyword['list'] as $key => $val){
            $objTmpKeyword = new Keyword_Object_Keyword();
            $objTmpKeyword->fetch(array('id' => $val['id']));
            $objTmpKeyword->weight += 1;
            $objTmpKeyword->save();
        }
        $ret = $objKeyword->save();
        return $ret;
    }
    
    public function getKeywordByInfo($keywordId){
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $keywordId));
        return $objKeyword->toArray();
    }
    
    public function search($query, $page, $pageSize){
        $arrKeyword  = Base_Search::Search('wiki', $query, $page, $pageSize, array('id'));
        $num         = $arrKeyword['num'];
        $arrKeyword  = $arrKeyword['data'];
        foreach ($arrKeyword as $key => $val){
            $keyword = $this->getKeywordByInfo($val['id']);
            $arrKeyword[$key]['title']  = empty($val['name'])?trim($keyword['name']):$val['name'];
            $arrKeyword[$key]['url']   = trim($keyword['url']);
            $arrKeyword[$key]['image'] = isset($keyword['image'])?Base_Image::getUrlByName($keyword['image']):'';
            unset($arrKeyword[$key]['name']);
        }
        return array('data' => $arrKeyword,'num' => $num);
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
    public function getKeywordList($sightId,$x,$y,$page,$pageSize,$arrParma = array()){
        $listKeyword  = new Keyword_List_Keyword();
        $arrFilter = array_merge(array('sight_id' => $sightId),$arrParma);
        $listKeyword->setFields(array('id','name','url','content','image','audio','audio_len','type','x','y'));
        $listKeyword->setOrder("`weight` asc");
        $listKeyword->setFilter($arrFilter);
        $listKeyword->setPage($page);
        $listKeyword->setPagesize($pageSize);
        $arrRet = $listKeyword->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $arrRet['list'][$key]['id']      = strval($val['id']);
            $arrRet['list'][$key]['content'] = Base_Util_String::delStartEmpty(Base_Util_String::getHtmlEntity($val['content']));
            $arrRet['list'][$key]['url']     = trim($val['url']);
            $arrRet['list'][$key]['audio']   = empty($val['audio'])?'':"/audio/".trim($val['audio']);
            $arrRet['list'][$key]['audio_len']   = trim($val['audio_len']);
            $arrRet['list'][$key]['desc']    = intval($val['type'])==1?'必玩':'';
            $arrRet['list'][$key]['x']    = strval($val['x']);
            $arrRet['list'][$key]['y']    = strval($val['y']);
            if(!empty($x) && !empty($y)){
                $dis   = Base_Util_Number::getEarthDist($x, $y, $val['x'], $val['y']);
                if($dis < 1000){
                    $arrRet['list'][$key]['dis']      = strval(ceil($dis));
                    $arrRet['list'][$key]['dis_unit'] = "m";
                }else{
                    $arrRet['list'][$key]['dis']      = strval(ceil($dis/1000));
                    $arrRet['list'][$key]['dis_unit'] = "km";
                }
            }else{
                $arrRet['list'][$key]['dis']      = '';
                $arrRet['list'][$key]['dis_unit'] = '';
            }            
            $arrRet['list'][$key]['image']   = Base_Image::getUrlByName($val['image']);
            unset($arrRet['list'][$key]['type']);
        }
        return $arrRet['list'];
    }
    
    
    /**
     * 获取词条的百科信息,并将数据写入数据库
     * @param string $word
     * @return array
     */
    public function getKeywordSource($keywordId,$status = Keyword_Type_Status::PUBLISHED){
        $arrItems  = array();
        $arrRet    = array();
        $arrTemp   = array();
        $hash      = '';
        require_once(APP_PATH."/application/library/Base/HtmlDom.php");
        $keyword   = Keyword_Api::queryById($keywordId);
        $arrItems    = array();
        
        $wikiUrl     = $keyword['url'];
        $html        = @file_get_html($wikiUrl);
        if(empty($html)){
            return false;
        }
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
        
        if(empty($name)){
            $image       = $html->find('div.summary-pic a img',0);
            if(!empty($image)){
                $name = $image->getAttribute('src');
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
                if(empty($content)){
                    $content = $html->find('div.para',0);
                }
                $content     = strip_tags($content->innertext);
            }
            foreach($html->find('li[class="level1"]') as $e){
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
            $content  = strip_tags($content->innertext);
            foreach($html->find('div[class^="catalog-item "]') as $e){
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
        $arrTemp['content']     = Base_Util_String::getHtmlEntity(Base_Util_String::delStartEmpty($content));
        $arrTemp['image']       = $hash;
        $arrTemp['url']         = $wikiUrl;
        $arrTemp['status']      = $status;
        
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $keywordId));
        if(!empty($objKeyword->image)){
            $this->delPic($objKeyword->image);
        }
        $objKeyword->content = $arrTemp['content'];
        $objKeyword->image   = $arrTemp['image'];
        $objKeyword->status  = $arrTemp['status'];
        $objKeyword->save();
        
        $listKeywordCata = new Keyword_List_Catalog();
        $listKeywordCata->setFilter(array('keyword_id' => $keywordId));
        $listKeywordCata->setPagesize(PHP_INT_MAX);
        $arrKeywordCata  = $listKeywordCata->toArray();
        foreach ($arrKeywordCata['list'] as $val){
            $objKeywordCatalog = new Keyword_Object_Catalog();
            $objKeywordCatalog->fetch(array('id' => $val['id']));
            $objKeywordCatalog->remove();
        }
        
        foreach ($arrItems as $id => $item){
            $objKeywordCatalog            = new Keyword_Object_Catalog();
            $objKeywordCatalog->name      = $item['name'];
            $objKeywordCatalog->url       = trim($item['url']);
            $objKeywordCatalog->keywordId = $objKeyword->id;
            $objKeywordCatalog->save();
        }
        if(empty($arrTemp)){
            Base_Log::error('keyword '.$keyword['name'].' can not get wiki!');
        }
        $arrRet[] = $arrTemp;
        $html->clear();
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
    
    public function getRecommend($page, $pageSize, $city='', $status = ''){
         $arrRet    = array();
         $arrRawData= array();
         $arrData   = array();
         $RAW_DATA  = "/home/work/publish/data/51data/scenics.txt";
         $RET_DATA  = "/home/work/publish/data/51data/findSight/unsolved.txt";
         $UNSOVED   = "/home/work/publish/data/51data/findSight/unsolvable.txt";
         $from      = ($page-1)*$pageSize;
         $to        = $page*$pageSize;
         //首先将原始数据内容加载入内存
         $arrRaw    = file($RAW_DATA);
         foreach ($arrRaw as $val){
             $tmp = explode("\t",$val);
             if(!empty($city)){
                 if(strstr($city,$tmp[2]) == false){
                     continue;
                 }
             }
             $arrRawData[$tmp[0]] = array(
                 'name' => $tmp[1],
                 'city' => $tmp[2],
                 'file' => $tmp[3],
            );
         }
         $arrTmp   = file($RET_DATA);
         foreach ($arrTmp as $key => $val){
             $val = explode("\t",$val);
             if($status !== ''){
                 if(intval($val[2]) !== $status){
                     continue;
                 }
             }
             if(!isset($arrRet[$val[0]])){
                 $arrRet[$val[0]] = array(array('id' => $val[1],'status' => $val[2]));
             }else{
                 $arrRet[$val[0]] = array_merge($arrRet[$val[0]],array(array('id' => $val[1],'status' => $val[2])));
             }
         }
         
         if($status == '' || $status == 0){
             $arrTmp   = file($UNSOVED);
             foreach ($arrTmp as $key => $val){
                 $val = explode("\t",$val);
                 $arrRet[$val[0]] = array(array('id' => '','status' => 2));
             }
         }
         
         $arrTmp    = $arrRet;
         $total     = 0;
         $index     = 0;
         $realIndex = 0;
         foreach ($arrTmp as $key => $val){
             if(isset($arrRawData[$key]['name'])){
                 $total += 1;
             }
         }
         
         foreach ($arrTmp as $key => $val){
             if($index<$from || !isset($arrRawData[$key]['name'])){
                 $index += 1;
                 continue;
             }
             $arrData[$realIndex]['id']   = $key;
             $arrData[$realIndex]['name'] = $arrRawData[$key]['name'];
             $arrData[$realIndex]['city'] = $arrRawData[$key]['city'];
             $arrData[$realIndex]['sights'] = array();
             foreach ($val as $data){
                 $objSightMeta = new Sight_Object_Meta();
                 $objSightMeta->fetch(array('id' => $data['id']));
                 if(!empty($objSightMeta->name)){
                     $arrData[$realIndex]['sights'][] = array('id' =>$data['id'],'name'=>$objSightMeta->name,'city'=>$objSightMeta->city,'status' => $data['status']);
                 }
             }
             $index     += 1;
             $realIndex += 1;
             if($realIndex>=$pageSize){
                 break;
             }
         }
         $arrRet  = array(
            'page'     => $page,
            'pagesize' => $pageSize,
            'pageall'  => ceil($total/$pageSize),
            'total'    => $total,
            'list'     => $arrData,
         );
         return $arrRet;
    }
    
    public function dealRecommend($id,$sightId,$status){
        $RAW_DATA   = "/home/work/publish/data/51data/scenics.txt";
        $str = file_get_contents($RAW_DATA);
        preg_match_all("/$id\t(.*?)\t/s",$str,$match);
        foreach ($match[1] as $val){
            $name = $val;
        }
        
        $RET_DATA   = "/home/work/publish/data/51data/findSight/unsolved.txt";
        $origin_str = file_get_contents($RET_DATA);        
        $update_str = preg_replace("/$id\t$sightId\t(.*?)\t/", "$id\t$sightId\t$status\t", $origin_str);
        $ret        = file_put_contents($RET_DATA, $update_str);
        
        if(intval($status) == 2){
            $origin_str = file_get_contents($RET_DATA);
            preg_match_all("/$id\s(.*?)\s(.*?)\r\n/s",$origin_str,$match);
            $bTest = true;
            foreach ($match[2] as $val){
                if(intval($val)!==2){
                    $bTest = false;
                }
            }
            if($bTest){
                $fpUnsolvable = fopen("/home/work/publish/data/51data/findSight/unsolvable.txt","a");
                $string = sprintf("%d\t%s\r\n",intval($id),$name);
                fwrite($fpUnsolvable, $string);
                fclose($fpUnsolvable);
            }
        }
        return $ret;
    }
    
    public function addalias($from, $to){
        $objTo   = new Keyword_Object_Keyword();
        $objTo->fetch(array('id' => $to));
        $objTo->setFileds(array('x','y','content','image','audio','audio_len'));
        $arrTo   = $objTo->toArray();
        $objFrom = new Keyword_Object_Keyword();
        $objFrom->fetch(array('id' => $from));
        foreach ($arrTo as $key => $val){
            if(empty($val)){
                $key = $this->getprop($key);
                $objTo->$key = $objFrom->$key;
            }else{
                if($key=='audio' || $key == 'image'){
                    $this->delPic($objFrom->$key);
                }
            }
        }
        $alias      = $objTo->alias;
        if(empty($alias)){
            $objTo->alias = $objFrom->name;
        }else{
            $arrAlias   = explode(",",$alias);
            $arrAlias[] = $objFrom->name;
            $objTo->alias = implode(",",$arrAlias);
        }
        $objTo->save();
        $ret = $objFrom->remove();
        return $ret;
    }
}