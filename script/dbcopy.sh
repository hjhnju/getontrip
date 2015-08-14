#!/bin/sh
#线上数据向线下数据拷贝

#首先删除线下阿里云中的图片
php freshImage.php del

#然后进行mysql数据的拷贝
mysqldump -hrdsz6zifavbu22m.mysql.rds.aliyuncs.com -P3306 -uxingjiaodai -pAsd7ZeW98-98_1E -n -B getontrip  > db.sql
cat db.sql | mysql -hxingjiaodai.mysql.rds.aliyuncs.com -P3306 -uxingjiaodai -pxingjiaodai

#然后进行postgresql数据的拷贝
export PGPASSWORD=xingjiaodai

/usr/pgsql-9.4/bin/pg_dump -U xingjiaodai -d getontrip -h 123.57.67.165 -p 5432 -t sight -a > pg.sql
/usr/pgsql-9.4/bin/psql -U xingjiaodai -d getontrip -h 123.57.46.229 -p 5432 -c TRUNCATE TABLE sight 
/usr/pgsql-9.4/bin/psql -U xingjiaodai -d getontrip -h 123.57.46.229 -p 5432 -f pg.sql

#最后更新线下阿里云中的图片
php freshImage.php add
