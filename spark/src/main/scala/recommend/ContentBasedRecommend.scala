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

    def main (args: Array[String]) {

        if(args.length < 1) {
            println("Arguments: <dataDir> <profiles> <newdocs> <idfmodel> <simout> <threshold> ")
            return
        }
        val dataDir = args(0)
        println("[ContentBasedRecommend] data dir = " + dataDir)

        val libsvmFile   = args(1) // "profiles.libsvm"
        val docsFile     = args(2) // "newdocs.txt"
        val idfModelFile = args(3) // "idf.model"
        val simOutDir    = args(4) // "similarity.out"
        // 相似度最低值
        val threshold    = args(5).toDouble

        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc   = new SparkContext(conf)

        // 读取各类的偏好特征向量
        val profiles: RDD[LabeledPoint] = MLUtils.loadLibSVMFile(sc, libsvmFile)

        // 读取待验证的文档向量
        val newDocs: RDD[(String, Seq[String])] = sc.textFile(docsFile).map {
            line =>
                val arrTmp = line.split( """\s+""")
                (arrTmp(0), arrTmp.drop(1).toSeq)
        }

        // 读取model
        val ois = new ObjectInputStream(new FileInputStream(idfModelFile))
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

        // 笛卡尔乘积
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
        val filterAndSortedSims = sims.groupByKey().map(x => (x._1, x._2.toArray.filter(_._2 >= threshold).sortBy(_._2).reverse)).filter(_._2.length > 0)

        val simOut = filterAndSortedSims.map{x => x._1 + " " + x._2.mkString(" ")}

        simOut.saveAsTextFile(simOutDir)
    }

    def similarity(v1: Vector, v2: Vector): Double =  {

        var d1 = new DenseVector(v1.toArray)
        var d2 = new DenseVector(v2.toArray)

        // 补齐空的维度
        if (d1.length < d2.length) {
            val d3 = DenseVector.zeros[Double](d2.length - d1.length)
            d1 = DenseVector.vertcat(d1, d3)
        } else if (d1.length > d2.length) {
            val d3 = DenseVector.zeros[Double](d1.length - d2.length)
            d2 = DenseVector.vertcat(d2, d3)
        }

        // 点乘必须保证维数一样
        val dotProduct = d1.dot(d2)

        val normA = Vectors.norm(v1, 2)
        val normB = Vectors.norm(v2, 2)
        var cosineSimilarity = dotProduct / (normA * normB)
        cosineSimilarity = (math rint cosineSimilarity * 10000) / 10000
        return cosineSimilarity
    }

}
