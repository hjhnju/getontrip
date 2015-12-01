#!/bin/sh
offline=(10.173.13.95)

#online=(10.252.34.151 10.165.112.153 10.171.116.218)
online=(10.252.34.151)

entity=(city sight content topic book wiki video)

if [ $1 = "online" ];then
    for ip in ${online[@]};
    do
        for action in ${entity[@]};
        do
            curl -d "command=delta-import&clean=true&commit=true&wt=json&indent=true&verbose=false&optimize=false&debug=false" http://$ip:8983/solr/$action/dataimport
        done
    done
else
    for ip in ${offline[@]};
    do
        for action in ${entity[@]};
        do
            curl -d "command=delta-import&clean=true&commit=true&wt=json&indent=true&verbose=false&optimize=false&debug=false" http://$ip:8983/solr/$action/dataimport
        done
    done
fi
