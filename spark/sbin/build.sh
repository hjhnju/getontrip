#! /bin/bash
cd `dirname $0`/../
echo "executing path = "`pwd`

dataDir=$HOME/publish/data/
#dataDir=$HOME/Dev/getontrip/spark/data

#build
sbt package
echo "sbt package"

#cp to target
cp target/scala-2.10/getontrip-sparking_2.10-1.0.jar $dataDir
echo "cp target/scala-2.10/getontrip-sparking_2.10-1.0.jar $dataDir"
