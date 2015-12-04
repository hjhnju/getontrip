#!/bin/bash  
num=1  
iNum=1  
while(( $num < 5 ))  
do  
    sn=`ps -ef | grep solr | grep -v grep |awk '{print $2}'`  
    echo $sn  
    if [ "${sn}" = "" ]    #如果为空,表示进程未启动  
    then  
        let "iNum++"  
        echo $iNum  
        nohup /home/work/local/solr-5.3.0/bin/solr start -e dih & #后台启动进程  
        echo start ok !  
    fi  
    sleep 60  
done
