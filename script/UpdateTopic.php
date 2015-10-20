<?php
require_once("env.inc.php");
$sql = <<<EOD
create table if not exists `topic_bak_%` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '话题id', 
  `title` varchar(100) NOT NULL COMMENT '标题',
  `subtitle` varchar(100) COMMENT '副标题',
  `content` text NOT NULL COMMENT '话题内容',
  `desc` varchar(1024) COMMENT '补充描述',
  `image` varchar(255) COMMENT '话题背景图片',
  `from` int(11) NOT NULL COMMENT'话题来源',
  `from_detail` varchar(255) DEFAULT '' COMMENT '来源的详细描述',
  `url` varchar(500) COMMENT '原链接',
  `hot1` int(11) NOT NULL DEFAULT 0 COMMENT '话题热度:7天',
  `hot2` int(11) NOT NULL DEFAULT 0 COMMENT '话题热度:30天',
  `hot3` int(11) NOT NULL DEFAULT 0 COMMENT '话题热度:xxx天，供扩展',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '话题状态',
  `x` double NOT NULL COMMENT '经度',
  `y` double NOT NULL COMMENT '纬度',
  `create_user` int(11) COMMENT '话题创建人ID',
  `update_user` int(11) COMMENT '话题修改人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='话题信息表';
EOD;
$db  = Base_Db::getInstance("getontrip");
$sql = str_replace("%",date('Ymd',time()),$sql);
$db->query($sql);
$listTopic = new Topic_List_Topic();
$listTopic->setPagesize(PHP_INT_MAX);
$arrTopics = $listTopic->toArray();
$count     = 1;
foreach ($arrTopics['list'] as $val){
    $topic = new Topic_Object_Topic();
    $topic->fetch(array('id' => $val['id']));
    $arrTopic = $topic->toArray();
    //数据备份
    $db->insert('topic_bak_'.date('Ymd',time()),$arrTopic);
    
    //旧数据更新
    $obj   = new Base_Extract('',$topic->content);
    $data  = $obj->preProcess();
    $ret   = $obj->dataUpdate($data);
    $topic->content = $ret;
    $topic->save();
    if($count%200 == 0){
        sleep(1);
    }
    $count += 1;
}