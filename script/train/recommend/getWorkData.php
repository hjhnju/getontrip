<?php
require_once "config.php";

const PRE_ARTICLE = "article_";

const DEFAULT_PAGESIZE = 500;

if(!is_dir(DATA_PATH)){
    mkdir(DATA_PATH);
}

$date = date("Y-m-d",time());

$fp = fopen(DATA_PATH.$date,"w");

$listArticle = new Recommend_List_Article();
$listArticle->setPagesize(DEFAULT_PAGESIZE);
$totalPage   = $listArticle->getPageTotal();
for($i = 1; $i<= $totalPage; $i ++){
    $listArticle->setPagesize(DEFAULT_PAGESIZE);
    $listArticle->setPage($i);
    $arrArticle  = $listArticle->toArray();
    foreach ($arrArticle['list'] as $val){
        $strBuffer = "";
        $content = preg_replace( '/<.*?>/s', "", $val['title'].$val['content']);
        if(empty($content)){
            continue;
        }
        $arrTopicVoc = Base_Util_String::ChineseAnalyzerAll($content);
        if(!empty($arrTopicVoc)){
            $strTopicVoc = implode("\t",$arrTopicVoc);
            $strBuffer  .= sprintf(PRE_ARTICLE."%d\t%s\r\n",$val['id'],$strTopicVoc);
            fwrite($fp, $strBuffer);
        }
    }
    sleep(1);
}
fclose($fp);