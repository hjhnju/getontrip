<?php
/*更新sight_meta表里面的city_id 字段*/
require_once("env.inc.php"); 
$save_path = '/home/work/user/fanyy/getontrip/mytestlog/';  
$filename = $save_path . "city_meta_log.txt"; 
$myfile = fopen($filename, "w",'utf-8');
$content = "日志开始:[" . date('y-m-d h:i:s',time()) . "]\n";
 
fwrite($myfile, $content);
echo $content;
$ret = setCityList($myfile);
 
$content = '日志结束[' . date('y-m-d h:i:s',time()) .']';
fwrite($myfile, $content);
echo $content; 
$content = '总数：'.$ret['totalCount'].',错误数：'.$ret['errorCount'];
fwrite($myfile, $content);
echo $content; 
fclose($myfile);

function setCityList($myfile){
    $logicSightMeta = new Sight_Logic_Meta();
    $logicCityMeta = new City_Logic_City();
    $countryArray = array();
    $continentArray = array('亚洲');
    //$continentArray = array('亚洲','欧洲','北美洲','大洋洲','南美洲','非洲','南极洲');
    $cityObj_id = 4999;
    $continentid = 10000;
    $countryid = 0;
    $provinceid = 0;
    $objList =array('continent_list'=>array(),'countriy_list'=>array(),'province_list'=>array(),'city_list'=>array());
    
     //1、查询所有大洲列表
    for ($i=0; $i < count($continentArray); $i++) {
       $continentName = $continentArray[$i];
       $cityObj_id++;
       //查询大洲是否存在
       $continent_meta = $logicCityMeta->getCityMeta(array('name'=>$continentName)); 
       if (empty($continent_meta)) { 
           //插入一条大洲信息
           $obj = array(
                      'id'=>$cityObj_id,
                      'name'=>$continentName,
                      'pid'=>0 ,
                      'continentid'=>0,
                      'countryid'=>0,
                      'provinceid'=>0,
                      'cityid'=>0 
                    );
           $continentid = $logicCityMeta->addCityMeta($obj);  
       }else{ 
           $continentid = $continent_meta['id'];
       }
       $content = '大洲 : continentid:'.$continentid.',continentName:'.$continentName."\n"; 
       fwrite($myfile, $content);
       echo $content; 
   
       //根据大洲查询所有国家列表
       $countryList = Sight_Api::getCountryList(array('continent'=>$continentName),1,PHP_INT_MAX);
       $countryArray=array_reverse($countryList['list'],false);
       for ($j=0; $j < count($countryArray); $j++) { 
            $countryItem = $countryArray[$j]; 
            $countryName = $countryItem['country'];
            $cityObj_id++;
            //查询国家是否存在
            $country_meta = $logicCityMeta->getCityMeta(array('name'=>$countryName,'countryid'=>0)); 
            if (empty($country_meta)) { 
               //插入一条国家信息
               $obj = array(
                  'id'=>$cityObj_id,
                  'name'=>$countryName,
                  'pid'=>$continentid,
                  'continentid'=>$continentid,
                  'countryid'=>0,
                  'provinceid'=>0,
                  'cityid'=>0
                );
               $countryid = $logicCityMeta->addCityMeta($obj);
               
            }else{ 
               $countryid = $country_meta['id'];
            } 
            if ($countryItem['country']!='中国') {
                continue;
            } 
            $content = '国家:countryid:'.$countryid.',countryName:'.$countryName."\n"; 
            fwrite($myfile, $content);
            echo $content; 

              //根据国家名称 查询省份列表 开始
            $provinceList = Sight_Api::getProvinceList(array('country'=>$countryName),1,PHP_INT_MAX);
           
            $unknownProvinceList = array();
            $knownProvinceList = array();
            //for ($k=0; $k < count($provinceList['list']); $k++) { 
                //$provinceItem = $provinceList['list'][$k]; 
            foreach ($provinceList['list'] as $key => $provinceItem){ 
                //特殊处理港澳台开始
                if ($provinceItem['province']!='台湾'&&$provinceItem['province']!='香港'&&$provinceItem['province']!='澳门') {
                    $isGangaotai = 0;
                    continue;
                }
                $isGangaotai = 1;//是否是港澳台
                //特殊处理港澳台结束
                
                if ($provinceItem['province']==$provinceItem['city']&&$provinceItem['is_china']==0) {
                    # 国外省份 ,城市==省份名称的默认归属到未知省份
                    $provinceName = '';
                    $cityName = $provinceItem['city'];
                    if (!in_array($cityName,$unknownProvinceList)) {
                       array_push($unknownProvinceList,$cityName); 
                    } 
                }else{ 
                   $provinceName = $provinceItem['province'];
                   echo $provinceName;
                   if (in_array($provinceName, $unknownProvinceList)) {
                       $cityName = $provinceItem['city']; 
                       if (!in_array($cityName,$unknownProvinceList)) {
                           array_push($unknownProvinceList,$cityName); 
                        }  
                   }else{   
                      if (!in_array($provinceName,$knownProvinceList)) {
                           array_push($knownProvinceList,$provinceName); 
                           //特殊处理港澳台
                               if ($isGangaotai==1) {
                                 continue;
                               }
                           //再把当前省份下面的城市全部列归到未知省份里面
                            //根据省份查询城市列表
                            $cityList = Sight_Api::getCityList(array('continent'=>$continentName,'country'=>$countryName,'province'=>$provinceName),1,PHP_INT_MAX);
                            foreach ($cityList['list'] as $key => $value) {
                               array_push($unknownProvinceList,$value['city']);  
                            }
                      }  
                   } 
                }  
            }
            
            //去重  
            foreach ($knownProvinceList as $key => $value) {
                if (in_array($value,$unknownProvinceList)) { 
                    unset($knownProvinceList[$key]); 
                }
            } 
            var_dump($unknownProvinceList);
            var_dump($knownProvinceList);
            //省份列表 结束
            $content = '国家:countryid:'.$countryid.',countryName:'.$countryName.'未知数：'.count($unknownProvinceList).'已知数：'.count($knownProvinceList)."\n"; 
            fwrite($myfile, $content);
            echo $content; 
            //未知省份  城市级别操作 
            $cityObj_id = setunknownProvinceList($myfile,$unknownProvinceList,$cityObj_id,$continentid,$countryid,$continentName,$countryName);
            //已知省份  城市级别操作
            
            $cityObj_id = setknownProvinceList($myfile,$knownProvinceList,$cityObj_id,$continentid,$countryid,$continentName,$countryName);

             
            $content = '总数量'.$cityObj_id."\n"; 
            fwrite($myfile, $content);
            echo $content; 
             
          
        }
    }  
}

//未知省份  城市级别操作
function setunknownProvinceList($myfile,$unknownProvinceList=array(),$cityObj_id,$continentid,$countryid,$continentName,$countryName){
    if (count($unknownProvinceList)==0) {
       return $cityObj_id;
    }
    $logicSightMeta = new Sight_Logic_Meta();
    $logicCityMeta = new City_Logic_City();
    $cityObj_id++;  
    $obj = array(
      'id'=>$cityObj_id,
      'name'=>'未知省份',
      'pid'=>$countryid,
      'continentid'=>$continentid,
      'countryid'=>$countryid,
      'provinceid'=>0,
      'cityid'=>0
    );
    $provinceid = $logicCityMeta->addCityMeta($obj);
 
    //如果是未知省份，则当前省份即为城市直接添加即可 
    foreach ($unknownProvinceList as $key => $provinceName){ 
         //如果是未知省份，则当前省份即为城市直接添加即可
        //$provinceName = $unknownProvinceList[$l];
        $cityObj_id++;  
        //插入一条城市信息
        $obj = array(
          'id'=>$cityObj_id,
          'name'=>$provinceName,
          'pid'=>$provinceid,
          'continentid'=>$continentid,
          'countryid'=>$countryid,
          'provinceid'=>$provinceid, 
          'cityid'=>0
        );
       $cityid = $logicCityMeta->addCityMeta($obj);  
       $content = '未知省份城市:cityid:'.$cityid.',cityName:'.$provinceName."\n"; 
       fwrite($myfile, $content);
       echo $content; 
    }  
    return $cityObj_id; 
}


//已知省份  城市级别操作
function setknownProvinceList($myfile,$knownProvinceList=array(),$cityObj_id,$continentid,$countryid,$continentName,$countryName){
     
    if (count($knownProvinceList)==0) { 
        return $cityObj_id;
    } 
    $logicSightMeta = new Sight_Logic_Meta();
    $logicCityMeta = new City_Logic_City();
    //for ($m=0; $m < count($knownProvinceList); $m++) { 
    foreach ($knownProvinceList as $key => $provinceName){
        //$provinceName = $knownProvinceList[$m];
        $cityObj_id++;
        //查询省份是否存在
        $province_meta = $logicCityMeta->getCityMeta(array('name'=>$provinceName,'countryid'=>$countryid)); 
        if (empty($province_meta)) { 
           //插入一条省份信息
           $obj = array(
              'id'=>$cityObj_id,
              'name'=>$provinceName,
              'pid'=>$countryid,
              'continentid'=>$continentid,
              'countryid'=>$countryid,
              'provinceid'=>0,
              'cityid'=>0
            );
           $provinceid = $logicCityMeta->addCityMeta($obj); 
        }else{ 
           $provinceid = $province_meta['id']; 
        }  
        $content = '省份:provinceid:'.$provinceid.',provinceName:'.$provinceName."\n"; 
        fwrite($myfile, $content);
        echo $content; 

        //根据省份查询城市列表
        $cityList = Sight_Api::getCityList(array('continent'=>$continentName,'country'=>$countryName,'province'=>$provinceName),1,PHP_INT_MAX);
        $content = '国家:'.$countryName.',省份：'.$provinceName.',总城市数：'.$cityList['total']."\n"; 
        fwrite($myfile, $content);
        echo $content; 

        for ($n=0; $n < $cityList['total']; $n++) {  
            $cityItem = $cityList['list'][$n]; 
            $cityName = $cityItem['city'];
            //查询城市是否存在
            $city_meta = $logicCityMeta->getCityMeta(array('name'=>$cityName,'provinceid'=>$provinceid,'cityid'=>0)); 
            if (empty($city_meta)) { 
               $cityObj_id++;
               //插入一条城市信息
               $obj = array(
                  'id'=>$cityObj_id,
                  'name'=>$cityName,
                  'pid'=>$provinceid,
                  'continentid'=>$continentid,
                  'countryid'=>$countryid,
                  'provinceid'=>$provinceid,
                  'cityid'=>0,
                );
                $cityid = $logicCityMeta->addCityMeta($obj); 
                $content = '城市:cityid:'.$cityid.',cityName:'.$cityName."\n"; 
                fwrite($myfile, $content);
                echo $content; 
            }else{ 
               $cityid = $province_meta['id'];
            }
        }
    }  
    return $cityObj_id; 
}





//从数组中查询元素是否存在，并返回该元素
function getValueFromArray($value,$list)
{
    if (in_array($value,$list)) {
        return $value;
    }
    return array();
}

//批量插入数据库
function insertToDb($value='')
{
    # code...
}

