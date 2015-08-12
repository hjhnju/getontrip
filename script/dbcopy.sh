#!/bin/sh
#线上数据向线下数据拷贝

#mysql的拷贝
mysqldump -hrdsz6zifavbu22m.mysql.rds.aliyuncs.com -P3306 -uxingjiaodai -pAsd7ZeW98-98_1E getontrip -n -t --replace > db.sql
mysql -hxingjiaodai.mysql.rds.aliyuncs.com -P3306 -uxingjiaodai -pxingjiaodai < db.sql

#postgresql的拷贝
/usr/pgsql-9.4/bin/pg_dump -U xingjiaodai -d getontrip -h 123.57.67.165 -p 5432 -a > pg.sql
/usr/pgsql-9.4/bin/psql -U xingjiaodai -d getontrip -h 123.57.46.229 -p 5432 -f pg.sql
