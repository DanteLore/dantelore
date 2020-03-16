
---
title: "Combining Shape Files"
date: 2013-09-10T20:33:13
draft: False
---

This is one of those things that's easy when you know how. Just so I don't forget, here's how to combine shape files using <strong>ogr2ogr</strong>.

I wrote it as a batch file to combine all the OSGB grid squares from the [OS VectorMap District](https://www.ordnancesurvey.co.uk/opendatadownload/products.html) dataset into a single large data file for use with MapServer.

[sourcecode language="powershell"]
echo off

set OGR2OGR=&quot;C:\ms4w\tools\gdal-ogr\ogr2ogr&quot;
set inputdir=&quot;D:\Dropbox\Data\OS VectorMap&quot;
set outputdir=&quot;D:\Dropbox\Data\OS VectorMap Big&quot;

set tiles=(HP HT HU HW HX HY HZ NA NB NC ND NF NG NH NJ NK NL NM NN NO NR NS NT NU NW NX NY NZ OV SC SD SE TA SH SJ SK TF TG SM SN SO SP TL TM SR SS ST TU TQ TR SV SW SX SY SZ TV)

set layers=(Airport AdministrativeBoundary Building ElectricityTransmissionLine Foreshore GlassHouse HeritageSite Land MotorwayJunction NamedPlace PublicAmenity RailwayStation RailwayTrack Road RoadTunnel SpotHeight SurfaceWater_Area SurfaceWater_Line TidalBoundary TidalWater Woodland)

del /Q %outputdir%\*.*

FOR %%L IN %layers% DO (
%OGR2OGR% %outputdir%\%%L.shp %inputdir%\SU_%%L.shp
)

FOR %%T IN %tiles% DO FOR %%L IN %layers% DO (
%OGR2OGR% -update -append %outputdir%\%%L.shp %inputdir%\%%T_%%L.shp -nln %%L
)
[/sourcecode]

After a little map file jiggery-pokery I can now render a huge map of the UK or tiles with smaller maps without the many layer definitions needed to use ~20 shape file sets.

[<img src="http://logicalgenetics.com/wp-content/uploads/2013/09/london-zoomed.jpg"/>](http://logicalgenetics.com/wp-content/uploads/2013/09/london-zoomed.jpg)

[<img src="http://logicalgenetics.com/wp-content/uploads/2013/09/london-big.jpg"/>](http://logicalgenetics.com/wp-content/uploads/2013/09/london-big.jpg)

[<img src="http://logicalgenetics.com/wp-content/uploads/2013/09/uk-big.jpg"/>](http://logicalgenetics.com/wp-content/uploads/2013/09/uk-big.jpg)