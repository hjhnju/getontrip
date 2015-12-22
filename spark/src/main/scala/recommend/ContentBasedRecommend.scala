package recommend

import java.io.{FileInputStream, ObjectInputStream}

import breeze.linalg.{SparseVector, DenseVector}
import org.apache.spark.mllib.feature.{HashingTF, IDFModel}
import org.apache.spark.mllib.linalg.{Vectors, Vector}
import org.apache.spark.mllib.regression.LabeledPoint
import org.apache.spark.mllib.util.MLUtils
import org.apache.spark.rdd.RDD
import org.apache.spark.{SparkContext, SparkConf}

/**
  * Created by hejunhua on 15/12/10.
  */
object ContentBasedRecommend {

    // 相似度最低值
    val threshold: Double = 0.10

    def main (args: Array[String]) {

        if(args.length < 1) {
            println("arguments: <dataDir> ")
            return
        }
        val dataDir = args.last
        println("[ContentBasedRecommend] data dir = " + dataDir)

        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc   = new SparkContext(conf)

        // 读取各类的偏好特征向量
        val profiles: RDD[LabeledPoint] = MLUtils.loadLibSVMFile(sc, dataDir + "/profiles.libsvm")

        // 读取待验证的文档向量
        val newDocs: RDD[(String, Seq[String])] = sc.textFile(dataDir + "/newdocs.txt").map {
            line =>
                val arrTmp = line.split( """\s+""")
                (arrTmp(0), arrTmp.drop(1).toSeq)
        }

        // 读取model
        val ois = new ObjectInputStream(new FileInputStream(dataDir + "/idf.model"))
        val idfModel = ois.readObject().asInstanceOf[IDFModel]

        // 计算tf
        val hashingTF = new HashingTF(1048356)
        val tf: RDD[Vector] = hashingTF.transform(newDocs.values).map(v => v.toSparse)
        tf.cache()

        // 计算tfidf
        val tfidf: RDD[Vector] = idfModel.transform(tf).map(v => v.toSparse)

        // 合并回去
        val vectors: RDD[(Long, Vector)]  = tfidf.zipWithIndex().map(line => (line._2, line._1))
        val labels: RDD[(Long, String)]   = newDocs.keys.zipWithIndex().map(line => (line._2, line._1))
        val docVsm: RDD[(String, Vector)] = labels.join(vectors).values

        val sims = docVsm.cartesian(profiles).map{ line =>
            val docId = line._1._1
            val docProfile = line._1._2
            val point = line._2

            val v1 = docProfile
            val v2 = point.features
            val cosineSimilarity = this.similarity(v1, v2)

            val reason = ""
            (docId, (point.label.toInt, cosineSimilarity, reason))
        }

        // 过滤掉相似度小的; 推荐标签按相似度降序排序; 无推荐结果不输出
        val filterAndSortedSims = sims.groupByKey().map(x => (x._1, x._2.toArray.filter(_._2 >= this.threshold).sortBy(_._2).reverse)).filter(_._2.length > 0)

        val simOut = filterAndSortedSims.map{x => x._1 + " " + x._2.mkString(" ")}

        simOut.saveAsTextFile(dataDir + "/similarity.out")
    }

    def similarity(v1: Vector, v2: Vector): Double =  {

        val d1 = new DenseVector(v1.toArray)
        val d2 = new DenseVector(v2.toArray)
        val dotProduct = d1.dot(d2)

        val normA = Vectors.norm(v1, 2)
        val normB = Vectors.norm(v2, 2)
        var cosineSimilarity = dotProduct / (normA * normB)
        cosineSimilarity = (math rint cosineSimilarity * 10000) / 10000
        return cosineSimilarity
    }

}
