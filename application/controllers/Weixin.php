<?php

//class WeixinController extends Yaf_Controller_Abstract {
class WeixinController extends Base_Controller_Abstract {

    //define your token
    const TOKEN = "GetontripWeixin123";

    public function init() {
        $this->setNeedLogin(false);
        parent::init();
        //接入时要验证
        $this->valid();
    }

    private function valid() {
        //接入时要验证
        $echoStr = isset($_GET["echostr"]) ? $_GET["echostr"] : "";
        if(!empty($echoStr)) {
            //valid signature , option
            if($this->checkSignature()){
                echo $echoStr;
                exit;
            }
        }
    }

    public function searchAction(){
        $object = new Sight_Object_Sight();
        $object->fetch(array("name"=>"北海公园"));
        var_dump($object->id);
        exit;
    }

    //消息回复
    public function indexAction()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        Base_Log::notice(array("postStr"=>$postStr));

        //extract post data
        if (!empty($postStr)){
           /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
              the best way is to check the validity of xml by yourself */
           libxml_disable_entity_loader(true);
           $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
           $fromUsername = $postObj->FromUserName;
           $toUsername = $postObj->ToUserName;
           $keyword = trim($postObj->Content);
           $event   = trim($postObj->Event);
           $eventkey = trim($postObj->EventKey);
           $time = time();
           $textTpl = "<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                       <MsgType><![CDATA[%s]]></MsgType>
                       <Content><![CDATA[%s]]></Content>
                       <FuncFlag>0</FuncFlag>
                       </xml>";
           if($event == "subscribe"){
               $msgType = "text";
               $contentStr = "你来了～ 小途带你了解旅行目的地的历史文化，人文地理和风土人情。\n\n带上旅行的百科全书，亦行亦读，走万里路读万卷书。\n\n直接戳菜单 或 输入城市名、景点名 看看有没惊喜[坏笑][胜利]";
               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
               echo $resultStr;
           }

           Base_Log::notice(array('event'=>$event, 'eventkey'=>$eventkey));
           if($event == "CLICK"){
               if($eventkey == "SEARCH"){
                   $msgType = "text";
                   $contentStr = '回复景点名称，如<a href="http://www.getontrip.cn/m/sight?id=7">“颐和园”</a>, <a href="http://www.getontrip.cn/m/sight?id=23">“梅里雪山”</a> 马上获取语音讲解、精彩故事。';
                   $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                   echo $resultStr;
               }
               exit;
           }

           if(!empty($keyword)) {
               $msgType = "text";

               //若是景点
               $object = new Sight_Object_Sight();
               $object->fetch(array("name"=>$keyword));
               $destId = $object->id;
               if(!empty($destId)){
                   $contentTpl = '点击 <a href="http://www.getontrip.cn/m/sight?id=%s">“%s”</a> 获取景观导游、有趣话题、必吃必玩。';
                   $contentStr = sprintf($contentTpl, $destId, $keyword);
                   $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                   echo $resultStr;
                   exit;
               }
               
               //若是城市
               //$destId = 1;
               //$contentTpl = '点击 <a href="http://www.getontrip.cn/m/sight/guide?id=%s">“%s”</a> 获取景点导游、有趣话题、必吃必玩。';
               //$contentStr = sprintf($contentTpl, $destId, $keyword);

               $contentStr = "小途会跟进你的需求哦[坏笑]";
               $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
               echo $resultStr;
               exit;
           }else{
               echo "Input something...";
           }

        }else {
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : "";
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"]: "";
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : "";

        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}

