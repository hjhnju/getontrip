#!/bin/bash  
num=1  
while(( $num < 5 ))  
do  
    sn=`ps -ef | grep solr | grep start |awk '{print $2}'`  
    if [ "${sn}" = "" ]    #如果为空,表示进程未启动  
    then  
        #/home/work/local/solr-5.3.0/bin/solr start -e dih  >> /home/work/crontab.log 2>&1 #启动进程  
        /home/work/user/huwei/var/solr-5.3.0/bin/solr start -e dih >> /home/work/user/huwei/crontab.log 2>&1 #启动进程 
    fi 
    sleep 300 
done
