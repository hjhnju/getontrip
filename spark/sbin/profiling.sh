#/bin/bash
cd `dirname $0`/../
echo "executing path = "`pwd`
rm -rf data/profiles
rm -rf data/labelrdds
spark-submit \
  --class "recommend.ContentBasedProfiling" \
  --master local[4] \
  --executor-memory 3G \
  --driver-memory 3G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  target/scala-2.10/getontrip-sparking_2.10-1.0.jar
