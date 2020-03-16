
---
title: "Shape Files and SQL Server"
date: 2013-09-23T13:43:15
draft: False
---

Over the last couple of weeks I have been doing a lot of work importing polygons into an SQL server database, using them for some data processing tasks and then exporting the results as KML for display.  I thought it'd be worth a post to record how I did it.

Inserting polygons (or any other geometry type) from a shape file to the database can be done with the <strong>ogr2ogr</strong> tool which ships with the gdal libraries (and with Mapserver for Windows).  I knocked up a little batch file to do it:

[sourcecode language="powershell"]
SET InputShapeFile=&quot;D:\Dropbox\Data\SingleView\Brazillian Polygons\BRA_adm3.shp&quot;

SET SqlConnectionString=&quot;MSSQL:Server=tcp:yourserver.database.windows.net;Database=danTest;Uid=usernname@yourserver.database.windows.net;Pwd=yourpassword;&quot;

SET TEMPFILE=&quot;D:\Dropbox\Data\Temp.shp&quot;
SET OGR2OGR=&quot;C:\ms4w\tools\gdal-ogr\ogr2ogr.exe&quot;
SET TABLENAME=&quot;TestPolygons&quot;

%OGR2OGR% -overwrite -simplify 0.01 %TEMPFILE% %InputShapeFile% -progress

%OGR2OGR% -lco &quot;SHPT=POLYGON&quot; -f &quot;MSSQLSpatial&quot; %SqlConnectionString% %TEMPFILE% -nln %TABLENAME% -progress
[/sourcecode]

The first ogr2ogr call is used to simplify the polygons.  The value 0.01 is the minimum length of an edge (in degrees in this case) to be stored.  Results of this command are pushed to a temporary shape file set. The second call to ogr2ogr pushes the polygons from the temp file up to a database in Windows Azure. The same code would work for a local SQL Server, you just need to tweak the connection string.

You can use SQL Server Management Studio to show the spatial results of your query, which is nice!  Here I just did a "select * from testPolygons" to see the first 5000 polygons from my file.

<a href="http://logicalgenetics.com/wp-content/uploads/2013/09/PolygonsInSqlServer.png"><img src="http://logicalgenetics.com/wp-content/uploads/2013/09/PolygonsInSqlServer.png"/></a>

Sql Server contains all sorts of interesting data processing options, which I'll look at another time.  Here I'll just skip to the final step - exporting the polygon data from the database to a local KML file.

<a href="http://logicalgenetics.com/wp-content/uploads/2013/09/polygonsInKml.jpg"><img src="http://logicalgenetics.com/wp-content/uploads/2013/09/polygonsInKml.jpg"/></a>

[sourcecode language="powershell"]
SET KmlFile=&quot;D:\Dropbox\Data\Brazil.kml&quot;

SET SqlConnectionString=&quot;MSSQL:Server=tcp:yourserver.database.windows.net;Database=danTest;Uid=usernname@yourserver.database.windows.net;Pwd=yourpassword;&quot;

SET TEMPFILE=&quot;D:\Dropbox\Data\Temp.shp&quot;
SET OGR2OGR=&quot;C:\ms4w\tools\gdal-ogr\ogr2ogr.exe&quot;
SET SQL=&quot;select * from TestPolygons&quot;

%OGR2OGR% -lco &quot;SHPT=POLYGON&quot; -f &quot;KML&quot; %KmlFile% -sql %SQL% %SqlConnectionString%  -progress
[/sourcecode]

Obviously you can make the SQL in that command as complex as you like.

Polygons here are from <a href="http://www.diva-gis.org/gdata">this site</a> which allows you to download various polygon datasets for various countries.