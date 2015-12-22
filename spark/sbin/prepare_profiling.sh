#!/bin/bash
#第1列0～numClasses-1
#第2～N列 one-based and asc order
#获取数据

dataDir=$HOME"/publish/data/"

cd $dataDir

#标签映射
cat $dataDir/label_index | awk '{sub(/\r/, " ");print $1" "$2$3;}' > $dataDir/labels.txt

#分词后的文档集合
tar zxvf documents.tar.gz

docs=$((`ls -l documents | wc -l` - 1))
labels=`cat labels.txt | wc -l`
echo "document classes:"$docs
echo "labels:"$labels
