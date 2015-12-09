name := "GetOntrip Sparking"
version := "1.0"
scalaVersion := "2.10.4"
libraryDependencies ++= Seq(
"org.apache.spark"  %% "spark-core"   % "1.5.2",
"org.apache.spark"  %% "spark-mllib"  % "1.5.2",
"com.github.scopt" %% "scopt" % "3.3.0"
)

ideaExcludeFolders += ".idea"
ideaExcludeFolders += ".idea_modules"

resolvers += Resolver.sonatypeRepo("public")
