<?php
//postgresql 数据导入mysql
require_once("../env.inc.php");
$model = new SightModel();
$logic    = new Base_Logic();
$arrSight = $model->getSightList(1, PHP_INT_MAX,Sight_Type_Status::ALL);

foreach ($arrSight as $val){
    $objSight = new Sight_Object_Sight();
    foreach ($val as $key => $data){
        $key = $logic->getprop($key);
        $objSight->$key = $data;
    }
    $objSight->save();
}