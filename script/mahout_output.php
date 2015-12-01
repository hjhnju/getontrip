<?php
require_once "env.inc.php";
$arrSight = array();
$arrRet = array();
//景点对应的索引文件
$file_labelindex = file("/home/work/var/sight/labelindex.txt");
//索引文件的开头两行及最后一行是无用信息，需去除
$file_labelindex = array_slice($file_labelindex, 2,-1);
foreach ($file_labelindex as $data){
    $tmp = explode(":", $data);
    $arr['id']   = $tmp[3];
    $arr['name'] = $tmp[1];
    $arrSight[]  = $arr;
}

//结果向量
$file_tmpRet     = file("/home/work/var/sight/testing/result.txt");
//结果向量的开关两行及最后一行是无用信息，需去除
$file_tmpRet = array_slice($file_tmpRet, 2,-1);

foreach ($file_tmpRet as $index => $val){
    $temp = explode("Value:",$val);
    $temp[1]  = str_replace("{", "", $temp[1]);
    $str      = str_replace("}", "", $temp[1]);
    
    $arrTmpSight = array();
    $temp = explode(",",$str);
    foreach ($temp as $val){
        $data  = explode(":",$val);
        $arrTmpSight[$data[0]] = abs(doubleval($data[1]));
    }
    asort($arrTmpSight);
    $ret['id']    = $index;
    $ret['sight'] = $arrTmpSight;
    $arrRet[]     = $ret;
}

//最终结果
$file_finalRet   = fopen("/home/work/var/sight/testing/final.result","a");
foreach ($arrRet as $val){
    $arrSightId = array();
    foreach ($val['sight'] as $key => $data){
        $arrSightId[] = $key;
    }
    $strData = implode(",",$arrSightId);
    $str     = sprintf("%s:%s\r\n",$val['id'],$strData);
    fwrite($file_finalRet, $str);
}
fclose($file_finalRet);





