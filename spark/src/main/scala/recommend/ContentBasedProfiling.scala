package recommend

import java.io.{ObjectOutputStream, FileOutputStream, DataOutputStream}

import breeze.linalg.DenseVector
import org.apache.spark.mllib.feature.{IDF, HashingTF}
import org.apache.spark.mllib.regression.LabeledPoint
import org.apache.spark.mllib.stat.{MultivariateStatisticalSummary, Statistics}
import org.apache.spark.mllib.util.MLUtils
import org.apache.spark.rdd.RDD
import org.apache.spark.{SparkContext, SparkConf}
import org.apache.spark.mllib.linalg.{Vectors, SparseVector, Vector}

/**
  *  基于内容的推荐, 学习景点的喜好特征
  *  Created by hejunhua on 15/12/9.
  */
object ContentBasedProfiling {

    def main (args: Array[String]) {

        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc   = new SparkContext(conf)

        //1. item representation (tf-idf vector)
        var labelMap = Map[String, String]()
        //documents: label, item_list
        var documents: RDD[(String, Seq[String])] = null
        for(row <- scala.io.Source.fromFile("data/labels.txt").getLines.map{line => line.split("""\s+""")}) {
            if (row.length >= 2) {
                labelMap   += (row(0) -> row(1))
                val label   = row(0)
                val newDocs = sc.textFile("data/documents/" + label).map(
                    line => (label, line.split("""\s+""").drop(1).toSeq)
                )
                if(documents == null) {
                    documents = newDocs
                } else {
                    documents = documents.union(newDocs)
                }
            }
        }

        // vector space model
        val vsmDocuments = presentItem(sc, documents)
        vsmDocuments.cache()

        //2. profile learning (这里采用mean of vectors, 广义的可以是分类模型)
        val profiles = profileLearning(sc, vsmDocuments)
        MLUtils.saveAsLibSVMFile(profiles, "data/profiles.libsvm")

        //3. recommend
        // do recommend in ContentBasedRecommend
    }

    /**
      * represent documents with VSM(Vector Space Model)
      * @param sc
      * @param documents
      * @return
      */
    def presentItem(sc: SparkContext, documents: RDD[(String, Seq[String])]): RDD[(String, Vector)] = {

        val hashingTF = new HashingTF(1048356)
        val tf: RDD[Vector] = hashingTF.transform(documents.values).map(v => v.toSparse)
        tf.cache()
        // 最少在两篇文章出现的才计入idf, 否则idf=0
        val idfModel = new IDF(minDocFreq = 2).fit(tf)
        // 保存idf model, 推荐时使用
        val oos = new ObjectOutputStream(new FileOutputStream("data/idf.model"))
        oos.writeObject(idfModel)
        oos.close

        val tfidf: RDD[Vector] = idfModel.transform(tf).map(v => v.toSparse)

        // 合并回去
        val vectors: RDD[(Long, Vector)]  = tfidf.zipWithIndex().map(line => (line._2, line._1))
        val labels: RDD[(Long, String)]   = documents.keys.zipWithIndex().map(line => (line._2, line._1))
        val docVsm: RDD[(String, Vector)] = labels.join(vectors).values

        return docVsm
    }

    /**
      * "偏好特征"的学习, 这里采用一个类别中所有向量的平均值作为该类的偏好特征
      * @param sc
      * @param docVsm
      * @return
      */
    def profileLearning(sc: SparkContext, docVsm: RDD[(String, Vector)]): RDD[LabeledPoint] = {

        // 所有 偏好特征 的集合
        var profiles = Array[LabeledPoint]()

        //仅适用小数据集labelPoints
        val byKey    = docVsm.groupByKey().collect()
        val rddByKey = byKey.map{case (k,v) => k -> sc.makeRDD(v.toSeq)}
        rddByKey.foreach{
            case (k, rdd) =>
                val summary: MultivariateStatisticalSummary = Statistics.colStats(rdd)
                val profile: Vector = summary.mean.toSparse
                profiles +:= LabeledPoint(k.toDouble, profile)
        }

        return sc.makeRDD(profiles)
    }

}
