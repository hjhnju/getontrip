<?php
require_once("env.inc.php");
$base = new Base_Logic();
$oss       = Oss_Adapter::getInstance();
$listKeyword = new Keyword_List_Keyword();
$listKeyword->setPagesize(PHP_INT_MAX);
$arrKeyword  = $listKeyword->toArray();
foreach ($arrKeyword['list'] as $val){
      if(!empty($val['audio'])){
          $fp = fopen("tmp","w");
          $data = $oss->getMeta($val['audio']);
          fwrite($fp, $data);
          $arrInfo['audio_len'] = Base_Audio::getInstance()->getLen("tmp");
          
          
          $keyword = new Keyword_Object_Keyword();
          $keyword->fetch(array('id' => $val['id']));
          $keyword->audioLen = "";
          $keyword->save();
      }
}
unlink("tmp");