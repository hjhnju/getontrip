<?php
require_once "config.php";
$arrLabel = array();
$fp_label = file(WORK_PATH.INDEX_LABEL_SIGHT);
foreach ($fp_label as $val){
    $arrTemp = explode("\t",$val);
    $arrSub  = explode(":",$arrTemp[1]);
    $arrLabel[$arrTemp[0]]['type'] = Recommend_Type_Label::TAG;
    $arrLabel[$arrTemp[0]]['id'] = $arrSub[1];
}
$date = date("Ymd",time());
$arrFile = file(RESULT_TAG_PATH.$date);
foreach ($arrFile as $data){
    $arrRet = explode(" ",$data);
    sscanf($arrRet[0],"article_%d",$articleId);
    unset($arrRet[0]);
    foreach ($arrRet as $val){
        $rate   = 0;
        $reason = "";
        sscanf($val,"(%d,%f,%[^)])",$labelId,$rate,$reason);
        $objRecomendRet = new Recommend_Object_Result();
        $objRecomendRet->fetch(array('obj_id' => $articleId,'label_id' => $arrLabel[$labelId]['id'],'label_type' => $arrLabel[$labelId]['type']));
        if(!empty($objRecomendRet->id)){
            $objRecomendRet->rate    = $rate;
            $objRecomendRet->reason  = $reason;
            $objRecomendRet->save();
        }else{
            $objRecomendRet->objId     = $articleId;
            $objRecomendRet->labelId   = $arrLabel[$labelId]['id'];
            $objRecomendRet->labelType = $arrLabel[$labelId]['type'];
            $objRecomendRet->rate    = $rate;
            $objRecomendRet->reason  = $reason;
            $objRecomendRet->save();
        }
    }
}