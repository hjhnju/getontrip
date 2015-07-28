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
        $conf      = new Yaf_Config_INI(CONF_PATH. "/jd.ini");
        $sight     = Sight_Api::getSightById($sightId);
        $query     = trim($sight[0]['name']);

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
        $temp = $arr['jingdong_ware_product_search_list_get_responce']['searchProductList']['wareInfo'];
        
        foreach ($temp as $key => $val){
            if($val['isBook'] == false){
                unset($temp[$key]);
                continue;
            }
            $temp[$key]['url']       = "http://item.jd.com/".$val['skuId'].".html";
            $temp[$key]['imageUrl']  = $this->uploadPic(self::TYPE_BOOK, $query.$page.$key,$temp[$key]['imageUrl']);
            $detailRequest = new WareBasebookGetRequest();
            $detailRequest->setSkuId($val['skuId']);
            $detail = $c->execute($detailRequest);
            $ret = $detail->resp;
            $arr = json_decode($ret,true);
            $temp[$key]['detail'] =  $arr['jingdong_ware_basebook_get_responce']['BookEntity'][0]['book_info'];
            
            $redis = Base_Redis::getInstance();
            $index = ($page-1)*$pageSize+$key+1;
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'title',$temp[$key]['wareName']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'author',isset($temp[$key]['detail']['author'])?$temp[$key]['detail']['author']:'');
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'price_mart',$temp[$key]['martPrice']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'price_jd',$temp[$key]['jdPrice']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'press',isset($temp[$key]['detail']['publishers'])?$temp[$key]['detail']['publishers']:'');
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'isbn',isset($temp[$key]['detail']['isbn'])?$temp[$key]['detail']['isbn']:'');
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'url',$temp[$key]['url']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'image',$temp[$key]['imageUrl']);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'status',Book_Type_Type::NOTPUBLISHED);
            $redis->hset(Book_Keys::getBookInfoName($sightId, $index),'create_time',time());
            $redis->setTimeout(Book_Keys::getBookInfoName($sightId, $index),self::REDIS_TIME_OUT);
        }
        return $temp;
    }   
}