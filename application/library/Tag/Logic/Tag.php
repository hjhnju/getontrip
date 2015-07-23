<?php
class Tag_Logic_Tag{
    public function __construct(){
        
    }
    
    /**
     * 获取标签信息列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getTagList($page, $pageSize){
        $listTag = new Tag_List_Tag();
        $listTag->setPage($page);
        $listTag->setPagesize($pageSize);
        return $listTag->toArray();
    }
    
    /**
     * 获取热门标签列表，根据话题所有的标签数量排序作为热度
     * @param integer $size
     * @return array
     */
    public function getHotTags($size){
        $redis    = Base_Redis::getInstance();
        $arrCount = array();
        $listTag  = new Tag_List_Tag();
        $listTag->setPagesize(PHP_INT_MAX);
        $arrTag = $listTag->toArray();
        foreach ($arrTag['list'] as $val){
            $arrCount[] = $redis->hGet(Tag_Keys::getTagInfoKey($val['id']),'num');
        }
        array_multisort($arrCount, SORT_DESC , $arrTag['list']);
        $arrRet = array_slice($arrTag['list'],0,$size);
        return $arrRet;
    }
    
    /**
     * 编辑标签信息
     * @param integer $id
     * @param string $name
     * @return boolean
     */
    public function editTag($id, $name){
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $id));
        $objTag->name = $name;
        return $objTag->save();
    }
    
    /**
     * 添加标签信息
     * @param string $name
     * @return boolean
     */
    public static function saveTag($name){
        $objTag = new Tag_Object_Tag();
        $objTag->name = $name;
        $ret1 = $objTag->save();
    
        $redis = Base_Redis::getInstance();
        $ret2 = $redis->hSet(Tag_Keys::getTagInfoKey(),$objTag->id,$objTag->name);
        return $ret1&&$ret2;
    }
    
    /**
     * 标签删除接口
     * @param integer $id
     * @return boolean
     */
    public function delTag($id){
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $id));
        $ret1 = $objTag->remove();
    
        $listTopictag = new Topic_List_Tag();
        $listTopictag->setFilter(array('tag_id' => $id));
        $listTopictag->setPagesize(PHP_INT_MAX);
        $arrList = $listTopictag->toArray();
        foreach ($arrList['list'] as $val){
            $objTag = new Topic_Object_Tag();
            $objTag->fetch(array('id'=>$val['id']));
            $objTag->remove();
        }
    
        $redis = Base_Redis::getInstance();
        $ret2 = $redis->hDel(Tag_Keys::getTagInfoKey(),$id);
    
        return $ret1&&$ret2;
    }
}