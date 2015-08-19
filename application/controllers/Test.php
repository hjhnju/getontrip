<?php
class TestController extends Base_Controller_Api {
    public function init() {
        parent::init();        
    }
    
    /**
     * assign至前端邀请url
     * inviteUrl 用户的专属邀请链接
     * userinfo 左上角信息
     */
    public function indexAction() {       
        //$ret =  Topic_Api::editTopic(3,array('sights' => array(3)));
        //$http = Base_Network_Http::instance();
        //$ret = $http->url("http://baike.baidu.com/search/word?word=鲜花")->exec();
        
        $logic = new Base_Logic();        
        $redis = Base_Redis::getInstance();
        $arrKeys = $redis->keys("video*");
        foreach ($arrKeys as $key){
            $data = $redis->hGetAll($key);
            $name = explode("/",$data['image']);
            $logic->delPic($name[2]);
            $redis->delete($key);
        }
        $arrKeys = $redis->keys("book*");
        foreach ($arrKeys as $key){
            $redis->delete($key);
        }
        $arrKeys = $redis->keys("wiki*");
        foreach ($arrKeys as $key){
            $data = $redis->hGetAll($key);
            $name = explode("/",$data['image']);
            $logic->delPic($name[2]);
            $redis->delete($key);
        }
        $arrKeys = $redis->keys("catalog*");
        foreach ($arrKeys as $key){
            $redis->delete($key);
        }

        //$ret = Topic_Api::getTopicNum(array('sightId'=>1));
        //return $this->ajax($ret);
        //$ret = Video_Api::getVideos(1,1);
        //return $this->ajax($ret);
        //$obj = Spider_Factory::getInstance("Auto", "http://blog.163.com/yanhailiang_123/blog/static/163124545201311175266458/");
        //$obj = Spider_Factory::getInstance("Auto", "<b>aaa",Spider_Type_Source::STRING);
        //$obj      = new Base_Extract('','<b>aaa');
        //$content  = $obj->preProcess();
        //$content  = $obj->dataClean($content,false);
        //var_dump($content); die;   
        //return $this->ajax($obj->getBody());
        //var_dump($ret);die;
        //$model = new GisModel();
        //var_dump($model->getEarthDistanceToPoint(116.318,39.976,116.397,39.9172));
        /*$oss   = Oss_Adapter::getInstance();
        $total = 0;
        $arrRet = $oss->freshTestImage();       
        while(count($arrRet) == 100){
            $total += count($arrRet);
            $arrRet = $oss->freshTestImage($arrRet[99]);
        }
        $total += count($arrRet);
        $this->ajax($total);*/
    }  
}
?>