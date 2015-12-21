#!/bin/bash
#第1列0～numClasses-1
#第2～N列 one-based and asc order
#获取数据
SRC="/home/work/publish/data/"
mkdir -p data/data

if [ -d data/documents ]; then
    rm -rf data/documents
fi

cp $SRC"documents.tar.gz" data/data/
cp $SRC"label_index" data/data/

#标签映射
cat data/data/label_index | awk '{sub(/\r/, " ");print $1" "$2$3;}' > data/labels.txt

#分词后的文档集合
tar zxvf data/data/documents.tar.gz
mv documents data/
docs=$((`ls -l data/documents | wc -l` - 1))
labels=`cat data/labels.txt | wc -l`
echo "document classes:"$docs
echo "labels:"$labels