#! /bin/bash
# nohup sh sbin/recommend.sh -d 20151214 2>&1 1>nohup.out &
cd `dirname $0`/../
echo "executing path = "`pwd`

#使用说明
function _usage(){
    FILE=`basename $0`
    echo "Execute recommend articles to sights."
    echo "Usage: sh $FILE [-d]"
    echo -e "\t-d YYYYMMDD, is option, default is today=$DATE"
    echo -e "\t-h, this page"
    exit 0
}

DATE=`date +%Y%m%d`
while getopts "d:h" opt
do
    case $opt in
        d)
            DATE=$OPTARG;;
        h)
            _usage;;
    esac
done

# 输入
inputfile=$HOME"/publish/data/newdocs."$DATE
echo "inputfile is $inputfile"

inputProfiles=$HOME"/publish/data/profiles.libsvm"
inputIdfModel=$HOME"/publish/data/idf.model"

if [ ! -f "$inputfile" ]; then
    echo "no input file $inputfile"
    exit 1
fi

echo "cp $inputfile data/newdocs.txt"
cp $inputfile data/newdocs.txt

if [ -d "data/profiles.libsvm" ]; then
    rm -rf data/profiles.libsvm
fi

echo "cp -r $inputProfiles data/profiles.libsvm"
cp -r $inputProfiles data/profiles.libsvm

echo "cp $inputIdfModel data/idf.model"
cp $inputIdfModel data/idf.model

# 输出
outputfile=$HOME"/publish/data/similarity."$DATE
echo "outputfile is $outputfile"

if [ -d "data/similarity.out" ]; then
    echo "rm -rf data/similarity.out"
    rm -rf data/similarity.out
fi

# 执行
echo "executing spark-submit for recommend.ContentBasedRecommend"
spark-submit \
  --class "recommend.ContentBasedRecommend" \
  --master local[4] \
  --executor-memory 4G \
  --driver-memory 4G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  target/scala-2.10/getontrip-sparking_2.10-1.0.jar

echo "cat data/similarity.out/part-* > $outputfile"
cat data/similarity.out/part-* > $outputfile
echo "output file is $outputfile"
