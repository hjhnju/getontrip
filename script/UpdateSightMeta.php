<?php
/*更新sight_meta表里面的city_id 字段*/
require_once("env.inc.php"); 
$save_path = '/home/work/user/fanyy/getontrip/mytestlog/';  
$filename = $save_path . "cityid_log.txt"; 


$myfile = fopen($filename, "w");
$content = "日志开始:[" . date('y-m-d h:i:s',time()) . "]\n";
 
fwrite($myfile, $content);
echo $content;

$ret = setCityID($myfile);
 
$content = '日志结束[' . date('y-m-d h:i:s',time()) .']';
fwrite($myfile, $content);
echo $content; 
$content = '总数：'.$ret['totalCount'].',错误数：'.$ret['errorCount'];
fwrite($myfile, $content);
echo $content; 
fclose($myfile);

function setCityID($myfile)
{
    $errorCount = 0;
    $totalCount = 0;
    //查询国外所有的二级城市列表
    $logicCity = new City_Logic_City();
    $logicSight = new Sight_Logic_Meta(); 
    $objSight = new Sight_Object_Meta(); 
    //$arrayParam = array('continentid' => 10000);
    $arrayParam = array('provinceid' => 5035);
    $cityList = $logicCity->queryCity($arrayParam, 1, PHP_INT_MAX); 
    
    for ($i=0; $i < $cityList['total']; $i++) { 
        $cityItem = $cityList['list'][$i];  
        mb_regex_encoding('utf-8');//设置正则替换所用到的编码 
        $cityName = mb_ereg_replace('[市|区|县|盟]', '', $cityItem['name']);//注意这里的和preg_replace不一样 它无需用正则的/xxxxx/这种限定符 直接写主体即可
        $cityName = mb_ereg_replace('[自治州]', '', $cityName); 
        $cityName = mb_ereg_replace('[地区]', '', $cityName); 
        //根据城市名称修改city_id 
        $city_id = $cityItem['id']; 
        //先查找匹配的景点
        $sightList = $logicSight->searchMeta(array('city' => $cityName),1,PHP_INT_MAX);
        for ($j=0; $j < $sightList['total']; $j++) {
            $item = $sightList['list'][$j];  
            foreach ($item as $key => $val){ 
                $objSight->$key = $val; 
            } 
            $objSight->cityId = $city_id;  
            $ret = $objSight->save();
            if (!$ret) { 
                $errorCount++;
                $content = '[' . date('y-m-d h:i:s',time()) .']失败 name:'.$item['name'].',id:'.$item['id'].',city_id:'.$city_id."\n";
            } 
            $totalCount++;
            $content = '[' . date('y-m-d h:i:s',time()) .']成功 name:'.$item['name'].',id:'.$item['id'].',city_id:'.$city_id."\n";
            fwrite($myfile, $content);
            echo $totalCount;
            echo $content; 
        }  
     } 
    return array('totalCount'=>$totalCount,'errorCount'=>$errorCount);
}

