#!/bin/bash
#第1列0～numClasses-1
#第2～N列 one-based and asc order
#获取数据
scp -r work@123.57.67.165://home/work/data/ data/

#标签映射
cat data/data/label_index | awk '{sub(/\r/, " ");print $1" "$2;}' > data/labels.txt

#特征映射
cat data/data/feature_sight_index data/data/feature_voc_index | awk '{sub(/\r/, " ");print $1" "$2;}' > data/features.txt 

#训练集
cat data/data/model_total_vector | awk '{$2=$2-1;$1="";sub(/^[[:blank:]]*/,"",$0);print $0;}' > data/training.txt

#将多元分类数据拆分为多个二元分类数据
spark-submit \
  --class "MultiClassSpliting" \
  --master local[4] \
  --executor-memory 8G \
  --driver-memory 8G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  target/scala-2.10/getontrip-sparking_2.10-1.0.jar
