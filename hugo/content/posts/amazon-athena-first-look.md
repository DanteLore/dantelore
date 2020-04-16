
---
title: "Amazon Athena - First Look"

date: "2017-01-04T07:00:24"

featured_image: "http://logicalgenetics.com/wp-content/uploads/2017/01/athena-150x150.jpg"
---


Amazon recently launched Athena - their answer to Google's Big Query. It's basically an SQL interpreter which runs over files in S3.  It reminds me of Apache Drill, but people round the office say it looks more like Hive.

<img class="noborder aligncenter wp-image-1241 size-thumbnail" src="/images/amazon-athena-first-look/athena-150x150.jpg" alt="AWS Athena is in no way associated with the ancient goddess of wisdom. Any similarity is purely coincidental." width="150" height="150">

The barrier to entry is very low. Upload the data files (CSV, Parquet and JSON are supported, amongst others), define a table, run a query. All this is done using a simple query editor.
# Quick "Hello World"
To test Athena I uploaded some Parquet files, containing data from the <a href="http://prod.publicdata.landregistry.gov.uk.s3-website-eu-west-1.amazonaws.com/pp-complete.csv">open house price dataset</a> to an S3 bucket (I had wanted to load the CSV files "as is" but due to limitations in the CSV reader I couldn't). I then declared a table like so:
```sql
CREATE EXTERNAL TABLE IF NOT EXISTS house_prices.price_paid (
`id` string,
`price` int,
`date` string,
`postcode` string,
`property_type` string,
`old_or_new` string,
`tenure_duration` string,
`address1` string,
`address2` string,
`street` string,
`locality` string,
`town` string,
`district` string,
`county` string,
`ppd_category` string,
`record_status` string,
`month` string
)
ROW FORMAT SERDE 'org.apache.hadoop.hive.ql.io.parquet.serde.ParquetHiveSerDe'
WITH SERDEPROPERTIES (
'serialization.format' = '1'
) LOCATION 's3://logicalgenetics.data/price-paid/'

```
And a few seconds later we're ready to go:
```sql
select town, avg(price) as price
from house_prices.price_paid
group by town
order by price desc

```
```
1	GATWICK	2683329.6666666665
2	THORNHILL	985000.0
3	VIRGINIA WATER	741140.2347652348
4	CHALFONT ST GILES	731333.515394913
5	COBHAM	610556.8430019713
6	BEACONSFIELD	587652.6552173913
7	KESTON	584417.7181571815
8	ESHER	551595.5002180074
9	GERRARDS CROSS	513740.5765843979
10	ASCOT	461468.9531164819

```

# Good Stuff
The ease of setup in simple cases makes this technology very lightweight. If you already have data in S3, you can just start using Athena straight away. It's perfect for ad-hoc querying, sanity checking and QA/test activities.

Athena uses a "server less" model - you pay for the rows you scan - no need to set up a cluster etc. At the time of writing, it's something like $5 per 1TB of data scanned. As with everything on AWS, this is bearable but not exactly cheap.
# Not Good Stuff
At the time of writing, Athena is very new. There are many missing features at the moment, which I hope Amazon will be adding in future.

Firstly, CSV read is limited to pure comma-separated data. Quotes are *not* supported. This is painfully annoying, as almost all CSV data has quotes around string fields. If you have to transform existing CSV data to remove quotes, the cost is going to outweigh any benefit you might have got from doing the direct queries.

The other annoyance to me is the lack of options for saving data back to S3. **select into** and **create as select** style statements are not (yet) supported. This breaks a key use-case for me: the ability to do one-off transforms of legacy or 3rd party data to new file formats. Wouldn't it be nice to take a CSV file, uploaded by a 3rd party, change a few field names, transform to parquet (or JSON or whatever) and save back into your data warehouse? Yes it would. But you can't. Sorry.
# Conclusion
Athena is pretty good if you want a simple tool for doing basic ad-hoc querying over data stored in S3 - provided that data is in a compatible format.

Sadly though, Athena is just not ready for the big time, as yet. With the addition of support for more data formats and the ability to save data back to S3, it could be an incredibly useful tool, but right now I could count the number of use-cases on one hand.

One to watch!