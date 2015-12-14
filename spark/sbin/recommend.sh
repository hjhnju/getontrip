#/bin/bash
rm -rf data/similarity.out
spark-submit \
  --class "recommend.ContentBasedRecommend" \
  --master local[4] \
  --executor-memory 8G \
  --driver-memory 8G \
  --conf spark.shuffle.spill=false \
  --conf "spark.executor.extraJavaOptions=-XX:+PrintGCDetails -XX:+PrintGCTimeStamps" \
  target/scala-2.10/getontrip-sparking_2.10-1.0.jar

