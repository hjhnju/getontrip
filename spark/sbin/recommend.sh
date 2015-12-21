#/bin/bash
cd `dirname $0`/../
echo "executing path = "`pwd`

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
INPUTFILE="newdocs."$DATE
echo "inputfile is $INPUTFILE"

tarFile="~/data/work/$INPUTFILE.tar.gz"
if [ -f "$tarFile" ]; then 
    echo "cp $tarFile data/"
    cp $tarFile data/

    echo "tar zxvf $tarFile"
    tar zxvf $tarFile

    echo "mv $INPUTFILE data/"
    mv $INPUTFILE data/
fi

if [ ! -f "data/$INPUTFILE" ]; then
    echo "no input file data/$INPUTFILE"
    exit 1
fi

echo "cp data/$INPUTFILE data/newdocs.txt"
cp data/$INPUTFILE data/newdocs.txt

if [ -d "data/similarity.out" ]; then
    echo "rm -rf data/similarity.out"
    rm -rf data/similarity.out
fi

echo "executing spark-submit for recommend.ContentBasedRecommend"
spark-submit \
  --class "recommend.ContentBasedRecommend" \
  --master local[4] \
  --executor-memory 3G \
  --driver-memory 3G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  target/scala-2.10/getontrip-sparking_2.10-1.0.jar

echo "cp -r data/similarity.out data/similarity.$DATE"
cp -r data/similarity.out data/similarity.$DATE

