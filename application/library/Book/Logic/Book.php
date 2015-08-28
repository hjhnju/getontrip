<?php
require_once(APP_PATH."/application/library/Base/HtmlDom.php");
class Book_Logic_Book extends Base_Logic{  
    public function __construct(){
        
    }
    
    /**
     * 获取书籍信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getBooks($sightId,$page,$pageSize,$status = Book_Type_Status::PUBLISHED){
        $redis  = Base_Redis::getInstance();
        $from   = ($page-1)*$pageSize+1;
        $to     = $page*$pageSize;
        $ret    = array();
        $arrRet = array();
        $arrBookKeys = array();
        $num    = 1;
        $arrBookKeys = $redis->keys(Book_Keys::getBookInfoName($sightId, "*"));
        $arrBookKeys = $this->keySort($arrBookKeys);
        $count       = count($arrBookKeys);
        foreach ($arrBookKeys as  $key){
            $ret = $redis->hGetAll($key);
            if (($num >= $from)&&($num <= $to)){
                if ($status == Book_Type_Status::ALL || $status == $ret['status']){
                    $ret['totalNum'] = $count;
                    $arrRet[] = $ret;
                }
            }
            $num += 1;
        }
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
        $conf      = new Yaf_Config_INI(CONF_PATH. "/jd.ini");
        $sight     = Sight_Api::getSightById($sightId);
        $name      = trim($sight['name']);
        $totalCount = 0;

        $appKey    = $conf['appKey'];
        $appSecret = $conf['appSecret'];
        
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
        
        //书籍总数
        //$item = $html->find('div.total span strong',0);
        //$totalCount = intval($item->innertext);
        
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
        
        $logicBlack = new Black_Logic_Black();        
        $arrBlackId = $logicBlack->getList(Black_Type_Type::BOOK);
        $key        = 0;
        foreach ($arrIds as $val){
            if (in_array($val,$arrBlackId)) {
                continue;
            }
            $base = new WareProductDetailSearchListGetRequest();
            $base->setSkuId($val);
            $base->setIsLoadWareScore("false");
            $base->setClient("m");           
            $ret = $c->execute($base);
            $temparr = json_decode($ret->resp,true);
           
            $arr   = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['productInfo'];
            $image = $temparr['jingdong_ware_product_detail_search_list_get_responce']['productDetailList']['imagePaths'][0];
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
            
            $temp[$key]['title'] = $arr['wname'];
            
            $temp[$key]['author'] = isset($detail['author'])?$detail['author']:'';
            
            $temp[$key]['price_mart'] = isset($arr['marketPrice'])?$arr['marketPrice']:'';
            
            $temp[$key]['price_jd'] = isset($arr['jdPrice'])?$arr['jdPrice']:'';
            
            $temp[$key]['press'] = isset($detail['publishers'])?$detail['publishers']:'';
            $temp[$key]['isbn'] = isset($detail['isbn'])?$detail['isbn']:'';
            
            
            $temp[$key]['image']  = Base_Image::getUrlByName($this->uploadPic($image['bigpath']));
            
            $temp[$key]['status'] = Book_Type_Status::PUBLISHED;
            $temp[$key]['create_time'] = time();
            
            $temp[$key]['pages']    = isset($detail['pages'])?$detail['pages']:'';
            
            
            $obj = new WareBookbigfieldGetRequest();
            $obj->setSkuId($val);
            $detail = $c->execute($obj);
            $ret = $detail->resp;
            $arr = json_decode($ret,true);
            $info =  $arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"];
            $temp[$key]['content_desc'] = isset($info["content_desc"])?html_entity_decode(strip_tags($info["content_desc"])):'';            
            $temp[$key]['content_desc'] = Base_Util_String::trimall($temp[$key]['content_desc']);
            
            
            $redis = Base_Redis::getInstance();
            $index = ($page-1)*$pageSize+$key+1;
            
            $picName = $redis->hget(Book_Keys::getBookInfoName($sightId, $index),'image');
            if(!empty($picName)){
                $this->delPic($picName);
            }
            $redis->delete(Book_Keys::getBookInfoName($sightId, $index));
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'id',$index);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'title',$temp[$key]['title']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'author',$temp[$key]['author']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'price_mart',$temp[$key]['price_mart']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'price_jd',$temp[$key]['price_jd']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'press',$temp[$key]['press']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'isbn',$temp[$key]['isbn']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'url',$temp[$key]['url']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'image',$temp[$key]['image']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'pages',$temp[$key]['pages']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'content_desc',$temp[$key]['content_desc']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'status',$temp[$key]['status']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'create_time',$temp[$key]['create_time']);
    
            $temp[$key]['id'] = $index;
            if($key >= 10){
                break;
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
        $logicBlack   = new Black_Logic_Black();
        $picName      = $redis->hget(Book_Keys::getBookInfoName($sightId, $id),'image');
        $id           = $redis->hget(Book_Keys::getBookInfoName($sightId, $id),'isbn');
        $logicBlack->addBlack($id, Black_Type_Type::BOOK);
        if(!empty($picName)){
            $this->delPic($picName);
        }
        $ret      = $redis->delete(Book_Keys::getBookInfoName($sightId, $id));
        return $ret;
    }
}