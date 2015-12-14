<?php
require_once "config.php";
$arrLabel = array();
$fp_label = file(WORK_PATH.INDEX_LABEL);
foreach ($fp_label as $val){
    $arrTemp = explode("\t",$val);
    $arrSub  = explode(":",$arrTemp[1]);
    if($arrSub[0] == 'sight'){
       $arrLabel[$arrTemp[0]]['type'] = Recommend_Type_Label::SIGHT; 
    }else{
        $arrLabel[$arrTemp[0]]['type'] = Recommend_Type_Label::GENERAL;
    }
    $arrLabel[$arrTemp[0]]['id'] = $arrSub[1];
}
$date = date("Y-m-d",time());
$arrFile = file(RESULT_PATH);
foreach ($arrFile as $data){
    $arrRet = explode(" ",$data);
    $articleId = $arrRet[0];
    unset($arrRet[0]);
    foreach ($arrRet as $val){
        $rate   = 0;
        $reason = "";
        sscanf($val,"(%d,%f,%[^)])",$labelId,$rate,$reason);
        $objRecomendRet = new Recommend_Object_Result();
        $objRecomendRet->objId = $articleId;
        $objRecomendRet->labelId   = $arrLabel[$labelId]['id'];
        $objRecomendRet->labelType = $arrLabel[$labelId]['type'];
        $objRecomendRet->rate    = $rate;
        $objRecomendRet->reason  = $reason;
        $objRecomendRet->save();
    }
}