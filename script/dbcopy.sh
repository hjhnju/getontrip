#!/bin/sh
#线上数据向线下数据拷贝

#首先删除线下阿里云中的图片
php freshImage.php del

#然后进行mysql数据的拷贝
mysqldump -hrdsz6zifavbu22m.mysql.rds.aliyuncs.com -P3306 -uxingjiaodai -pAsd7ZeW98-98_1E -n -B ontrip  > db.sql
cat db.sql | mysql -hxingjiaodai.mysql.rds.aliyuncs.com -P3306 -uxingjiaodai -pxingjiaodai

#最后更新线下阿里云中的图片
php freshImage.php add
