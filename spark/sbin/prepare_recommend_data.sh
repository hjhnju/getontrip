#!/bin/bash
#第1列0～numClasses-1
#第2～N列 one-based and asc order
#获取数据
mkdir -p data/data
rm -rf data/data/documents
scp -r work@123.57.67.165://home/work/data/documents.tar data/data/ 
scp -r work@123.57.67.165://home/work/data/label_index data/data/ 

#标签映射
cat data/data/label_index | awk '{sub(/\r/, " ");print $1" "$2$3;}' > data/labels.txt

#分词后的文档集合
tar zxvf data/data/documents.tar
mv documents data/
docs=$((`ls -l data/documents | wc -l` - 1))
labels=`cat data/labels.txt | wc -l` 
echo "document classes:"$docs
echo "labels:"$labels
