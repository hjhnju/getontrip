<?php
require_once("env.inc.php");
ini_set('memory_limit','512M');

$arrItems  = array();
$arrRet    = array();
$arrTemp   = array();
$hash      = '';
require_once(APP_PATH."/application/library/Base/HtmlDom.php");

$listSightMeta = new Sight_List_Meta();
$listSightMeta->setFilter(array('city_id' =>2));
$listSightMeta->setPagesize(PHP_INT_MAX);
$arrSightMeta  = $listSightMeta->toArray();
foreach ($arrSightMeta['list'] as $val){
    if(empty($val['status'])){
        continue;
    }
    $html        = @file_get_html("http://baike.baidu.com/item/".$val['name']);
    if(empty($html)){
        continue;
    }
    $content   = $html->find('div.card-summary-content',0);
    if(empty($content)){
        $content = $html->find('div[class="lemmaWgt-lemmaSummary"]',0);
        if(empty($content)){
            $content = $html->find('div.lemma-summary',0);
            if(empty($content)){
                $content = $html->find('div.para',0);
            }
            $content     = strip_tags($content->innertext);
        }
    }else{
        $content  = strip_tags($content->innertext);
    }
    $content = str_replace("&nbsp;", "", $content);
    if(!empty($content)){
        $objSightMeta = new Sight_Object_Meta();
        $objSightMeta->fetch(array('id' => $val['id']));
        $objSightMeta->wiki = $content;
        $objSightMeta->save();
    }
}