#! /bin/bash
# nohup sh sbin/recommend.sh -d 20151214 2>&1 1>nohup.out &
cd `dirname $0`/../
echo "executing path = "`pwd`

dataDir=$HOME"/publish/data/"
#dataDir=$HOME"/Dev/getontrip/spark/data/"

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
inputfile=$dataDir"/newdocs."$DATE
echo "inputfile is $inputfile"

inputProfiles=$dataDir"/profiles.libsvm"
inputIdfModel=$dataDir"/idf.model"

if [ ! -f "$inputfile" ]; then
    echo "no input file $inputfile"
    exit 1
fi

echo "cp $inputfile $dataDir/newdocs.txt"
cp $inputfile $dataDir"/newdocs.txt"

if [ ! -d "$inputProfiles" ]; then
    echo "no input profiles $inputProfiles"
    exit 1
fi

if [ ! -f "$inputIdfModel" ]; then
    echo "no input idfmodel $inputIdfModel"
    exit 1
fi

# 输出
outputfile=$dataDir"/similarity."$DATE
echo "outputfile is $outputfile"

if [ -d "$dataDir/similarity.out" ]; then
    echo "rm -rf $dataDir/similarity.out"
    rm -rf "$dataDir/similarity.out"
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
  $dataDir/getontrip-sparking_2.10-1.0.jar \
  $dataDir

echo "cat $dataDir/similarity.out/part-* > $outputfile"
cat $dataDir/similarity.out/part-* > $outputfile
echo "output file is $outputfile"
