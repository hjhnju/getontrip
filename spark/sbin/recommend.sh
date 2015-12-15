#/bin/bash
cd `dirname $0`/../
echo "executing path = "`pwd`
cp data/newdocs.20151211 data/newdocs.txt
rm -rf data/similarity.out
spark-submit \
  --class "recommend.ContentBasedRecommend" \
  --master local[4] \
  --executor-memory 3G \
  --driver-memory 3G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  target/scala-2.10/getontrip-sparking_2.10-1.0.jar

cp data/similarity.out data/similarity.20151211




