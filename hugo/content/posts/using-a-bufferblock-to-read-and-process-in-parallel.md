
---
title: "Using a BufferBlock to Read and process in Parallel"

date: "2013-03-19T18:22:19"

featured_image: "/images/using-a-bufferblock-to-read-and-process-in-parallel/"
---


Wrote an app this week - top secret of course - to load data from a database and process the contents.  The reading from the database is the slow part and the processing takes slightly less time. I decided it might help if I could read a batch of results into memory and process it while loading the next batch. 

Batching was dead easy, I found an excellent extension method <a href="http://josheinstein.com/blog/index.php/2009/12/ienumerable-batch/">on the internet</a> that batches up an enumerable and yields you a sequence of arrays.  The code looks like this, in case you can't be bothered to click the link:
```csharp
public static IEnumerable<T[]> Batch<T>(this IEnumerable<T> sequence, int batchSize)
{
    var batch = new List<T>(batchSize);

    foreach (var item in sequence)
    {
        batch.Add(item);

        if (batch.Count >= batchSize)
        {
            yield return batch.ToArray();
            batch.Clear();
        }   
    }  

    if (batch.Count > 0)
    {
        yield return batch.ToArray();
        batch.Clear();
    }  
}
```
That works really well, but it doesn't give me the parallel read and process I'm looking for. After a large amount of research, some help from an esteemed colleague and quite a bit of inappropriate language, I ended up with the following. It uses the **BufferBlock** class which is a new thing from <a href="http://msdn.microsoft.com/en-gb/library/hh228604.aspx">Microsoft's new Dataflow Pipeline libraries</a> (which provide all sorts of very useful stuff which I may well write an article on at a later date).  The BufferBlock marshals data over thread boundaries in a very clean and simple way.
```csharp
public static IEnumerable<T[]> BatchAsync<T>(this IEnumerable<T> sequence, int batchSize)
{
    BufferBlock<T[]> buffer = new BufferBlock<T[]>();

    var reader = new Thread(() =>
        {
            foreach (var batch in sequence.Batch(batchSize))
            {
                buffer.Post(batch);
            }
            buffer.Post(null);
            buffer.Complete();
        }) { Name = "Batch Reader Async" };
    reader.Start();

    T[] blocktoProcess;
    while ((blocktoProcess = buffer.Receive()) != null)
    {
        yield return blocktoProcess;
    }
}
```
The database read is done on a new thread and data is pulled back to the calling thread in batches.  This makes for nice clean code on the consumer side!