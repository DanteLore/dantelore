
---
title: "Mapserver Revisited"
date: 2012-11-08T09:16:38
draft: False
---

Years ago, before they invented Google Earth and Bing Maps and all that, I did some work to show GPS data using Mapserver.  At work yesterday I was forced to revisit it while maintaining an aged and creaking part of our product.  It took a while to get my head back into the right state but once I'd got going I started to enjoy it again.  This time around, because I'm at work, I'm using <a href="http://spatialhorizons.com/2007/05/19/mapserver-1-10-minute-tutorial/">found here</a>.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web1.jpg"><img src="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web1.jpg"/></a>

When I last played with Mapserver there was very little decent data.  I had to settle for a very low resolution map of the world (vmap0) and some pretty poor raster data.  Yesterday, after a little googling, I found that OS have released <a href="http://mapserver.org/tutorial/example1-1.html">reasonable online tutorials</a> too.  I spent a little time getting this to render and the results are shown here in the scaled image above and full-size image below.

I decided to go with a black background as I was thinking of a Raspberry Pi based "GPS" system to show The Duke's location on a small screen.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web2.png"><img src="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web2.png"/></a>

Note that the image is a little jagged looking.  Turns out you can fix this by adding the following snippet into your map file (under the MAP element).  Of course, you pay a high price in processing time and image size.

[sourcecode]
OUTPUTFORMAT
NAME png
DRIVER &quot;AGG/PNG&quot;
MIMETYPE &quot;image/png&quot;
		# Change to IMAGEMODE RGBA for transparent background
IMAGEMODE RGB
EXTENSION &quot;png&quot;
FORMATOPTION &quot;INTERLACE=OFF&quot;
END
[/sourcecode]

<a href="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web3.jpg"><img src="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web3.jpg"/></a>

The benefit of Mapserver over Google or Bing maps is that it works on local data with no requirement for an internet connection. This means it's more reliable for a GPS sort of system. Of course, the downside is that there are fewer layers available and you have to do a lot of coding. Was good to play with, though, and I hope to get it working on the raspberry soon.

Here's my (large) Map file:

[sourcecode]
MAP
	IMAGETYPE      PNG
	#Whole UK would be...
	#EXTENT         0 0 660000 1230000
	# SU Grid is...
	#EXTENT		400000 100000 500000 200000
	# The top right of SU is...
	EXTENT		450000 150000 500000 200000

	FONTSET &quot;C:\Users\Dan.Taylor\Desktop\Mapfiles\Fonts.txt&quot;

	SIZE           6400 6400
	IMAGECOLOR     0 0 0
	CONFIG &quot;MS_ERRORFILE&quot; &quot;C:\Users\Dan.Taylor\Desktop\Mapfiles\Errors.txt&quot;
	CONFIG &quot;CPL_DEBUG&quot; &quot;ON&quot;
	CONFIG &quot;PROJ_DEBUG&quot; &quot;ON&quot;
	DEBUG 5

	SYMBOL
		NAME &quot;triangle&quot;
		TYPE vector
		POINTS
			0 1
			0.5 0
			1 1
			0 1
		END
		FILLED TRUE
    END

	LAYER
		NAME         Woodland
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_Woodland&quot;
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 20 40 20
			END
		END
	END

	LAYER
		NAME         SurfaceWater
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_SurfaceWater_Area&quot;
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 80 80 128
			END
		END
	END

	LAYER
		NAME         TidalWater
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_TidalWater&quot;
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 60 60 100
			END
		END
	END

	LAYER
		NAME         Buildings
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_Building&quot;
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 80 60 80
			END
		END
	END

	LAYER
		NAME         Foreshore
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_Foreshore&quot;
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 80 80 128
			END
		END
	END

	LAYER
		NAME         Roads
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_Road&quot;
		STATUS       default
		TYPE         LINE

		CLASSITEM	&quot;CLASSIFICA&quot;

		CLASS
			EXPRESSION	 &quot;Motorway&quot;
			STYLE
				COLOR 20 128 128
				WIDTH 8
			END
			STYLE
				COLOR 200 200 255
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 &quot;Primary Road&quot;
			STYLE
				COLOR 20 128 20
				WIDTH 6
			END
			STYLE
				COLOR 200 200 0
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 &quot;A Road&quot;
			STYLE
				COLOR 20 128 20
				WIDTH 6
			END
		END
		CLASS
			EXPRESSION	 &quot;B Road&quot;
			STYLE
				COLOR 128 20 20
				WIDTH 4
			END
		END
		CLASS
			EXPRESSION	&quot;Private Road, Publicly Accessible&quot;
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 &quot;Local Street&quot;
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	&quot;Pedestrianised Street&quot;
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 &quot;Minor Road&quot;
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
	END

	SYMBOL
		NAME &quot;station&quot;
		TYPE ellipse
		FILLED true
		POINTS
			1 1
		END
	END

	LAYER
		NAME         RailwayStations
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_RailwayStation&quot;
		STATUS       default
		TYPE         POINT
		LABELITEM    &quot;NAME&quot;

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 10
				POSITION cr
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL station
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
				SIZE 20
			END
		END
	END

	LAYER
		NAME         Airports
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_Airport&quot;
		STATUS       default
		TYPE         POINT
		LABELITEM    &quot;NAME&quot;

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 10
				POSITION cr
				COLOR 255 180 50
				OUTLINECOLOR 100 50 0
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL &quot;triangle&quot;
				COLOR 255 180 50
				OUTLINECOLOR 100 50 0
				SIZE 20
			END
		END
	END

	LAYER
		NAME         NamedPlaces
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_NamedPlace&quot;
		STATUS       off
		TYPE         POINT
		LABELITEM    &quot;NAME&quot;

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 10
				POSITION cc
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL station
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
			END
		END
	END

	LAYER
		NAME         MotorwayJunctions
		DATA         &quot;C:\Users\Dan.Taylor\Desktop\SU\SU_MotorwayJunction&quot;
		STATUS       default
		TYPE         POINT
		LABELITEM    &quot;JUNCTIONNU&quot;

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 14
				POSITION cc
				COLOR 200 200 255
				OUTLINECOLOR 200 200 255
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL station
				COLOR 20 128 128
				OUTLINECOLOR 200 200 255
				SIZE 30
			END
		END
	END
END
[/sourcecode]