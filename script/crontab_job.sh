#!/bin/sh
offline=(123.57.46.229)

online=(123.57.67.165 123.57.39.40 182.92.101.94)

entity=(city sight content topic book wiki video)

if [ $1 = "online" ];then
    for ip in ${online[@]};
    do
        for action in ${entity[@]};
        do
            curl http://$ip:8983/solr/$action/dataimport
        done
    done
else
    for ip in ${offline[@]};
    do
        for action in ${entity[@]};
        do
            curl http://$ip:8983/solr/$action/dataimport
        done
    done
fi
