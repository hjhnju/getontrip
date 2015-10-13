<?php
class Tongji_Logic_Visit{
    
    public function __construct(){
    }
    
    public function addVisit($type,$objId){
        $objVisit           = new Tongji_Object_Visit();
        $objVisit->type     = $type;
        $objVisit->userId   = User_Api::getCurrentUser();
        $objVisit->objId    = $objId;
        return $objVisit->save();
    }    
}