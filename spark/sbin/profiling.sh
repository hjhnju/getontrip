#! /bin/bash
cd `dirname $0`/../
echo "executing path = "`pwd`

#使用说明
function _usage(){
    FILE=`basename $0`
    echo "Execute profiling articles to sights/tags."
    echo "Usage: sh $FILE -t sight|tag"
    echo -e "\t-h, this page"
    exit 0
}

target=""
while getopts "t:h" opt
do
    case $opt in
        t)
            target=$OPTARG;;
        h)
            _usage;;
    esac
done

# 输入
dataDir=$HOME"/publish/data"
lablesFile=$dataDir"/labels_"$target".txt"
documentsDir=$dataDir"/documents_"$target

# 输出
profilesDir=$dataDir"/profiles_"$target".libsvm"
idfModelFile=$dataDir"/idf_"$target".model"


# 输入文件
if [ ! -d "$documentsDir" ]; then
    echo "input dir $documentsDir not exists"
    exit 255
fi

if [ ! -f "$lablesFile" ]; then
    echo "input file $lablesFile not exists"
    exit 255
fi

# 输出文件
if [ -d "$profilesDir" ]; then
    echo "mv $profilesDir $dataDir/backup/"
    mkdir -p $dataDir/backup/
    mv $profilesDir $dataDir/backup/
fi
if [ -f "$idfModelFile" ]; then
    echo "mv $idfModelFile $dataDir/backup/"
    mkdir -p $dataDir/backup/
    mv $idfModelFile $dataDir/backup/
fi

# 计算偏好特征
echo "executing spark-submit for recommend.ContentBasedProfiling"
echo "Arguments: <dataDir> <labels> <documents> <idfmodel> <profiles>"
spark-submit \
  --class "recommend.ContentBasedProfiling" \
  --master local[4] \
  --executor-memory 4G \
  --driver-memory 4G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  $dataDir/getontrip-sparking_2.10-1.0.jar \
  $dataDir $lablesFile $documentsDir $idfModelFile $profilesDir
