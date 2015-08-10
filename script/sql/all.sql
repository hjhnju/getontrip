#城市信息表：
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `pinyin` varchar(50) DEFAULT NULL COMMENT '拼音',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父ID 0为省',
  `provinceid` int(11) NOT NULL DEFAULT '0' COMMENT '所属省',
  `cityid` int(11) NOT NULL DEFAULT '0' COMMENT '所属市',
  `x` double NOT NULL COMMENT '经度',
  `y` double NOT NULL COMMENT '纬度',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  index `nid` (`pinyin`),
  index `pid` (`pid`),
  index `province` (`provinceid`),
  index `city` (`cityid`),
  index `nid_pid` (`pinyin`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='城市信息';

#标签信息表：
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标签id',
  `name` varchar(255) NOT NULL  unique COMMENT '标签名称',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签信息表';

#话题标签关系表：
DROP TABLE IF EXISTS `topic_tag`;
CREATE TABLE `topic_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `topic_id` int(11) NOT NULL COMMENT '话题id',
  `tag_id` int(11) NOT NULL COMMENT '标签id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `topicid`(topic_id),
  index `tagid`(tag_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签信息表';

#收藏信息表：
DROP TABLE IF EXISTS `collect`;
CREATE TABLE `collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` tinyint(4) NOT NULL COMMENT '用户ID',
  `type` tinyint(4) NOT NULL COMMENT '收藏类型',
  `obj_id` tinyint(4) NOT NULL COMMENT '收藏对象ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `userid`(user_id),
  index `objid`(obj_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏信息表';


#话题信息表：
DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '话题id', 
  `title` varchar(100) NOT NULL COMMENT '标题',
  `subtitle` varchar(100) COMMENT '副标题',
  `content` text NOT NULL COMMENT '话题内容',
  `desc` varchar(1024) COMMENT '补充描述',
  `image` varchar(255) COMMENT '话题背景图片',
  `user_id` int(11) COMMENT '话题作者ID',
  `from` int(11) NOT NULL COMMENT'话题来源',
  `url` varchar(500) COMMENT '原链接',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '话题状态',
  `x` double NOT NULL COMMENT '经度',
  `y` double NOT NULL COMMENT '纬度',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='话题信息表';

#话题来源表：
DROP TABLE IF EXISTS `source`;
CREATE TABLE `source` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(100) NOT NULL unique COMMENT '来源名称', 
  `url` varchar(500) COMMENT 'URL规则串',
  `type` tinyint(4) NOT NULL DEFAULT 2 COMMENT '来源类型',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `idxname`(name),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='话题来源关系表';

#景点的相关词条信息表：
DROP TABLE IF EXISTS `keyword`;
CREATE TABLE `keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sight_id` int(11) NOT NULL COMMENT '来源名称', 
  `name` varchar(500) NOT NULL COMMENT '词条名',
  `url` varchar(500) NOT NULL COMMENT '词条的网址',
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '状态 1未确认 2已确认'
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `sight_id`(sight_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='景点的相关词条信息表';


#景点话题关系表：
DROP TABLE IF EXISTS `sight_topic`;
CREATE TABLE `sight_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sight_id` int(11) NOT NULL COMMENT '景点id', 
  `topic_id`int(11) NOT NULL COMMENT '话题id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `sight_id`(sight_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='景点话题关系表';


#评论信息表：
DROP TABLE IF EXISTS  `comment`;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `topic_id` int(11) NOT NULL COMMENT '话题id',
  `from_user_id` int(11) NOT NULL COMMENT 'from的用户ID',
  `to_user_id` int(11) NOT NULL COMMENT 'to的用户ID',
  `status` tinyint(4) NOT NULL COMMENT '评论状态',
  `content` varchar(1024) NOT NULL COMMENT '评论内容',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `topic_id`(topic_id),
  index `userid`(from_user_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论信息表';


#主题信息表：
DROP TABLE IF EXISTS  `theme`;
CREATE TABLE `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主题id',
  `name` varchar(255) NOT NULL COMMENT '主题名称',
  `title` varchar(255) COMMENT '主题副标题',
  `image` varchar(255) COMMENT '主题背景图片',
  `content` text COMMENT '主题内容',
  `author` varchar(255) COMMENT '主题作者',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '主题状态',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题信息表';



#景观信息表：
DROP TABLE IF EXISTS  `landscape`;
CREATE TABLE `landscape` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '景观id',
  `city_id` int(11) NOT NULL COMMENT '城市id',
  `name` varchar(255) NOT NULL COMMENT '景观名称',
  `title` varchar(255)  COMMENT '景观标题',
  `image` varchar(255) COMMENT '景观背景图片',
  `content` text COMMENT '景观内容',
  `author` varchar(255) COMMENT '景观作者',
  `x` double NOT NULL COMMENT '经度',
  `y` double NOT NULL COMMENT '纬度',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '景观状态',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='景观信息表';


#主题景观关系表：
DROP TABLE IF EXISTS  `theme_landscape`;
CREATE TABLE `theme_ landscape` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `theme_id` int(11) NOT NULL COMMENT '主题id',
  `landscape_id` int(11) NOT NULL COMMENT '景观id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `theme_sight`( theme_id, landscape_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题景观关系表';


#用户信息表：
DROP TABLE IF EXISTS  `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `device_id` varchar(20) NOT NULL COMMENT '设备ID',
  `nick_name` varchar(100) COMMENT '用户昵称',
  `city_id` int(11) COMMENT '城市ID',
  `image` varchar(255) COMMENT '用户图像',
  `sex` tinyint (3) COMMENT '性别',
  `accept_pic` tinyint(3) default 1 COMMENT '是否无图模式',
  `accept_msg` tinyint (3) default 1 COMMENT '是否关闭消息',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `deviceid`(device_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='用户信息表';


#登录信息表：
DROP TABLE IF EXISTS `login`;
CREATE TABLE `user_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` varchar(20) NOT NULL COMMENT '用户ID',
  `auth_type` tinyint(3) NOT NULL COMMENT '第三方openid类型',
  `open_id` varchar(20) NOT NULL COMMENT 'openid',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `login_time` int(10) NOT NULL COMMENT '最近登录时间',
  index `auth_type_open_id`(auth_type,open_id),
  index `userid`(user_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='登录信息表';


#消息信息表：
DROP TABLE IF EXISTS  `msg`;
CREATE TABLE `msg` (
  `mid` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `sender` int(11) NOT NULL COMMENT '发送人',
  `receiver` int(11) NOT NULL COMMENT '接收人',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息标题',
  `type` varchar(50) DEFAULT NULL COMMENT '消息类型',
  `content` varchar(4096) NOT NULL COMMENT '消息内容',
  `status` tinyint(3) NOT NULL DEFAULT '2' COMMENT '状态 1已读 2未读 -1删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `read_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '阅读时间',
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息';

#管理员信息表：
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `name` varchar(30) NOT NULL COMMENT '用户名',
  `passwd` varchar(20) NOT NULL COMMENT '密码',
  `role` tinyint  NOT NULL COMMENT '用户身份类型',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `login_time` int(10) NOT NULL COMMENT '最近登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员信息表';

#意见反馈表：
DROP TABLE IF EXISTS  `advise`;
CREATE TABLE `advise` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' ID',
  `userid` int(11) NOT NULL COMMENT '发送人',
  `content` varchar(4096) NOT NULL COMMENT '意见内容',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态 ',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deal_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '处理时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈';