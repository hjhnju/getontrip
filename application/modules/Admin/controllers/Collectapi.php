<?php
/**
 * 用户收藏
 * fyy
 */
class CollectapiController extends Base_Controller_Api{
     
     public function init() {
        parent::init();
    }
    
 
    /**
     * list
     *  
     */    
    public function listAction(){  
        //第一条数据的起始位置，比如0代表第一条数据
        //
        $start =isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page = ($start/$pageSize)+1;
 
        $arrParam = isset($_REQUEST['params'])?$_REQUEST['params']:array();
      
        $List =  Collect_Api::getCollectList($page, $pageSize, $arrParam);
        $tmpList=$List['list'];
        
        for($i=0;$i<count($tmpList);$i++){ 
            $tmpList[$i] = $this->getCollectInfo($tmpList[$i]);
            
        }
        
        $List['list'] =  $tmpList;

        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
		return $this->ajax($retList);
         
    }

    public function getCollectInfo($item)
    {   
        $id = $item['obj_id'];
        $obj_info=array();
        //处理类型  、对象信息
        $item['type_name'] = Collect_Type::getTypeName($item['type']);
        switch ($item['type']) {
            case Collect_Type::COTENT: 
                $item['obj_table'] = 'content'; 
                //$obj_info = Topic_Api::getTopicById($id);
                $item['obj_title'] = '';
                break;
            case Collect_Type::BOOK: 
                $item['obj_table'] = 'book'; 
                $obj_info = Book_Api::getBookInfo($id);
                $item['obj_title'] = $obj_info['title'];
                break;
            case Collect_Type::SIGHT: 
                $item['obj_table'] = 'sight'; 
                $obj_info = Sight_Api::getSightById($id);
                $item['obj_title'] = $obj_info['name'];
                break;
            case Collect_Type::CITY: 
                $item['obj_table'] = 'city'; 
                $obj_info = City_Api::getCityById($id);
                $item['obj_title'] = $obj_info['name'];
                break;
            case Collect_Type::TOPIC: 
                $item['obj_table'] = 'topic'; 
                $obj_info = Topic_Api::getTopicById($id);
                $item['obj_title'] = $obj_info['title'];
                break;
            default:
                $type = '';
                $item['obj_table'] = ''; 
                break;
        } 

        return $item;
    }
 
  
}