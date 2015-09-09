#﻿城市信息表：
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

#﻿标签信息表：
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标签id',
  `name` varchar(255) NOT NULL  unique COMMENT '标签名称',
  `create_user` int(11) COMMENT '标签创建人ID',
  `update_user` int(11) COMMENT '标签修改人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签信息表';

#﻿话题标签关系表：
DROP TABLE IF EXISTS `topic_tag`;
CREATE TABLE `topic_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `topic_id` int(11) NOT NULL COMMENT '话题id',
  `tag_id` int(11) NOT NULL COMMENT '标签id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `topicid`(topic_id),
  index `tagid`(tag_id),
  unique index `topic_tag`(topic_id, tag_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签信息表';

#﻿收藏信息表：
DROP TABLE IF EXISTS `collect`;
CREATE TABLE `collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` tinyint(4) NOT NULL COMMENT '用户ID',
  `type` tinyint(4) NOT NULL COMMENT '收藏类型',
  `obj_id` tinyint(4) NOT NULL COMMENT '收藏对象ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  UNIQUE `collect_type_user` (`user_id`, `type`, `obj_id`) ,  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏信息表';

#﻿访问信息表：
DROP TABLE IF EXISTS `visit`;
CREATE TABLE `visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `device_id` int(11) NOT NULL COMMENT '设备ID',
  `type` tinyint(4) NOT NULL COMMENT '访问类型类型，1:话题详情',
  `obj_id` varchar(20) NOT NULL COMMENT '访问对象ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `deviceid`(device_id),
  index `objid`(obj_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问信息表';


#﻿话题信息表：
DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '话题id', 
  `title` varchar(100) NOT NULL COMMENT '标题',
  `subtitle` varchar(100) COMMENT '副标题',
  `content` text NOT NULL COMMENT '话题内容',
  `desc` varchar(1024) COMMENT '补充描述',
  `image` varchar(255) COMMENT '话题背景图片',
  `create_user` int(11) COMMENT '话题创建人ID',
  `update_user` int(11) COMMENT '话题修改人ID',
  `from` int(11) NOT NULL COMMENT'话题来源',
  `from_detail` varchar(255) DEFAULT '' COMMENT '来源的详细描述';
  `url` varchar(500) COMMENT '原链接',
  `hot1` int(11) NOT NULL DEFAULT 0 COMMENT '话题热度:7天',
  `hot2` int(11) NOT NULL DEFAULT 0 COMMENT '话题热度:30天',
  `hot3` int(11) NOT NULL DEFAULT 0 COMMENT '话题热度:xxx天，供扩展',
  `status` tinyint(4) NOT NULL COMMENT DEFAULT 1'话题状态',
  `x` double NOT NULL COMMENT '经度',
  `y` double NOT NULL COMMENT '纬度',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='话题信息表';

#﻿话题来源表：
DROP TABLE IF EXISTS `source`;
CREATE TABLE `source` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(100) NOT NULL unique COMMENT '来源名称', 
  `url` varchar(500) COMMENT 'URL规则串',
  `type` tinyint(4) NOT NULL DEFAULT 2 COMMENT '来源类型,1:微信公众号，2:其他',
  `create_user` int(11) COMMENT '来源创建人ID',
  `update_user` int(11) COMMENT '来源修改人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `idxname`(name),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='话题来源关系表';


#﻿景点的相关词条信息表：
DROP TABLE IF EXISTS `keyword`;
CREATE TABLE `keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sight_id` int(11) NOT NULL COMMENT '景点ID', 
  `name` varchar(500) NOT NULL COMMENT '词条名',
  `url` varchar(500) NOT NULL COMMENT '词条的网址',
  `weight` tinyint(3) NOT NULL DEFAULT 1 COMMENT '权重值，权重为1的是主词条'，
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '状态 1未确认 2已确认'，
  `x` double COMMENT '经度',
  `y` double COMMENT '纬度',
  `create_user` int(11) COMMENT '词条创建人ID',
  `update_user` int(11) COMMENT '词条修改人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `sight_id`(sight_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='景点的相关词条信息表';


#﻿景点话题关系表：
DROP TABLE IF EXISTS `sight_topic`;
CREATE TABLE `sight_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sight_id` int(11) NOT NULL COMMENT '景点id', 
  `topic_id`int(11) NOT NULL COMMENT '话题id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `sight_id`(sight_id),
  unique index `sight_topic`(sight_id, topic_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='景点话题关系表';




#﻿评论信息表：
DROP TABLE IF EXISTS  `comment`;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `up_id` int(11) NOT NULL DEFAULT 0 COMMENT '上级评论id',
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


#﻿主题信息表：
DROP TABLE IF EXISTS  `theme`;
CREATE TABLE `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主题id',
  `name` varchar(255) NOT NULL COMMENT '主题名称',
  `title` varchar(255) COMMENT '主题副标题',
  `image` varchar(255) COMMENT '主题背景图片',
  `content` text COMMENT '主题内容',
  `period` smallint(11) NOT NULL DEFAULT 1 COMMENT '主题期数',
  `author` varchar(255) COMMENT '主题作者',
  `create_user` int(11) COMMENT '主题创建人ID',
  `update_user` int(11) COMMENT '主题修改人ID',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '主题状态',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题信息表';



#﻿景观信息表：
DROP TABLE IF EXISTS  `landscape`;
CREATE TABLE `landscape` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '景观id',
  `city_id` int(11) NOT NULL COMMENT '城市id',
  `name` varchar(255) NOT NULL COMMENT '景观名称',
  `title` varchar(255)  COMMENT '景观标题',
  `image` varchar(255) COMMENT '景观背景图片',
  `content` text COMMENT '景观内容',
  `author` varchar(255) COMMENT '景观作者',
  `create_user` int(11) COMMENT '标签创建人ID',
  `update_user` int(11) COMMENT '标签修改人ID',
  `x` double NOT NULL COMMENT '经度',
  `y` double NOT NULL COMMENT '纬度',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '景观状态',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='景观信息表';


#﻿主题景观关系表：
DROP TABLE IF EXISTS  `theme_landscape`;
CREATE TABLE `theme_ landscape` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `theme_id` int(11) NOT NULL COMMENT '主题id',
  `landscape_id` int(11) NOT NULL COMMENT '景观id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  unique index `theme_sight`( theme_id, landscape_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题景观关系表';


#﻿用户信息表：
DROP TABLE IF EXISTS  `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `device_id` varchar(20) NOT NULL COMMENT '设备ID',
  `nick_name` varchar(100) COMMENT '用户昵称',
  `city_id` int(11) COMMENT '城市ID',
  `image` varchar(255) COMMENT '用户图像',
  `sex` tinyint (3) COMMENT '性别，1:男性,2:女性',
  `accept_pic` tinyint(3) default 1 COMMENT '是否无图模式',
  `accept_msg` tinyint (3) default 1 COMMENT '是否关闭消息',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  index `deviceid`(device_id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='用户信息表';


#﻿登录信息表：
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


#﻿消息信息表：
DROP TABLE IF EXISTS  `msg`;
CREATE TABLE `msg` (
  `mid` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `sender` int(11) NOT NULL COMMENT '发送人',
  `receiver` int(11) NOT NULL COMMENT '接收人',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息标题',
  `type` tinyint(3) DEFAULT NULL COMMENT '消息类型',
  `content` varchar(4096) NOT NULL COMMENT '消息内容',
  `attach` varchar(1024) COMMENT '消息的附加信息',
  `image` varchar(30) COMMENT '消息图片',
  `status` tinyint(3) NOT NULL DEFAULT '2' COMMENT '状态 1已读 2未读 3删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息';

#﻿管理员信息表：
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

#﻿意见反馈表：
DROP TABLE IF EXISTS  `advise`;
CREATE TABLE `advise` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' ID',
  `userid` int(11) NOT NULL COMMENT '发送人ID或反馈ID',
  `content` varchar(4096) NOT NULL COMMENT '意见内容',
  `type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '类型，1:提问,2:回答',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态 ',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_user` int(10) NOT NULL COMMENT '创建人',
  `update_user` int(10) NOT NULL COMMENT '更新人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈';

#﻿黑名单信息表：
DROP TABLE IF EXISTS `black`;
CREATE TABLE `black` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` tinyint(4) NOT NULL COMMENT '类型，1:视频,2:书籍',
  `obj_id` varchar(20) NOT NULL COMMENT '访问对象ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `create_user` int(10) NOT NULL COMMENT '创建人',
  `update_user` int(10) NOT NULL COMMENT '更新人',
  unique index `objtype`(obj_id,type),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='黑名单信息表';
