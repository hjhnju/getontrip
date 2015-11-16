<?php
class Tongji_Logic_Visit{
    
    public function __construct(){
    }
    
    public function addVisit($type,$objId){
        $objVisit           = new Tongji_Object_Visit();
        $objVisit->type     = $type;
        $objVisit->user     = session_id();
        $objVisit->objId    = $objId;
        return $objVisit->save();
    }    
    
    public function getVisitCount($type,$objId){
        if($type == Collect_Type::TOPIC){
            $type = Tongji_Type_Visit::TOPIC;
        }elseif($type == Collect_Type::BOOK){
            $type = Tongji_Type_Visit::BOOK;
        }
        $listVisit = new Tongji_List_Visit();
        $listVisit->setFilter(array('type' => $type, 'obj_id' => $objId));
        return $listVisit->getTotal();
    }
}