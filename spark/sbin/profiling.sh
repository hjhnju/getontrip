#/bin/bash
cd `dirname $0`/../
echo "executing path = "`pwd`

# 输入文件
input="data/documents/"
if [ ! -d "$input" ]; then
    echo "input dir $input not exists"
    exit 255
fi
input="data/labels.txt"
if [ ! -f "$input" ]; then
    echo "input file $input not exists"
    exit 255
fi

# 输出文件
if [ -d "data/profiles.svm" ]; then
    echo "rm -rf data/profiles.svm"
    rm -rf data/profiles.svm
fi
if [ -f "data/idf.model" ]; then
    echo "rm -rf data/idf.model"
    rm -rf data/idf.model
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
  target/scala-2.10/getontrip-sparking_2.10-1.0.jar
