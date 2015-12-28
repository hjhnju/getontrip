#!/bin/bash
#第1列0～numClasses-1
#第2～N列 one-based and asc order
#获取数据

dataDir=$HOME"/publish/data/"
#dataDir=$HOME"/Dev/getontrip/spark/data/"

cd $dataDir

#标签映射
cat $dataDir/label_sight | awk '{sub(/\r/, " ");print $1" "$2$3;}' > $dataDir/labels_sight.txt
cat $dataDir/label_tag | awk '{sub(/\r/, " ");print $1" "$2$3;}' > $dataDir/labels_tag.txt

#分词后的文档集合
docs=$((`ls -l documents_sight | wc -l` - 1))
labels=`cat labels_sight.txt | wc -l`
echo "sight document classes:"$docs
echo "sight labels:"$labels

docs=$((`ls -l documents_tag | wc -l` - 1))
labels=`cat labels_tag.txt | wc -l`
echo "tag document classes:"$docs
echo "tag labels:"$labels

