<?php
require_once(APP_PATH."/application/library/Base/HtmlDom.php");
class Book_Logic_Book extends Base_Logic{  
    
    const PAGE_SIZE   = 20;
    
    const CONTENT_LEN = 40;
    
    public function __construct(){
        
    }
    
    /**
     * 获取书籍信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getBooks($sightId,$page,$pageSize,$arrParam = array()){
        $listSightBook   = new Sight_List_Book();
        $objBook         = new Book_Object_Book();
        $arrRet          = array();
        $listSightBook->setFilter(array('sight_id' => $sightId));
        $listSightBook->setPage($page);
        $listSightBook->setPagesize($pageSize);
        $ret = $listSightBook->toArray();
        foreach ($ret['list'] as $val){
            $arrFilter = array_merge($arrParam,array('id' => $val['book_id']));
            $objBook->fetch($arrFilter);
            $data = $objBook->toArray();
            if(!empty($data)){
                $data['content_desc'] = trim(Base_Util_String::getSubString($data['content_desc'], self::CONTENT_LEN));
                $arrRet[] = $data;
            }
        }
        $ret['list']  = $arrRet;
        return $ret;
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
        $items  = $html->find('li.item-book div.p-img a');
        foreach ($items as $item){
            $str = $item->getAttribute('href');           
            preg_match("/\/(\d+).htm/is", $str, $match);
            if(isset($match[1])){
                $arrIds[] = $match[1];
            }
        }
        if(count($arrIds) >= self::PAGE_SIZE){
            $url = "http://search.jd.com/s.php?keyword=".$name."&enc=utf-8&book=y&page=".($page+1)."&start=".count($arrIds);
            $html = file_get_html($url);
            $items  = $html->find('li.item-book div.p-img a');
            foreach ($items as $item){
                $str = $item->getAttribute('href');
                preg_match("/\/(\d+).htm/is", $str, $match);
                if(isset($match[1])){
                    $arrIds[] = $match[1];
                }
            }
        }else{//如果全词找不全，就分词再搜索
            $arrNames = Base_Util_String::ChineseAnalyzer($name);
            foreach ($arrNames as $name){
                $url  = "http://search.jd.com/Search?keyword=".$name."&enc=utf-8&book=y&page=".$page;
                $html = file_get_html($url);
                $items  = $html->find('li.item-book div.p-img a');
                foreach ($items as $item){
                    $str = $item->getAttribute('href');
                    preg_match("/\/(\d+).htm/is", $str, $match);
                    if(isset($match[1])){
                        $arrIds[] = $match[1];
                    }
                }
            }
        }
        
        $key        = 0;
        foreach ($arrIds as $val){
            $base = new WareProductDetailSearchListGetRequest();
            $base->setSkuId($val);
            $base->setIsLoadWareScore("false");
            $base->setClient("m");           
            $ret = $c->execute($base);
            $temparr = json_decode($ret->resp,true);
           
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
            
            $temp[$key]['title']      = $arr['wname'];
            
            $temp[$key]['author']     = isset($detail['author'])?$detail['author']:$author;           
            $temp[$key]['price_mart'] = isset($arr['marketPrice'])?$arr['marketPrice']:'';            
            $temp[$key]['price_jd']   = isset($arr['jdPrice'])?$arr['jdPrice']:'';            
            $temp[$key]['press']      = isset($detail['publishers'])?$detail['publishers']:$press;
               
            if(empty($image)){
                $image = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['imagePaths'][0];
                $image = $image['bigpath'];
            }
            
            $temp[$key]['image']  = $this->uploadPic($image);
            
            $temp[$key]['status'] = Book_Type_Status::PUBLISHED;
            
            $temp[$key]['pages']  = isset($detail['pages'])?$detail['pages']:'';
            
            
            $obj = new WareBookbigfieldGetRequest();
            $obj->setSkuId($val);
            $detail = $c->execute($obj);
            $ret = $detail->resp;
            $arr = json_decode($ret,true);
            $info =  isset($arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"])?$arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"]:'';
            $temp[$key]['content_desc'] = isset($info["content_desc"])?trim($info["content_desc"]):''; 
            $temp[$key]['catalog']      = isset($info['catalogue'])?trim($info['catalogue']):'';
            
            if(empty($temp[$key]['content_desc'])){
                $temp[$key]['content_desc'] = $summary;
            }
            
            if(empty($temp[$key]['catalog'])){
                $temp[$key]['catalog']      = $catalog;
            }
            
            //摘要为空时,不取此条数据
            if(empty($temp[$key]['content_desc'])){
                $this->delPic($temp[$key]['image']);
                continue;
            }
    
            $objBook = new Book_Object_Book();
            $id      = $this->getBookIdByISBN($temp[$key]['isbn']);
            if(!empty($id)){
                $objBook->fetch(array('id' => $id));
                //如果要更换图片,得先删除原有图片
                if(isset($temp[$key]['image'])){
                    $this->delPic($objBook->image);
                }
            }
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
            $objBook->save();
            if(empty($id)){
                $objBook->status  = $temp[$key]['status'];
                
                $objSightBook     = new Sight_Object_Book();
                $objSightBook->sightId = $sightId;
                $objSightBook->bookId  = $objBook->id;
                $objSightBook->save();
            }                       
            $key += 1;
        }
        return $temp;
    }

    /**
     * 修改书籍数据
     * @param integer $sightId,景点ID
     * @param integer $id,书籍ID
     * @param array $arrInfo
     * @return boolean
     */
    public function editBook($sightId,$id,$arrInfo){
        $redis   = Base_Redis::getInstance();
        $ret     = false;
        $arr     = $redis->hGetAll(Book_Keys::getBookInfoName($sightId, $id));
        $arrKeys  = array_keys($arr);
        foreach ($arrInfo as $key => $val){
            if(in_array($key,$arrKeys)){
                $arr[$key] = $val;
            }
        }
        $ret = $redis->hMset(Book_Keys::getBookInfoName($sightId, $id),$arr);
        return $ret;
    }
    
    /**
     * 删除书籍数据
     * @param integer $sightId,景点ID
     * @param integer $id,书籍ID
     * @return boolean
     */
    public function delBook($sightId,$id){
        $redis        = Base_Redis::getInstance();
        $picName      = $redis->hget(Book_Keys::getBookInfoName($sightId, $id),'image');
        $id           = $redis->hget(Book_Keys::getBookInfoName($sightId, $id),'isbn');
        $ret      = $redis->delete(Book_Keys::getBookInfoName($sightId, $id));
        return $ret;
    }
    
    public function getBookById($bookId){
        $objBook = new Book_Object_Book();
        $objBook->fetch(array('id' => $bookId));
        return $objBook->toArray();
    }
    
    public function search($query, $page, $pageSize){
        $arrBook  = Base_Search::Search('book', $query, $page, $pageSize, array('id'));
        foreach ($arrBook as $key => $val){
            $book = $this->getBookById($val['id']);
            $arrBook[$key]['name']  = trim($book['title']);
            $arrBook[$key]['desc']  = trim($book['content_desc']);
            $arrBook[$key]['image'] = isset($book['image'])?Base_Image::getUrlByName($book['image']):''; 
        }
        return $arrBook;
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
    
    public static function getBookNum($sightId){
        $listSightBook = new Sight_List_Book();
        $listSightBook->setFilter(array('sight_id' => $sightId));
        $listSightBook->setPagesize(PHP_INT_MAX);
        return $listSightBook->countAll();
    }
}