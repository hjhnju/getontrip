#/bin/bash
classes=`cat data/training.txt | awk '{print $1}' | uniq |wc -l`
echo "总分类:"$classes
