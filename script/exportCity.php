<?php
/*导出cityid=0 的二级城市列表*/

require_once("env.inc.php"); 
$save_path = '/home/work/user/fanyy/getontrip/spider/article/datafiles/';  

$logfilename = $save_path . "cityid_name_log.txt";
$logfile = fopen($logfilename, "w",'utf-8');

$myfilename = $save_path . "cityid_name.txt"; 
$myfile = fopen($myfilename, "w",'utf-8');

$logText = "日志开始:[" . date('y-m-d h:i:s',time()) . "]\n"; 
fwrite($logfile, $logText);
echo $logText;
fwrite($myfile, '[');
$ret = getCityList($myfile,$logfile);
fwrite($myfile, ']'); 
$logText = '日志结束[' . date('y-m-d h:i:s',time()) .']';
fwrite($logfile, $logText);
echo $logText; 

$logText = '总数：'.$ret['totalCount'];
fwrite($logfile, $logText);
echo $logText; 
fclose($logfile);


function getCityList($myfile,$logfile){ 
    $logicCity = new City_Logic_City();
    $arrInfo = array();
    $List = $logicCity->queryCity($arrInfo,1, PHP_INT_MAX);
    $datalist = $List['list']; 

    //循环加入文件
    foreach ($datalist as $key => $item) {
        if ($key==0) {
            $content = '{"id":'.$item['id'].',"name":"'.$item['name'].'"}'."\n"; 
        }
        else{
            $content = ',{"id":'.$item['id'].',"name":"'.$item['name'].'"}'."\n"; 
        } 
        fwrite($myfile, $content);
        $logText = ($key+1).','.$item['id'].','.$item['name']."\n";
        fwrite($logfile, $logText);
        echo $logText;
    }
    return array('totalCount' => $List['total']);
}