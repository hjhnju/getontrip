#! /bin/bash
# nohup sh sbin/recommend.sh -d 20151214 2>&1 1>nohup.out &
cd `dirname $0`/../
echo "executing path = "`pwd`

#使用说明
function _usage(){
    FILE=`basename $0`
    echo "====================================="
    echo "Execute recommend articles to sights."
    echo "Usage: sh $FILE [-d YYYYMMDD] -t sight|tag"
    echo -e "\t-d YYYYMMDD, is option, default is today=$DATE"
    echo -e "\t-h, this page"
    exit 0
}

DATE=`date +%Y%m%d`
target=""
while getopts "d:t:h" opt
do
    case $opt in
        d)
            DATE=$OPTARG;;
        t)
            target=$OPTARG;;
        h)
            _usage;;
    esac
done


dataDir=$HOME"/publish/data/"
# 输入
newdocsFile=$dataDir"/newdocs."$DATE
inputProfiles=$dataDir"/profiles_"$target".libsvm"
idfModelFile=$dataDir"/idf_"$target".model"
threshold="0.10"
echo "输入文件:"
echo -e "\tnewdocsFile=$newdocsFile"
echo -e "\tinputProfiles=$inputProfiles"
echo -e "\tidfModelFile=$idfModelFile"
echo -e "\tthreshold=$threshold"

# 输出
outputDir=$dataDir"/similarity_"$target".out"
outputFile=$dataDir"/similarity_"$target"."$DATE
echo "输出文件:"
echo -e "outputfile=$outputFile"

if [ ! -f "$newdocsFile" ]; then
    echo "no input file $newdocsFile"
    _usage
    exit 1
fi

if [ ! -d "$inputProfiles" ]; then
    echo "no input profiles $inputProfiles"
    _usage
    exit 1
fi

if [ ! -f "$idfModelFile" ]; then
    echo "no input idfmodel $idfModelFile"
    _usage
    exit 1
fi

if [ -d "$outputDir" ]; then
    echo "rm -rf $outputDir"
    rm -rf "$outputDir"
fi

# 执行
echo "executing spark-submit for recommend.ContentBasedRecommend"
echo "Arguments: <dataDir> <profiles> <newdocs> <idfmodel> <outdir> <threshold>"
echo "Arguments: $dataDir $inputProfiles $newdocsFile $idfModelFile $outputDir $threshold"

spark-submit \
  --class "recommend.ContentBasedRecommend" \
  --master local[4] \
  --executor-memory 4G \
  --driver-memory 4G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  $dataDir/getontrip-sparking_2.10-1.0.jar \
  $dataDir $inputProfiles $newdocsFile $idfModelFile $outputDir $threshold

echo "cat $outputDir/part-* > $outputFile"
cat $outputDir/part-* > $outputFile
echo "output file is $outputFile"
