package processing

import org.apache.spark.mllib.regression.LabeledPoint
import org.apache.spark.mllib.util.MLUtils
import org.apache.spark.rdd.RDD
import org.apache.spark.{SparkContext, SparkConf}

/**
  * Created by hejunhua on 15/12/9.
  */
object MultiClassSpliting {

    def main(args: Array[String]): Unit = {

        //        val defaultConfig = Config()
        //        val parser = new OptionParser[Config]("DecisionTreeTraining") {
        //            head("DecisionTreeTraining: Training a model with DecisionTree")
        //            arg[String]("input")
        //                .text("input path to labeled examples" +
        //                    s" default: ${defaultConfig.input}")
        //                .action((x, c) => c.copy(input = x))
        //            opt[String]("testInput")
        //                .text(s"input path to test dataset." +
        //                    s" default: ${defaultConfig.testInput}")
        //                .action((x, c) => c.copy(testInput = x))
        //        }
        //
        //        parser.parse(args, defaultConfig).map { config =>
        //            run(config)
        //        }.getOrElse {
        //            sys.exit(1)
        //        }

        val defaultConfig   = new Config()
        defaultConfig.input = "data/training.txt"
        run(defaultConfig)
    }

    def run(config: Config) {

        // Load and parse the data file.
        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc = new SparkContext(conf)
        var trainingData: RDD[LabeledPoint] = MLUtils.loadLibSVMFile(sc, config.input)

        // 拆分训练数据为二元分类数据
        val labels = trainingData.map { point => point.label }.collect.distinct
        for (label <- labels) {
            val binaryTrainingData = trainingData.map { point =>
                var binaryLabel = 0
                if (point.label == label) {
                    binaryLabel = 1
                }
                val lp = new LabeledPoint(binaryLabel, point.features)
                lp
            }
            MLUtils.saveAsLibSVMFile(binaryTrainingData, "data/training/" + label.toInt)
        }
    }
}
