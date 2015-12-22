#! /bin/bash
cd `dirname $0`/../
echo "executing path = "`pwd`

dataDir=$HOME"/publish/data/"

# 输入文件
input1=$dataDir"/documents/"
if [ ! -d "$input1" ]; then
    echo "input dir $input1 not exists"
    exit 255
fi
input2=$dataDir"/labels.txt"
if [ ! -f "$input2" ]; then
    echo "input file $input2 not exists"
    exit 255
fi

# 输出文件
if [ -d $dataDir"/profiles.svm" ]; then
    echo "mv $dataDir/profiles.svm $dataDir/backup/"
    mkdir -p $dataDir/backup/
    mv $dataDir/profiles.svm $dataDir/backup/
fi
if [ -f "$dataDir/idf.model" ]; then
    echo "mv $dataDir/idf.model $dataDir/backup/idf.model"
    mkdir -p $dataDir/backup/
    mv $dataDir/idf.model $dataDir/backup/
fi

# 计算偏好特征
echo "executing spark-submit for recommend.ContentBasedProfiling"
spark-submit \
  --class "recommend.ContentBasedProfiling" \
  --master local[4] \
  --executor-memory 4G \
  --driver-memory 4G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  $dataDir/getontrip-sparking_2.10-1.0.jar
