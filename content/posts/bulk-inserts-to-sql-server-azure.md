
---
title: "Bulk Inserts to SQL Server Azure"

date: "2013-03-01T18:04:05"

featured_image: "http://logicalgenetics.com/wp-content/uploads/2013/03/BCP.png"
---


This has been driving me mad all day, so I'll document it here if only so I don't forget!

SQL Server Azure doesn't support the "traditional" batch insert stuff and you can't just send an SQL file with 50,000+ "insert into..." statements either as the query processor will run out of space.

What you *can* do is run a tool called **BCP**.  This tool is specially designed for loading large datasets into the cloud and is perfect for all your dimension table needs.  The tool takes a tab delimited file as input as well as a huge list of command line parameters.

[sourcecode]C:\>bcp [myDatabase].[dbo].[TableName] in C:\Users\Dan.Taylor\Dropbox\Stuff\DansDataFile.txt -c -U dansUsername
@dansAzureServerName -P <password> -S tcp:dansAzureServerName.database.windows.net -e c:\errors.txt[/sourcecode]

Piping the errored records to a text file is very helpful!

<a href="http://logicalgenetics.com/wp-content/uploads/2013/03/BCP.png"><img src="http://logicalgenetics.com/wp-content/uploads/2013/03/BCP.png"/></a>

The first column in my table is an auto-generated ID, so I make sure that every line in my file starts with a tab.  This basically nulls the first column, letting the database generate an ID as normal.

Also, BCP can not parse dates, times or datetimes from strings.  I haven't found a nice way around this yet - in the end I changed the data type of the column because I don't really care about the date in my dataset very much anyway!  Others have said they created a temporary table and then select/inserted the data over to the real table with a date conversion.