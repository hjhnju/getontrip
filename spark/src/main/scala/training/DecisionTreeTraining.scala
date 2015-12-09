/**
  * 对景点数据进行决策树分类训练，生成模型
  * @author hejunhua
  * @since 2015.12.03
  */

import org.apache.spark.SparkContext
import org.apache.spark.SparkConf
import org.apache.spark.mllib.regression.LabeledPoint
import org.apache.spark.mllib.util.MLUtils
import org.apache.spark.rdd.RDD
import scopt.OptionParser
import training.Config

object DecisionTreeTraining {


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

        val defaultConfig = new Config()

        // Load and parse the data file.
        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc   = new SparkContext(conf)
        run(sc, defaultConfig)
    }

    def run(sc: SparkContext, config: Config) {

        var trainingData: RDD[LabeledPoint] = MLUtils.loadLibSVMFile(sc, config.input)

        // 为每个分类器训练
        val labels = trainingData.map { point => point.label }.collect.distinct
        for (label <- labels) {
            var lb = label.toInt
            val trainer = new MultiClassDecisionTreeTraining()
            val perLabelConfig = new Config()
            perLabelConfig.input     = "data/training/" + label.toInt
            perLabelConfig.testInput = "data/training/" + label.toInt
            perLabelConfig.modelPath = "data/model/" + label.toInt
            perLabelConfig.modelDesc = "data/model/" + label.toInt + ".txt"

            trainer.run(sc, perLabelConfig)
        }
    }
}
