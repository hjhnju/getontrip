<?php
require_once("env.inc.php");
ini_set('memory_limit','512M');
require_once(APP_PATH."/application/library/Base/HtmlDom.php");
$listKeyword  = new Keyword_List_Keyword();
$listKeyword->setPagesize(PHP_INT_MAX);
$arrKeyword   = $listKeyword->toArray();
foreach ($arrKeyword['list'] as $val){
    $html        = @file_get_html($val['url']);
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
    if(!empty($content)){
        $content    = Base_Util_String::delStartEmpty($content);
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $val['id']));
        $objKeyword->content = trim($content);
        $objKeyword->save();
    }
}