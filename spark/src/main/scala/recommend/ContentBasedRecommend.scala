package recommend

import org.apache.spark.{SparkContext, SparkConf}

/**
  * Created by hejunhua on 15/12/10.
  */
object ContentBasedRecommend {

    def main (args: Array[String]) {

        val conf = new SparkConf().setAppName("GetOntrip Sparking")
        val sc = new SparkContext(conf)

        for(row <- scala.io.Source.fromFile("data/profiles").getLines.map{line => line.split("""\s+""")}) {

        }
    }

}
