
---
title: "Data Engineering in Real Time"
date: 2018-11-21T12:36:28
draft: False
---


This article is part 1 of a mini-series about events and models.&nbsp; The following parts are quite technical, with Kafka, Scala and KSQL code, but this part is a bit of an introduction to the problem at hand.&nbsp; Enjoy!
<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->
I'm a Data Engineer by trade.&nbsp; I've been working with Data Platforms for some years now, usually building tools to ingest and manage data, in a whole heap of industries from supermarket fridges to telecoms networks to insurance websites.&nbsp; Each industry has it's own unique challenges, be it latency, data volume or data quality  but each use-case has ended up being essentially the same...
<!-- wp:quote {"className":"is-style-large"} -->
<blockquote class="wp-block-quote is-style-large">"The fundamental role of a Data Engineer is to translate&nbsp;*<strong>events</strong>*&nbsp;into&nbsp;*<strong>entities</strong>*"<cite>- Dan Taylor, Today<br></cite></blockquote>
<!-- /wp:quote -->
Our lives, the universe, *everything* is simply a stream of events. People buy things on websites, make calls on their phones, order beers, click *like* on cat videos.  Everything that makes us who we are can be encapsulated in a time-ordered change log.  These days, many companies are adopting event-driven architectures to handle the endless firehose of stuff that happens.

<img src="http://logicalgenetics.com/wp-content/uploads/2018/11/Life-Events.jpg"/>

Humans, however, don't think in terms of events - they think in terms of of&nbsp;things*:&nbsp;&nbsp;*How much money do I have? How many customers bought avocados? What's the mobile coverage like in my street?&nbsp;We just can't help but think in terms of *entities* and *attributes*.&nbsp; As a result, pretty much every company still has an old-school BI function; a SQL data warehouse; an enterprise data model (or just an ER diagram on the office wall); overnight batch ETL jobs; daily and monthly reports...&nbsp; These old-school tools help us to rationalise the world in a way that feels natural.

The job of Data Engineers is to collect, filter, clean and store the endless stream of events and to map the changes they represent onto an entity-based model of the 'world'.&nbsp;

<img src="http://logicalgenetics.com/wp-content/uploads/2018/11/EventsToModels.png"/>

What surprises me is that, even though this problem has been around forever (as far back as the 50's and 60's in fact) there is no standard pattern for solving it.&nbsp; As the latency of life decreases and we start to eek out the margin from every single minute and second, the pressure mounts to paint an accurate picture of our customers and systems in real time, 24x7.

### Batch Processing

In the olden days... by which I mean, this still happens now, but when asked about it, people look sheepish and refer to it as "legacy"... anyway, in the olden days conversion from events to models happened as an overnight batch.&nbsp; Take this classic example of a bank...

<img src="http://logicalgenetics.com/wp-content/uploads/2018/11/Batch-vs-Transaction.png"/>

Every night, all reporting operations stop, while a monstrous ETL job aggregates all the transactions from the past 24 hours, adds them to the yesterday's account balances  and updates the master database. Remember when your bank balance changed once a day? 

There's actually nothing wrong with this!&nbsp; Locking users out for a couple of hours while running an overnight  job is a *great* way to ensure that everyone arrives in the office next day to a view of the world which is correct, consistent, stable and reliable.&nbsp; It's perfect for financial reporting, where accuracy is more important than timeliness... but if you want a more up-to-date view, you have to come back tomorrow.

<img src="http://logicalgenetics.com/wp-content/uploads/2018/11/Batch-in-AWS.png"/>

A modern batch process would usually be used to populate bulk data in warehouses and data marts used for financial reporting and long term forecasting.&nbsp; Updating overnight means results are consistent all day, across all reports/departments etc.&nbsp; In a typical AWS deployment, data is tapped from the ingest pipeline and archived to a "data lake" as files in S3.&nbsp; Overnight a series of EMR pipelines pick up, aggregate and transform the raw data, storing the results into a relational database like RedShift.

### Streaming and Event Sourcing

In the front office, things have been changing over the last few years.&nbsp; For all sorts of sensible reasons, the front-end folks are moving away from fat, monolithic applications, desktop clients and complex MVC apps towards lightweight [micro-services](https://martinfowler.com/articles/microservices.html).&nbsp; The single responsibility principal now applies to services not classes, and scores of them are wired together in complex webs promoting re-use, scalability and a clear separation of concerns.

But getting consistent and repeatable results from a complex web of services is hard. Timing starts to play a big part in what happens, as calls happen slightly earlier or later and the state of data in services becomes prone to race conditions and emergent behaviour.  For this reason, the next step on from micro services is  [Event Sourcing](https://martinfowler.com/eaaDev/EventSourcing.html), which uses a central log of events to give consistency, repeatability and traceability.

<img src="http://logicalgenetics.com/wp-content/uploads/2018/11/Micro-Services.png"/>

To cut a long story short, the front office is now sending events to record everything that happens, as it happens, in real time.&nbsp; If Data teams are going to join in the fun, then all our reports, models and snazzy-pants neural networks need to play the same game.&nbsp; We need to be predicting a customer's next action *now*, not overnight.&nbsp; Which means we now need to convert events into models in real time.

### Events to Models in Real Time

The challenge is simple - we need to process a stream of events, ensuring that every change is immediately mapped onto a database entity.&nbsp; The standard, simple, usual way to do this is to just use a fast database as a cache, thus:
<!-- wp:preformatted -->
foreach(event : ProductPurchasedEvent) {<br>    with(new DatabaseTransaction) {<br>        customer = cacheDB.selectOrCreateCustomerRecord(event.customerId)<br>        customer.productsPurchased++<br>        customer.mostRecentProduct = event.productId<br>        cacheDB.saveCustomerRecord(customer)<br>    }<br>}
<!-- /wp:preformatted -->
Every time a customer buys a product, we pull their customer record from the cache database, update the appropriate fields and write the data back.&nbsp; Since we want to avoid race conditions and respect running queries, this is all done under a transaction.

<img src="http://logicalgenetics.com/wp-content/uploads/2018/11/Events-to-Models-Old-School.png"/>

On this plus side, this gives us a database full of up-to-date model data. There are downsides though:
<!-- wp:list -->
<ul><li>Because it forms the centre of all live data access, the cache database needs to be *very* fast, which means very large and very expensive.</li><li>Querying the cache database (for example, to see popular products in the last hour) is the only way to access the live model data, which means we need a technology that supports fast lookup *and* efficient  querying and aggregations.</li><li>If we choose to periodically offload data to secondary stores, data marts and so on, it just gets us  back to the timing issues we had before.&nbsp; Plus the bulk queries to offload data could impact performance.&nbsp;</li><li>For the customer-based example here, this will work fine - if you're lucky enough to have 50 million customers you can afford a big database.&nbsp; However, if you start dealing with real&nbsp;*big data* (like telecoms data), at tens of millions of rows per minute it's  going to blow up.&nbsp;</li><li>All your business logic is hidden away, and tied to a database schema which you're going to find hard to change!</li></ul>
<!-- /wp:list -->
### Kafka, Confluent and the Table/Stream Duality

It seems that there may be a better way though.&nbsp; Over the past couple of years, the folks working on Kafka,  especially those at Confluent, have been looking into this exact problem - how can we turn events to models in real time, without blowing all our cash on servers?

Kafka has, for a long time now, been the worlds greatest data integration tool.&nbsp; It was just a scalable commit log; a queue of things which supported ordering and multiple producers and consumers.&nbsp; It was incredibly reliable and popular, exactly because of its simplicity.

With the addition of Kafka Streams and KSQL, Kafka has grown to become a fully fledged streaming data platform, which provides us with a solution to our events to models problem,  out of the box. You can watch Jay Kreps, one of the original authors of Kafka, talking about this [here](https://www.confluent.io/kafka-summit-SF18/apache-kafka-and-event-oriented-architecture).

In part 2 I'll walk through how I investigated these cool new features and show a demo based on a Beer Festival...

<img src="http://logicalgenetics.com/wp-content/uploads/2018/11/Kafka-Beer.jpg"/>
