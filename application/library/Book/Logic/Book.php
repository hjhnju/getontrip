<?php
require_once(APP_PATH."/application/library/Base/HtmlDom.php");
class Book_Logic_Book extends Base_Logic{  
    
    const PAGE_SIZE   = 20;
    
    const CONTENT_LEN = 100;
    
    const DEFAULT_WEIGHT = 0;
    
    protected $fields = array('title', 'author', 'press', 'content_desc', 'catalog', 'url', 'image', 'isbn', 'price_jd', 'price_mart', 'pages', 'status', 'create_time', 'update_time', 'create_user', 'update_user', 'publish_time');
    
    public function __construct(){
        
    }
    
    /**
     * 获取书籍信息，后端用
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getBooks($page,$pageSize,$arrParam = array()){
        $arrBook['list']    = array();
        $arrRet['list']     = array();
        $title    = '';
        if(isset($arrParam['sight_id'])){
            $sightId    = $arrParam['sight_id'];
            $arrBookIds = array();            
            $listSightBook = new Sight_List_Book();
            $listSightBook->setFilter(array('sight_id' => $sightId));
            if(isset($arrParam['order'])){
                $listSightBook->setOrder($arrParam['order']);
                unset($arrParam['order']);
            }
            $listSightBook->setPagesize(PHP_INT_MAX);
            $ret = $listSightBook->toArray();
            if(isset($arrParam['title'])){
                $title  = $arrParam['title'];
                unset($arrParam['title']);
            }
            foreach ($ret['list'] as $val){
                $arrParam = array_merge($arrParam,array('id' => $val['book_id']));                
                $objBook = new Book_Object_Book();
                $objBook->fetch($arrParam);
                $arrTmpBook = $objBook->toArray();
                if(!empty($title) && isset($arrTmpBook['title'])){
                    if( false ==  strstr($arrTmpBook['title'],$title)){
                        continue;
                    }
                }
                if(!empty($arrTmpBook)){
                    $arrBook['list'][] = $arrTmpBook['id'];
                }
            }          
            $arrBook['page'] = $page;
            $arrBook['pagesize'] = $pageSize;
            $arrBook['pageall'] = ceil(count($arrBook['list'])/$pageSize);
            $arrBook['total'] = count($arrBook['list']);
            $arrBook['list'] = array_slice($arrBook['list'], ($page-1)*$pageSize,$pageSize); 
        }else{
            $listBook = new Book_List_Book();
            if(!empty($arrParam)){
                $filter = "1";
                if(isset($arrParam['title'])){
                    $filter .= " and `title` like '".$arrParam['title']."%'";
                    unset($arrParam['title']);
                }
                foreach ($arrParam as $key => $val){
                    $filter .= " and `".$key."` =".$val;
                }
                $listBook->setFilterString($filter);
            }          
            $listBook->setPage($page);
            $listBook->setPagesize($pageSize);
            $arrBook = $listBook->toArray();  
            foreach ($arrBook['list'] as $key => $val){
                $arrBook['list'][$key] = $val['id'];   
            }
        }
        $arrRet['page'] = $arrBook['page'];
        $arrRet['pagesize'] = $arrBook['pagesize'];
        $arrRet['pageall'] = $arrBook['pageall'];
        $arrRet['total'] = $arrBook['total'];
        foreach ($arrBook['list'] as $key => $val){
            $objBook = new Book_Object_Book();
            $objBook->fetch(array('id' => $val));
            $arrRet['list'][$key] = $objBook->toArray();
            $listSightBook = new Sight_List_Book();
            $listSightBook->setFilter(array('book_id' => $val));
            $listSightBook->setPagesize(PHP_INT_MAX);
            $arrSightBook  = $listSightBook->toArray();
            $arrRet['list'][$key]['sights'] = array();
            foreach ($arrSightBook['list'] as $data){
                $temp['id']     = $data['sight_id'];
                $sight          = Sight_Api::getSightById($data['sight_id']);
                $temp['name']   = $sight['name'];               
                $temp['weight'] = $data['weight']; 
                $arrRet['list'][$key]['sights'][] = $temp;
            }
            if(empty($arrRet['list'][$key]['content_desc'])){
                $arrRet['list'][$key]['has_content'] = false;
            }else{
                $arrRet['list'][$key]['has_content'] = true;
            }
            unset($arrRet['list'][$key]['content_desc']);
            unset($arrRet['list'][$key]['catalog']);
        }
        return $arrRet;
    }
    
    /**
     * 获取书籍信息，前端用
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getBookList($sightId,$page,$pageSize,$arrParam = array()){
        $listSightBook   = new Sight_List_Book();      
        $arrRet          = array();
        $listSightBook->setFilter(array('sight_id' => $sightId));
        $listSightBook->setOrder('`weight` asc');
        $listSightBook->setPagesize(PHP_INT_MAX);
        $ret = $listSightBook->toArray();
        foreach ($ret['list'] as $val){
            $objBook         = new Book_Object_Book();
            $arrFilter = array_merge($arrParam,array('id' => $val['book_id']));
            $objBook->fetch($arrFilter);            
            $data = $objBook->toArray();
            if(!empty($data)){
                $temp['id']           = strval($data['id']);
                $temp['author']        = empty($data['author'])?'':"作者：".Base_Util_String::getHtmlEntity($data['author']);
                $temp['image']        = Base_Image::getUrlByName($data['image']);
                $temp['title']       = isset($data['title'])?trim($data['title']):'';
                $temp['content_desc'] = Base_Util_String::getSubString($data['content_desc'], self::CONTENT_LEN);
                $temp['url']          = Base_Config::getConfig('web')->root.'/book/detail?id='.$data['id'];
                $arrRet[] = $temp;
            }
        }
        $arrRet = array_slice($arrRet,($page-1)*$pageSize,$pageSize);
        return $arrRet;
    }
    
    /**
     * 获取京东商城图书信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getJdBooks($sightId,$page,$pageSize){        
        Base_JosSdk::register();
        $temp      = array();
        $arrIds    = array();
        $conf      = new Yaf_Config_INI(CONF_PATH. "/Key.ini");
        $sight     = Sight_Api::getSightById($sightId);
        $name      = trim($sight['name']);

        $appKey    = $conf['jd']['appKey'];
        $appSecret = $conf['jd']['appSecret'];
        
        $c = new JosClient();
        $c->appkey = $appKey;
        $c->secretKey = $appSecret;
        
        $url  = "http://search.jd.com/Search?keyword=".$name."&enc=utf-8&book=y&page=".$page;
        
        $html = file_get_html($url);
        $items  = $html->find('li.gl-item');
        foreach ($items as $item){
            $str = $item->getAttribute('data-sku');                       
            $arrIds[] = $str;
        }
        if(count($arrIds) >= self::PAGE_SIZE){
            $url = "http://search.jd.com/s.php?keyword=".$name."&enc=utf-8&book=y&page=".($page+1)."&start=".count($arrIds);
            $html = file_get_html($url);
            $items  = $html->find('li.gl-item');
            foreach ($items as $item){
                $str = $item->getAttribute('data-sku');                       
                $arrIds[] = $str;
            }
        }else{//如果全词找不全，就分词再搜索
            $arrNames = Base_Util_String::ChineseAnalyzer($name);
            foreach ($arrNames as $name){
                $url  = "http://search.jd.com/Search?keyword=".$name."&enc=utf-8&book=y&page=".$page;
                $html = file_get_html($url);
                $items  = $html->find('li.gl-item');
                foreach ($items as $item){
                    $str = $item->getAttribute('data-sku');                       
                    $arrIds[] = $str;
                }
            }
        }
        if(empty($arrIds)){
            Base_Log::error('sight '.$sightId.' can not get jd books!');
            return 0;
        }
        $key        = 0;
        foreach ($arrIds as $val){
            $base = new WareProductDetailSearchListGetRequest();
            $base->setSkuId($val);
            $base->setIsLoadWareScore("false");
            $base->setClient("m");           
            $ret = $c->execute($base);
            $temparr = json_decode($ret->resp,true);
            if(!isset($temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['productInfo'])){
                continue;
            }
            $arr   = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['productInfo'];
            //$image = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['imagePaths'][0];
            if(!$arr['isbook']){
                continue;
            }
            $temp[$key]['url']       = "http://item.jd.com/".$val.".html";
            $detailRequest = new WareBasebookGetRequest();
            $detailRequest->setSkuId($val);
            $detail = $c->execute($detailRequest);
            $ret = $detail->resp;
            $arrBook = json_decode($ret,true);
            $detail =  $arrBook['jingdong_ware_basebook_get_responce']['BookEntity'][0]['book_info'];
            $temp[$key]['isbn'] = isset($detail['isbn'])?$detail['isbn']:'';
            
            //从豆瓣网获取图片及摘要,目录
            $conf      = new Yaf_Config_Ini(CONF_PATH. "/Key.ini");
            $doubanKey = $conf['douban']['appKey'];
            
            $ret       = file_get_contents('https://api.douban.com/v2/book/isbn/'.$temp[$key]['isbn']."?apikey=".$doubanKey);
            $arrDouban = json_decode($ret,true);
            $author    = isset($arrDouban['author'][0])?trim($arrDouban['author'][0]):'';
            $press     = isset($arrDouban['publisher'])?trim($arrDouban['publisher']):'';
            $summary   = isset($arrDouban['summary'])?trim($arrDouban['summary']):'';
            $image     = isset($arrDouban['images']['large'])?$arrDouban['images']['large']:'';
            $catalog   = isset($arrDouban['catalog'])?trim($arrDouban['catalog']):'';
            $pubdate   = isset($arrDouban['pubdate'])?trim($arrDouban['pubdate']):'';
            $temp[$key]['title']        = $arr['wname'];
            
            $temp[$key]['author']       = isset($detail['author'])?$detail['author']:$author;           
            $temp[$key]['price_mart']   = isset($arr['marketPrice'])?$arr['marketPrice']:'';            
            $temp[$key]['price_jd']     = isset($arr['jdPrice'])?$arr['jdPrice']:'';            
            $temp[$key]['press']        = isset($detail['publishers'])?$detail['publishers']:$press;
            $temp[$key]['publish_time'] = isset($detail['publish_time'])?$detail['publish_time']:$pubdate;            
            
            if(empty($image)||strstr($image,"book-default-large")){
                $image = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['imagePaths'][0];
                $image = $image['bigpath'];
            }
            
            $temp[$key]['image']  = $this->uploadPic($image);
            
            $temp[$key]['status'] = Book_Type_Status::NOTPUBLISHED;
            
            $temp[$key]['pages']  = isset($detail['pages'])?$detail['pages']:'';
            
            
            $obj = new WareBookbigfieldGetRequest();
            $obj->setSkuId($val);
            $detail = $c->execute($obj);
            $ret = $detail->resp;
            $arr = json_decode($ret,true);
            $info =  isset($arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"])?$arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"]:'';
            
            $temp[$key]['content_desc'] = empty($summary)?(isset($info["content_desc"])?$info["content_desc"]:''):$summary;
            $temp[$key]['catalog']      = empty($catalog)?(isset($info['catalogue'])?$info['catalogue']:''):$catalog;         
            
            //摘要为空时,不取此条数据
            if(empty($temp[$key]['content_desc'])){
                $this->delPic($temp[$key]['image']);
                continue;
            }
            $fomat = new  Base_Extract("",$temp[$key]['content_desc']);
            $temp[$key]['content_desc']  = $fomat->preProcess();
            $temp[$key]['content_desc']  = $fomat->dataUpdate($temp[$key]['content_desc']);
            
            if(!empty($temp[$key]['catalog'])){
                $fomat = new  Base_Extract("",$temp[$key]['catalog']);
                $temp[$key]['catalog']  = $fomat->preProcess();
                $temp[$key]['catalog']  = $fomat->dataUpdate($temp[$key]['catalog']);
            }            
            
            $temp[$key]['content_desc'] = Base_Util_String::trimall($temp[$key]['content_desc']);
            $temp[$key]['catalog']      = Base_Util_String::trimall($temp[$key]['catalog']);
            
            $objBook = new Book_Object_Book();
            $id      = $this->getBookIdByISBN($temp[$key]['isbn']);
            if(!empty($id)){
                //仅仅删除上传的图片，其它字段以原来为准，不改变
                if(!empty($temp[$key]['image'])){
                    $this->delPic($temp[$key]['image']);
                }
            }else{
                $objBook->title       = $temp[$key]['title'];
                $objBook->author      = $temp[$key]['author'];
                $objBook->priceJd     = $temp[$key]['price_jd'];
                $objBook->priceMart   = $temp[$key]['price_mart'];
                $objBook->press       = $temp[$key]['press'];
                $objBook->isbn        = $temp[$key]['isbn'];
                $objBook->url         = $temp[$key]['url'];
                $objBook->image       = $temp[$key]['image'];
                $objBook->pages       = $temp[$key]['pages'];
                $objBook->contentDesc = $temp[$key]['content_desc'];
                $objBook->catalog     = $temp[$key]['catalog'];
                $objBook->publishTime = $temp[$key]['publish_time'];
                $objBook->status      = $temp[$key]['status'];
                $objBook->save();               
                
                $objSightBook     = new Sight_Object_Book();
                $objSightBook->sightId = $sightId;
                $objSightBook->bookId  = $objBook->id;
                $objSightBook->weight  = self::DEFAULT_WEIGHT;
                $objSightBook->save();
            }                       
            $key += 1;
        }
        return $temp;
    }

    /**
     * 修改书籍数据
     * @param integer $id,书籍ID
     * @param array $arrInfo
     * @return boolean
     */
    public function editBook($id,$arrInfo){
        $this->updateRedis($id);
        if(isset($arrInfo['status'])){
            $arrSightIds = array();
            $weight      = array();
            $objBook     = new Book_Object_Book();
            $objBook->fetch(array('id' => $id));
            $objBook->status = $arrInfo['status'];
            $listSightBook   = new Sight_List_Book();
            $listSightBook->setFilter(array('book_id' => $id));
            $listSightBook->setPagesize(PHP_INT_MAX);
            $arrSightBook    = $listSightBook->toArray();
            foreach ($arrSightBook['list'] as $val){
                $objSightBook = new Sight_Object_Book();
                $objSightBook->fetch(array('id' => $val['id']));
                               
                if($arrInfo['status'] == Book_Type_Status::BLACKLIST){
                    $ret   = $objSightBook->remove();
                }else{
                    $objSightBook->weight = $this->getBookWeight($val['sight_id']);
                    $objSightBook->save();
                }                            
            }
            if($arrInfo['status'] == Book_Type_Status::BLACKLIST){
                return $objBook->save();
            }
        }
        
        $objBook   = new Book_Object_Book();
        $sightId   = array();
        
        if(isset($arrInfo['sight_id'])){
            $sightId = $arrInfo['sight_id'];
            unset($arrInfo['sight_id']);
            
            $listSightBook = new Sight_List_Book();
            $listSightBook->setFilter(array('book_id' => $id));
            $listSightBook->setPagesize(PHP_INT_MAX);
            $arrSightBook  = $listSightBook->toArray();
            foreach ($arrSightBook['list'] as $val){
                $objSightBook = new Sight_Object_Book();
                $objSightBook->fetch(array('id' => $val['id']));
                $objSightBook->remove();
            }
        }
        $objBook->fetch(array('id' => $id));
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                if(($key == 'image') && ($objBook->image !== $val) && (!empty($objBook->image))){
                    $this->delPic($objBook->image);
                }
                $objBook->$key = $val;
            }
        }        
        $ret =  $objBook->save();
        foreach ($sightId as $key => $id){
            $objSightBook = new Sight_Object_Book();
            $objSightBook->sightId = $id;
            $objSightBook->bookId  = $objBook->id;
            $objSightBook->weight  = $this->getBookWeight($id);
            $objSightBook->save();    
            
        }    
        return $ret;
    }
    
    /**
     * 添加书籍
     * @param array $arrInfo
     * @return boolean
     */
    public function addBook($arrInfo){
        $objBook = new Book_Object_Book();
        $sightId = array();
        if(isset($arrInfo['sight_id'])){
            $sightId = $arrInfo['sight_id'];
            unset($arrInfo['sight_id']);
        }
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$this->fields)){
                $key = $this->getprop($key);
                $objBook->$key = $val;
            }
        }        
        $ret =  $objBook->save();
        
        foreach($sightId as $id){
            $objSightBook = new Sight_Object_Book();
            $objSightBook->sightId = $id;
            $objSightBook->bookId  = $objBook->id;
            $objSightBook->weight  = $this->getBookWeight($sightId);
            $objSightBook->save();
        }      
        $this->updateRedis($objBook->id);
        return $objBook->id;
    }
    
    /**
     * 删除书籍数据
     * @param integer $id,书籍ID
     * @return boolean
     */
    public function delBook($id){
       $arrSightIds    = array();
       $weight         = array();
       $this->updateRedis($id);
       $objBook = new Book_Object_Book();
       $objBook->fetch(array('id' => $id));
       $image   = $objBook->image;
       if(!empty($image)){
           $this->delPic($image);
       }
       $ret = $objBook->remove();
       
       $listSightBook = new Sight_List_Book();
       $listSightBook->setFilter(array('book_id' => $id));
       $listSightBook->setPagesize(PHP_INT_MAX);
       $arrSightBook  = $listSightBook->toArray();
       foreach ($arrSightBook['list'] as $val){
           $objSightBook = new Sight_Object_Book();
           $objSightBook->fetch(array('id' => $id));
           $weight[]      = $objSightBook->weight;
           $arrSightIds[] = $objSightBook->sightId;
           $objSightBook->remove();
       }
       
       foreach ($arrSightIds as $key => $id){
           $listSightBook = new Sight_List_Book();
           $listSightBook->setFilterString("`weight` >".$weight[$key]);
           $listSightBook->setPagesize(PHP_INT_MAX);
           $arrSightBook  = $listSightBook->toArray();
           foreach ($arrSightBook['list'] as $val){
               $objSightBook = new Sight_Object_Book();
               $objSightBook->fetch(array('id' => $val['id']));
               $objSightBook->weight -= 1;
               $objSightBook->save();
           }
       }
       return $ret;
    }
    
    public function getBookById($bookId){
        $objBook = new Book_Object_Book();
        $objBook->setFileds(array('id','title','author','press','content_desc','url','image','isbn','publish_time'));
        $objBook->fetch(array('id' => $bookId));
        $arrBook = $objBook->toArray();
        $strDesc = '';
        if(!empty($arrBook)){
            $arrBook['id']        = strval($arrBook['id']);  
            $arrTemp = array();          
            if(!empty($arrBook['author'])){
                $arrTemp[]  = $arrBook['author'];
            }
            if(!empty($arrBook['press'])){
                $arrTemp[]  = $arrBook['press'];
            }
            if(!empty($arrBook['publish_time'])){
                $arrTemp[]  = $arrBook['publish_time'];
            }
            if(!empty($arrBook['isbn'])){
                $arrTemp[]  = 'ISBN:'.$arrBook['isbn'];
            }
            $arrBook['info'] = implode("/",$arrTemp);
            $arrBook['content_desc'] = htmlspecialchars_decode($arrBook['content_desc']);
            $arrBook['content_desc'] = Base_Util_String::trimall($arrBook['content_desc']);
            //$arrBook['content_desc'] = Base_Util_String::delStartEmpty($arrBook['content_desc']);
            $arrBook['image']        = isset($arrBook['image'])?Base_Image::getUrlByName($arrBook['image']):'';
            $logicCollect            = new Collect_Logic_Collect();
            $arrBook['collected']    = strval($logicCollect->checkCollect(Collect_Type::BOOK, $bookId));
            
            //话题点赞情况
            $logicPraise             = new Praise_Logic_Praise();
            $arrBook['praised']      = strval($logicPraise->checkPraise(Praise_Type_Type::BOOK, $bookId));
            $arrBook['praiseNum']    = strval($logicPraise->getPraiseNum($bookId, Praise_Type_Type::BOOK));
            
            $arrBook['buyurl']       = $arrBook['url'];
            $arrBook['shareurl']     = Base_Config::getConfig('web')->root.'/book/detail?id='.$bookId;
            $arrBook['url']          = $arrBook['shareurl']."&isapp=1";
            
        }
        return $arrBook;
    }
    
    public function search($query, $page, $pageSize){
        $arrBook  = Base_Search::Search('book', $query, $page, $pageSize, array('id','title'));
        $num      = $arrBook['num'];
        $arrBook  = $arrBook['data'];
        foreach ($arrBook as $key => $val){
            $book = $this->getBookById($val['id']);
            $arrBook[$key]['url']   = trim($book['url']);
            $arrBook[$key]['image'] = $book['image'];
            $arrBook[$key]['search_type'] = 'book';
        }
        return array('data' => $arrBook, 'num' => $num);
    }
    
    public function getBookIdByISBN($strIsbn){
        $ret     = '';
        $objBook = new Book_Object_Book();
        $objBook->fetch(array('isbn' => $strIsbn));
        if(isset($objBook->id)){
            $ret = $objBook->id;
        }
        return $ret;
    }
    
    //书籍发布时，其权重设置成发布的书籍中的最小
    public function getBookWeight($sightId){    
        $maxWeight  = 0;    
        $listSightBook = new Sight_List_Book();
        $listSightBook->setFilter(array('sight_id' => $sightId));
        $listSightBook->setPagesize(PHP_INT_MAX);
        $arrSightBook  = $listSightBook->toArray();
        foreach ($arrSightBook['list'] as $val){
            $objBook   = new Book_Object_Book();
            $objBook->fetch(array('id' => $val['book_id']));
            if($objBook->status !== Book_Type_Status::PUBLISHED){
                continue;
            }
            if($val['weight'] > $maxWeight){
                $maxWeight = $val['weight'];
            }
        }
        return $maxWeight + 1;
    }
    
    public function getBookNum($sightId, $status = Book_Type_Status::PUBLISHED){
        if($status == Book_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $ret   = $redis->hGet(Sight_Keys::getSightTongjiKey($sightId),Sight_Keys::BOOK);
            if(!empty($ret)){
                return $ret;
            }
        }
        $listSightBook = new Sight_List_Book();
        $listSightBook->setFilter(array('sight_id' => $sightId));
        $listSightBook->setPagesize(PHP_INT_MAX);
        if(empty($status)){
            return $listSightBook->countAll();
        }
        $count = 0;
        $arrSightBook  = $listSightBook->toArray();
        foreach ($arrSightBook['list'] as $val){
            $objBook = new Book_Object_Book();
            $objBook->fetch(array('id' => $val['book_id']));
            if($objBook->status == $status){
                $count += 1;
            }
        }
        if($status == Book_Type_Status::PUBLISHED){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Sight_Keys::getSightTongjiKey($sightId),Sight_Keys::BOOK,$count);
        }
        return $count;
    }
    
    /**
     * @param string $strIsbn
     * @param integer $type
     * @return array
     */
    public  function getBookSourceFromIsbn($strSkuId, $type){
        $temp      = array();
        if( $type == Book_Type_Source::JD){
            Base_JosSdk::register();     
            $conf      = new Yaf_Config_INI(CONF_PATH. "/Key.ini");            
            $appKey    = $conf['jd']['appKey'];
            $appSecret = $conf['jd']['appSecret'];
            
            $c = new JosClient();
            $c->appkey = $appKey;
            $c->secretKey = $appSecret;
             
            $base = new WareProductDetailSearchListGetRequest();
            $base->setSkuId($strSkuId);
            $base->setIsLoadWareScore("false");
            $base->setClient("m");
            $ret = $c->execute($base);
            $temparr = json_decode($ret->resp,true);
             
            $arr   = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['productInfo'];
            $temp['title']        = $arr['wname'];
            $temp['price_mart']   = isset($arr['marketPrice'])?$arr['marketPrice']:'';
            $temp['price_jd']     = isset($arr['jdPrice'])?$arr['jdPrice']:'';
            $temp['url']          = "http://item.jd.com/".$strSkuId.".html";
            
            $detailRequest = new WareBasebookGetRequest();
            $detailRequest->setSkuId($strSkuId);
            $detail = $c->execute($detailRequest);
            $ret = $detail->resp;
            $arrBook = json_decode($ret,true);
            $detail =  $arrBook['jingdong_ware_basebook_get_responce']['BookEntity'][0]['book_info'];
            $temp['isbn']         = isset($detail['isbn'])?$detail['isbn']:'';
            
            //从豆瓣网获取图片及摘要,目录
            $conf      = new Yaf_Config_Ini(CONF_PATH. "/Key.ini");
            $doubanKey = $conf['douban']['appKey'];
            
            $ret       = file_get_contents('https://api.douban.com/v2/book/isbn/'.$temp['isbn']."?apikey=".$doubanKey);
            $arrDouban = json_decode($ret,true);
            $author    = isset($arrDouban['author'][0])?trim($arrDouban['author'][0]):'';
            $press     = isset($arrDouban['publisher'])?trim($arrDouban['publisher']):'';
            $summary   = isset($arrDouban['summary'])?trim($arrDouban['summary']):'';
            $image     = isset($arrDouban['images']['large'])?$arrDouban['images']['large']:'';
            $catalog   = isset($arrDouban['catalog'])?trim($arrDouban['catalog']):'';
            $pubdate   = isset($arrDouban['pubdate'])?trim($arrDouban['pubdate']):'';
            $temp['author']       = isset($detail['author'])?$detail['author']:$author;
            $temp['price_mart']   = isset($arr['marketPrice'])?$arr['marketPrice']:'';
            $temp['price_jd']     = isset($arr['jdPrice'])?$arr['jdPrice']:'';
            $temp['press']        = isset($detail['publishers'])?$detail['publishers']:$press;
            $temp['publish_time'] = isset($detail['publish_time'])?$detail['publish_time']:$pubdate;
            
            $temp['press']        = isset($detail['publishers'])?$detail['publishers']:$press;
            $temp['publish_time'] = isset($detail['publish_time'])?$detail['publish_time']:$pubdate;
             
            if(empty($image) || strstr($image,"book-default-large")){
                $image = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['imagePaths'][0];
                $image = $image['bigpath'];
            }
            
            $temp['image']  = $this->uploadPic($image);
            
            $temp['status'] = Book_Type_Status::NOTPUBLISHED;
            
            $temp['pages']  = isset($detail['pages'])?$detail['pages']:'';
                        
            $obj = new WareBookbigfieldGetRequest();
            $obj->setSkuId($strSkuId);
            $detail = $c->execute($obj);
            $ret = $detail->resp;
            $arr = json_decode($ret,true);
            $info =  isset($arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"])?$arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"]:'';
            $temp['content_desc'] = isset($info["content_desc"])?trim($info["content_desc"]):'';
            $temp['catalog']      = isset($info['catalogue'])?trim($info['catalogue']):'';
            
            if(empty($temp['content_desc'])){
                $temp['content_desc'] = $summary;
            }
            
            if(empty($temp['catalog'])){
                $temp['catalog']      = $catalog;
            }
            if(!empty($temp['content_desc'])){
                $fomat = new  Base_Extract("",$temp['content_desc']);
                $temp['content_desc']  = $fomat->preProcess();
                $temp['content_desc']  = $fomat->dataUpdate($temp['content_desc']);
            }
            
            if(!empty($temp['catalog'])){
                $fomat = new  Base_Extract("",$temp['catalog']);
                $temp['catalog']  = $fomat->preProcess();
                $temp['catalog']  = $fomat->dataUpdate($temp['catalog']);
            }
            
            $temp['content_desc'] = Base_Util_String::trimall($temp['content_desc']);
            $temp['catalog']      = Base_Util_String::trimall($temp['catalog']);
            
            $temp['status'] = Book_Type_Status::NOTPUBLISHED;
            
        }else{
            //从豆瓣网获取图片及摘要,目录
            $conf      = new Yaf_Config_Ini(CONF_PATH. "/Key.ini");
            $doubanKey = $conf['douban']['appKey'];
            
            $ret       = file_get_contents('https://api.douban.com/v2/book/isbn/'.$strSkuId."?apikey=".$doubanKey);
            $arrDouban = json_decode($ret,true);
            $temp['title']        = isset($arrDouban['title'])?trim($arrDouban['title']):'';
            $temp['author']       = isset($arrDouban['author'][0])?trim($arrDouban['author'][0]):'';
            $temp['publish_time'] = isset($arrDouban['pubdate'])?trim($arrDouban['pubdate']):'';
            $temp['catalog']      = isset($arrDouban['catalog'])?trim($arrDouban['catalog']):'';
            $temp['pages']        = isset($arrDouban['pages'])?trim($arrDouban['pages']):'';
            $temp['url']          = isset($arrDouban['url'])?trim($arrDouban['url']):'';
            $temp['content_desc'] = isset($arrDouban['summary'])?trim($arrDouban['summary']):'';
            $temp['price_mart']   = isset($arrDouban['price'])?trim($arrDouban['price']):'';
            $temp['press']        = isset($arrDouban['publisher'])?trim($arrDouban['publisher']):'';
            $temp['isbn']         = isset($arrDouban['isbn13'])?trim($arrDouban['isbn13']):'';
            $image                = isset($arrDouban['images']['large'])?$arrDouban['images']['large']:'';
            $temp['image']        = $this->uploadPic($image);
            $temp['status']       = Book_Type_Status::NOTPUBLISHED;
        }
        return $temp;
    }
    
    public function getBookInfo($id){
        $objBook = new Book_Object_Book();
        $objBook->fetch(array('id' => $id));
        $arrBook = $objBook->toArray();
        
        $listSightBook = new Sight_List_Book();
        $listSightBook->setFilter(array('book_id' => $id));
        $listSightBook->setPagesize(PHP_INT_MAX);
        $arrSightBook  = $listSightBook->toArray();
        foreach ($arrSightBook['list'] as $val){
            $temp['id']   = $val['sight_id'];
            $sight        = Sight_Api::getSightById($val['sight_id']);
            $temp['name'] = $sight['name'];
            $arrBook['sights'][] = $temp;
        }
        return $arrBook;
    }
    
    /**
     * 修改某景点下的书籍的权重
     * @param integer $sightId 景点ID
     * @param integer $id 书籍ID
     * @param integer $to 需要排的权重位置
     * @return boolean
     */
    public function changeWeight($sightId, $id, $to){
        $strBookids = '';
        $model = new BookModel();
        $ret   = $model->getSightBook($sightId, Book_Type_Status::PUBLISHED);
        $strBookids = implode(",",$ret);
        
        $objSightBook = new Sight_Object_Book();
        $objSightBook->fetch(array('sight_id' => $sightId,'book_id' => $id));
        $from       = $objSightBook->weight;
        $objSightBook->weight = $to;        
    
        $listSightBook = new Sight_List_Book();
        $filter ="`sight_id` =".$sightId." and `book_id` in (".$strBookids.") and `weight` >= $to and `weight` != $from";
        $listSightBook->setFilterString($filter);       
        $listSightBook->setPagesize(PHP_INT_MAX);
        $arrSightBook = $listSightBook->toArray();
        foreach ($arrSightBook['list'] as $key => $val){
            $objTmpSightBook = new Sight_Object_Book();
            $objTmpSightBook->fetch(array('id' => $val['id']));
            $objTmpSightBook->weight += 1;
            $objTmpSightBook->save();
        }
        $ret = $objSightBook->save();
        return $ret;
    }
    
    public function updateRedis($bookId){
        $redis = Base_Redis::getInstance();
        $listSightBook = new Sight_List_Book();
        $listSightBook->setFilter(array('book_id' => $bookId));
        $listSightBook->setPagesize(PHP_INT_MAX);
        $arrSightBook  = $listSightBook->toArray();
        foreach ($arrSightBook['list'] as $val){
            $redis->hDel(Sight_Keys::getSightTongjiKey($val['sight_id']),Sight_Keys::BOOK);
        }
    }
}