<?php
class Visit_Logic_Visit{
    
    public function __construct(){
        
    }
    
    public function addVisit($type,$device_id,$objId){
        $objVisit           = new Visit_Object_Visit();
        $objVisit->type     = $type;
        $objVisit->deviceId = $device_id;
        $objVisit->objId    = $objId;
        return $objVisit->save();
    }    
}