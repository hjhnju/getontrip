/**
 * 对景点数据进行决策树分类训练，生成模型
 * @author hejunhua
 * @since 2015.12.03
 */

import org.apache.spark.SparkContext
import org.apache.spark.SparkConf
import org.apache.spark.mllib.regression.LabeledPoint
import org.apache.spark.mllib.tree.DecisionTree
import org.apache.spark.mllib.tree.configuration.{Strategy, Algo}
import org.apache.spark.mllib.tree.impurity.Gini
import org.apache.spark.mllib.tree.model.DecisionTreeModel
import org.apache.spark.mllib.util.MLUtils
import org.apache.spark.rdd.RDD
import scopt.OptionParser
import training.Config

import scala.reflect.io.File

class MultiClassDecisionTreeTraining {

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

        // Load and parse the data file.
        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc   = new SparkContext(conf)

        val defaultConfig = new Config()
        run(sc, defaultConfig)
    }

    def run(sc: SparkContext, config: Config) {

        var trainingData = MLUtils.loadLibSVMFile(sc, config.input)
        var testData     = MLUtils.loadLibSVMFile(sc, config.testInput)

        // Train a DecisionTree model.
        // Empty categoricalFeaturesInfo indicates all features are continuous.
        val numClasses = calcNumClasses(trainingData)
        val categoricalFeaturesInfo = Map[Int, Int]()
        val impurity = "gini"
        val maxDepth = 5
        val maxBins  = 32
        val maxMemoryInMB = 8192
        val strategy = new Strategy(Algo.Classification, Gini, maxDepth, numClasses, maxBins = maxBins, categoricalFeaturesInfo = categoricalFeaturesInfo, maxMemoryInMB = maxMemoryInMB)
        val model = DecisionTree.train(trainingData, strategy)

        // Evaluate model on test instances and compute test error
        val labelAndPreds = testData.map { point =>
          val prediction = model.predict(point.features)
            (point.label, prediction)
        }
        val testErr = labelAndPreds.filter(r => r._1 != r._2).count.toDouble / testData.count()
        println("Test Error = " + testErr)
        println("Learned classification tree model:\n" + translate(model.toDebugString))
    }

    /**
      * 保存模型
      * @param sc
      * @param config
      * @param model
      */
    def saveModel(sc: SparkContext, config: Config, model: DecisionTreeModel) = {

        model.save(sc, config.modelPath)

        // 保存描述
        val labelPattern = """(\d+)""".r
        var labelMap = Map[String, String]()
        for(row <- scala.io.Source.fromFile("data/labels.txt").getLines.map{line => line.split(" ")}) {
            if(row.length >= 2) {
                labelMap += (row(0) -> row(1))
            }
        }
        var modelDescPath = config.modelDesc
        modelDescPath = labelPattern.replaceAllIn(modelDescPath, m => m + "(" + labelMap.get(m.group(1)).getOrElse("") + ")")

        File(modelDescPath).writeAll(model.toDebugString)
    }

    /**
    * 计算分类数
    * @param data
    * @return
    */
    def calcNumClasses(data: RDD[LabeledPoint]): Int = {
        val labels     = data.map{ point => point.label }
        val numClasses = labels.collect.distinct.length
        println("Classification numClasses = " + numClasses)
        return numClasses
    }

    /**
    * show features, labels human readable
    * @param desc
    * @return
    */
    def translate(desc: String): String = {
        val featurePattern = """feature (\d+)""".r
        var featureMap = Map[String, String]()
        for(row <- scala.io.Source.fromFile("data/features.txt").getLines.map{line => line.split(" ")}) {
            if(row.length >= 2) {
                featureMap += (row(0) -> row(1))
            }
        }
        var resDesc = featurePattern.replaceAllIn(desc, m => m + "(" + featureMap.get(m.group(1)).getOrElse("") + ")")

        /*
        val labelPattern = """Predict: (\d+).\d+""".r
        var labelMap = Map[String, String]()
        for(row <- scala.io.Source.fromFile("data/labels.txt").getLines.map{line => line.split(" ")}) {
            if(row.length >= 2) {
                labelMap += (row(0) -> row(1))
            }
        }
        resDesc = labelPattern.replaceAllIn(resDesc, m => m + "(" + labelMap.get(m.group(1)).getOrElse(m.group(1)) + ")")
        */
        return resDesc
    }
}
