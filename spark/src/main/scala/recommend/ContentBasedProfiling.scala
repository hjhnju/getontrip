package recommend

import org.apache.spark.mllib.feature.{IDF, HashingTF}
import org.apache.spark.mllib.stat.{MultivariateStatisticalSummary, Statistics}
import org.apache.spark.rdd.RDD
import org.apache.spark.{SparkContext, SparkConf}
import org.apache.spark.mllib.linalg.Vector

/**
  *  基于内容的推荐, 学习景点的喜好特征
  *  Created by hejunhua on 15/12/9.
  */
object ContentBasedProfiling {

    def main (args: Array[String]) {

        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc   = new SparkContext(conf)

        var labelMap = Map[String, String]()
        val profileOutput = scala.tools.nsc.io.File("data/profiles")

        for(row <- scala.io.Source.fromFile("data/labels.top5").getLines.map{line => line.split("""\s+""")}) {
            if (row.length >= 2) {
                labelMap += (row(0) -> row(1))
                val label = row(0)

                //1. item representation (tf-idf vector)
                //input: documents with words
                //output: RDD[Vector]
                val documents: RDD[Seq[String]] = sc.textFile("data/documents/" + label).map(_.split("""\s+""").toSeq)
                val tfidf = presentItem(sc, documents)

                //2. profile learning (这里采用mean of vectors, 广义的可以是分类模型)
                //input: RDD[Vector]
                //output: Vector(using) or Model
                val profile: Vector = profileLearning(tfidf)
                profileOutput.appendAll(profile.toArray.mkString)
                //3. recommend
                // do recommend in ContentBasedRecommend
            }
        }
    }

    /**
      * //1. item representation (tf-idf vector)
      * //input: documents with words
      * //output: RDD[Vector]
      * @param sc
      * @return
      */
    def presentItem(sc: SparkContext, documents: RDD[Seq[String]]): RDD[Vector] = {
        val hashingTF = new HashingTF()
        val tf: RDD[Vector] = hashingTF.transform(documents)
        tf.cache()
        // 最少在两篇文章出现的才计入idf, 否则idf=0
        val idf = new IDF(minDocFreq = 2).fit(tf)
        val tfidf: RDD[Vector] = idf.transform(tf)
        return tfidf
    }

    def profileLearning(tfidf: RDD[Vector]): Vector = {
        //val profileVector = new Vector()
        tfidf.cache()
        val summary: MultivariateStatisticalSummary = Statistics.colStats(tfidf)
        val profileVector: Vector = summary.mean
        return profileVector
    }

}
