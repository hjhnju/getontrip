DROP TABLE IF EXISTS `sight`;
CREATE TABLE sight (
  id serial PRIMARY KEY, 
  name varchar(50) NOT NULL,
  image varchar(100),
  describe varchar(100),
  level varchar(3) NOT NULL,
  city_id int NOT NULL,
  x double precision,
  y double precision,
  hastopic int NOT NULL DEFAULT 0,
  `create_user` int(11) COMMENT '话题创建人ID',
  `update_user` int(11) COMMENT '话题修改人ID',
  create_time int NOT NULL,
  update_time int NOT NULL
);