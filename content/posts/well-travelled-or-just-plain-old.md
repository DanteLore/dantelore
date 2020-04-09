
---
title: "Well travelled or just plain old?"

date: "2016-10-04T17:22:12"

featured_image: "http://logicalgenetics.com/wp-content/uploads/2016/10/144-150x150.jpg"
---


A friend of mine has always said that young cars with high mileage are better than old cars with low mileage. The theory being that company cars, which have spent their time cruising on the motorways, have had a much easier life than their stay-at-home cousins who've done short hops around town and sat on their driveways seizing up.

So I pointed some very simple Spark queries at the <a href="http://logicalgenetics.com/predicting-mot-pass-rates-with-spark-mllib/">here</a>). First factoid to note is that both mileage and age are relevant when it comes to predicting pass rates. The following two charts show pass rate vs mileage and age.
## Pass Rate vs Mileage
[d3-source canvas="wpd3-1167-1"]
## Pass Rate vs Age
[d3-source canvas="wpd3-1167-2"]

To look at all three variables together I created the following chart which shows shows age on on the x axis and mileage on the y. Pass rate is a colour scale with red being the worst and green the best. Green squares show combinations of mileage and age at which vehicles are more likely to pass their MOT on the first attempt. Red squares show combinations where a first-try failure is likely.
## Pass Rate vs Mileage and Age
[d3-source canvas="wpd3-1167-3"]

There is some truth to my mate's theory - at least if this chart is to be believed - the pass rate for 3-5 year old cars looks pretty good even at very high mileages. Looking horizontally for very-low-mileage cars of increasing age there seems to be something quite odd going on for vehicles on less than 20k miles. For the 20k-40k range there does seem to be a green stripe across the ages, but it is not as apparent as it's vertical counterpart.

So should we all be buying a four-year-old car with 180k miles on the clock? Well, no. At least not if we want to keep it for more than a year or two. Cars with high mileages on the clock go into the red much earlier than those with low mileage (based on the fact that vehicles can only move *right* and *up* through the chart as they get older and drive further).
## Pass Rate vs Mileage and Age... to the MAX
[d3-source canvas="wpd3-1167-4"]

That last chart shows the same heat-matrix view, but to the full extents of the data. There are some interesting facts hidden in that chart... but I'll leave them as an exercise for the reader!<img class="aligncenter size-thumbnail wp-image-1192" src="http://logicalgenetics.com/wp-content/uploads/2016/10/144-150x150.jpg" alt="144" width="150" height="150">

## UPDATE: Proper Stats:
So it turns out that calculating correlation and covariance with Spark is pretty easy. Here's the results and the code:
<blockquote>**For cars < 20 years and < 250,000 miles**
cov(testMileage, pass) =** -3615.011**
corr(testMileage, pass) = **-0.195**
cov(age, pass) = **-0.401**
corr(age, pass) =** -0.235**
**For all data**
cov(testMileage, pass) =** -3680.0456**
corr(testMileage, pass) =** -0.177**
cov(age, pass) = **-0.383**
corr(age, pass) = **-0.152**</blockquote>
Looking at cars in the "normal" range (i.e. less than 20 years old and less than 250k miles) there's a stronger correlation between age and pass rate than between mileage and pass rate. Interestingly, looking over the full range of the data this relationship is inverted, with mileage being *very slightly* better.  There's little to separate the two as a predictor for pass or fail - not least because age and mileage are largely dependant on each other (with a correlation of **0.277** across all data).

Basic statistical functions are available under **DataFrame.stat**. See the calls hidden in the **println** lines below:
```scala
it should "calculate covariance and correlation for normal cars" in {
val motTests = Spark.sqlContext.read.parquet(parquetData).toDF()
motTests.registerTempTable("mot_tests")

val df = motTests
.filter("testClass like '4%'") // Cars, not buses, bikes etc
.filter("testType = 'N'") // only interested in the first test
.filter("age &amp;amp;amp;amp;lt;= 20")
.filter("testMileage &amp;amp;amp;amp;lt;= 250000")
.withColumn("pass", passCodeToInt(col("testResult")))

println("For cars &amp;amp;amp;amp;lt; 20 years and &amp;amp;amp;amp;lt; 250,000 miles")
println(s"cov(testMileage, pass) = ${df.stat.cov("testMileage", "pass")}")
println(s"corr(testMileage, pass) = ${df.stat.corr("testMileage", "pass")}")

println(s"cov(age, pass) = ${df.stat.cov("age", "pass")}")
println(s"corr(age, pass) = ${df.stat.corr("age", "pass")}")
}

```
