<?php
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
    public function getBooks($sightId,$page,$pageSize,$status=Book_Type_Status::PUBLISHED){
        $redis  = Base_Redis::getInstance();
        $from   = ($page-1)*$pageSize+1;
        $to     = $page*$pageSize;
        $ret    = array();
        $arrRet = array();
        if($status == Book_Type_Status::ALL){
            for($i = $from; $i<=$to; $i++){
                $arrItem = array();
                $ret = $redis->hGetAll(Book_Keys::getBookInfoName($sightId, $i));
                if(empty($ret)){
                    break;
                }
                $arrRet[] = $ret;
            }
        }else{
            $arrBookKeys = $redis->keys(Book_Keys::getBookInfoName($sightId, "*"));
            foreach ($arrBookKeys as $index => $BookKey){
                $ret = $redis->hGetAll($BookKey);
                $num = $index + 1;
                if(($ret['status'] == $status)&&($num >= $from)&&($num <= $to)){
                    $arrRet[] = $ret;
                }
            }
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
        $arrTemp   = array();
        $conf      = new Yaf_Config_INI(CONF_PATH. "/jd.ini");
        $sight     = Sight_Api::getSightById($sightId);
        $query     = trim($sight['name']);

        $appKey    = $conf['appKey'];
        $appSecret = $conf['appSecret'];
        
        $c = new JosClient();
        $c->appkey = $appKey;
        $c->secretKey = $appSecret;
        
        $req = new WareProductSearchListGetRequest();
        $req->setPage($page);
        $req->setPageSize($pageSize);
        $req->setSort(1);
        $req->setKeyword($query."书");
        $req->setIsLoadAverageScore("false");
        $req->setIsLoadPromotion("false");
        $req->setClient("m");
         
        $resp = $c->execute($req);
        $ret = $resp->resp;
        $arr = json_decode($ret,true);
        
        $count = $arr['jingdong_ware_product_search_list_get_responce']['searchProductList']['wareCount'];
        
        if($count){
            $arrTemp = $arr['jingdong_ware_product_search_list_get_responce']['searchProductList']['wareInfo'];
        }else{
            return array();
        }
        $key = 0;        
        foreach ($arrTemp as $val){
            if($val['isBook'] == false){
                continue;
            }
            $temp[$key]['url']       = "http://item.jd.com/".$val['skuId'].".html";
            $detailRequest = new WareBasebookGetRequest();
            $detailRequest->setSkuId($val['skuId']);
            $detail = $c->execute($detailRequest);
            $ret = $detail->resp;
            $arr = json_decode($ret,true);
            $detail =  $arr['jingdong_ware_basebook_get_responce']['BookEntity'][0]['book_info'];
            
            $temp[$key]['title'] = $val['wareName'];
            
            $temp[$key]['author'] = isset($detail['author'])?$detail['author']:'';
            
            $temp[$key]['price_mart'] = isset($val['martPrice'])?$val['martPrice']:'';
            
            $temp[$key]['price_jd'] = isset($val['jdPrice'])?$val['jdPrice']:'';
            
            $temp[$key]['press'] = isset($detail['publishers'])?$detail['publishers']:'';
            $temp[$key]['isbn'] = isset($detail['isbn'])?$detail['isbn']:'';
            
            //此处拿到大图
            $objImage = new WareBaseproductGetRequest();
            $objImage->setSkuId($val['skuId']);
            $objImage->setBase('image_path');
            $tempRet = $c->execute($objImage);
            $tempRet = json_decode($tempRet->resp,true);
            if(isset($tempRet['jingdong_ware_baseproduct_get_responce']['product_base'][0]["image_path"])){
                $temp[$key]['image'] = $tempRet['jingdong_ware_baseproduct_get_responce']['product_base'][0]["image_path"];
            }else{
                $temp[$key]['image'] = $val['imageUrl'];
            }
            $temp[$key]['image']  = Base_Image::getUrlByName($this->uploadPic($temp[$key]['image']));
            
            $temp[$key]['status'] = Book_Type_Status::PUBLISHED;
            $temp[$key]['create_time'] = time();
            
            $temp[$key]['totalNum'] = $count;
            
            $temp[$key]['pages']    = isset($detail['pages'])?$detail['pages']:'';
            
            
            $obj = new WareBookbigfieldGetRequest();
            $obj->setSkuId($val['skuId']);
            $detail = $c->execute($obj);
            $ret = $detail->resp;
            $arr = json_decode($ret,true);
            $info =  $arr["jingdong_ware_bookbigfield_get_responce"]["BookBigFieldEntity"][0]["book_big_field_info"];
            $temp[$key]['content_desc'] = isset($info["content_desc"])?html_entity_decode(strip_tags($info["content_desc"])):'';            
            $temp[$key]['content_desc'] = Base_Util_String::trimall($val['content_desc']);
            
            
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
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'totalNum',$temp[$key]['totalNum']);
    
            $temp[$key]['id'] = $index;
            
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
        $picName  = $redis->hget(Book_Keys::getBookInfoName($sightId, $id),'image');
        if(!empty($picName)){
            $this->delPic($picName);
        }
        $ret      = $redis->delete(Book_Keys::getBookInfoName($sightId, $id));
        return $ret;
    }
}